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
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral Domain Bundle.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class Domain
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
   * @var string
   */
  protected string $host;

  /**
   * ReplaceConfigValues constructor.
   *
   * @param RequestStack $requestStack
   * @param DomainEntityManager $domainEntityManager
   */
  public function __construct(RequestStack $requestStack, DomainEntityManager $domainEntityManager)
  {
    $request = $requestStack->getCurrentRequest();
    $this->host = $request->getHost();
    $this->language = $request ? $request->getLocale() : null;
    $this->domainEntityManager = $domainEntityManager;
  }

  /**
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function getDomainMaster(): ?DomainInterface
  {
    return $this->domainEntityManager->retreiveByMaster();
  }

  /**
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function getCurrentDomain(): ?DomainInterface
  {
    return $this->domainEntityManager->retreiveByDomain($this->host);
  }



}