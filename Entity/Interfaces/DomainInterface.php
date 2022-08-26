<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Entity\Interfaces;

use Austral\WebsiteBundle\Entity\Domain;

/**
 * Austral Domain Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface DomainInterface
{

  /**
   * @return PageInterface|null
   */
  public function getHomepage(): ?PageInterface;

  /**
   * @param PageInterface|null $homepage
   *
   * @return DomainInterface
   */
  public function setHomepage(?PageInterface $homepage): DomainInterface;

  /**
   * @return string|null
   */
  public function getDomain(): ?string;

  /**
   * @param string|null $domain
   *
   * @return DomainInterface
   */
  public function setDomain(?string $domain): DomainInterface;

  /**
   * @return string
   */
  public function getScheme(): string;

  /**
   * @param string $scheme
   *
   * @return DomainInterface
   */
  public function setScheme(string $scheme): DomainInterface;

  /**
   * Get themes
   * @return array
   */
  public function getSubDomains(): array;

  /**
   * @param array $subDomains
   *
   * @return DomainInterface
   */
  public function setSubDomains(array $subDomains): DomainInterface;

  /**
   * @return bool
   */
  public function isMaster(): bool;

  /**
   * @param bool $isMaster
   *
   * @return DomainInterface
   */
  public function setIsMaster(bool $isMaster): DomainInterface;

  /**
   * @return bool
   */
  public function isEnabled(): bool;

  /**
   * @param bool $isEnabled
   *
   * @return DomainInterface
   */
  public function setIsEnabled(bool $isEnabled): DomainInterface;

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string;

  /**
   * @param string|null $redirectUrl
   *
   * @return DomainInterface
   */
  public function setRedirectUrl(?string $redirectUrl): DomainInterface;

  /**
   * @return bool
   */
  public function getOnePage(): bool;

  /**
   * @param bool $onePage
   *
   * @return $this
   */
  public function setOnePage(bool $onePage): DomainInterface;

  /**
   * @return string|null
   */
  public function getLanguage(): ?string;

  /**
   * @param string|null $language
   *
   * @return DomainInterface
   */
  public function setLanguage(?string $language): DomainInterface;


}

    
    
      