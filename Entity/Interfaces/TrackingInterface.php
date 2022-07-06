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
 * Austral Tracking Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface TrackingInterface
{
  /**
   * @return string|null
   */
  public function getTemplateScript(): ?string;

  /**
   * Get name
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * Set name
   *
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): TrackingInterface;

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
  public function setKeyname(?string $keyname): TrackingInterface;

  /**
   * Get type
   * @return string|null
   */
  public function getType(): ?string;

  /**
   * Set type
   *
   * @param string|null $type
   *
   * @return $this
   */
  public function setType(?string $type): TrackingInterface;

  /**
   * Get isRgpdActivated
   * @return bool
   */
  public function isRgpdActivated(): bool;

  /**
   * Set isRgpdActivated
   *
   * @param bool $isRgpdActivated
   *
   * @return $this
   */
  public function setIsRgpdActivated(bool $isRgpdActivated): TrackingInterface;

  /**
   * Get rgpdTagName
   * @return string|null
   */
  public function getRgpdTagName(): ?string;

  /**
   * Set rgpdTagName
   *
   * @param string|null $rgpdTagName
   *
   * @return $this
   */
  public function setRgpdTagName(?string $rgpdTagName): TrackingInterface;

  /**
   * Get domPosition
   * @return string|null
   */
  public function getDomPosition(): ?string;

  /**
   * Set domPosition
   *
   * @param string|null $domPosition
   *
   * @return $this
   */
  public function setDomPosition(?string $domPosition): TrackingInterface;

}

    
    
      