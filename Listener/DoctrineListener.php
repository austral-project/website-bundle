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
use Austral\SeoBundle\Entity\Interfaces\TreePageInterface;
use Austral\WebsiteBundle\Entity\Interfaces\WebsitePageParentInterface;
use App\Entity\Austral\WebsiteBundle\Page;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
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
   * @var array
   */
  protected array $pagesParent = array();

  /**
   * DoctrineListener constructor.
   */
  public function __construct()
  {
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
    if($object instanceof TreePageInterface && $object instanceof WebsitePageParentInterface)
    {
      if($websitePages = $this->getPageParent($args, $object->getClassnameForMapping()))
      {
        /** @var PageInterface $pageParent */
        foreach ($websitePages as $pageParent)
        {
          $object->addTreePageParent($pageParent, $pageParent->getDomainId() ?? "current");
          $pageParent->addChildEntities($object);
        }
      }
    }
  }

  /**
   * @param LifecycleEventArgs $args
   * @param $class
   *
   * @return array
   */
  protected function getPageParent(LifecycleEventArgs $args, $class): array
  {
    if(!$this->pagesParent)
    {
      $this->pagesParent = $args->getObjectManager()->getRepository(Page::class)->selectByEntityExtends();
    }
    $websitePagesSelected = array();

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