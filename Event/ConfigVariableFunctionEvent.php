<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Event;


use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral ConfigVariable Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ConfigVariableFunctionEvent extends Event
{

  const EVENT_AUSTRAL_CONFIG_VARIABLE_FUNCTION_BASE = "austral.event.config.variable.function";

  /**
   * @var ConfigInterface
   */
  private ConfigInterface $config;

  /**
   * @var string|null
   */
  private ?string $value = null;

  /**
   * ConfigVariableFunctionEvent constructor
   *
   * @param ConfigInterface $config
   */
  public function __construct(ConfigInterface $config)
  {
    $this->config = $config;
  }

  /**
   * @return ConfigInterface
   */
  public function getConfig(): ConfigInterface
  {
    return $this->config;
  }

  /**
   * @return string|null
   */
  public function getValue(): ?string
  {
    return $this->value;
  }

  /**
   * @param string|null $value
   *
   * @return $this
   */
  public function setValue(?string $value): ConfigVariableFunctionEvent
  {
    $this->value = $value;
    return $this;
  }

}