<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Controller;

use Austral\HttpBundle\Controller\HttpController;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Austral Website Controller Abstract.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
abstract class WebsiteController extends HttpController
{
  use ContainerAwareTrait;

  /**
   * @param Request $request
   *
   * @return Response
   */
  public function homepage(Request $request): Response
  {
    return $this->render(
      $this->handlerManager->getTemplateParameters()->getPath(),
      $this->handlerManager->getTemplateParameters()->__serialize()
    );
  }

  /**
   * @param Request $request
   * @param string $slug
   *
   * @return Response
   */
  public function pageBySlug(Request $request, string $slug): Response
  {
    if($redirectUrl = $this->handlerManager->getRedirectUrl()) {
      return $this->redirect($redirectUrl);
    }
    return $this->render(
      $this->handlerManager->getTemplateParameters()->getPath(),
      $this->handlerManager->getTemplateParameters()->__serialize()
    );
  }

  /**
   * @param Request $request
   *
   * @return Response
   */
  public function sitemap(Request $request): Response
  {
    return $this->render(
      $this->handlerManager->getTemplateParameters()->getPath(),
      $this->handlerManager->getTemplateParameters()->__serialize()
    );
  }

  /**
   * @param Request $request
   *
   * @return Response
   */
  public function robots(Request $request): Response
  {
    return $this->render(
      $this->handlerManager->getTemplateParameters()->getPath(),
      $this->handlerManager->getTemplateParameters()->__serialize()
    );
  }

  /**
   * @param Request $request
   *
   * @return Response
   */
  public function guideline(Request $request): Response
  {
    return $this->render(
      $this->handlerManager->getTemplateParameters()->getPath(),
      $this->handlerManager->getTemplateParameters()->__serialize()
    );
  }

  /**
   * @param string $_locale
   *
   * @return Response
   */
  public function translationJson(string $_locale): Response
  {
    $return = array();
    $domains = array("form", "header", "messages", "austral");

    /** @var MessageCatalogue $catalogue */
    if($catalogue = $this->getTranslate()->getCatalogue($_locale))
    {
      foreach($domains as $domain)
      {
        if($messages = $catalogue->all($domain))
        {
          foreach($messages as $key => $message)
          {
            $return["{$domain}.{$key}"] = $message;
          }
        }
      }
    }
    $json = json_encode($return);
    $translations = $this->renderView(
      '@AustralAdmin/Layout/translation.js.twig',
      array(
        'json' => $json
      )
    );
    return new Response($translations, 200,
      array('Content-Type' => 'text/javascript')
    );
  }


}
