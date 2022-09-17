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
      FormAdminEvent::EVENT_UPDATE_BEFORE     =>  "formUpdateBefore",
      FormAdminEvent::EVENT_END               =>  "formEnd",
      ListAdminEvent::EVENT_END               =>  "listEnd",
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
   * @param string|null $domainId
   *
   * @return bool
   */
  protected function isMultiDomain(?string $domainId = null): bool
  {
    $isMultidomain = false;
    if(!$domainId)
    {
      if($this->container->get("austral.http.domains.management")->getEnabledDomainWithoutVirtual() > 1)
      {
        $isMultidomain = true;
      }
    }
    return $isMultidomain;
  }


  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    /** @var string|null $domainId */
    $domainId = $listAdminEvent->getCurrentModule()->getParametersByKey("austral_filter_by_domain");
    $isMultiDomain = $this->isMultiDomain($domainId);
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
      );

      if(!$isMultiDomain)
      {
        $listAdminEvent->getListMapper()->getSection("homepage")
          ->setMapperType("list")
          ->setTitle("pages.list.page.homepage")
          ->buildDataHydrate(function(DataHydrateORM $dataHydrate) {
            $dataHydrate->addQueryBuilderClosure(function(QueryBuilder $queryBuilder) {
              $queryBuilder->andWhere("root.isHomepage = :isHomepage")
                ->setParameter("isHomepage", true);
              return $queryBuilder;
            });
            $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
              return $queryBuilder->orderBy("root.position", "ASC")
                ->leftJoin("root.translates", "translates")->addSelect("translates")
                ->addOrderBy("translates.language", "ASC");
            });
          })
          ->end();
      }

      $listAdminEvent->getListMapper()->getSection("default")
        ->setMapperType("list")
        ->childrenRow(function(PageInterface $page) {
          return $page->getChildren();
        })
        ->buildDataHydrate(function(DataHydrateORM $dataHydrate) use($isMultiDomain) {
          $dataHydrate->addQueryBuilderClosure(function(QueryBuilder $queryBuilder) use($isMultiDomain) {
            if(!$isMultiDomain)
            {
              $queryBuilder->leftJoin("root.parent", "parent")
                ->andWhere("parent.isHomepage = :isParentHomepage")
                ->setParameter("isParentHomepage", true)
                ->andWhere("root.isHomepage != :isHomepage")
                ->setParameter("isHomepage", true);
            }
            else
            {
              $queryBuilder->leftJoin("root.parent", "parent")
                ->andWhere("parent.id IS NULL");
            }
          });
          $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
            return $queryBuilder
              ->leftJoin("root.translates", "translates")->addSelect("translates")
              ->leftJoin("root.children", "children")->addSelect("children")
              ->leftJoin("children.children", "subChildren")->addSelect("subChildren")
              ->leftJoin("subChildren.children", "ThreeChildren")->addSelect("ThreeChildren")
              ->orderBy("root.position", "ASC")
              ->addOrderBy("translates.language", "ASC");
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
    /** @var string|null $domainId */
    $domainId = $formAdminEvent->getCurrentModule()->getParametersByKey("austral_filter_by_domain");
    $isMultiDomain = $this->isMultiDomain($domainId);

    $countPages = $this->container->get('austral.entity_manager.page')->countAll(function(QueryBuilder $queryBuilder) use ($domainId) {
      $queryBuilder->where("root.domainId = :domainId")
        ->setParameter("domainId", $domainId);
    });
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->setViewName(false)
        ->add(Field\EntityField::create("parent", Page::class,
          array(
            'query_builder'     => function (EntityRepository $er) use($formAdminEvent, $domainId) {
              $queryBuilder = $er->createQueryBuilder('u')
                ->where("u.id != :pageId")
                ->setParameter("pageId", $formAdminEvent->getFormMapper()->getObject()->getId())
                ->leftJoin("u.translates", "translates")->addSelect("translates")
                ->orderBy('translates.refUrl', 'ASC');
              if($domainId) {
                $queryBuilder->andWhere("u.domainId = :domainId")
                  ->setParameter("domainId", $domainId);
              }
              else {
                $queryBuilder->andWhere("u.domainId IS NULL");
              }
              return $queryBuilder;
            },
            'choice_label' => '__toString',
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
          ), array('isView' => function() use($isMultiDomain) {
            return $this->container->get('security.authorization_checker')->isGranted('ROLE_ROOT') && !$isMultiDomain;
          })
        ))
      ->end()

      ->addFieldset("fieldset.dev.config")
        ->setCollapse(true)
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
  
        ->add(Field\TextField::create("name", array(
            "entitled"    => "fields.mainTitle.entitled",
            "placeholder" => "fields.mainTitle.placeholder"
          )
        ))
        ->add(Field\TextField::create("refH1", array(
            "placeholder" => "fields.refH1.placeholder",
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
   * @final
   */
  protected function formEnd(FormAdminEvent $formAdminEvent)
  {
    if($formAdminEvent->getFormMapper()->getObject()->getIsHomepage())
    {
      $formAdminEvent->getFormMapper()
        ->getSubFormMapperByKey("urlParameters")
        ->removeAllField("pathLast");
    }
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

    /** @var string|null $domainId */
    $domainId = $formAdminEvent->getCurrentModule()->getParametersByKey("austral_filter_by_domain");
    $isMultiDomain = $this->isMultiDomain($domainId);
    if($initDefaultParent && !$object->getIsHomepage() && !$isMultiDomain)
    {
      $parentDefault = $this->container->get("austral.entity_manager.page")->retreiveByKeyname("homepage", function(QueryBuilder $queryBuilder) use($domainId) {
        $queryBuilder->andWhere("root.domainId = :domainId")
          ->setParameter("domainId", $domainId);
      });
      $object->setParent($parentDefault);
    }
  }




}