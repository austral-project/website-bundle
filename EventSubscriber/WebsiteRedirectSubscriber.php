<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\WebsiteBundle\EventSubscriber;

use Austral\WebsiteBundle\Event\WebsiteRedirectEvent;
use Austral\WebsiteBundle\Handler\WebsiteHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral WebsiteRedirect Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class WebsiteRedirectSubscriber implements EventSubscriberInterface
{

  /**
   * @var WebsiteHandler
   */
  protected WebsiteHandler $websiteHandler;

  /**
   * ContentBlockSubscriber constructor.
   *
   * @param WebsiteHandler $websiteHandler
   */
  public function __construct(WebsiteHandler $websiteHandler)
  {
    $this->websiteHandler = $websiteHandler;
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents(): array
  {
    return [
      WebsiteRedirectEvent::EVENT_AUSTRAL_REQUEST_REDIRECTION =>  ["redirection", 1024]
    ];
  }

  /**
   * @param WebsiteRedirectEvent $requestRedirectionEvent
   *
   * @return void
   */
  public function redirection(WebsiteRedirectEvent $requestRedirectionEvent)
  {
    $this->websiteHandler->setRedirectUrl($requestRedirectionEvent->getRedirectUrl());
  }
}