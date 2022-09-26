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
use Austral\EntityBundle\Entity\Interfaces\RobotInterface;
use Austral\WebsiteBundle\Services\ConfigVariable;

use Austral\AdminBundle\Dashboard\Values as DashboardValues;
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