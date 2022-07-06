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
use Austral\EntityFileBundle\Entity\Interfaces\EntityFileInterface;
use Austral\EntitySeoBundle\Services\Pages;
use Austral\HttpBundle\Handler\HttpHandler;
use Austral\NotifyBundle\Mercure\Mercure;
use Austral\WebsiteBundle\Entity\Traits\EntityTemplateTrait;
use Austral\WebsiteBundle\Services\ConfigVariable;

use Austral\ContentBlockBundle\Entity\Interfaces\EntityContentBlockInterface;
use Austral\ContentBlockBundle\Entity\Interfaces\LibraryInterface;
use Austral\ContentBlockBundle\Entity\Traits\EntityComponentsTrait;
use Austral\ContentBlockBundle\Event\ContentBlockEvent;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntitySeoBundle\Entity\Interfaces\EntityRobotInterface;
use Austral\EntitySeoBundle\Entity\Interfaces\EntitySeoInterface;

use Austral\WebsiteBundle\Entity\Interfaces\EntitySocialNetworkInterface;
use Austral\WebsiteBundle\Handler\Interfaces\WebsiteHandlerInterface;

/**
 * Handler Website Master abstract.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class WebsiteHandler extends HttpHandler implements WebsiteHandlerInterface
{

  /**
   * @var EntityInterface|EntitySeoInterface|null
   */
  protected ?EntityInterface $page = null;

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
    if($this->page instanceof EntityRobotInterface)
    {
      $this->robotsParameters($this->page);
    }
    if($this->page instanceof EntitySeoInterface)
    {
      $this->seoParameters($this->page);
    }
    if($this->page instanceof EntitySocialNetworkInterface)
    {
      $this->socialParameters($this->page);
    }

    if($this->page instanceof EntityContentBlockInterface)
    {
      $contentBlockEvent = new ContentBlockEvent($this->page, "Front");
      $this->dispatcher->dispatch($contentBlockEvent, ContentBlockEvent::EVENT_AUSTRAL_CONTENT_BLOCK_COMPONENTS_HYDRATE);
    }

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
    return $this;
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
      ->setDefaultObjectPage($this->container->get('austral.entity_manager.page')->create());
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
    /** @var Pages $serviceSeo */
    $seoPages = $this->container->get('austral.entity_seo.pages');
    $this->templateParameters->addParameters("urls", $seoPages->getUrls());
    return $this;
  }

  /**
   * @param EntityRobotInterface $page
   *
   * @return $this
   * @throws \Exception
   */
  protected function robotsParameters(EntityRobotInterface $page): WebsiteHandler
  {
    $this->templateParameters->addParameters("robots", array(
      "index"           =>  $this->configVariable->getValueVariableByKey("site.index", false) && $page->getIsIndex(),
      "follow"          =>  $this->configVariable->getValueVariableByKey("site.follow", false) && $page->getIsFollow(),
      "status"          =>  $page->getStatus(),
    ));
    return $this;
  }

  /**
   * @param EntitySeoInterface $page
   *
   * @return $this
   */
  protected function seoParameters(EntitySeoInterface $page): WebsiteHandler
  {
    $this->templateParameters->addParameters("seo", array(
      "title"           =>  $page->getRefTitle() ?? $page->__toString(),
      "description"     =>  $page->getRefDescription() ?? $page->__toString(),
      "canonical"       =>  $page->getCanonical(),
    ));
    return $this;
  }

  /**
   * @param EntitySocialNetworkInterface|EntityFileInterface $page
   *
   * @return $this
   */
  protected function socialParameters(EntitySocialNetworkInterface $page): WebsiteHandler
  {
    $this->templateParameters->addParameters("social", array(
      "title"           =>  $page->getSocialTitle(),
      "description"     =>  $page->getSocialDescription(),
      "image"           =>  $this->uploadsFileLinkGenerator($page, 'socialImage', "original", "i", 1200, 630)
    ));
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
   * @param EntityFileInterface $object
   * @param string $fieldname
   * @param array $params
   *
   * @return ?string
   */
  protected function downloadFileLinkGenerator(EntityFileInterface $object, string $fieldname, array $params = array()): ?string
  {
    if($this->container->has('austral.entity_file.link.generator'))
    {
      return $this->container->get('austral.entity_file.link.generator')->download($object, $fieldname, $params);
    }
    return null;
  }

  /**
   * @param EntityFileInterface $object
   * @param string $fieldname
   * @param string $type
   * @param string $mode
   * @param int|null $width
   * @param int|null $height
   * @param array $params
   *
   * @return ?string
   */
  protected function uploadsFileLinkGenerator(EntityFileInterface $object, string $fieldname, string $type = "original", string $mode = "i", int $width = null, int $height = null, array $params = array()): ?string
  {
    if($this->container->has('austral.entity_file.link.generator'))
    {
      return $this->container->get('austral.entity_file.link.generator')->image($object, $fieldname, $type, $mode, $width, $height, $params);
    }
    return null;
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
   * @return EntityInterface|EntitySeoInterface|EntityTemplateTrait|null
   */
  public function getPage(): ?EntityInterface
  {
    return $this->page;
  }

}