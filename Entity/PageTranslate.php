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

use Austral\ContentBlockBundle\Entity\Interfaces\EntityContentBlockInterface;
use Austral\ContentBlockBundle\Entity\Traits\EntityComponentsTrait;
use Austral\EntityFileBundle\Annotation as AustralFile;
use Austral\EntityFileBundle\Entity\Traits\EntityFileCropperTrait;
use Austral\EntitySeoBundle\Entity\Traits\EntityHomepageTranslateTrait;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\WebsiteBundle\Entity\Interfaces\EntitySocialNetworkInterface;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\WebsiteBundle\Entity\Traits\EntitySocialNetworkTrait;

use Austral\EntitySeoBundle\Entity\Traits\EntityRobotTrait;
use Austral\EntitySeoBundle\Entity\Traits\EntitySeoTrait;
use Austral\EntitySeoBundle\Entity\Interfaces\EntityRobotInterface;
use Austral\EntitySeoBundle\Entity\Interfaces\EntitySeoInterface;

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateChildTrait;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

use Austral\WebsiteBundle\Entity\Interfaces\PageTranslateInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;


/**
 * Austral PageTranslate Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class PageTranslate extends Entity implements PageTranslateInterface, EntityInterface, EntityTranslateChildInterface, EntitySeoInterface, EntityRobotInterface, EntityContentBlockInterface, EntitySocialNetworkInterface
{

  use EntityTimestampableTrait;
  use EntityTranslateChildTrait;
  use EntityRobotTrait;
  use EntitySeoTrait;
  use EntitySocialNetworkTrait;
  use EntityComponentsTrait;
  use EntityFileCropperTrait;
  use EntityHomepageTranslateTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;

  /**
   * @var PageInterface|EntityTranslateMasterInterface
   *
   * @ORM\ManyToOne(targetEntity="Austral\WebsiteBundle\Entity\Interfaces\PageInterface", inversedBy="translates", cascade={"persist"})
   * @ORM\JoinColumn(name="master_id", referencedColumnName="id")
   */
  protected EntityTranslateMasterInterface $master;

  /**
   * @var string|null
   * @ORM\Column(name="name", type="string", length=255, nullable=false )
   */
  protected ?string $name = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="summary", type="text", nullable=true )
   */
  protected ?string $summary = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="image", type="string", length=255, nullable=true)
   * @AustralFile\UploadParameters(configName="page_image", virtualnameField="imageReelname")
   * @AustralFile\ImageSize()
   * @AustralFile\Croppers({
   *  "desktop",
   *  "tablet",
   *  "mobile"
   * })
   */
  protected ?string $image = null;

  /**
   * @var string|null
   * @ORM\Column(name="image_reelname", type="string", length=255, nullable=true)
   */
  protected ?string $imageReelname = null;

  /**
   * @var string|null
   * @ORM\Column(name="image_alt", type="string", length=255, nullable=true)
   */
  protected ?string $imageAlt = null;

  /**
   * PageTranslate constructor.
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
  }

  /**
   * @return bool
   */
  public function getRefUrlLastEnabled(): bool
  {
    return !$this->getMaster()->getRefUrlLastEnabled();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->name ?? "";
  }

  /**
   * @return PageInterface|EntitySeoInterface|null
   */
  public function getPageParent(): ?EntitySeoInterface
  {
    return $this->getMaster()->getPageParent();
  }

  /**
   * Get name
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Set name
   *
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): PageTranslateInterface
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Get summary
   * @return string|null
   */
  public function getSummary(): ?string
  {
    return $this->summary;
  }

  /**
   * Set summary
   *
   * @param string|null $summary
   *
   * @return $this
   */
  public function setSummary(?string $summary): PageTranslateInterface
  {
    $this->summary = $summary;
    return $this;
  }

  /**
   * Get image
   * @return string|null
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * Set image
   *
   * @param string|null $image
   *
   * @return $this
   */
  public function setImage(?string $image): PageTranslateInterface
  {
    $this->image = $image;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getImageAlt(): ?string
  {
    return $this->imageAlt;
  }

  /**
   * @param string|null $imageAlt
   * @return $this
   */
  public function setImageAlt(?string $imageAlt): PageTranslateInterface
  {
    $this->imageAlt = $imageAlt;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getImageReelname(): ?string
  {
    return $this->imageReelname;
  }

  /**
   * @param string|null $imageReelname
   *
   * @return PageTranslate
   */
  public function setImageReelname(?string $imageReelname): PageTranslate
  {
    $this->imageReelname = $imageReelname;
    return $this;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function getPath(): string
  {
    return $this->getBaseUrl().$this->getRefUrlLast();
  }

}