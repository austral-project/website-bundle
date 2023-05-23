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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral ConfigVariable Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ConfigVariableFunctionEventSubscriber implements EventSubscriberInterface
{

  /**
   * @return array
   */
  public static function getSubscribedEvents(): array
  {
    return [
    ];
  }

}