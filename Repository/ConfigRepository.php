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

use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;

use Austral\EntityBundle\Repository\EntityRepository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;

/**
 * Austral Config Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ConfigRepository extends EntityRepository
{

  /**
   * @param string $keyname
   * @param \Closure|null $closure
   *
   * @return ConfigInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname(string $keyname, \Closure $closure = null): ?ConfigInterface
  {
    return $this->retreiveByKey("keyname", $keyname, $closure);
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
      $queryBuilder->leftJoin('root.translates', 'translates')
        ->addSelect('translates')
        ->leftJoin('translates.valuesByDomain', 'valuesByDomain')
        ->addSelect('valuesByDomain');
    }
    return $queryBuilder;
  }

  /**
   * @param string|null $language
   *
   * @return Collection|array
   * @throws QueryException
   */
  public function selectAllByIndexKeyname(?string $language = null, ?string $domainCurrentId = null)
  {
   $queryBuilder = $this->createQueryBuilder('root')
      ->leftJoin('root.translates', 'translates')->addSelect('translates')
      ->leftJoin('translates.valuesByDomain', 'valuesByDomain')->addSelect('valuesByDomain')
      ->where("translates.language = :language")
      ->setParameter("language",  $language);

   if($domainCurrentId)
   {
     $queryBuilder->andWhere("valuesByDomain.domainId = :domainId OR valuesByDomain.id IS NULL")
       ->setParameter("domainId", $domainCurrentId);
   }


   $queryBuilder->indexBy("root", "root.keyname");

    $query =$queryBuilder->getQuery();
    try {
      $objects = $query->execute();
    } catch (\Doctrine\Orm\NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

  /**
   * @param $language
   *
   * @return array
   */
  public function selectArrayResultAll($language): array
  {
    $dql = "SELECT config.id, config.keyname, config.configType, config.isRgpd, config.typeContenu,
          translates.language, translates.content, translates.rgpdDescription
          
          FROM Austral\WebsiteBundle\Entity\Config config
          INDEX BY config.keyname
          
          LEFT JOIN config.translates translates
          
          WHERE translates.language = '$language'
          
          GROUP BY config.keyname
          ORDER BY config.keyname ASC";
    
    $query = $this->createQueryBuilder("config")->getQuery()->setDql($dql);
    return $query->getArrayResult();
  }

}
