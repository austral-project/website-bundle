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

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Website RequestRedirection Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class WebsiteRedirectEvent extends Event
{

  const EVENT_AUSTRAL_REQUEST_REDIRECTION = "austral.event.request.redirection";

  /**
   * @var string|null
   */
  private ?string $redirectUrl;

  /**
   * @var int
   */
  private int $redirectStatus;

  /**
   * WebsiteRedirectEvent constructor
   *
   * @param string|null $redirectUrl
   * @param int $redirectStatus
   */
  public function __construct(string $redirectUrl = null, int $redirectStatus = 302)
  {
    $this->redirectUrl = $redirectUrl;
    $this->redirectStatus = $redirectStatus;
  }

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  /**
   * @param string|null $redirectUrl
   *
   * @return $this
   */
  public function setRedirectUrl(?string $redirectUrl): WebsiteRedirectEvent
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * @return int
   */
  public function getRedirectStatus(): int
  {
    return $this->redirectStatus;
  }

  /**
   * @param int $redirectStatus
   *
   * @return $this
   */
  public function setRedirectStatus(int $redirectStatus): WebsiteRedirectEvent
  {
    $this->redirectStatus = $redirectStatus;
    return $this;
  }

}