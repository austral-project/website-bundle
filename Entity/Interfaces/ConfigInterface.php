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
 * Austral Config Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface ConfigInterface
{

  /**
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): ConfigInterface;

  /**
   * @return string|null
   */
  public function getKeyname(): ?string;

  /**
   * @param string|null $keyname
   *
   * @return $this
   */
  public function setKeyname(?string $keyname): ConfigInterface;

  /**
   * @return string|null
   */
  public function getType(): ?string;

  /**
   * @param string $type
   *
   * @return $this
   */
  public function setType(string $type): ConfigInterface;

  /**
   * @return bool
   */
  public function getWithDomain(): bool;

  /**
   * @param bool $withDomain
   *
   * @return $this
   */
  public function setWithDomain(bool $withDomain): ConfigInterface;

}

    
    
      