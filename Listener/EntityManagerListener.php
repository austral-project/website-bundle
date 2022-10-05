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

use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\ToolsBundle\AustralTools;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
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
   * @param PageEntityManager $pageEntityManager
   */
  public function __construct(PageEntityManager $pageEntityManager)
  {
    $this->pageEntityManager = $pageEntityManager;
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