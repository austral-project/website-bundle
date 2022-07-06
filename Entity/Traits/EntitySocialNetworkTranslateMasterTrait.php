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
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;
use Exception;

/**
 * Austral Translate Entity Social Network To Master Translate Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntitySocialNetworkTranslateMasterTrait
{
  use EntityTranslateMasterTrait;

  /**
   * @return EntitySocialNetworkTrait|EntityTranslateChildInterface|EntityInterface
   * @throws Exception
   */
  private function getTranslateCurrentSocialNetwork(): EntityTranslateChildInterface
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
   * @return EntitySocialNetworkTranslateMasterTrait|EntityTranslateMasterInterface|EntityInterface
   * @throws Exception
   */
  public function setSocialTitle(?string $socialTitle): EntityInterface
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
   * @return EntitySocialNetworkTranslateMasterTrait|EntityTranslateMasterInterface|EntityInterface
   * @throws Exception
   */
  public function setSocialDescription(?string $socialDescription): EntityInterface
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
   * @return EntitySocialNetworkTranslateMasterTrait|EntityTranslateMasterInterface|EntityInterface
   * @throws Exception
   */
  public function setSocialImage(?string $socialImage): EntityInterface
  {
    if($this->getTranslateCurrentSocialNetwork())
    {
      $this->getTranslateCurrentSocialNetwork()->setSocialImage($socialImage);
    }
    return $this;
  }

}
