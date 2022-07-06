<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Listener;

use Austral\AdminBundle\Event\DashboardEvent;
use Austral\EntitySeoBundle\Entity\Interfaces\EntityRobotInterface;
use Austral\WebsiteBundle\Services\ConfigVariable;

use Austral\AdminBundle\Dashboard\Values as DashboardValues;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral DashboardListener Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DashboardListener
{

  /**
   * @var ConfigVariable
   */
  protected ConfigVariable $configVariable;
  /**
   * @var ContainerInterface
   */
  protected ContainerInterface $container;
  /**
   * @var Request|null
   */
  protected ?Request $request;

  /**
   * @param ContainerInterface $container
   * @param RequestStack $requestStack
   * @param ConfigVariable $configVariable
   */
  public function __construct(ContainerInterface $container, RequestStack $requestStack, ConfigVariable $configVariable)
  {
    $this->container = $container;
    $this->request = $requestStack->getCurrentRequest();
    $this->configVariable = $configVariable;
  }


  /**
   * @param DashboardEvent $dashboardEvent
   *
   * @throws \Exception
   */
  public function dashboard(DashboardEvent $dashboardEvent)
  {
    $modules = $this->container->get('austral.admin.modules');

    $nbPagesPublished = $this->container->get('austral.entity_manager.page')->countAll(function(QueryBuilder $queryBuilder) use($modules) {
      $queryBuilder->leftJoin("root.translates", "translates")
        ->where("translates.status = :status")
        ->andWhere("translates.language = :language")
        ->setParameter("status", EntityRobotInterface::STATUS_PUBLISHED)
        ->setParameter("language", $modules->getModuleByKey("page")->getLanguageDefault());
    });

    $nbPagesDraft = $this->container->get('austral.entity_manager.page')->countAll(function(QueryBuilder $queryBuilder) use($modules) {
      $queryBuilder->leftJoin("root.translates", "translates")
        ->where("translates.status = :status")
        ->andWhere("translates.language = :language")
        ->setParameter("status", EntityRobotInterface::STATUS_DRAFT)
        ->setParameter("language", $modules->getModuleByKey("page")->getLanguageDefault());
    });

    $dashboardTilePagesPublished = new DashboardValues\Tile("pages_published");
    $dashboardTilePagesPublished->setEntitled("dashboard.tiles.pages.published.entitled")
      ->setIsTranslatableText(true)
      ->setColorNum(4)
      ->setPicto("file")
      ->setValue($nbPagesPublished);

    $dashboardTilePagesDraft = new DashboardValues\Tile("pages_draft");
    $dashboardTilePagesDraft->setEntitled("dashboard.tiles.pages.draft.entitled")
      ->setIsTranslatableText(true)
      ->setPicto("design")
      ->setColorNum(5)
      ->setValue($nbPagesDraft);

    if($modules->getModuleByKey("page")->isGranted())
    {
      $dashboardTilePagesPublished->setUrl($modules->getModuleByKey("page")->generateUrl("list"));
      $dashboardTilePagesDraft->setUrl($modules->getModuleByKey("page")->generateUrl("list"));
    }


    $dashboardEvent->getDashboardBlock()->getChild("austral_tiles_values")
      ->addValue($dashboardTilePagesPublished)
      ->addValue($dashboardTilePagesDraft);


    $dashboardOnOffIndex = new DashboardValues\OnOff("onOff_index");
    $dashboardOnOffIndex->setEntitled("fields.isIndex.entitled")
      ->setDescription("fields.isIndex.information")
      ->setIsTranslatableText(true)
      ->setIsEnabled($this->configVariable->getValueVariableByKey("site.index", false) === true);

    $dashboardOnOffFollow = new DashboardValues\OnOff("onOff_follow");
    $dashboardOnOffFollow->setEntitled("fields.isFollow.entitled")
      ->setDescription("fields.isFollow.information")
      ->setIsTranslatableText(true)
      ->setIsEnabled($this->configVariable->getValueVariableByKey("site.follow", false) === true);

    $dashboardOnOffSitemap = new DashboardValues\OnOff("onOff_sitemap");
    $dashboardOnOffSitemap->setEntitled("fields.inSitemap.entitled")
      ->setDescription("fields.inSitemap.information")
      ->setIsTranslatableText(true)
      ->setIsEnabled( true);

    $dashboardEvent->getDashboardBlock()->getChild("austral_configuration_values")
      ->addValue($dashboardOnOffIndex)
      ->addValue($dashboardOnOffFollow)
      ->addValue($dashboardOnOffSitemap);

    if($modules->getModuleByKey("page")->isGranted("create"))
    {
      $dashboardActionPage = new DashboardValues\Action("austral_action_page");
      $dashboardActionPage->setEntitled("actions.create")
        ->setPosition(1)
        ->setPicto($modules->getModuleByKey("page")->getPicto())
        ->setIsTranslatableText(true)
        ->setUrl($modules->getModuleByKey("page")->generateUrl("create"))
        ->setTranslateParameters(array(
            "module_gender" =>  $modules->getModuleByKey("page")->translateGenre(),
            "module_name"   =>  $modules->getModuleByKey("page")->translateSingular()
          )
        );

      $dashboardEvent->getDashboardBlock()->getChild("austral_actions")
        ->addValue($dashboardActionPage);
    }

  }

}