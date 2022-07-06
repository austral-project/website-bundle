<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\WebsiteBundle\Model;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
use Ramsey\Uuid\Uuid;

/**
 * Austral SubDomain Model.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class SubDomain extends Entity implements EntityInterface
{

  /**
   * @var string
   */
  protected $id;

  /**
   * @var string|null
   */
  protected ?string $subDomain = null;

  /**
   * Theme constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
  }

  public function __toString()
  {
    return $this->subDomain;
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * @param string $id
   *
   * @return SubDomain
   */
  public function setId(string $id): SubDomain
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getSubDomain(): ?string
  {
    return $this->subDomain;
  }

  /**
   * @param string|null $subDomain
   *
   * @return SubDomain
   */
  public function setSubDomain(?string $subDomain): SubDomain
  {
    $this->subDomain = $subDomain;
    return $this;
  }

}