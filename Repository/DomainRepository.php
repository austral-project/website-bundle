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

use Austral\EntityBundle\Repository\EntityRepository;
use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Doctrine\ORM\NoResultException;

/**
 * Austral Domain Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DomainRepository extends EntityRepository
{

  /**
   * @param string $host
   * @param bool $includeSubDomain
   *
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByDomain(string $host, bool $includeSubDomain = true): ?DomainInterface
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->where("root.domain = :domain")
      ->andWhere("root.isEnabled = :isEnabled")
      ->setParameter("domain", $host)
      ->setParameter("isEnabled", true);

    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }

  /**
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByMaster(): ?DomainInterface
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->where("root.isMaster = :isMaster")
      ->andWhere("root.isEnabled = :isEnabled")
      ->setParameter("isMaster", true)
      ->setParameter("isEnabled", true)
      ->setMaxResults(1);

    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }

}
