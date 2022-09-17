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
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\ToolsBundle\AustralTools;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;

use Austral\WebsiteBundle\EntityManager\PageEntityManager;
use Doctrine\ORM\Query\QueryException;
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
   * @var DomainsManagement
   */
  protected DomainsManagement $domains;

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
   * @param DomainsManagement $domains
   * @param PageEntityManager $pageEntityManager
   * @param Generator $fileLinkGenerator
   * @param AdminConfiguration $adminConfiguration
   *
   * @throws QueryException
   */
  public function __construct(TranslatorInterface $translator,
    DomainsManagement $domains,
    PageEntityManager $pageEntityManager,
    Generator $fileLinkGenerator,
    AdminConfiguration $adminConfiguration)
  {
    $this->translator = $translator;
    $this->domains = $domains->initialize();
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
    if($moduleEvent->getModule()->getModuleKey() === "page" && false)
    {
      $modulePageChange = false;
      $domains = $this->domains->getDomains();
      $moduleParameters = $moduleEvent->getModuleParameters();
      $moduleParameters["translate_disabled"] = true;
      $moduleName = $moduleParameters["name"];
      if(count($domains) > 1) {
        /** @var DomainInterface $domain */
        foreach($domains as $domain)
        {
          if(!$domain->getIsVirtual())
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

            $countPages = $this->pageEntityManager->countAll(function(QueryBuilder $queryBuilder) use ($domain) {
              $queryBuilder->where("root.domainId = :domainId")
                ->setParameter("domainId", $domain->getId());
            });
            $moduleEvent->getModules()->getModules()[$moduleSubPath]->addParameters("austral_filter_by_domain", $domain->getId());
            $moduleEvent->getModules()->getModules()[$moduleSubPath]->addParameters("tile", array(
              "subEntitled"   =>  $this->trans("pages.names.pageByDomain.countElement", array('%count%'=>$countPages)),
              "img"           =>  $this->fileLinkGenerator->image($domain, "favicon")
            ));
          }
        }
        if($modulePageChange)
        {
          $moduleParameters["name"] = "{$moduleName} - All";
          $moduleSubKey = "{$moduleEvent->getModule()->getModulePath()}-all-domains";
          $moduleSubPath = "{$moduleEvent->getModule()->getModulePath()}/all-domains";
          $moduleEvent->getModules()->addModule($moduleSubPath,
            $moduleParameters,
            $moduleSubKey,
            "entity",
            false,
            $moduleEvent->getModule()->navigationPosition(),
            array(),
            $moduleEvent->getModule()
          );

          $moduleEvent->getModules()->getModules()[$moduleSubPath]->addParameters("austral_filter_by_domain", "all-domains");
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