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

use Austral\EntityBundle\Entity\Interfaces\FileInterface;
use Austral\EntityFileBundle\Entity\Traits\EntityFileTrait;
use Austral\HttpBundle\Entity\Traits\FilterByDomainTrait;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigTranslateInterface;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigValueByDomainInterface;

use Austral\EntityFileBundle\Annotation as AustralFile;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;


/**
 * Austral ConfigValueByDomain Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class ConfigValueByDomain extends Entity implements ConfigValueByDomainInterface, EntityInterface, FileInterface
{

  use EntityFileTrait;
  use EntityTimestampableTrait;
  use FilterByDomainTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;
  
  /**
   * @var ConfigTranslateInterface
   *
   * @ORM\ManyToOne(targetEntity="\Austral\WebsiteBundle\Entity\Interfaces\ConfigTranslateInterface", inversedBy="valuesByDomain", cascade={"persist"})
   * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
   */
  protected ConfigTranslateInterface $config;

  /**
   * @var string|null
   * @ORM\Column(name="image", type="string", length=255, nullable=true )
   * @AustralFile\UploadParameters()
   * @AustralFile\ImageSize()
   */
  protected ?string $image = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="file", type="string", length=255, nullable=true )
   * @AustralFile\UploadParameters(configName="default_file")
   */
  protected ?string $file = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="content_text", type="text", nullable=true )
   */
  protected ?string $contentText = null;

  /**
   * @var bool
   * @ORM\Column(name="content_boolean", type="boolean", nullable=true )
   */
  protected bool $contentBoolean = false;

  /**
   * @var string|null
   * @ORM\Column(name="internal_link", type="string", length=255, nullable=true )
   */
  protected ?string $internalLink = null;

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
   * __ToString
   * 
   * @return string
   */
  public function __toString()
  {
    return $this->getId();
  }

  /**
   * @return ConfigTranslateInterface
   */
  public function getConfig(): ConfigTranslateInterface
  {
    return $this->config;
  }

  /**
   * @param ConfigTranslateInterface $config
   *
   * @return ConfigValueByDomain
   */
  public function setConfig(ConfigTranslateInterface $config): ConfigValueByDomain
  {
    $this->config = $config;
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
  public function setImage(?string $image): ConfigValueByDomainInterface
  {
    $this->image = $image;
    return $this;
  }

  /**
   * Get file
   * @return string|null
   */
  public function getFile(): ?string
  {
    return $this->file;
  }

  /**
   * Set file
   *
   * @param string|null $file
   *
   * @return $this
   */
  public function setFile(?string $file): ConfigValueByDomainInterface
  {
    $this->file = $file;
    return $this;
  }

  /**
   * Get contentText
   * @return string|null
   */
  public function getContentText(): ?string
  {
    return $this->contentText;
  }

  /**
   * Set contentText
   *
   * @param string|null $contentText
   *
   * @return $this
   */
  public function setContentText(?string $contentText): ConfigValueByDomainInterface
  {
    $this->contentText = $contentText;
    return $this;
  }

  /**
   * Get contentBoolean
   * @return bool
   */
  public function getContentBoolean(): bool
  {
    return $this->contentBoolean;
  }

  /**
   * @param bool $contentBoolean
   *
   * @return ConfigValueByDomainInterface
   */
  public function setContentBoolean(bool $contentBoolean): ConfigValueByDomainInterface
  {
    $this->contentBoolean = $contentBoolean;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getInternalLink(): ?string
  {
    return $this->internalLink;
  }

  /**
   * @param string|null $internalLink
   *
   * @return ConfigValueByDomainInterface
   */
  public function setInternalLink(?string $internalLink): ConfigValueByDomainInterface
  {
    $this->internalLink = $internalLink;
    return $this;
  }

}