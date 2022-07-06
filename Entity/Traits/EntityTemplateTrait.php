<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\WebsiteBundle\Entity\Traits;

use Austral\EntityBundle\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Translate Entity Social Network Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTemplateTrait
{

  /**
   * @var string|null
   * @ORM\Column(name="template", type="string", length=128, nullable=true )
   */
  protected ?string $template = null;

  /**
   * @return string|null
   */
  public function getTemplate(): ?string
  {
    return $this->template ? : "default";
  }

  /**
   * @param string|null $template
   *
   * @return EntityTemplateTrait|EntityInterface
   */
  public function setTemplate(?string $template): EntityInterface
  {
    $this->template = $template;
    return $this;
  }

}
