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

use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Austral\WebsiteBundle\Repository\DomainRepository;

use Austral\EntityBundle\EntityManager\EntityManager;

/**
 * Austral Domain EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DomainEntityManager extends EntityManager
{

  /**
   * @var DomainRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return DomainInterface
   */
  public function create(array $values = array()): DomainInterface
  {
    return parent::create($values);
  }

  /**
   * @param string $host
   * @param bool $includeSubDomain
   *
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByDomain(string $host, bool $includeSubDomain = true): ?DomainInterface
  {
    return $this->repository->retreiveByDomain($host, $includeSubDomain);
  }

  /**
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByMaster(): ?DomainInterface
  {
    return $this->repository->retreiveByMaster();
  }

}
