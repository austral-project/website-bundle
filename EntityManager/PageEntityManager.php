<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\EntityManager;

use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterRobotTrait;
use Austral\WebsiteBundle\Repository\PageRepository;

use Austral\EntityBundle\EntityManager\EntityManager;

use Doctrine\ORM\NonUniqueResultException;

/**
 * Austral Page EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class PageEntityManager extends EntityManager
{

  /**
   * @var PageRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return PageInterface
   * @throws \Exception
   */
  public function create(array $values = array()): PageInterface
  {
    /** @var PageInterface|TranslateMasterInterface $object */
    $object = parent::create($values);
    $object->setCurrentLanguage($this->currentLanguage);
    $object->createNewTranslateByLanguage();
    return $object;
  }

  /**
   * @param $keyname
   * @param \Closure|null $closure
   *
   * @return PageInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname($keyname, \Closure $closure = null): ?PageInterface
  {
    return $this->repository->retreiveByKeyname($keyname, $closure);
  }

  /**
   * @param PageInterface|null $page
   *
   * @return PageInterface
   */
  public function createObjectPage(PageInterface $page = null): PageInterface
  {
    $class = $this->getClass();
    /** @var PageInterface $object */
    $object = new $class;
    if($page)
    {
      $object->setParent($page);
    }
    return $object;
  }

  /**
   * @param $entityExtends
   *
   * @return PageInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByEntityExtends($entityExtends): ?PageInterface
  {
    return $this->getRepository()->retreiveByEntityExtends($entityExtends);
  }


}
