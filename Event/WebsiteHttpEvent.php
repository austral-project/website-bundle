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


use Austral\HttpBundle\Event\HttpEvent;

/**
 * Austral Website Http Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class WebsiteHttpEvent extends HttpEvent
{

  const EVENT_AUSTRAL_HTTP_REQUEST_INITIALISE = "austral.event.http.website.request.initialise";
  const EVENT_AUSTRAL_HTTP_REQUEST = "austral.event.http.website.request";
  const EVENT_AUSTRAL_HTTP_CONTROLLER = "austral.event.http.website.controller";
  const EVENT_AUSTRAL_HTTP_RESPONSE = "austral.event.http.website.response";
  const EVENT_AUSTRAL_HTTP_EXCEPTION = "austral.event.http.website.exception";

}