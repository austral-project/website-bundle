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
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Austral Doctrine Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineListener implements EventSubscriber
{

  /**
   * @var mixed
   */
  protected $name;

  /**
   * @var EntityManagerListener
   */
  protected EntityManagerListener $entityManagerListener;

  /**
   * DoctrineListener constructor.
   */
  public function __construct(EntityManagerListener $entityManagerListener)
  {
    $this->entityManagerListener = $entityManagerListener;
  }

  /**
   * @return string[]
   */
  public function getSubscribedEvents()
  {
    return array(
      Events::postLoad
    );
  }

  /**
   * @param LifecycleEventArgs $args
   */
  public function postLoad(LifecycleEventArgs $args): void
  {
    $ea = $this->getEventAdapter($args);
    /** @var EntityInterface $object */
    $object = $ea->getObject();
    $this->entityManagerListener->addTreePageParent($object);
  }


  /**
   * @param EventArgs $args
   *
   * @return EventArgs
   */
  protected function getEventAdapter(EventArgs $args): EventArgs
  {
    return $args;
  }

  /**
   * @return string
   */
  protected function getNamespace(): string
  {
    return __NAMESPACE__;
  }
}