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
use Austral\EntityBundle\Entity\Interfaces\TreePageInterface;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;
use Austral\EntityBundle\Entity\Interfaces\ComponentsInterface;
use Austral\EntityBundle\Entity\Interfaces\RobotInterface;
use Austral\EntityBundle\Entity\Interfaces\SeoInterface;
use Austral\EntityBundle\Entity\Interfaces\FilterByDomainInterface;
use Austral\EntityBundle\Entity\Interfaces\SocialNetworkInterface;
use Austral\EntityBundle\Entity\Interfaces\FileInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;

use Austral\HttpBundle\Entity\Traits\FilterByDomainTrait;

use Austral\WebsiteBundle\Entity\Traits\EntitySocialNetworkTranslateMasterTrait;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;

use Austral\EntityFileBundle\Entity\Traits\EntityFileTrait;

use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterRobotTrait;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterSeoTrait;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterFileCropperTrait;
use Austral\EntityTranslateBundle\Annotation\Translate;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterComponentsTrait;

use Austral\SeoBundle\Annotation\ObjectUrl;

use Austral\WebsiteBundle\Entity\Traits\EntityTemplateTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;


/**
 * Austral Page Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 * @Translate(relationClass="Austral\WebsiteBundle\Entity\Interfaces\PageTranslateInterface")
 * @ObjectUrl(methodGenerateLastPath="stringToLastPath")
 */
abstract class Page extends Entity implements PageInterface,
  EntityInterface,
  TranslateMasterInterface,
  FileInterface,
  SeoInterface,
  RobotInterface,
  SocialNetworkInterface,
  ComponentsInterface,
  FilterByDomainInterface,
  TreePageInterface
{
  use EntityTimestampableTrait;
  use EntityFileTrait;
  use EntityTranslateMasterTrait;
  use EntityTranslateMasterRobotTrait;
  use EntityTranslateMasterSeoTrait;
  use EntitySocialNetworkTranslateMasterTrait;
  use EntityTranslateMasterComponentsTrait;
  use EntityTranslateMasterFileCropperTrait;
  Use EntityTemplateTrait;
  use FilterByDomainTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;

  /**
   * @var string|null
   * @ORM\Column(name="keyname", type="string", length=128, unique=true, nullable=false )
   */
  protected ?string $keyname = null;
  
  /**
   * @ORM\OneToMany(targetEntity="Austral\WebsiteBundle\Entity\Interfaces\PageTranslateInterface", mappedBy="master", cascade={"persist", "remove"})
   */
  protected Collection $translates;
  
  /**
   * @var PageInterface|null
   * @Gedmo\SortableGroup
   * @ORM\ManyToOne(targetEntity="Austral\WebsiteBundle\Entity\Interfaces\PageInterface", inversedBy="children")
   * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
   */
  protected ?PageInterface $parent = null;

  /**
   * @var Collection
   *
   * @ORM\OneToMany(targetEntity="Austral\WebsiteBundle\Entity\Interfaces\PageInterface", mappedBy="parent")
   * @ORM\OrderBy({"position" = "ASC"})
   */
  protected Collection $children;

  /**
   * @var int|null
   * @Gedmo\SortablePosition
   * @ORM\Column(name="position", type="integer", nullable=false )
   */
  protected ?int $position;
  
  /**
   * @var boolean
   * @ORM\Column(name="is_homepage", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $isHomepage = false;
  
  /**
   * @var string|null
   * @ORM\Column(name="austral_picto_class", type="string", length=50, nullable=true )
   */
  protected ?string $australPictoClass = null;

  /**
   * @var string|null
   * @ORM\Column(name="entity_extends", type="string", length=128, nullable=true )
   */
  protected ?string $entityExtends = null;

  /**
   * @var array
   */
  protected array $childrenEntities = array();

  /**
   * Page constructor.
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
    $this->translates = new ArrayCollection();
    $this->children = new ArrayCollection();
  }


  /**
   * @return string|null
   * @throws Exception
   */
  public function stringToLastPath(): ?string
  {
    if($this->getIsHomepage())
    {
      return null;
    }
    return $this->__toString();
  }

  /**
   * @return bool
   */
  public function getRefUrlLastEnabled(): bool
  {
    return !$this->getIsHomepage();
  }

  /**
   * @return int|string
   * @throws Exception
   */
  public function __toString()
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->__toString() : "";
  }

  /**
   * @return SeoInterface|PageInterface
   */
  public function getPageParent(): ?SeoInterface
  {
    return $this->getParent();
  }

  /**
   * @return PageInterface
   */
  public function getHomepage(): PageInterface
  {
    return $this->retreiveHomepagePage($this);
  }

  /**
   * @param PageInterface $page
   *
   * @return PageInterface
   */
  public function retreiveHomepagePage(PageInterface $page): PageInterface
  {
    if(!$page->getIsHomepage())
    {
      $parent = $page->getParent();
      if($parent)
      {
        return $this->retreiveHomepagePage($parent);
      }
    }
    return $page;
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getName(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getName() : null;
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getSummary(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getSummary() : null;
  }

  /**
   * Set parent
   *
   * @param PageInterface|null $parent
   *
   * @return $this
   */
  public function setParent(?PageInterface $parent): Page
  {
    $this->parent = $parent;
    return $this;
  }

  /**
   * Get parent
   *
   * @return PageInterface|null
   */
  public function getParent(): ?PageInterface
  {
    return $this->parent;
  }

  /**
   * @return TreePageInterface|null
   */
  public function getTreePageParent(): ?TreePageInterface
  {
    return $this->getPageParent();
  }

  /**
   * @param TreePageInterface $parent
   *
   * @return TreePageInterface|null
   */
  public function setTreePageParent(TreePageInterface $parent): ?TreePageInterface
  {
    $this->setParent($parent);
    return $this;
  }

  /**
   * Get parent
   *
   * @return int|string|null
   */
  public function getParentId()
  {
    return $this->parent ? $this->parent->getId() : null;
  }

  /**
   * Add child
   *
   * @param PageInterface $child
   *
   * @return $this
   */
  public function addChild(PageInterface $child): Page
  {
    if(!$this->children->contains($child))
    {
      $this->children->add($child);
      $child->setParent($this);
    }
    return $this;
  }

  /**
   * Remove child
   *
   * @param PageInterface $child
   *
   * @return $this
   */
  public function removeChild(PageInterface $child): Page
  {
    if($this->children->contains($child))
    {
      $child->setParent(null);
      $this->children->removeElement($child);
    }
    return $this;
  }

  /**
   * Get children
   *
   * @return Collection
   */
  public function getChildren(): Collection
  {
    return $this->children;
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
  public function setKeyname(?string $keyname): PageInterface
  {
    $this->keyname = $this->keynameGenerator($keyname);
    return $this;
  }

  /**
   * Get position
   * @return int|null
   */
  public function getPosition(): ?int
  {
    return $this->position;
  }

  /**
   * Set position
   *
   * @param int|null $position
   *
   * @return $this
   */
  public function setPosition(?int $position): Page
  {
    $this->position = $position;
    return $this;
  }

  /**
   * Get isHomepage
   * @return bool
   */
  public function getIsHomepage(): bool
  {
    return $this->isHomepage;
  }

  /**
   * @param bool $isHomepage
   *
   * @return Page
   */
  public function setIsHomepage(bool $isHomepage): Page
  {
    $this->isHomepage = $isHomepage;
    return $this;
  }

  /**
   * Get australPictoClass
   * @return string
   */
  public function getAustralPictoClass(): string
  {
    return $this->australPictoClass ? : ($this->isHomepage == true ? "austral-picto-home" : "austral-picto-file-paper");
  }

  /**
   * @param string|null $australPictoClass
   *
   * @return Page
   */
  public function setAustralPictoClass(?string $australPictoClass = null): Page
  {
    $this->australPictoClass = $australPictoClass;
    return $this;
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
  public function setImage($image): Page
  {
    $this->getTranslateCurrent()->setImage($image);
    return $this;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function getImageAlt(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getImageAlt() : null;
  }

  /**
   * @param $imageAlt
   *
   * @return $this
   * @throws Exception
   */
  public function setImageAlt($imageAlt): Page
  {
    $this->getTranslateCurrent()->setImageAlt($imageAlt);
    return $this;
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getImageFilenameRewrite(): ?string
  {
    return $this->getTranslateCurrent()->getImageReelname();
  }

  /**
   * @return string|null
   */
  public function getEntityExtends(): ?string
  {
    return $this->entityExtends;
  }

  /**
   * @param string|null $entityExtends
   *
   * @return Page
   */
  public function setEntityExtends(?string $entityExtends): Page
  {
    $this->entityExtends = $entityExtends;
    return $this;
  }

  /**
   * @return array
   */
  public function getChildrenEntities(): array
  {
    return $this->childrenEntities;
  }

  /**
   * @param array $childrenEntities
   *
   * @return Page
   */
  public function setChildrenEntities(array $childrenEntities): Page
  {
    $this->childrenEntities = $childrenEntities;
    return $this;
  }

  /**
   * @param EntityInterface $childrenEntities
   *
   * @return Page
   */
  public function addChildEntities(EntityInterface $childrenEntities): Page
  {
    $this->childrenEntities[$childrenEntities->getId()] = $childrenEntities;
    return $this;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function getPath(): ?string
  {
    return $this->getTranslateCurrent() ? $this->getTranslateCurrent()->getPath() : null;
  }

}