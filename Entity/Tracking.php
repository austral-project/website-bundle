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


use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;
use Austral\WebsiteBundle\Entity\Interfaces\TrackingInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Ramsey\Uuid\Uuid;


/**
 * Austral Tracking Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class Tracking extends Entity implements TrackingInterface, EntityInterface, EntityTranslateMasterInterface
{

  const TYPE_USER = "user";
  const TYPE_ROBOT = "robot";

  use EntityTimestampableTrait;
  use EntityTranslateMasterTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;
  
  /**
   * @var string|null
   * @ORM\Column(name="name", type="string", length=255, nullable=false )
   */
  protected ?string $name = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="keyname", type="string", length=255, nullable=false )
   */
  protected ?string $keyname = null;

  /**
   * @var string|null
   * @ORM\Column(name="type", type="string", length=255, nullable=true )
   */
  protected ?string $type = null;
  
  /**
   * @ORM\OneToMany(targetEntity="\Austral\WebsiteBundle\Entity\Interfaces\TrackingTranslateInterface", mappedBy="master", cascade={"persist", "remove"})
   */
  protected Collection $translates;

  /**
   * @var boolean
   * @ORM\Column(name="is_rgpd_activated", type="boolean", nullable=true)
   */
  protected bool $isRgpdActivated = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="rgpd_tag_name", type="string", length=255, nullable=true )
   */
  protected ?string $rgpdTagName = "other";
  
  /**
   * @var string|null
   * @ORM\Column(name="dom_position", type="string", length=255, nullable=true )
   */
  protected ?string $domPosition = null;


  /**
   * Constructor
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
    $this->type = self::TYPE_USER;
    $this->translates = new ArrayCollection();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->name ?? "";
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getTemplateScript(): ?string
  {
    $script = $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getScript() : null;
    $class = $this->getRgpdTagName() != "other" ? $this->getRgpdTagName() : $this->getKeyname();

    if(strpos($script, "class='") !== false)
    {
      $script = str_replace("class='", "class='".$class." ", $script);
    }
    elseif(strpos($script, "class=\"") !== false)
    {
      $script = str_replace("class=\"", "class=\"".$class." ", $script);
    }
    else
    {
      $script = str_replace("<script", "<script class=\"".$class."\"", $script);
    }
    return $script;
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
  public function setName(?string $name): TrackingInterface
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Get keyname
   * @return string|null
   */
  public function getKeyname(): ?string
  {
    return $this->keyname;
  }

  /**
   * Set keyname
   *
   * @param string|null $keyname
   *
   * @return $this
   */
  public function setKeyname(?string $keyname): TrackingInterface
  {
    $this->keyname = $this->keynameGenerator($keyname);
    return $this;
  }

  /**
   * Get type
   * @return string|null
   */
  public function getType(): ?string
  {
    return $this->type;
  }

  /**
   * Set type
   *
   * @param string|null $type
   *
   * @return $this
   */
  public function setType(?string $type): TrackingInterface
  {
    $this->type = $type;
    return $this;
  }

  /**
   * Get isRgpdActivated
   * @return bool
   */
  public function isRgpdActivated(): bool
  {
    return $this->isRgpdActivated;
  }

  /**
   * Set isRgpdActivated
   *
   * @param bool $isRgpdActivated
   *
   * @return $this
   */
  public function setIsRgpdActivated(bool $isRgpdActivated): TrackingInterface
  {
    $this->isRgpdActivated = $isRgpdActivated;
    return $this;
  }

  /**
   * Get rgpdTagName
   * @return string|null
   */
  public function getRgpdTagName(): ?string
  {
    return $this->rgpdTagName;
  }

  /**
   * Set rgpdTagName
   *
   * @param string|null $rgpdTagName
   *
   * @return $this
   */
  public function setRgpdTagName(?string $rgpdTagName): TrackingInterface
  {
    $this->rgpdTagName = $rgpdTagName;
    return $this;
  }

  /**
   * Get domPosition
   * @return string|null
   */
  public function getDomPosition(): ?string
  {
    return $this->domPosition;
  }

  /**
   * Set domPosition
   *
   * @param string|null $domPosition
   *
   * @return $this
   */
  public function setDomPosition(?string $domPosition): TrackingInterface
  {
    $this->domPosition = $domPosition;
    return $this;
  }

}