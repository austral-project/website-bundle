<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\WebsiteBundle\Admin;

use App\Entity\Austral\WebsiteBundle\ConfigValueByDomain;
use Austral\AdminBundle\Admin\Event\FilterEventInterface;
use Austral\FormBundle\Mapper\Fieldset;
use Austral\FormBundle\Mapper\FormMapper;
use Austral\HttpBundle\Entity\Domain;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\ListBundle\DataHydrate\DataHydrateORM;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;

use Austral\FilterBundle\Filter\Type as FilterType;
use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\FormBundle\Field as Field;
use Austral\ListBundle\Column as Column;

use Austral\WebsiteBundle\Event\ConfigVariableFunctionEvent;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Config Admin.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class ConfigAdmin extends Admin implements AdminModuleInterface
{

  /**
   * @return array
   */
  public function getEvents() : array
  {
    return array(
      FormAdminEvent::EVENT_UPDATE_BEFORE =>  "formUpdateBefore"
    );
  }

  /**
   * @param FilterEventInterface $listAdminEvent
   *
   * @return void
   * @throws Exception
   */
  public function configureFilterMapper(FilterEventInterface $listAdminEvent)
  {
    $listAdminEvent->getFilterMapper()->filter("default")
      ->add(new FilterType\StringType("name"))
      ->add(new FilterType\StringType("keyname"))
      ->add(new FilterType\StringChoiceType("type", array(
          "choices.config.type.text"            =>  "text",
          "choices.config.type.internal-link"   =>  "internal-link",
          "choices.config.type.image"           =>  "image",
          "choices.config.type.imageText"       =>  "image-text",
          "choices.config.type.file"            =>  "file",
          "choices.config.type.fileText"        =>  "file-text",
          "choices.config.type.checkbox"        =>  "checkbox",
          "choices.config.type.function-name"   =>  "function-name",
        ), array(
          "formField"   =>  array(
            "class"       =>  Field\SelectField::class
          )
        ))
      )
      ->add(new FilterType\RangeType("updated", array()));
  }

  /**
   * @param ListAdminEvent $listAdminEvent
   *
   * @throws Exception
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $listAdminEvent->getListMapper()
      ->buildDataHydrate(function(DataHydrateORM $dataHydrate) {
        $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
          return $queryBuilder->orderBy("root.name", "ASC");
        });
      })
      ->addColumn(new Column\Value("name"))
      ->addColumn(new Column\Value("keyname"))
      ->addColumn(new Column\Value("contentText"))
      ->addColumn(new Column\Languages())
      ->addColumn(new Column\Date("updated", null, "d/m/Y"));
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  public function configureFormMapper(FormAdminEvent $formAdminEvent)
  {
    $pagesList = array();

    /** @var DomainsManagement $domainsManagement */
    $domainsManagement = $this->container->get('austral.http.domains.management');

    if($domainsManagement->getEnabledDomainWithoutVirtual())
    {
      $formAdminEvent->getFormMapper()->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->add(Field\ChoiceField::create("withDomain",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ), array(
            "attr"        =>  array(
              "data-view-by-choices-parent"   =>  ".form-container",
              "data-view-by-choices-children" =>  ".fieldset-by-choice",
              'data-view-by-choices' =>  json_encode(array(
                1           =>  "fieldset-values-by-domain",
                0           =>  "fieldset-values-for-all-domains"
              ))
            ),

          ))
        )
      ->end();
    }
    $formAdminEvent->getFormMapper()->addFieldset("fieldset.generalInformation")
        ->add(Field\TextField::create("name"))
        ->add(Field\TextField::create("keyname"))
        ->add(Field\SelectField::create("type", array(
            "choices.config.type.all"             =>  "all",
            "choices.config.type.text"            =>  "text",
            "choices.config.type.internal-link"   =>  "internal-link",
            "choices.config.type.image"           =>  "image",
            "choices.config.type.imageText"       =>  "image-text",
            "choices.config.type.file"            =>  "file",
            "choices.config.type.fileText"        =>  "file-text",
            "choices.config.type.checkbox"        =>  "checkbox",
            "choices.config.type.function-name"   =>  "function-name",
          ),
            array(
              "required"    =>  true,
              "attr"        =>  array(
                "data-view-by-choices-parent" =>  ".form-container .central-container",
                'data-view-by-choices' =>  json_encode(array(
                  'all'           =>  "element-view-all",
                  "text"          =>  "element-view-text",
                  "internal-link" =>  "element-view-internal-link",
                  "image"         =>  "element-view-image",
                  "image-text"    =>  array("element-view-image", "element-view-text"),
                  "file"          =>  "element-view-file",
                  "file-text"     =>  array("element-view-file", "element-view-text"),
                  "checkbox"      =>  "element-view-checkbox",
                  "function-name" =>  "element-view-function-name",
                ))
              )
            )
          )
        )
      ->end();

      $formAdminEvent->getFormMapper()->addFieldset("fieldset.content")
        ->setAttr(array("class" =>  "fieldset-content-parent fieldset-by-choice fieldset-values-for-all-domains"))
        ->add(Field\TextField::create("functionName", array("container" =>  array('class'=>"view-element-by-choices element-view-function-name"))))
        ->add(Field\TemplateField::create(
          "functionNameKey",
          "@AustralWebsite/Admin/Config/function-name-key.html.twig",
          array("container" =>  array('class'=>"view-element-by-choices element-view-function-name")),
          array("configVariable_functionBase" =>  ConfigVariableFunctionEvent::EVENT_AUSTRAL_CONFIG_VARIABLE_FUNCTION_BASE)
        ))
        ->add(Field\TextareaField::create("contentText", null, array("container" =>  array('class'=>"view-element-by-choices element-view-all element-view-text"))))
        ->add(Field\SelectField::create("internalLink", $pagesList, array("container"  =>  array('class'=>"view-element-by-choices element-view-all element-view-internal-link"))))
        ->add(Field\SwitchField::create("contentBoolean", array("container"  =>  array('class'=>"view-element-by-choices element-view-all element-view-checkbox"))))
        ->add(Field\UploadField::create("image", array("container"  =>  array('class'=>"view-element-by-choices element-view-all element-view-image"))))
        ->add(Field\UploadField::create("file", array(
            "container"   =>  array('class'=>"view-element-by-choices element-view-all element-view-file"),
            "blockSize"   =>  Field\UploadField::LIGHT
          )
        ))
      ->end();

      if($domainsManagement->getEnabledDomainWithoutVirtual())
      {
        $this->addFieldValuesByDomain($formAdminEvent->getFormMapper(), $domainsManagement);
      }

  }


  protected function addFieldValuesByDomain(FormMapper $formMapper, DomainsManagement $domainsManagement)
  {
    $configValuesByDomainExist = $formMapper->getObject()->getTranslateCurrent()->getValuesByDomain();

    $configValuesByDomain = array();
    /** @var ConfigValueByDomain $valueByDomain */
    foreach ($configValuesByDomainExist as $valueByDomain)
    {
      $configValuesByDomain[$valueByDomain->getDomainId()] = $valueByDomain;
    }

    /** @var Domain $domain */
    foreach ($domainsManagement->getDomainsWithoutVirtual() as $domain)
    {
      if(!array_key_exists($domain->getId(), $configValuesByDomain))
      {
        $configValueByDomain = new ConfigValueByDomain();
        $configValueByDomain->setDomainId($domain->getId());
        $configValuesByDomain[$domain->getId()] = $configValueByDomain;
      }
    }

    $configByValueDomain = new ConfigValueByDomain();
    $configByValueDomainFormMapper = new FormMapper();
    $configByValueDomainFormMapper->setObject($configByValueDomain);


    $valueByDomainFormType = $this->container->get('austral.website.config_value_by_domain.form_type');
    $valueByDomainFormType->setFormMapper($configByValueDomainFormMapper);

    $formMapper->addSubFormMapper("valuesByDomains", $configByValueDomainFormMapper);


    $configByValueDomainFormMapper->addFieldset("fieldset.content_by_domain")
      ->setAttr(array("class" =>  "fieldset-content-parent fieldset-by-choice fieldset-values-by-domain"))
      ->setClosureTranslateArgument(function(Fieldset $fieldset, $object) use($domainsManagement) {
        $fieldset->addTranslateArguments("%domainName%",
          $domainsManagement->getDomainById($object->getDomainId()) ? $domainsManagement->getDomainById($object->getDomainId())->getName() : "");
      })
      ->add(Field\TextareaField::create("contentText", null, array("container" =>  array('class'=>"view-element-by-choices element-view-all element-view-text"))))
      ->add(Field\SelectField::create("internalLink", array(), array("container"  =>  array('class'=>"view-element-by-choices element-view-all element-view-internal-link"))))
      ->add(Field\SwitchField::create("contentBoolean", array("container"  =>  array('class'=>"view-element-by-choices element-view-all element-view-checkbox"))))
      ->add(Field\UploadField::create("image", array("container"  =>  array('class'=>"view-element-by-choices element-view-all element-view-image"))))
      ->add(Field\UploadField::create("file", array(
          "container"   =>  array('class'=>"view-element-by-choices element-view-all element-view-file"),
          "blockSize"   =>  Field\UploadField::LIGHT
        )
      ))
    ->end();

    $formMapper->addFieldset("valuesByDomains", false)
      ->setPositionName(Fieldset::POSITION_NONE)
      ->add(Field\CollectionEmbedField::create("valuesByDomains", array(
          "button"              =>  "button.new.emailAddress",
          "allow"               =>  array(
            "child"               =>  false,
            "add"                 =>  false,
            "delete"              =>  false,
          ),
          "entry"               =>  array("type"  => get_class($valueByDomainFormType)),
          "sortable"            =>  array(
            "value"               =>  function($configValueByDomain) use($domainsManagement) {
              return $domainsManagement->getDomainById($configValueByDomain->getDomainId())->getPosition()."-".$configValueByDomain->getDomainId();
            },
          ),
          "getter"              =>  function($object) use($configValuesByDomain) {
            return $configValuesByDomain;
          },
          "setter"              =>  function($object) use($configValuesByDomain) {
            /** @var ConfigValueByDomain $configValueByDomain */
            foreach($configValuesByDomain as $configValueByDomain)
            {
              $configValueByDomain->setConfig($object->getTranslateCurrent());
              $object->getTranslateCurrent()->addValueByDomain($configValueByDomain);
            }
          }
        )
      ))
      ->end();


  }


  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  protected function formUpdateBefore(FormAdminEvent $formAdminEvent)
  {
    /** @var ConfigInterface|EntityInterface $object */
    $object = $formAdminEvent->getFormMapper()->getObject();
    if(!$object->getKeyname()) {
      $object->setKeyname($object->getName());
    }
  }

}