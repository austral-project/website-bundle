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
use Austral\EntityBundle\Entity\Interfaces\SocialNetworkInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;
use Exception;

/**
 * Austral Translate Entity Social Network To Master Translate Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @deprecated
 */
trait EntitySocialNetworkTranslateMasterTrait
{
  use EntityTranslateMasterTrait;

  /**
   * @return SocialNetworkInterface|TranslateChildInterface|EntityInterface
   * @throws Exception
   */
  private function getTranslateCurrentSocialNetwork(): SocialNetworkInterface
  {
    return $this->getTranslateCurrent();
  }

  /**
   * Get socialTitle
   * @return string|null
   * @throws Exception
   */
  public function getSocialTitle(): ?string
  {
    return $this->getTranslateCurrentSocialNetwork() ? $this->getTranslateCurrentSocialNetwork()->getSocialTitle() : null;
  }

  /**
   * Set socialTitle
   *
   * @param string|null $socialTitle
   *
   * @return SocialNetworkInterface|TranslateMasterInterface|EntityInterface
   * @throws Exception
   */
  public function setSocialTitle(?string $socialTitle): SocialNetworkInterface
  {
    if($this->getTranslateCurrentSocialNetwork())
    {
      $this->getTranslateCurrentSocialNetwork()->setSocialTitle($socialTitle);
    }
    return $this;
  }

  /**
   * Get socialDescription
   * @return string
   * @throws Exception
   */
  public function getSocialDescription(): ?string
  {
    return $this->getTranslateCurrentSocialNetwork() ? $this->getTranslateCurrentSocialNetwork()->getSocialDescription() : null;
  }

  /**
   * Set socialDescription
   *
   * @param string|null $socialDescription
   *
   * @return SocialNetworkInterface|TranslateMasterInterface|EntityInterface
   * @throws Exception
   */
  public function setSocialDescription(?string $socialDescription): SocialNetworkInterface
  {
    if($this->getTranslateCurrentSocialNetwork())
    {
      $this->getTranslateCurrentSocialNetwork()->setSocialDescription($socialDescription);
    }
    return $this;
  }

  /**
   * Get socialImage
   * @return string
   * @throws Exception
   */
  public function getSocialImage(): ?string
  {
    return $this->getTranslateCurrentSocialNetwork() ? $this->getTranslateCurrentSocialNetwork()->getSocialImage() : null;
  }

  /**
   * Set socialImage
   *
   * @param string|null $socialImage
   *
   * @return SocialNetworkInterface|TranslateMasterInterface|EntityInterface
   * @throws Exception
   */
  public function setSocialImage(?string $socialImage): SocialNetworkInterface
  {
    if($this->getTranslateCurrentSocialNetwork())
    {
      $this->getTranslateCurrentSocialNetwork()->setSocialImage($socialImage);
    }
    return $this;
  }

}
