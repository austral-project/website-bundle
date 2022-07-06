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

use Austral\WebsiteBundle\Entity\Interfaces\TrackingInterface;

use Austral\EntityBundle\Repository\EntityRepository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Orm\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;

/**
 * Austral Tracking Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class TrackingRepository extends EntityRepository
{

  /**
   * @param $keyname
   * @param \Closure|null $closure
   *
   * @return TrackingInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname($keyname, \Closure $closure = null): ?TrackingInterface
  {
    return $this->retreiveByKey("keyname", $keyname, $closure);
  }

  /**
   * @param $fileName
   * @param \Closure|null $closure
   *
   * @return TrackingInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByFileName($fileName, \Closure $closure = null): ?TrackingInterface
  {
    return $this->retreiveByKey("fileName", $fileName, $closure);
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
    if($this->currentLanguage)
    {
      $queryBuilder->andWhere('translates.language = :language')
        ->setParameter("language", $this->currentLanguage);
    }
    return $queryBuilder;
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
    $queryBuilder = $this->createQueryBuilder('root')
      ->leftJoin('root.translates', 'translates')->addSelect('translates')
      ->where("root.type = :type")
      ->andWhere("translates.language = :language")
      ->setParameter("type",  $type)
      ->setParameter("language",  $language);
    if(!$this->isPgsql())
    {
      $queryBuilder->groupBy("root.keyname");
    }
    $queryBuilder->indexBy("root", "root.keyname");

    $query = $queryBuilder->getQuery();
    try {
      $objects = $query->execute();
    } catch (NoResultException $e) {
      $objects = null;
    }
    return $objects;
    }
}
