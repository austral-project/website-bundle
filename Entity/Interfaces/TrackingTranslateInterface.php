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

use Austral\WebsiteBundle\Entity\TrackingTranslate;

/**
 * Austral TrackingTranslate Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface TrackingTranslateInterface
{

  /**
   * @return string|null
   */
  public function getRgpdDescription(): ?string;

  /**
   * @param string|null $rgpdDescription
   *
   * @return TrackingTranslate
   */
  public function setRgpdDescription(?string $rgpdDescription): TrackingTranslate;

  /**
   * @return string|null
   */
  public function getRgpdLink(): ?string;

  /**
   * @param string|null $rgpdLink
   *
   * @return TrackingTranslate
   */
  public function setRgpdLink(?string $rgpdLink): TrackingTranslate;

  /**
   * @return string|null
   */
  public function getScript(): ?string;

  /**
   * @param string|null $script
   *
   * @return TrackingTranslate
   */
  public function setScript(?string $script): TrackingTranslate;

  /**
   * @return string|null
   */
  public function getNoScript(): ?string;

  /**
   * @param string|null $noScript
   *
   * @return TrackingTranslate
   */
  public function setNoScript(?string $noScript): TrackingTranslate;

  /**
   * @return bool
   */
  public function isActive(): bool;

  /**
   * @param bool $isActive
   *
   * @return TrackingTranslate
   */
  public function setIsActive(bool $isActive): TrackingTranslate;

  /**
   * @return string|null
   */
  public function getFileName(): ?string;

  /**
   * @param string|null $fileName
   *
   * @return TrackingTranslate
   */
  public function setFileName(?string $fileName): TrackingTranslate;

  /**
   * @return string|null
   */
  public function getFileContent(): ?string;

  /**
   * @param string|null $fileContent
   *
   * @return TrackingTranslate
   */
  public function setFileContent(?string $fileContent): TrackingTranslate;

}

    
    
      