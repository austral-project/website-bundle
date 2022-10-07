<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\WebsiteBundle\Entity;

use Austral\ContentBlockBundle\Entity\Traits\EntityComponentsTrait;
use Austral\EntityFileBundle\Entity\Interfaces\EntityFileInterface;
use Austral\EntityFileBundle\Entity\Traits\EntityFileCropperTrait;
use Austral\EntityFileBundle\Entity\Traits\EntityFileTrait;
use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\WebsiteBundle\Model\SubDomain;
use Austral\EntityFileBundle\Annotation as AustralFile;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

use Exception;


/**
 * Austral Domain Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class Domain extends Entity implements DomainInterface, EntityInterface, EntityFileInterface
{

  use EntityFileTrait;
  use EntityFileCropperTrait;
  use EntityTimestampableTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;

  /**
   * @var PageInterface|null
   *
   * @ORM\ManyToOne(targetEntity="Austral\WebsiteBundle\Entity\Interfaces\PageInterface", inversedBy="domains")
   * @ORM\JoinColumn(name="homepage_id", referencedColumnName="id", onDelete="SET NULL")
   */
  protected ?PageInterface $homepage = null;

  /**
   * @var string|null
   * @ORM\Column(name="domain", type="string", length=255, nullable=false )
   */
  protected ?string $domain = null;

  /**
   * @var string|null
   * @ORM\Column(name="name", type="string", length=255, nullable=true )
   */
  protected ?string $name = null;

  /**
   * @var string|null
   * @ORM\Column(name="favicon", type="string", length=255, nullable=true )
   * @AustralFile\UploadParameters(configName="page_image")
   * @AustralFile\ImageSize()
   * @AustralFile\Croppers(croppers={
   *   @AustralFile\Cropper(name="logo", ratio="1/1", picto="austral-picto-globe" )
   * })
   */
  protected ?string $favicon = null;

  /**
   * @var string|null
   * @ORM\Column(name="scheme", type="string", length=255, nullable=true )
   */
  protected ?string $scheme = null;

  /**
   * @var array
   * @ORM\Column(name="sub_domains", type="json", nullable=true )
   */
  protected array $subDomains = array();
  
  /**
   * @var boolean
   * @ORM\Column(name="is_master", type="boolean", nullable=true)
   */
  protected bool $isMaster = false;
  
  /**
   * @var boolean
   * @ORM\Column(name="is_enabled", type="boolean", nullable=true)
   */
  protected bool $isEnabled = false;

  /**
   * @var boolean
   * @ORM\Column(name="is_virtual", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $isVirtual = false;

  /**
   * @var string|null
   * @ORM\Column(name="redirect_url", type="string", length=255, nullable=true )
   */
  protected ?string $redirectUrl = null;

  /**
   * @var boolean
   * @ORM\Column(name="with_uri", type="boolean", nullable=false, options={"default": false})
   */
  protected bool $withUri = false;

  /**
   * @var boolean
   * @ORM\Column(name="one_page", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $onePage = false;

  /**
   * @var string|null
   * @ORM\Column(name="language", type="string", length=255, nullable=true )
   */
  protected ?string $language = null;

  /**
   * Constructor
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->name ?? $this->domain;
  }

  /**
   * @return PageInterface|null
   */
  public function getHomepage(): ?PageInterface
  {
    return $this->homepage;
  }

  /**
   * @param PageInterface|null $homepage
   *
   * @return Domain
   */
  public function setHomepage(?PageInterface $homepage): Domain
  {
    $this->homepage = $homepage;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getDomain(): ?string
  {
    return $this->domain;
  }

  /**
   * @param string|null $domain
   *
   * @return Domain
   */
  public function setDomain(?string $domain): Domain
  {
    $this->domain = $domain;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): Domain
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFavicon(): ?string
  {
    return $this->favicon;
  }

  /**
   * @param string|null $favicon
   *
   * @return $this
   */
  public function setFavicon(?string $favicon): Domain
  {
    $this->favicon = $favicon;
    return $this;
  }

  /**
   * @return string
   */
  public function getScheme(): string
  {
    return $this->scheme ?: self::SCHEME_HTTPS;
  }

  /**
   * @param string $scheme
   *
   * @return Domain
   */
  public function setScheme(string $scheme): Domain
  {
    $this->scheme = $scheme;
    return $this;
  }

  /**
   * Get themes
   * @return array
   */
  public function getSubDomains(): array
  {
    $subDomains = array();
    foreach($this->subDomains as $subDomainValues)
    {
      /** @var SubDomain $subDomainObject */
      $subDomainObject = unserialize($subDomainValues);
      $subDomains[$subDomainObject->getId()] = $subDomainObject;
    }
    return $subDomains;
  }

  /**
   * @param array $subDomains
   *
   * @return Domain
   */
  public function setSubDomains(array $subDomains): Domain
  {
    $this->subDomains = array();
    /** @var SubDomain $subDomain */
    foreach ($subDomains as $id => $subDomain)
    {
      $subDomain->setId($id);
      $this->subDomains[$subDomain->getId()] = serialize($subDomain);
    }
    ksort($this->subDomains);
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsMaster(): bool
  {
    return $this->isMaster;
  }

  /**
   * @param bool $isMaster
   *
   * @return Domain
   */
  public function setIsMaster(bool $isMaster): Domain
  {
    $this->isMaster = $isMaster;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsEnabled(): bool
  {
    return $this->isEnabled;
  }

  /**
   * @param bool $isEnabled
   *
   * @return Domain
   */
  public function setIsEnabled(bool $isEnabled): Domain
  {
    $this->isEnabled = $isEnabled;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsVirtual(): bool
  {
    return $this->isVirtual;
  }

  /**
   * @param bool $isVirtual
   *
   * @return $this
   */
  public function setIsVirtual(bool $isVirtual): Domain
  {
    $this->isVirtual = $isVirtual;
    return $this;
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
   * @return Domain
   */
  public function setRedirectUrl(?string $redirectUrl): Domain
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * @return bool
   */
  public function getOnePage(): bool
  {
    return $this->onePage;
  }

  /**
   * @param bool $onePage
   *
   * @return $this
   */
  public function setOnePage(bool $onePage): Domain
  {
    $this->onePage = $onePage;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getLanguage(): ?string
  {
    return $this->language;
  }

  /**
   * @param string|null $language
   *
   * @return Domain
   */
  public function setLanguage(?string $language): Domain
  {
    $this->language = $language;
    return $this;
  }

  /**
   * @return bool
   */
  public function getWithUri(): bool
  {
    return $this->withUri;
  }

  /**
   * @param bool $withUri
   *
   * @return Domain
   */
  public function setWithUri(bool $withUri): Domain
  {
    $this->withUri = $withUri;
    return $this;
  }

}