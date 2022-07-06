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
 * Austral PageTranslate Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface PageTranslateInterface
{

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
  public function setName(?string $name): PageTranslateInterface;

  /**
   * Get summary
   * @return string|null
   */
  public function getSummary(): ?string;

  /**
   * Set summary
   *
   * @param string|null $summary
   *
   * @return $this
   */
  public function setSummary(?string $summary): PageTranslateInterface;

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
  public function setImage(?string $image): PageTranslateInterface;

}

    
    
      