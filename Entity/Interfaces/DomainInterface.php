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

/**
 * Austral Domain Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface DomainInterface
{

  const SCHEME_HTTPS = "https";
  const SCHEME_HTTP = "http";

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
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * @param string|null $name
   *
   * @return DomainInterface
   */
  public function setName(?string $name): DomainInterface;

  /**
   * @return string|null
   */
  public function getFavicon(): ?string;

  /**
   * @param string|null $favicon
   *
   * @return $this
   */
  public function setFavicon(?string $favicon): DomainInterface;

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
  public function getIsMaster(): bool;

  /**
   * @param bool $isMaster
   *
   * @return DomainInterface
   */
  public function setIsMaster(bool $isMaster): DomainInterface;

  /**
   * @return bool
   */
  public function getIsEnabled(): bool;

  /**
   * @param bool $isEnabled
   *
   * @return DomainInterface
   */
  public function setIsEnabled(bool $isEnabled): DomainInterface;

  /**
   * @return bool
   */
  public function getIsVirtual(): bool;

  /**
   * @param bool $isVirtual
   *
   * @return $this
   */
  public function setIsVirtual(bool $isVirtual): DomainInterface;

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

  /**
   * @return bool
   */
  public function getWithUri(): bool;

  /**
   * @param bool $withUri
   *
   * @return DomainInterface
   */
  public function setWithUri(bool $withUri): DomainInterface;

}

    
    
      