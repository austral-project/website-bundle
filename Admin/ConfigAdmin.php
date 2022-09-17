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

use Austral\AdminBundle\Admin\Event\FilterEventInterface;
use Austral\EntityBundle\Entity\Interfaces\SeoInterface;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;

use Austral\FilterBundle\Filter\Type as FilterType;
use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\FormBundle\Field as Field;
use Austral\ListBundle\Column as Column;

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
      ->addColumn(new Column\Value("name"))
      ->addColumn(new Column\Value("keyname"))
      ->addColumn(new Column\Value("contentText"))
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
    $pages = $this->container->get("austral.seo.pages")->getUrls();
    /** @var SeoInterface|EntityInterface $object */
    foreach($pages as $object)
    {
      if(!array_key_exists($object->getClassname(), $pagesList))
      {
        $pagesList[$object->getClassname()] = array();
      }
      $pagesList[$object->getClassname()][$object->__toString()] = "{$object->getClassname()}:{$object->getId()}";
    }

    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.generalInformation")
        ->add(Field\TextField::create("name"))
        ->add(Field\TextField::create("keyname"))
      ->end()
      ->addFieldset("fieldset.content")
        ->add(Field\SelectField::create("type", array(
              "choices.config.type.all"             =>  "all",
              "choices.config.type.text"            =>  "text",
              "choices.config.type.internal-link"   =>  "internal-link",
              "choices.config.type.image"           =>  "image",
              "choices.config.type.imageText"       =>  "image-text",
              "choices.config.type.file"            =>  "file",
              "choices.config.type.fileText"        =>  "file-text",
              "choices.config.type.checkbox"        =>  "checkbox",
            ),
            array(
              "required"    =>  true,
              "attr"        =>  array(
                'data-view-by-choices' =>  json_encode(array(
                  'all'           =>  "element-view-all",
                  "text"          =>  "element-view-text",
                  "internal-link" =>  "element-view-internal-link",
                  "image"         =>  "element-view-image",
                  "image-text"    =>  array("element-view-image", "element-view-text"),
                  "file"          =>  "element-view-file",
                  "file-text"     =>  array("element-view-file", "element-view-text"),
                  "checkbox"      =>  "element-view-checkbox",
                ))
              )
            )
          )
        )
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