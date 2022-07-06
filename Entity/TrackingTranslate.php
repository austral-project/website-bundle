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

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\WebsiteBundle\Entity\Interfaces\TrackingInterface;
use Austral\WebsiteBundle\Entity\Interfaces\TrackingTranslateInterface;

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateChildTrait;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;


/**
 * Austral TrackingTranslate Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class TrackingTranslate extends Entity implements TrackingTranslateInterface, EntityInterface, EntityTranslateChildInterface
{
  
  use EntityTimestampableTrait;
  use EntityTranslateChildTrait;
  
  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;
  
  /**
   * @var TrackingInterface|EntityTranslateMasterInterface
   *
   * @ORM\ManyToOne(targetEntity="\Austral\WebsiteBundle\Entity\Interfaces\TrackingInterface", inversedBy="translates", cascade={"persist"})
   * @ORM\JoinColumn(name="master_id", referencedColumnName="id")
   */
  protected EntityTranslateMasterInterface $master;

  /**
   * @var string|null
   * @ORM\Column(name="rgpd_description", type="text", nullable=true )
   */
  protected ?string $rgpdDescription;

  /**
   * @var string|null
   * @ORM\Column(name="rgpd_link", type="text", nullable=true )
   */
  protected ?string $rgpdLink;
  
  /**
   * @var string|null
   * @ORM\Column(name="script", type="text", nullable=true )
   */
  protected ?string $script;
  
  /**
   * @var string|null
   * @ORM\Column(name="no_script", type="string", length=255, nullable=true )
   */
  protected ?string $noScript;
  
  /**
   * @var boolean
   * @ORM\Column(name="is_active", type="boolean", nullable=true)
   */
  protected bool $isActive = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="file_name", type="string", length=255, nullable=true )
   */
  protected ?string $fileName;
  
  /**
   * @var string|null
   * @ORM\Column(name="file_content", type="text", nullable=true )
   */
  protected ?string $fileContent;
  
  
  /**
   * Constructor
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
   * @return string|null
   */
  public function getRgpdDescription(): ?string
  {
    return $this->rgpdDescription;
  }

  /**
   * @param string|null $rgpdDescription
   *
   * @return TrackingTranslate
   */
  public function setRgpdDescription(?string $rgpdDescription): TrackingTranslate
  {
    $this->rgpdDescription = $rgpdDescription;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getRgpdLink(): ?string
  {
    return $this->rgpdLink;
  }

  /**
   * @param string|null $rgpdLink
   *
   * @return TrackingTranslate
   */
  public function setRgpdLink(?string $rgpdLink): TrackingTranslate
  {
    $this->rgpdLink = $rgpdLink;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getScript(): ?string
  {
    return $this->script;
  }

  /**
   * @param string|null $script
   *
   * @return TrackingTranslate
   */
  public function setScript(?string $script): TrackingTranslate
  {
    $this->script = $script;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getNoScript(): ?string
  {
    return $this->noScript;
  }

  /**
   * @param string|null $noScript
   *
   * @return TrackingTranslate
   */
  public function setNoScript(?string $noScript): TrackingTranslate
  {
    $this->noScript = $noScript;
    return $this;
  }

  /**
   * @return bool
   */
  public function isActive(): bool
  {
    return $this->isActive;
  }

  /**
   * @param bool $isActive
   *
   * @return TrackingTranslate
   */
  public function setIsActive(bool $isActive): TrackingTranslate
  {
    $this->isActive = $isActive;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFileName(): ?string
  {
    return $this->fileName;
  }

  /**
   * @param string|null $fileName
   *
   * @return TrackingTranslate
   */
  public function setFileName(?string $fileName): TrackingTranslate
  {
    $this->fileName = $fileName;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFileContent(): ?string
  {
    return $this->fileContent;
  }

  /**
   * @param string|null $fileContent
   *
   * @return TrackingTranslate
   */
  public function setFileContent(?string $fileContent): TrackingTranslate
  {
    $this->fileContent = $fileContent;
    return $this;
  }

}