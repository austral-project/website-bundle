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

use Austral\EntityBundle\Entity\EntityInterface;

/**
 * Austral Social Network Entity Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntitySocialNetworkInterface
{

  /**
   * Set socialTitle
   *
   * @param string|null $socialTitle
   *
   * @return EntitySocialNetworkInterface|EntityInterface
   */
  public function setSocialTitle(?string $socialTitle): EntityInterface;

  /**
   * Get socialTitle
   *
   * @return string|null
   */
  public function getSocialTitle(): ?string;

  /**
   * Set socialDescription
   *
   * @param string|null $socialDescription
   *
   * @return EntitySocialNetworkInterface|EntityInterface
   */
  public function setSocialDescription(?string $socialDescription): EntityInterface;

  /**
   * Get socialDescription
   *
   * @return string|null
   */
  public function getSocialDescription(): ?string;

  /**
   * Set socialImage
   *
   * @param string|null $socialImage
   *
   * @return EntitySocialNetworkInterface|EntityInterface
   */
  public function setSocialImage(?string $socialImage): EntityInterface;

  /**
   * Get socialImage
   *
   * @return string|null
   */
  public function getSocialImage(): ?string;

}
