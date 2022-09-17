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
use Austral\ToolsBundle\AustralTools;
use Austral\EntityBundle\Entity\Interfaces\PageParentInterface;
use App\Entity\Austral\WebsiteBundle\Page;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
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
    if($object instanceof PageParentInterface)
    {
      if($pageParent = $this->getPageParent($args, ClassUtils::getClass($object)))
      {
        $object->setPageParent($pageParent);
        $pageParent->addChildEntities($object);
      }
    }
  }

  /**
   * @param LifecycleEventArgs $args
   * @param $class
   *
   * @return array|mixed|string|null
   */
  protected function getPageParent(LifecycleEventArgs $args, $class)
  {
    if(!$this->pagesParent)
    {
      $this->pagesParent = $args->getEntityManager()->getRepository(Page::class)->selectByEntityExtends();
    }
    return AustralTools::getValueByKey($this->pagesParent, $class, null);
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