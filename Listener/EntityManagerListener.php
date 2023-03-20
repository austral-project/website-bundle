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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\SeoBundle\Entity\Interfaces\TreePageInterface;
use Austral\ToolsBundle\AustralTools;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\WebsiteBundle\Entity\Interfaces\WebsitePageParentInterface;
use Austral\WebsiteBundle\Entity\Page;
use Austral\WebsiteBundle\EntityManager\PageEntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Austral EntityManager Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerListener
{

  /**
   * @var PageEntityManager
   */
  protected PageEntityManager $pageEntityManager;

  /**
   * @var array
   */
  protected array $pagesParent = array();

  /**
   * @param PageEntityManager $pageEntityManager
   */
  public function __construct(PageEntityManager $pageEntityManager)
  {
    $this->pageEntityManager = $pageEntityManager;
  }

  /**
   * initPagesParent
   * @return $this
   */
  public function initPagesParent()
  {
    if(!$this->pagesParent)
    {
      $this->pagesParent = $this->pageEntityManager->getRepository()->selectByEntityExtends();
    }
    return $this;
  }

  /**
   * create
   *
   * @param EntityManagerEvent $entityManagerEvent
   *
   * @return void
   */
  public function create(EntityManagerEvent $entityManagerEvent)
  {
    $this->addTreePageParent($entityManagerEvent->getObject());
  }

  /**
   * addTreePageParent
   *
   * @param EntityInterface $object
   *
   * @return void
   */
  public function addTreePageParent(EntityInterface $object)
  {
    if($object instanceof TreePageInterface && $object instanceof WebsitePageParentInterface)
    {
      if($websitePages = $this->getPageParent($object->getClassnameForMapping()))
      {
        /** @var PageInterface $pageParent */
        foreach ($websitePages as $pageParent)
        {
          $object->addTreePageParent($pageParent, $pageParent->getDomainId() ?? DomainsManagement::DOMAIN_ID_MASTER);
          $pageParent->addChildEntities($object);
        }
      }
    }
  }

  /**
   * @param $class
   *
   * @return array
   */
  protected function getPageParent($class): array
  {
    $websitePagesSelected = array();
    $this->initPagesParent();

    /** @var PageInterface $pageParent */
    foreach ($this->pagesParent as $pageParent)
    {
      if(strpos($pageParent->getEntityExtends(), ",") !== false)
      {
        $keyExplode = explode(",",$pageParent->getEntityExtends());
        if(count($keyExplode)>1)
        {
          foreach ($keyExplode as $newKey)
          {
            if(trim($newKey) === $class)
            {
              $websitePagesSelected[] = $pageParent;
            }
          }
        }
      }
      elseif($pageParent->getEntityExtends() === $class)
      {
        $websitePagesSelected[] = $pageParent;
      }
    }
    return $websitePagesSelected;
  }

  /**
   * @param EntityManagerEvent $entityManagerEvent
   *
   * @throws \Exception
   */
  public function duplicate(EntityManagerEvent $entityManagerEvent)
  {
    if($entityManagerEvent->getObject() instanceof PageInterface)
    {
      /** @var PageInterface $object */
      $object = $entityManagerEvent->getObject();

      /** @var PageInterface $objectSource */
      $objectSource = $entityManagerEvent->getSourceObject();
      if($object->getDomainId() === $objectSource->getDomainId())
      {
        $object->setKeyname($objectSource->getKeyname()."-".AustralTools::random(4));
      }
      else
      {
        if($object->getParent() && !$object->getIsHomepage())
        {
          $domainId = $object->getDomainId();
          $parentDefault = $this->pageEntityManager->retreiveByKeyname("homepage", function(QueryBuilder $queryBuilder) use($domainId) {
            $queryBuilder->andWhere("root.domainId = :domainId")
              ->setParameter("domainId", $domainId);
          });
          $object->setParent($parentDefault);
        }
      }
    }
  }

}