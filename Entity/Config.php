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
use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;
use Austral\EntityTranslateBundle\Annotation\Translate;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Austral Config Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 * @Translate(relationClass="Austral\WebsiteBundle\Entity\Interfaces\ConfigTranslateInterface")
 */
abstract class Config extends Entity implements ConfigInterface, EntityInterface, TranslateMasterInterface, FileInterface
{

  use EntityFileTrait;
  use EntityTranslateMasterTrait;
  use EntityTimestampableTrait;

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
   * @var boolean
   * @ORM\Column(name="with_domain", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $withDomain = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="keyname", type="string", length=255, nullable=false )
   */
  protected ?string $keyname = null;
  
  /**
   * @ORM\OneToMany(targetEntity="\Austral\WebsiteBundle\Entity\Interfaces\ConfigTranslateInterface", mappedBy="master", cascade={"persist", "remove"})
   */
  protected Collection $translates;

  /**
   * @var string
   * @ORM\Column(name="type", type="string", length=255, nullable=false )
   */
  protected string $type;

  /**
   * @var string|null
   * @ORM\Column(name="function_name", type="string", length=255, nullable=true )
   */
  protected ?string $functionName = null;

  /**
   * Config constructor.
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
    $this->type = "text";
    $this->translates = new ArrayCollection();
  }

  /**
   * @return array
   */
  public function getFieldsToDelete(): array
  {
    return array("image", "file");
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->name ?? "";
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
  public function setName(?string $name): Config
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
  public function setKeyname(?string $keyname): Config
  {
    $this->keyname = $this->keynameGenerator($keyname);
    return $this;
  }

  /**
   * Get type
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * Set type
   *
   * @param string $type
   *
   * @return $this
   */
  public function setType(string $type): Config
  {
    $this->type = $type;
    return $this;
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getContentText(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getContentText() : null;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function getImage(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getImage() : null;
  }

  /**
   * @param $image
   *
   * @return $this
   * @throws Exception
   */
  public function setImage($image): Config
  {
    $this->getTranslateCurrent()->setImage($image);
    return $this;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function getFile(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getFile() : null;
  }

  /**
   * @param $file
   *
   * @return $this
   * @throws Exception
   */
  public function setFile($file): Config
  {
    $this->getTranslateCurrent()->setFile($file);
    return $this;
  }

  /**
   * @return bool
   */
  public function getWithDomain(): bool
  {
    return $this->withDomain;
  }

  /**
   * @param bool $withDomain
   *
   * @return $this
   */
  public function setWithDomain(bool $withDomain): Config
  {
    $this->withDomain = $withDomain;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFunctionName(): ?string
  {
    return $this->functionName;
  }

  /**
   * @param string|null $functionName
   *
   * @return $this
   */
  public function setFunctionName(?string $functionName): Config
  {
    $this->functionName = $functionName;
    return $this;
  }

}