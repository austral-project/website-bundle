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
use Austral\EntityFileBundle\Annotation as AustralFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Translate Entity Social Network Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntitySocialNetworkTrait
{
  
  /**
   * @var string|null
   * @ORM\Column(name="social_title", type="string", length=255, nullable=true)
   */
  protected ?string $socialTitle = null;
  
  /**
   * @var string|null
   * @ORM\Column(name="social_description", type="text", nullable=true)
   */
  protected ?string $socialDescription = null;

  /**
   * @var string|null
   * @ORM\Column(name="social_image", type="string", length=255, nullable=true)
   * @AustralFile\ImageSize()
   * @AustralFile\Croppers({
   *  "social"
   * })
   * @AustralFile\UploadParameters(configName="social_image")
   */
  protected ?string $socialImage = null;
  

  /**
   * Set socialTitle
   *
   * @param string|null $socialTitle
   *
   * @return EntitySocialNetworkTrait|EntityInterface
   */
  public function setSocialTitle(?string $socialTitle): EntityInterface
  {
    $this->socialTitle = $socialTitle;
    return $this;
  }

  /**
   * Get socialTitle
   *
   * @return string|null
   */
  public function getSocialTitle(): ?string
  {
    return $this->socialTitle;
  }

  /**
   * Set socialDescription
   *
   * @param string|null $socialDescription
   *
   * @return EntitySocialNetworkTrait|EntityInterface
   */
  public function setSocialDescription(?string $socialDescription): EntityInterface
  {
    $this->socialDescription = $socialDescription;
    return $this;
  }

  /**
   * Get socialDescription
   *
   * @return string|null
   */
  public function getSocialDescription(): ?string
  {
    return $this->socialDescription;
  }

  /**
   * Set socialImage
   *
   * @param string|null $socialImage
   *
   * @return EntitySocialNetworkTrait|EntityInterface
   */
  public function setSocialImage(?string $socialImage): EntityInterface
  {
    $this->socialImage = $socialImage;
    return $this;
  }

  /**
   * Get socialImage
   *
   * @return string|null
   */
  public function getSocialImage(): ?string
  {
    return $this->socialImage;
  }

}
