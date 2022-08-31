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

use Austral\AdminBundle\Configuration\AdminConfiguration;
use Austral\AdminBundle\Event\ModuleEvent;
use Austral\EntityFileBundle\File\Link\Generator;
use Austral\ToolsBundle\AustralTools;
use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;

use Austral\WebsiteBundle\EntityManager\PageEntityManager;
use Austral\WebsiteBundle\Services\DomainRequest;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Austral DashboardListener Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ModuleListener
{

  /**
   * @var TranslatorInterface
   */
  protected TranslatorInterface $translator;

  /**
   * @var DomainRequest
   */
  protected DomainRequest $domainRequest;

  /**
   * @var AdminConfiguration
   */
  protected AdminConfiguration $adminConfiguration;

  /**
   * @var Generator
   */
  protected Generator $fileLinkGenerator;

  /**
   * @var PageEntityManager
   */
  protected PageEntityManager $pageEntityManager;


  /**
   * @param TranslatorInterface $translator
   * @param DomainRequest $domainRequest
   * @param PageEntityManager $pageEntityManager
   * @param Generator $fileLinkGenerator
   * @param AdminConfiguration $adminConfiguration
   */
  public function __construct(TranslatorInterface $translator,
    DomainRequest $domainRequest,
    PageEntityManager $pageEntityManager,
    Generator $fileLinkGenerator,
    AdminConfiguration $adminConfiguration)
  {
    $this->translator = $translator;
    $this->domainRequest = $domainRequest;
    $this->pageEntityManager = $pageEntityManager;
    $this->fileLinkGenerator = $fileLinkGenerator;
    $this->adminConfiguration = $adminConfiguration;
  }


  /**
   * @param ModuleEvent $moduleEvent
   *
   * @throws \Exception
   */
  public function moduleAdd(ModuleEvent $moduleEvent)
  {
    if($moduleEvent->getModule()->getModuleKey() === "page")
    {
      $modulePageChange = false;
      $domains = $this->domainRequest->selectEnabledAndNotVirtual();
      $moduleParameters = $moduleEvent->getModuleParameters();
      $moduleParameters["translate_disabled"] = true;
      $moduleName = $moduleParameters["name"];
      if(count($domains) > 1) {
        /** @var DomainInterface $domain */
        foreach($domains as $domain)
        {
          $moduleParameters["name"] = "{$moduleName} - {$domain->getName()}";
          $modulePageChange = true;
          $moduleSubKey = "{$moduleEvent->getModule()->getModulePath()}-{$domain->getDomain()}";
          $moduleSubPath = "{$moduleEvent->getModule()->getModulePath()}/{$domain->getDomain()}";
          $moduleEvent->getModules()->addModule($moduleSubPath,
            $moduleParameters,
            $moduleSubKey,
            "entity",
            false,
            $moduleEvent->getModule()->navigationPosition(),
            array(),
            $moduleEvent->getModule()
          );

          $moduleEvent->getModules()->getModules()[$moduleSubPath]->setTranslates(array(
              'singular'  =>  $this->trans("pages.names.pageByDomain.singular", array('%domainName%'=>$domain->__toString())),
              'plural'    =>  $this->trans("pages.names.pageByDomain.plural", array('%domainName%'=>$domain->__toString())),
              "type"      =>  AustralTools::getValueByKey($moduleEvent->getModule()->getParameters(), "translate", "default"),
              "key"       =>  "pageByDomain"
            )
          );

          $homepageId = $domain->getHomepage() ? $domain->getHomepage()->getId() : "create";
          $countPages = $this->pageEntityManager->countAll(function(QueryBuilder $queryBuilder) use ($homepageId) {
            $queryBuilder->where("root.homepageId = :homepageId")
              ->setParameter("homepageId", $homepageId);
          });
          $moduleEvent->getModules()->getModules()[$moduleSubPath]->addParameters("filters", array('homepageId'=>$homepageId));
          $moduleEvent->getModules()->getModules()[$moduleSubPath]->addParameters("domain", $domain);
          $moduleEvent->getModules()->getModules()[$moduleSubPath]->addParameters("tile", array(
            "subEntitled"   =>  $this->trans("pages.names.pageByDomain.countElement", array('%count%'=>$countPages)),
            "img"           =>  $this->fileLinkGenerator->image($domain, "favicon")
          ));
        }
        if($modulePageChange)
        {

          $moduleParameters["name"] = "{$moduleName} - All";
          $moduleSubKey = "{$moduleEvent->getModule()->getModulePath()}-all";
          $moduleSubPath = "{$moduleEvent->getModule()->getModulePath()}/all";
          $moduleEvent->getModules()->addModule($moduleSubPath,
            $moduleParameters,
            $moduleSubKey,
            "entity",
            false,
            $moduleEvent->getModule()->navigationPosition(),
            array(),
            $moduleEvent->getModule()
          );

          $moduleEvent->getModules()->getModules()[$moduleSubPath]->setTranslates(array(
              'singular'  =>  $this->trans("pages.names.pageAll.singular"),
              'plural'    =>  $this->trans("pages.names.pageAll.plural"),
              "type"      =>  AustralTools::getValueByKey($moduleEvent->getModule()->getParameters(), "translate", "default"),
              "key"       =>  "pageByDomain"
            )
          );

          $moduleEvent->getModule()->setActionName("listChildrenModules");
          $moduleEvent->getModule()->setPathActions(array());
        }
      }
    }
  }

  /**
   * @param $key
   * @param array $parameters
   *
   * @return string
   */
  public function trans($key, array $parameters = array()): string
  {
    return $this->translator->trans($key, $parameters, "austral");
  }

}