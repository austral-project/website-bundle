<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Repository;

use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;

use Austral\EntityBundle\Repository\EntityRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Orm\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Austral Page Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class PageRepository extends EntityRepository
{

  /**
   * @param $keyname
   * @param \Closure|null $closure
   *
   * @return PageInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname($keyname, \Closure $closure = null): ?PageInterface
  {
    return $this->retreiveByKey("keyname", $keyname, $closure);
  }

  /**
   * @param $id
   * @param \Closure|null $closure
   *
   * @return PageInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveById($id, \Closure $closure = null): ?PageInterface
  {
    return $this->retreiveByKey("id", $id, $closure);
  }

  /**
   * @param $entityExtends
   * @param \Closure|null $closure
   *
   * @return PageInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByEntityExtends($entityExtends, \Closure $closure = null): ?PageInterface
  {
    return $this->retreiveByKey("entityExtends", $entityExtends, $closure);
  }

  /**
   * @return ArrayCollection|array
   * @throws \Doctrine\ORM\Query\QueryException
   */
  public function selectByEntityExtends()
  {
    $queryBuilder = $this->createQueryBuilder('root');
    $queryBuilder->where("root.entityExtends IS NOT NULL")
      ->leftJoin("root.translates", "translates")->addSelect("translates");
    $queryBuilder->indexBy("root", "root.id");
    $query = $queryBuilder->getQuery();
    try {
      $objects = $query->execute();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

  /**
   * @param $name
   * @param QueryBuilder $queryBuilder
   *
   * @return QueryBuilder
   */
  public function queryBuilderExtends($name, QueryBuilder $queryBuilder): QueryBuilder
  {
    if(strpos($name, "count") === false)
    {
      $queryBuilder->leftJoin('root.translates', 'translates')->addSelect('translates');
    }
    return $queryBuilder;
  }

}
