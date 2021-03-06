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

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterRobotTrait;
use Austral\WebsiteBundle\Repository\PageRepository;

use Austral\EntityBundle\EntityManager\EntityManager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
    /** @var PageInterface|EntityTranslateMasterRobotTrait|EntityTranslateMasterInterface $object */
    $object = parent::create($values);
    $object->setCurrentLanguage($this->currentLanguage);
    $object->createNewTranslateByLanguage();
    $object->setInSitemap(true);
    $object->setIsIndex(true);
    $object->setIsFollow(true);
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
   * @param $parentCode
   *
   * @return ArrayCollection|mixed
   */
  public function selectByParentCodeForAdmin($parentCode)
  {
    return $this->repository->selectByParentCodeForAdmin($parentCode);
  }

  /**
   * @param $parentId
   * @param null $limit
   *
   * @return ArrayCollection|Paginator
   */
  public function selectChildren($parentId, $limit = null)
  {
    return $this->repository->selectChildren($parentId, $limit);
  }

  /**
   * @param string $otherModule
   *
   * @return PageInterface|null
   */
  public function retreiveByOtherModule(string $otherModule): ?PageInterface
  {
    return $this->repository->retreiveByOtherModule($otherModule);
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
