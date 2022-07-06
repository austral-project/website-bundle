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
 * Austral ConfigTranslate Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface ConfigTranslateInterface
{

  /**
   * Get image
   * @return string|null
   */
  public function getImage(): ?string;

  /**
   * Set image
   *
   * @param string|null $image
   *
   * @return $this
   */
  public function setImage(?string $image): ConfigTranslateInterface;

  /**
   * Get file
   * @return string|null
   */
  public function getFile(): ?string;

  /**
   * Set file
   *
   * @param string|null $file
   *
   * @return $this
   */
  public function setFile(?string $file): ConfigTranslateInterface;

  /**
   * Get contentText
   * @return string|null
   */
  public function getContentText(): ?string;

  /**
   * Set contentText
   *
   * @param string|null $contentText
   *
   * @return $this
   */
  public function setContentText(?string $contentText): ConfigTranslateInterface;

  /**
   * Get contentBoolean
   * @return bool
   */
  public function getContentBoolean(): bool;

  /**
   * @param bool $contentBoolean
   *
   * @return ConfigTranslateInterface
   */
  public function setContentBoolean(bool $contentBoolean): ConfigTranslateInterface;

  /**
   * @return string|null
   */
  public function getInternalLink(): ?string;

  /**
   * @param string|null $internalLink
   *
   * @return ConfigTranslateInterface
   */
  public function setInternalLink(?string $internalLink): ConfigTranslateInterface;

}

    
    
      