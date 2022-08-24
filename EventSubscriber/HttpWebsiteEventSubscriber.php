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

use Austral\EntitySeoBundle\Services\Pages;
use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Austral\WebsiteBundle\Event\WebsiteHttpEvent;
use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;
use Austral\HttpBundle\EventSubscriber\HttpEventSubscriber;
use Austral\WebsiteBundle\Handler\Interfaces\WebsiteHandlerInterface;
use Austral\WebsiteBundle\Template\TemplateParameters;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Austral Http EventSubscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class HttpWebsiteEventSubscriber extends HttpEventSubscriber
{

  /**
   * @var string
   */
  protected string $debugContainer = "http.website.event";

  /**
   * @return array[]
   */
  public static function getSubscribedEvents(): array
  {
    return [
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_REQUEST     =>  ["onRequest", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_CONTROLLER  =>  ["onController", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_RESPONSE    =>  ["onResponse", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_EXCEPTION   =>  ["onException", 1024],
    ];
  }

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return void
   */
  public function onRequest(HttpEventInterface $httpEvent)
  {
    /** @var AttributeBagInterface $requestAttributes */
    $requestAttributes = $httpEvent->getKernelEvent()->getRequest()->attributes;

    $requestUri = urldecode($httpEvent->getKernelEvent()->getRequest()->getRequestUri());
    $pathInfo = urldecode(trim($httpEvent->getKernelEvent()->getRequest()->getPathInfo(), "/"));

    if($redirection = $this->container->get('austral.entity_manager.redirection')->retreiveByUrlSource($pathInfo , $httpEvent->getKernelEvent()->getRequest()->getLocale()))
    {
      $urlRedirect = str_replace($pathInfo, $redirection->getUrlDestination(), $requestUri);
      $response = new RedirectResponse($urlRedirect, $redirection->getStatusCode());
      $httpEvent->getKernelEvent()->setResponse($response);
      return;
    }

    $host = $httpEvent->getKernelEvent()->getRequest()->headers->get('host');
    /** @var DomainInterface $domain */
    $domain = $this->container->get("austral.entity_manager.domain")->getRepository()->retreiveByKey("domain", $host, function(QueryBuilder $queryBuilder) {
      $queryBuilder->andWhere("root.isEnabled = :isEnabled")
        ->setParameter("isEnabled", true);
    });

    /** @var Pages servicePages */
    $servicePages = $this->container->get("austral.entity_seo.pages");

    if($domain && $domain->getHomepage())
    {
      $servicePages->setHomepageId($domain->getHomepage()->getId());
    }

    /** @var HttpTemplateParametersInterface|TemplateParameters $templateParameters */
    $templateParameters = $this->container->get("austral.website.template");

    /** @var HttpHandlerInterface|WebsiteHandlerInterface $websiteHandler */
    $websiteHandler = $this->container->get("austral.website.handler");

    $websiteHandler->setTemplateParameters($templateParameters);

    $handlerMethod = $requestAttributes->get('_handler_method', null);
    if($requestAttributes->get('_austral_page', false))
    {
      if(!$currentPage = $servicePages->retreiveByRefUrl($requestAttributes->get('slug', null)))
      {
        if(!$domain || !$domain->getOnePage())
        {
          $websiteHandler->pageNotFound();
        }
        else
        {
          $currentPage = $domain->getHomepage();
        }
      }

      $websiteHandler->setPage($currentPage);
      $handlerMethod = $handlerMethod ? : "page";
      $templateName = "default";
      if(method_exists($currentPage, "getTemplate"))
      {
        $templateName = $currentPage->getTemplate();
      }
      $templateParameters->addParameters("currentPage", $currentPage);
    }
    else
    {
      $templateName = $handlerMethod;
    }
    if($handlerMethod)
    {
      $websiteHandler->setHandlerMethod($handlerMethod);
    }
    if(!$templatePath = $this->configuration->get("templates.{$templateName}.path"))
    {
      $templatePath = $this->configuration->get("templates.default.path");
    }
    $templateParameters->setPath($templatePath);
    $websiteHandler->initHandler();

    if($urlRedirect = $websiteHandler->getRedirectUrl())
    {
      $response = new RedirectResponse($urlRedirect, $websiteHandler->getRedirectStatus());
      $httpEvent->getKernelEvent()->setResponse($response);
    }
    $httpEvent->setHandler($websiteHandler);
  }

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return void
   */
  public function onController(HttpEventInterface $httpEvent)
  {

  }

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return void
   */
  public function onResponse(HttpEventInterface $httpEvent)
  {
    $response = $httpEvent->getKernelEvent()->getResponse();
    $responseContent = $this->container->get('austral.website.config_replace_dom')->replaceDom($response->getContent());
    $response->setContent($responseContent);
    $httpEvent->getKernelEvent()->setResponse($response);
  }

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return void
   */
  public function onException(HttpEventInterface $httpEvent)
  {
    // You get the exception object from the received event
    $exception = $httpEvent->getKernelEvent()->getThrowable();
    // Customize your response object to display the exception details
    $response = new Response();
    if ($exception instanceof HttpExceptionInterface) {
      $response->setStatusCode($exception->getStatusCode());
      $response->headers->replace($exception->getHeaders());
    } else {
      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @var Pages servicePages */
    $servicePages = $this->container->get("austral.entity_seo.pages");
    if($currentPage = $servicePages->retreiveByCode("Page_error-{$response->getStatusCode()}"))
    {
      try {
        /** @var HttpTemplateParametersInterface|TemplateParameters $templateParameters */
        $templateParameters = $this->container->get("austral.website.template");

        /** @var HttpHandlerInterface|WebsiteHandlerInterface $websiteHandler */
        $websiteHandler = $this->container->get("austral.website.handler");

        $websiteHandler->setTemplateParameters($templateParameters);

        $websiteHandler->setPage($currentPage);
        $templateName = "default";
        if(method_exists($currentPage, "getTemplate"))
        {
          $templateName = $currentPage->getTemplate();
        }
        $templateParameters->addParameters("currentPage", $currentPage);
        $websiteHandler->setHandlerMethod("page");
        if(!$templatePath = $this->configuration->get("templates.{$templateName}.path"))
        {
          $templatePath = $this->configuration->get("templates.default.path");
        }
        $templateParameters->setPath($templatePath);
        $websiteHandler->initHandler();

        $twigParameters = $websiteHandler->getTemplateParameters()->__serialize();
        if($session = $httpEvent->getKernelEvent()->getRequest()->getSession())
        {
          if($flashMessages = $session->getFlashBag()->all())
          {
            $twigParameters['flashMessages'] = $flashMessages;
            $session->getFlashBag()->clear();
          }
        }
        $twigTemplate = $this->container->get('twig')->render($websiteHandler->getTemplateParameters()->getPath(), $twigParameters);
        $response->setContent($twigTemplate);
        $httpEvent->getKernelEvent()->setResponse($response);
      } catch(\Exception $e) {

      }
    }
  }


}