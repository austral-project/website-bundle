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

use Austral\WebsiteBundle\Entity\Interfaces\TrackingInterface;
use Austral\WebsiteBundle\Repository\TrackingRepository;

use Austral\EntityBundle\EntityManager\EntityManager;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;

/**
 * Austral Tracking EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class TrackingEntityManager extends EntityManager
{

  /**
   * @var TrackingRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return TrackingInterface
   */
  public function create(array $values = array()): TrackingInterface
  {
    /** @var TrackingInterface $object */
    return parent::create($values);
  }

  /**
   * @param $keyname
   * @param \Closure|null $closure
   *
   * @return TrackingInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname($keyname, \Closure $closure = null): ?TrackingInterface
  {
    return $this->repository->retreiveByKeyname($keyname, $closure);
  }


  /**
   * @param $type
   * @param $language
   *
   * @return mixed|null
   * @throws QueryException
   */
  public function selectAllByIndexKeyname($type, $language)
  {
    return $this->repository->selectAllByIndexKeyname($type, $language);
  }

  /**
   * @param $fileName
   *
   * @return TrackingInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByFileName($fileName): ?TrackingInterface
  {
    return $this->repository->retreiveByFileName($fileName);
  }
}