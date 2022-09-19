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

use App\Entity\Austral\WebsiteBundle\Page;

use Austral\EntityBundle\Event\EntityManagerEvent;

use Austral\EntityBundle\Entity\Interfaces\PageParentInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EntityManager Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerListener implements EventSubscriberInterface
{

  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $dispatcher;

  /**
   * @param EventDispatcherInterface $dispatcher
   */
  public function __construct(EventDispatcherInterface $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  /**
   * @return string[]
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EntityManagerEvent::EVENT_CREATE => 'create',
    ];
  }

  /**
   * @param EntityManagerEvent $entityManagerEvent
   *
   * @throws NonUniqueResultException
   */
  public function create(EntityManagerEvent $entityManagerEvent)
  {
    $object = $entityManagerEvent->getObject();
    if($object instanceof PageParentInterface)
    {
      if($pageParent = $entityManagerEvent->getEntityManager()->getDoctrineEntityManager()->getRepository(Page::class)->retreiveByEntityExtends(get_class($object)))
      {
        $object->setPageParent($pageParent);
        $pageParent->addChildEntities($entityManagerEvent->getObject());
      }
    }
  }

}