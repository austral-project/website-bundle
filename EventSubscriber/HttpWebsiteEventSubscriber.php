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

use Austral\SeoBundle\Services\UrlParameterManagement;
use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;
use Austral\HttpBundle\EventSubscriber\HttpEventSubscriber;

use Austral\ToolsBundle\Configuration\ConfigurationInterface;
use Austral\ToolsBundle\Services\Debug;

use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\WebsiteBundle\Event\WebsiteHttpEvent;
use Austral\WebsiteBundle\Handler\Interfaces\WebsiteHandlerInterface;
use Austral\WebsiteBundle\Template\TemplateParameters;

use Doctrine\ORM\NonUniqueResultException;

use Doctrine\ORM\Query\QueryException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
   * @var DomainsManagement
   */
  protected DomainsManagement $domains;

  /**
   * HttpSubscriber constructor.
   *
   * @param ContainerInterface $container
   * @param ConfigurationInterface $configuration
   * @param Debug $debug
   * @param DomainsManagement $domains
   *
   * @throws QueryException
   */
  public function __construct(ContainerInterface $container, ConfigurationInterface $configuration,  DomainsManagement $domains, Debug $debug)
  {
    parent::__construct($container, $configuration, $debug);
    $this->domains = $domains->initialize();
  }

  /**
   * @return array[]
   */
  public static function getSubscribedEvents(): array
  {
    return [
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_REQUEST_INITIALISE     =>  ["onRequestInitialise", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_REQUEST                =>  ["onRequest", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_CONTROLLER             =>  ["onController", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_RESPONSE               =>  ["onResponse", 1024],
      WebsiteHttpEvent::EVENT_AUSTRAL_HTTP_EXCEPTION              =>  ["onException", 1024],
    ];
  }

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return void
   * @throws NonUniqueResultException
   */
  public function onRequestInitialise(HttpEventInterface $httpEvent)
  {
    $currentLocal = null;

    /** @var DomainInterface $currentDomain */
    $currentDomain = $this->domains->getCurrentDomain();

    if($currentDomain && $currentDomain->getLanguage()) {
      $currentLocal = $currentDomain->getLanguage();
    }
    if($httpEvent->getKernelEvent()->getRequest()->attributes->has("_locale"))
    {
      $currentLocal = $httpEvent->getKernelEvent()->getRequest()->attributes->get("_locale");
    }
    if(!$httpEvent->getKernelEvent()->getRequest()->attributes->has("language"))
    {
      $httpEvent->getKernelEvent()->getRequest()->attributes->set("language", $currentLocal ? : $this->container->getParameter('locale'));
    }
    $httpEvent->getHttpRequest()->setLanguage($currentLocal);
  }

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return void
   * @throws NonUniqueResultException
   */
  public function onRequest(HttpEventInterface $httpEvent)
  {
    /** @var AttributeBagInterface $requestAttributes */
    $requestAttributes = $httpEvent->getKernelEvent()->getRequest()->attributes;

    $requestUri = urldecode($httpEvent->getKernelEvent()->getRequest()->getRequestUri());
    $pathInfo = urldecode(trim($httpEvent->getKernelEvent()->getRequest()->getPathInfo(), "/"));

    /** @var DomainInterface $domain */
    $domain = $this->domains->getCurrentDomain();

    if($redirection = $this->container->get('austral.entity_manager.redirection')->retreiveByUrlSource($pathInfo , $domain->getId(), $httpEvent->getKernelEvent()->getRequest()->getLocale()))
    {
      $urlRedirect = str_replace($pathInfo, $redirection->getUrlDestination(), $requestUri);
      $response = new RedirectResponse($urlRedirect, $redirection->getStatusCode());
      $httpEvent->getKernelEvent()->setResponse($response);
      return;
    }

    $host = $httpEvent->getKernelEvent()->getRequest()->headers->get('host');

    $slug = $requestAttributes->get('slug', null);
    if(!$domain)
    {
      /** @var DomainInterface $domainMaster */
      $domainMaster = $this->domains->getDomainMaster();
      if($domainMaster)
      {
        $response = new RedirectResponse("{$domainMaster->getScheme()}://{$domainMaster->getDomain()}".($slug ? "/{$slug}" : ""), 301);
        $httpEvent->getKernelEvent()->setResponse($response);
        return;
      }
      else
      {
        throw new NotFoundHttpException("Domain {$host} not found !");
      }
    }
    else {
      if($domain->getRedirectUrl())
      {
        $response = new RedirectResponse($domain->getRedirectUrl(), 301);
        $httpEvent->getKernelEvent()->setResponse($response);
        return;
      }
      $this->domains->setFilterDomainId($domain->getId());
    }

    /** @var UrlParameterManagement $urlParameterManagement */
    $urlParameterManagement = $this->container->get("austral.seo.url_parameter.management");

    /** @var HttpTemplateParametersInterface|TemplateParameters $templateParameters */
    $templateParameters = $this->container->get("austral.website.template");

    /** @var HttpHandlerInterface|WebsiteHandlerInterface $websiteHandler */
    $websiteHandler = $this->container->get("austral.website.handler");

    $websiteHandler->setTemplateParameters($templateParameters);

    $handlerMethod = $requestAttributes->get('_handler_method', null);
    if($requestAttributes->get('_austral_page', false))
    {
      if(!$urlParameter = $urlParameterManagement->retrieveUrlParameters($slug))
      {
        if(!$domain || !$domain->getOnePage())
        {
          $websiteHandler->pageNotFound();
        }
        else
        {
          $urlParameter = $urlParameterManagement->retrieveUrlParameters("");
        }
      }
      $currentPage = $urlParameter->getObject();
      $websiteHandler->setUrlParameter($urlParameter)->setPage($currentPage);
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
    if($this->container->getParameter("kernel.environment") === "dev")
    {
      return;
    }
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

    /** @var Pages $servicePages */
    $servicePages = $this->container->get("austral.seo.pages");

    /** @var PageInterface $currentPage */
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