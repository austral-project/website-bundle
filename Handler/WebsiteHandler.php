<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\WebsiteBundle\Handler;

use Austral\ContentBlockBundle\Entity\Component;
use Austral\ContentBlockBundle\EntityManager\EditorComponentEntityManager;
use Austral\ContentBlockBundle\Event\GuidelineEvent;
use Austral\EntityBundle\Entity\Interfaces\FileInterface;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\SeoBundle\Entity\Interfaces\UrlParameterInterface;
use Austral\SeoBundle\Services\UrlParameterManagement;
use Austral\HttpBundle\Handler\HttpHandler;
use Austral\NotifyBundle\Mercure\Mercure;
use Austral\WebsiteBundle\Entity\Traits\EntityTemplateTrait;
use Austral\WebsiteBundle\Services\ConfigVariable;

use Austral\EntityBundle\Entity\Interfaces\ComponentsInterface;
use Austral\ContentBlockBundle\Entity\Interfaces\LibraryInterface;
use Austral\ContentBlockBundle\Entity\Traits\EntityComponentsTrait;
use Austral\ContentBlockBundle\Event\ContentBlockEvent;

use Austral\EntityBundle\Entity\EntityInterface;

use Austral\WebsiteBundle\Handler\Interfaces\WebsiteHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Handler Website Master abstract.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class WebsiteHandler extends HttpHandler implements WebsiteHandlerInterface
{

  /**
   * @var EntityInterface|null
   */
  protected ?EntityInterface $page = null;

  /**
   * @var UrlParameterInterface|FileInterface|null
   */
  protected ?UrlParameterInterface $urlParameter = null;

  /**
   * @var ConfigVariable
   */
  protected ConfigVariable $configVariable;

  /**
   * @var Mercure|null
   */
  protected ?Mercure $mercure = null;

  /**
   * @var string|null
   */
  protected ?string $handlerMethod = null;

  /**
   * @param ConfigVariable $configVariable
   *
   * @return $this
   */
  public function setConfigVariable(ConfigVariable $configVariable): WebsiteHandler
  {
    $this->configVariable = $configVariable;
    return $this;
  }

  /**
   * @param Mercure|null $mercure
   *
   * @return $this
   */
  public function setMercure(?Mercure $mercure): WebsiteHandler
  {
    $this->mercure = $mercure;
    return $this;
  }

  protected abstract function init();

  /**
   * @param string $handlerMethod
   *
   * @return $this
   */
  public function setHandlerMethod(string $handlerMethod): WebsiteHandler
  {
    $this->handlerMethod = $handlerMethod;
    return $this;
  }


  /**
   * @return $this
   * @throws \Exception
   */
  public function initHandler(): WebsiteHandler
  {
    if(method_exists($this, $this->handlerMethod)) {
      $this->{$this->handlerMethod}();
    }
    return $this;
  }

  /**
   * @return $this
   * @throws \Exception
   */
  protected function page(): WebsiteHandler
  {
    $this->pageBefore();
    if($this->page instanceof ComponentsInterface)
    {
      $contentBlockEvent = new ContentBlockEvent($this->page, "Front");
      $this->dispatcher->dispatch($contentBlockEvent, ContentBlockEvent::EVENT_AUSTRAL_CONTENT_BLOCK_COMPONENTS_HYDRATE);
    }

    if($libraries = $this->container->get('austral.entity_manager.library')->selectAllIndexBy("keyname", $this->domainsManagement->getCurrentDomain()->getId()))
    {
      /** @var LibraryInterface|EntityComponentsTrait $library */
      foreach($libraries as $library)
      {
        if($library->getIsEnabled())
        {
          $contentBlockEvent = new ContentBlockEvent($library, "Front");
          $this->dispatcher->dispatch($contentBlockEvent, ContentBlockEvent::EVENT_AUSTRAL_CONTENT_BLOCK_COMPONENTS_HYDRATE);
        }
      }
    }
    $this->templateParameters->addParameters("libraries", $libraries);
    $method = "initPageEntity{$this->page->getClassname()}";
    if(method_exists($this, $method))
    {
      $this->$method();
    }
    $this->init();
    if($this->mercure)
    {
      $this->initMercure();
    }

    $this->robotsParameters();
    $this->seoParameters($this->page);
    $this->socialParameters($this->page);
    $this->pageAfter();

    return $this;
  }

  protected function pageBefore()
  {

  }

  protected function pageAfter()
  {

  }


  /**
   * @return $this
   * @throws \Exception
   */
  protected function guideline(): WebsiteHandler
  {
    $this->templateParameters->addParameters("robots", array(
      "index"           =>  false,
      "follow"          =>  false,
      "status"          =>  false,
    ));

    $this->templateParameters->addParameters("seo", array(
      "title"           =>  "Austral - Guideline",
      "description"     =>  "",
      "canonical"       =>  "",
    ));

    if($libraries = $this->container->get('austral.entity_manager.library')->selectAllIndexBy())
    {
      /** @var LibraryInterface|EntityComponentsTrait $library */
      foreach($libraries as $library)
      {
        if($library->getIsEnabled())
        {
          $contentBlockEvent = new ContentBlockEvent($library, "Front");
          $this->dispatcher->dispatch($contentBlockEvent, ContentBlockEvent::EVENT_AUSTRAL_CONTENT_BLOCK_COMPONENTS_HYDRATE);
        }
      }
      $this->templateParameters->addParameters("libraries", $libraries);
    }

    /** @var EditorComponentEntityManager $editorComponent */
    $editorComponentManager = $this->container->get('austral.entity_manager.editor_component');
    $editorComponents = $editorComponentManager->selectAllEnabled();

    /** @var Component $componentObject */
    $componentObject = $this->container->get('austral.entity_manager.component')->create();

    $guidelineEvent = new GuidelineEvent($this->request->query->get('container', "default-0"), "Front");

    $guidelineEvent->setComponentObject($componentObject)
      ->setEditorComponents($editorComponents)
      ->setDefaultObjectPage($this->container->get('austral.entity_manager.page')->create())
      ->setGuidelineFormValues((array) $this->request->request->get('guideline', array()));
    $this->dispatcher->dispatch($guidelineEvent, GuidelineEvent::EVENT_AUSTRAL_CONTENT_BLOCK_GUIDELINE_INIT);

    $page = $this->container->get('austral.entity_manager.page')->create();
    $page->setKeyname("guideline");
    $this->templateParameters->addParameters("currentPage", $page);
    $this->page = $page;
    $this->templateParameters->addParameters("components", $guidelineEvent->getFinalComponents());
    $this->templateParameters->addParameters("containers", $guidelineEvent->getContainers());
    $this->init();
    return $this;
  }

  /**
   * @return $this
   */
  protected function sitemap(): WebsiteHandler
  {
    /** @var UrlParameterManagement $urlParameterManagement */
    $urlParameterManagement = $this->container->get('austral.seo.url_parameter.management');
    $this->templateParameters->addParameters("urls", $urlParameterManagement->getUrlParametersByDomainsByLanguage(DomainsManagement::DOMAIN_ID_CURRENT)->getUrlParametersPathIndexed());
    return $this;
  }

  /**
   * @return $this
   * @throws \Exception
   */
  protected function robotsParameters(): WebsiteHandler
  {
    if(!$this->templateParameters->hasParameter("robots"))
    {
      $this->templateParameters->addParameters("robots", array(
        "index"           =>  $this->configVariable->getValueVariableByKey("site.index", false) && $this->urlParameter ? $this->urlParameter->getIsIndex() : false,
        "follow"          =>  $this->configVariable->getValueVariableByKey("site.follow", false) && $this->urlParameter ? $this->urlParameter->getIsFollow() : false,
        "status"          =>  $this->urlParameter ? $this->urlParameter->getStatus() : UrlParameterInterface::STATUS_DRAFT,
      ));
    }
    return $this;
  }

  /**
   * @param EntityInterface|null $page
   *
   * @return $this
   */
  protected function seoParameters(?EntityInterface $page = null): WebsiteHandler
  {
    if(!$this->templateParameters->hasParameter("seo"))
    {
      $this->templateParameters->addParameters("seo", array(
        "title"           =>  $this->urlParameter && $this->urlParameter->getSeoTitle() ? $this->urlParameter->getSeoTitle() : ($page ? $page->__toString() : ""),
        "description"     =>  $this->urlParameter && $this->urlParameter->getSeoDescription() ? $this->urlParameter->getSeoDescription() : ($page ? $page->__toString() : ""),
        "canonical"       =>  $this->urlParameter && $this->urlParameter->getSeoCanonical() ? $this->urlParameter->getSeoCanonical() : null,
      ));
    }
    return $this;
  }

  /**
   * @param EntityInterface|FileInterface|null $page
   *
   * @return $this
   */
  protected function socialParameters(?EntityInterface $page = null): WebsiteHandler
  {
    if(!$this->templateParameters->hasParameter("social"))
    {
      $this->templateParameters->addParameters("social", array(
        "title"           =>  $this->urlParameter && $this->urlParameter->getSocialTitle() ? $this->urlParameter->getSocialTitle() : ($page ? $page->__toString() : ""),
        "description"     =>  $this->urlParameter && $this->urlParameter->getSocialDescription() ? $this->urlParameter->getSocialDescription() : ($page ? $page->__toString() : ""),
        "image"           =>  $this->urlParameter ? $this->uploadsFileLinkGenerator($this->urlParameter, 'socialImage', "original", "i", 1200, 630) : null
      ));
    }
    return $this;
  }


  /**
   * @return $this
   */
  protected function initMercure(): WebsiteHandler
  {
    $this->templateParameters->addParameters("mercure", array(
      "url"         =>  $this->mercure->getHub()->getPublicUrl(),
      "subscribes"  =>  $this->mercure->getSubscribes()
    ));
    return $this;
  }

  /**
   * @param FileInterface $object
   * @param string $fieldname
   * @param array $params
   *
   * @return ?string
   */
  protected function downloadFileLinkGenerator(FileInterface $object, string $fieldname, array $params = array()): ?string
  {
    if($this->container->has('austral.entity_file.link.generator'))
    {
      return $this->container->get('austral.entity_file.link.generator')->download($object, $fieldname, $params);
    }
    return null;
  }

  /**
   * @param FileInterface $object
   * @param string $fieldname
   * @param string $type
   * @param string $mode
   * @param int|null $width
   * @param int|null $height
   * @param array $params
   *
   * @return ?string
   */
  protected function uploadsFileLinkGenerator(FileInterface $object, string $fieldname, string $type = "original", string $mode = "i", int $width = null, int $height = null, array $params = array()): ?string
  {
    if($this->container->has('austral.entity_file.link.generator'))
    {
      return $this->container->get('austral.entity_file.link.generator')->image($object, $fieldname, $type, $mode, $width, $height, $params);
    }
    return null;
  }

  /**
   * Generates a URL from the given parameters.
   *
   * @param string $route         The name of the route
   * @param mixed          $parameters    An array of parameters
   * @param bool|string    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
   *
   * @return string The generated URL
   * @see UrlGeneratorInterface
   */
  public function australGenerateUrl(string $route, EntityInterface $object, $parameters = array(), string $domainId = "current", $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, $env = null): string
  {
    return $this->container->get('austral.seo.routing')->generate($route, $object, $parameters, $domainId, $referenceType);
  }

  /**
   * @param EntityInterface|null $page
   *
   * @return $this
   */
  public function setPage(?EntityInterface $page): WebsiteHandler
  {
    $this->page = $page;
    return $this;
  }

  /**
   * @return EntityInterface|EntityTemplateTrait|null
   */
  public function getPage(): ?EntityInterface
  {
    return $this->page;
  }

  /**
   * @param UrlParameterInterface|null $urlParameter
   *
   * @return $this
   */
  public function setUrlParameter(?UrlParameterInterface $urlParameter): WebsiteHandler
  {
    $this->urlParameter = $urlParameter;
    return $this;
  }

  /**
   * @return UrlParameterInterface|EntityInterface|null
   */
  public function getUrlParameter(): ?UrlParameterInterface
  {
    return $this->urlParameter;
  }

}