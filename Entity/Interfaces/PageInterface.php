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

use Austral\EntitySeoBundle\Entity\Interfaces\EntitySeoInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Austral Page Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface PageInterface
{

  /**
   * @param PageInterface $page
   * @return PageInterface
   */
  public function retreiveHomepagePage(PageInterface $page): PageInterface;

  /**
   * @return EntitySeoInterface|PageInterface|null
   */
  public function getPageParent(): ?EntitySeoInterface;

  /**
   * @return PageInterface
   */
  public function getHomepage(): PageInterface;

  /**
   * @return bool
   */
  public function getIsHomepage(): bool;

  /**
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * @return string|null
   */
  public function getSummary(): ?string;

  /**
   * Set parent
   *
   * @param PageInterface|null $parent
   *
   * @return $this
   */
  public function setParent(?PageInterface $parent): PageInterface;

  /**
   * Get parent
   *
   * @return PageInterface|null
   */
  public function getParent(): ?PageInterface;

  /**
   * Add child
   *
   * @param PageInterface $child
   *
   * @return $this
   */
  public function addChild(PageInterface $child): PageInterface;

  /**
   * Remove child
   *
   * @param PageInterface $child
   *
   * @return $this
   */
  public function removeChild(PageInterface $child): PageInterface;

  /**
   * Get children
   *
   * @return Collection
   */
  public function getChildren(): Collection;

  /**
   * Add child
   *
   * @param DomainInterface $domain
   *
   * @return $this
   */
  public function addDomain(DomainInterface $domain): PageInterface;

  /**
   * Remove child
   *
   * @param DomainInterface $domain
   *
   * @return $this
   */
  public function removeDomain(DomainInterface $domain): PageInterface;

  /**
   * Get children
   *
   * @return Collection
   */
  public function getDomains(): Collection;

  /**
   * Get keyname
   * @return string|null
   */
  public function getKeyname(): ?string;

  /**
   * Set keyname
   *
   * @param string|null $keyname
   *
   * @return $this
   */
  public function setKeyname(?string $keyname): PageInterface;

  /**
   * Get position
   * @return int|null
   */
  public function getPosition(): ?int;

  /**
   * Set position
   *
   * @param int|null $position
   *
   * @return $this
   */
  public function setPosition(?int $position): PageInterface;

  /**
   * Get australPictoClass
   * @return string
   */
  public function getAustralPictoClass(): string;

  /**
   * @param string|null $australPictoClass
   *
   * @return $this
   */
  public function setAustralPictoClass(?string $australPictoClass = null): PageInterface;

  /**
   * @return string|null
   */
  public function getEntityExtends(): ?string;

  /**
   * @param string|null $entityExtends
   *
   * @return PageInterface
   */
  public function setEntityExtends(?string $entityExtends): PageInterface;
}

    
    
      