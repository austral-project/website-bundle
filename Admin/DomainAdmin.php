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
use App\Entity\Austral\WebsiteBundle\Page;
use Austral\FormBundle\Mapper\Fieldset;
use Austral\FormBundle\Mapper\GroupFields;
use Austral\WebsiteBundle\Entity\Domain;
use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Austral\WebsiteBundle\Form\Type\SubDomainFormType;
use Austral\WebsiteBundle\Model\SubDomain;

use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\FormBundle\Field as Field;
use Austral\FormBundle\Mapper\FormMapper;
use Austral\ListBundle\Column as Column;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints as Constraints;

use Exception;

/**
 * Domain Admin.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class DomainAdmin extends Admin implements AdminModuleInterface
{

  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $listAdminEvent->getListMapper()
      ->addColumn(new Column\Value("domain"))
      ->addColumn(new Column\SwitchValue("isEnabled", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\SwitchValue("isMaster", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\SwitchValue("isVirtual", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\SwitchValue("onePage", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\Date("updated", null, "d/m/Y"));
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  public function configureFormMapper(FormAdminEvent $formAdminEvent)
  {
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->setViewName(false)
        ->add(Field\ChoiceField::create("isEnabled",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\ChoiceField::create("onePage",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\ChoiceField::create("isMaster",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\ChoiceField::create("isVirtual",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
      ->end()
      ->addFieldset("fieldset.generalInformation")
        ->addGroup("domain")
          ->add(Field\SelectField::create('scheme', array(
                DomainInterface::SCHEME_HTTPS => DomainInterface::SCHEME_HTTPS,
                DomainInterface::SCHEME_HTTP  => DomainInterface::SCHEME_HTTP,
              ), array(
                'required' => true
              )
            )->setGroupSize(GroupFields::SIZE_COL_2)
          )
          ->add(Field\TextField::create("domain")->setGroupSize(GroupFields::SIZE_COL_10))
        ->end()
        ->add(Field\TextField::create("name", array("entitled"=>"fields.nameDomain.entitled")))
        ->add(Field\TextField::create("language"))
        ->add(Field\TextField::create("redirectUrl"))
        ->add(Field\UploadField::create("favicon"))
        ->addPopin("popup-editor-favicon", "favicon", array(
            "button"  =>  array(
              "entitled"            =>  "actions.picture.edit",
              "picto"               =>  "",
              "class"               =>  "button-action"
            ),
            "popin"  =>  array(
              "id"            =>  "upload",
              "template"      =>  "uploadEditor",
            )
          )
        )
        ->end()
        ->add(Field\EntityField::create("homepage", Page::class, array(
          'query_builder'     => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
              ->leftJoin("u.translates", "translates")->addSelect("translates")
              ->orderBy('u.position', 'ASC');
          },
          "required"  =>  false
        )))
        ->addGroup("subDomain", "fields.subDomains.entitled")
          ->add($this->createCollectionSubDomain($formAdminEvent))
        ->end()
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @return Field\CollectionEmbedField
   * @throws Exception
   */
  protected function createCollectionSubDomain(FormAdminEvent $formAdminEvent): Field\CollectionEmbedField
  {
    $subDomainFormMapper = new FormMapper();
    $subDomain = new SubDomain();
    $subDomainFormMapper->setObject($subDomain)
      ->add(Field\TextField::create("subDomain", array("entitled"=>false))->setConstraints(array(
          new Constraints\NotNull(),
          new Constraints\Length(array(
              "max" => 255,
              "maxMessage" => "errors.length.max"
            )
          )
        )
      )
    );
    $formAdminEvent->getFormMapper()->addSubFormMapper("subDomains", $subDomainFormMapper);
    /** @var SubDomainFormType $subDomainFormType */
    $subDomainFormType = $this->container->get('austral.website.subDomain_form_type')->setFormMapper($subDomainFormMapper);
    return Field\CollectionEmbedField::create("subDomains", array(
        "button"              =>  "button.new.subDomain",
        "collections"        =>  array(
          "objects"             =>  "subDomains"
        ),
        "allow"               =>  array(
          "child"               =>  false,
          "add"                 =>  true,
          "delete"              =>  true,
        ),
        "entry"               =>  array("type"  =>  get_class($subDomainFormType)),
        "prototype"           =>  array("data"  =>  $subDomain),
        "sortable"            =>  array(
          "value"               =>  "id"
        )
      )
    );
  }

}