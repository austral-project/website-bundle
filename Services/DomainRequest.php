<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Services;

use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Austral\WebsiteBundle\EntityManager\DomainEntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral Domain Bundle.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class DomainRequest
{

  /**
   * @var string|null
   */
  protected ?string $language;

  /**
   * @var DomainEntityManager
   */
  protected DomainEntityManager $domainEntityManager;

  /**
   * @var ?string
   */
  protected ?string $host = null;

  /**
   * @var DomainInterface|null
   */
  protected ?DomainInterface $currentDomain = null;

  /**
   * ReplaceConfigValues constructor.
   *
   * @param RequestStack $requestStack
   * @param DomainEntityManager $domainEntityManager
   */
  public function __construct(RequestStack $requestStack, DomainEntityManager $domainEntityManager)
  {
    $request = $requestStack->getCurrentRequest();
    $this->host = $request ? $request->getHost() : null;
    $this->language = $request ? $request->getLocale() : null;
    $this->domainEntityManager = $domainEntityManager;
  }

  /**
   * @return DomainInterface|null
   * @throws NonUniqueResultException
   */
  public function getDomainMaster(): ?DomainInterface
  {
    return $this->domainEntityManager->retreiveByMaster();
  }

  /**
   * @return DomainInterface|null
   * @throws NonUniqueResultException
   */
  public function getCurrentDomain(): ?DomainInterface
  {
    if(!$this->currentDomain)
    {
      $this->retrieveCurrentDomain();
    }
    return $this->currentDomain;
  }

  /**
   * @param DomainInterface $currentDomain
   *
   * @return $this
   */
  public function setCurrentDomain(DomainInterface $currentDomain): DomainRequest
  {
    $this->currentDomain = $currentDomain;
    return $this;
  }

  /**
   * @param bool $enabledChecked
   *
   * @return $this
   * @throws NonUniqueResultException
   */
  public function retrieveCurrentDomain(bool $enabledChecked = false): DomainRequest
  {
    $this->currentDomain = $this->domainEntityManager->getRepository()
      ->retreiveByKey("domain", $this->host, function(QueryBuilder $queryBuilder)  use ($enabledChecked) {
        if($enabledChecked) {
          $queryBuilder->andWhere("root.isEnabled = :isEnabled")->setParameter("isEnabled", true);
        }
    });
    return $this;
  }

  /**
   * @return array|ArrayCollection
   */
  public function selectEnabledAndNotVirtual()
  {
    return $this->domainEntityManager->selectByClosure(function(QueryBuilder $queryBuilder) {
      $queryBuilder->leftJoin("root.homepage", "homepage")->addSelect("homepage")
        ->where("root.isEnabled = :isEnabled")
        ->andWhere("root.isVirtual = :isVirtual OR root.isVirtual IS NULL")
        ->setParameter("isEnabled", true)
        ->setParameter("isVirtual", false);
    });
  }



}