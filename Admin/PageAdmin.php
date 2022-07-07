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
use Austral\AdminBundle\Admin\Event\FilterEventInterface;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;

use Austral\ContentBlockBundle\Field\ContentBlockField;
use Austral\FilterBundle\Filter\Type as FilterType;

use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\EntityBundle\Entity\EntityInterface;

use Austral\FormBundle\Field as Field;
use Austral\FormBundle\Mapper\Fieldset;
use Austral\FormBundle\Mapper\GroupFields;

use Austral\ListBundle\Column as Column;
use Austral\ListBundle\DataHydrate\DataHydrateORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Page Admin.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class PageAdmin extends Admin implements AdminModuleInterface
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
      ->add(new FilterType\StringChoiceType("status", array(
        "choices.status.unpublished"   =>  array(
          "value"   =>  "unpublished",
          "styles"  =>  array(
            "--element-choice-current-background:var(--color-main-20)",
            "--element-choice-current-color:var(--color-main-90)",
            "--element-choice-hover-color:var(--color-main-90)"
          )
        ),
        "choices.status.draft"         =>  array(
          "value"   =>  "draft",
          "styles"  =>  array(
            "--element-choice-current-background:var(--color-purple-20)",
            "--element-choice-current-color:var(--color-purple-100)",
            "--element-choice-hover-color:var(--color-purple-100)"
          )
        ),
        "choices.status.published"     =>  array(
          "value"   =>  "published",
          "styles"  =>  array(
            "--element-choice-current-background:var(--color-green-20)",
            "--element-choice-current-color:var(--color-green-100)",
            "--element-choice-hover-color:var(--color-green-100)"
          )
        )
      )))
      ->add(new FilterType\RangeType("created", array()));

    $listAdminEvent->getFilterMapper()->filter("homepage")
      ->add(new FilterType\StringType("name"))
      ->add(new FilterType\StringChoiceType("status", array(
        "choices.status.unpublished"   =>  array(
          "value"   =>  "unpublished",
          "styles"  =>  array(
            "--element-choice-current-background:var(--color-main-20)",
            "--element-choice-current-color:var(--color-main-90)",
            "--element-choice-hover-color:var(--color-main-90)"
          )
        ),
        "choices.status.draft"         =>  array(
          "value"   =>  "draft",
          "styles"  =>  array(
            "--element-choice-current-background:var(--color-purple-20)",
            "--element-choice-current-color:var(--color-purple-100)",
            "--element-choice-hover-color:var(--color-purple-100)"
          )
        ),
        "choices.status.published"     =>  array(
          "value"   =>  "published",
          "styles"  =>  array(
            "--element-choice-current-background:var(--color-green-20)",
            "--element-choice-current-color:var(--color-green-100)",
            "--element-choice-hover-color:var(--color-green-100)"
          )
        )
      )));
  }

  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $listAdminEvent->getListMapper()
      ->addColumn(new Column\Template("picto", " ", "@AustralWebsite/Admin/_Components/pagePicto.html.twig"))
      ->addColumn(new Column\Template("name", "form.labels.title", "@AustralWebsite/Admin/_Components/pageTitle.html.twig", array(
        "class" =>  "flex-1"
      )))
      ->addColumn(new Column\Template("children", "fields.page.children.entitled",
        "@AustralWebsite/Admin/_Components/pageChildren.html.twig",
          array('class' =>  "right-position")
        ), "nb-children"
      )
      ->addColumn(new Column\Choices("status", "fields.page.status.entitled", array(
          "published"       => array(
            "entitled" => "choices.status.published",
            "styles"  =>  array(
              "--element-choice-current-background:var(--color-green-20)",
              "--element-choice-current-color:var(--color-green-100)",
              "--element-choice-hover-color:var(--color-green-100)"
            )
          ),
          "draft"           => array(
            "entitled" => "choices.status.draft",
            "styles"  =>  array(
              "--element-choice-current-background:var(--color-purple-20)",
              "--element-choice-current-color:var(--color-purple-100)",
              "--element-choice-hover-color:var(--color-purple-100)"
            )
          ),
          "unpublished"     => array(
            "entitled" => "choices.status.unpublished",
            "styles"  =>  array(
              "--element-choice-current-background:var(--color-main-20)",
              "--element-choice-current-color:var(--color-main-90)",
              "--element-choice-hover-color:var(--color-main-90)"
            )
          ),
        ), $listAdminEvent->getCurrentModule()->generateUrl("change", array('language'=>"__language__")),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        ), "page-status"
      )
      ->getSection("homepage")
        ->setMapperType("list")
        ->setTitle("pages.list.page.homepage")
        ->buildDataHydrate(function(DataHydrateORM $dataHydrate) {
          $dataHydrate->addQueryBuilderClosure(function(QueryBuilder $queryBuilder) {
            return $queryBuilder->where("root.isHomepage = true");
          });
          $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
            return $queryBuilder->orderBy("root.position", "ASC")
              ->leftJoin("root.translates", "translates")->addSelect("translates")
              ->addOrderBy("translates.language", "ASC");
          });
        })
      ->end()

      ->getSection("default")
        ->setMapperType("list")
        ->childrenRow(function(PageInterface $page) {
          return $page->getChildren();
        })
        ->buildDataHydrate(function(DataHydrateORM $dataHydrate) {
          $dataHydrate->addQueryBuilderClosure(function(QueryBuilder $queryBuilder) {
            return $queryBuilder->where("root.isHomepage != true");
          });
          $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
            return $queryBuilder->leftJoin("root.parent", "parent")
              ->where("parent.isHomepage = :isHomepage")
              ->leftJoin("root.translates", "translates")->addSelect("translates")
              ->leftJoin("root.children", "children")->addSelect("children")
              ->leftJoin("children.children", "subChildren")->addSelect("subChildren")
              ->leftJoin("subChildren.children", "ThreeChildren")->addSelect("ThreeChildren")
              ->orderBy("root.position", "ASC")
              ->addOrderBy("translates.language", "ASC")
              ->setParameter("isHomepage", true);
          });
        })
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  public function configureFormMapper(FormAdminEvent $formAdminEvent)
  {
    $countPages = $this->container->get('austral.entity_manager.page')->countAll();
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->setViewName(false)
        ->add(Field\EntityField::create("parent", Page::class,
          array(
            'query_builder'     => function (EntityRepository $er) use($formAdminEvent){
              return $er->createQueryBuilder('u')
                ->where("u.id != :pageId")
                ->setParameter("pageId", $formAdminEvent->getFormMapper()->getObject()->getId())
                ->leftJoin("u.translates", "translates")->addSelect("translates")
                ->orderBy('u.position', 'ASC');
            },
            'choice_label' => 'path',
            'isView' => array(
              function($object) {
                return !$object->getIsHomepage();
              },
              $formAdminEvent->getFormMapper()->getObject()
            ),
            "required"  =>  $countPages > 0
          )
        ))
        ->add(Field\ChoiceField::create("isHomepage",
          array(
            "choices.status.no"     =>  array(
              "value"   =>  false,
              "styles"  =>  array(
                "--element-choice-current-background:var(color-main-20)",
                "--element-choice-current-color:var(--color-main-100)",
                "--element-choice-hover-color:var(--color-main-100)"
              )
            ),
            "choices.status.yes"     =>  array(
              "value"   =>  true,
              "styles"  =>  array(
                "--element-choice-current-background:var(--color-green-20)",
                "--element-choice-current-color:var(--color-green-100)",
                "--element-choice-hover-color:var(--color-green-100)"
              )
            )
          ), array('isView' => function(){
            return $this->container->get('security.authorization_checker')->isGranted('ROLE_ROOT');
          })
        ))
        ->add(Field\ChoiceField::create("status",
          array(
            "choices.status.unpublished"   =>  array(
              "value"   =>  "unpublished",
              "styles"  =>  array(
                "--element-choice-current-background:var(--color-main-20)",
                "--element-choice-current-color:var(--color-main-90)",
                "--element-choice-hover-color:var(--color-main-90)"
              )
            ),
            "choices.status.draft"         =>  array(
              "value"   =>  "draft",
              "styles"  =>  array(
                "--element-choice-current-background:var(--color-purple-20)",
                "--element-choice-current-color:var(--color-purple-100)",
                "--element-choice-hover-color:var(--color-purple-100)"
              )
            ),
            "choices.status.published"     =>  array(
              "value"   =>  "published",
              "styles"  =>  array(
                "--element-choice-current-background:var(--color-green-20)",
                "--element-choice-current-color:var(--color-green-100)",
                "--element-choice-hover-color:var(--color-green-100)"
              )
            )
          )
        ))
      ->end()

      ->addFieldset("fieldset.dev.config")
        ->setIsView($this->container->get("security.authorization_checker")->isGranted("ROLE_ROOT"))
        ->add(Field\TextField::create("keyname", array(
            "autoConstraints" => false,
            "isView" => $this->container->get("security.authorization_checker")->isGranted("ROLE_ROOT")
          )
        ))
        ->add(Field\TextField::create("australPictoClass",  array(
          "isView" => $this->container->get("security.authorization_checker")->isGranted("ROLE_ROOT"),
        )))
        ->add(Field\TextField::create("entityExtends", array(
          "isView" => $this->container->get("security.authorization_checker")->isGranted("ROLE_ROOT"),
        )))
      ->end()

      ->addFieldset("fieldset.generalInformation")
        ->add(Field\TextField::create("refH1", array(
            "placeholder" => "fields.refH1.placeholder",
          )
        ))
        ->add(Field\TextField::create("name", array(
            "placeholder" => "fields.titleNavigation.placeholder"
          )
        ))
        ->addGroup("generalInformations")
          ->addGroup("generalInformations")
            ->setDirection(GroupFields::DIRECTION_COLUMN)
            ->setStyle(GroupFields::STYLE_NONE)
            ->setSize(GroupFields::SIZE_COL_6)
            ->add(Field\TextareaField::create("summary", null, array(
                  'attr' => array(
                    'data-austral-tag' => ""
                  ),
                  "group" =>  array(
                    'class' =>  "full"
                  )
                )
              )
            )
          ->end()
          ->addGroup("image")
            ->setDirection(GroupFields::DIRECTION_COLUMN)
            ->setStyle(GroupFields::STYLE_NONE)
            ->setSize(GroupFields::SIZE_COL_6)

            ->add(Field\UploadField::create("image"))
          ->end()
        ->end()
        ->addPopin("popup-editor-image", "image", array(
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
          ->add(Field\TextField::create("imageAlt", array('entitled'=>"fields.alt.entitled")))
          ->add(Field\TextField::create("imageReelname", array('entitled'=>"fields.reelname.entitled")))
        ->end()
      ->end()

      ->addFieldset("fieldset.contentBlock")
        ->add(new ContentBlockField())
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  protected function formUpdateBefore(FormAdminEvent $formAdminEvent)
  {
    /** @var PageInterface|EntityInterface $object */
    $object = $formAdminEvent->getFormMapper()->getObject();
    if(!$object->getKeyname())
    {
      $object->setKeyname($object->getName()."-".$object->getId());
    }

    if($object->getIsHomepage())
    {
      $object->setParent(null);
      $object->setAustralPictoClass( "austral-picto-home");
    }

    $initDefaultParent = false;
    /** @var PageInterface|EntityInterface $parent */
    if($parent = $object->getParent())
    {
      if($parent->getId() == $object->getId())
      {
        $initDefaultParent = true;
      }
    }
    else
    {
      $initDefaultParent = true;
    }
    if($initDefaultParent && !$object->getIsHomepage())
    {
      $parentDefault = $this->container->get("austral.entity_manager.page")->retreiveByKeyname("homepage");
      $object->setParent($parentDefault);
    }
  }




}