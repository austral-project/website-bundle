<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Listener;

use Austral\FormBundle\Event\FormEvent;
use Austral\FormBundle\Field as Field;

use Austral\FormBundle\Mapper\Fieldset;
use Austral\ToolsBundle\AustralTools;

use Austral\WebsiteBundle\Configuration\WebsiteConfiguration;
use Austral\WebsiteBundle\Entity\Traits\EntitySocialNetworkTrait;
use Austral\WebsiteBundle\Entity\Traits\EntitySocialNetworkTranslateMasterTrait;

use Austral\WebsiteBundle\Entity\Traits\EntityTemplateTrait;
use Exception;

/**
 * Austral FormListener Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class FormListener
{

  /**
   * @var WebsiteConfiguration
   */
  protected WebsiteConfiguration $websiteConfiguration;

  /**
   * FormListener constructor.
   *
   * @param WebsiteConfiguration $websiteConfiguration
   */
  public function __construct(WebsiteConfiguration $websiteConfiguration)
  {
    $this->websiteConfiguration = $websiteConfiguration;
  }

  /**
   * @param FormEvent $formEvent
   *
   * @throws Exception
   */
  public function formAddAutoFields(FormEvent $formEvent)
  {
    if($this->websiteConfiguration->get('form.socialNetworkEnabled') &&
      ( AustralTools::usedClass($formEvent->getFormMapper()->getObject(), EntitySocialNetworkTrait::class) ||
        AustralTools::usedClass($formEvent->getFormMapper()->getObject(), EntitySocialNetworkTranslateMasterTrait::class)
      )
    )
    {
      $formEvent->getFormMapper()->addFieldset("fieldset.socialNetwork")
        ->add(Field\TextField::create("socialTitle"))
        ->addGroup("socialNetwork.content")
          ->add(Field\UploadField::create("socialImage"))
          ->add(Field\TextareaField::create("socialDescription"))
        ->end()
      ->end();
    }

    if(AustralTools::usedClass($formEvent->getFormMapper()->getObject(), EntityTemplateTrait::class))
    {
      $templates = array();
      foreach($this->websiteConfiguration->getConfig("templates") as $key => $templateParameters)
      {
        if($templateParameters["isChoice"])
        {
          $templates["choices.templates.{$key}"] = $key;
        }
      }
      $formEvent->getFormMapper()->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->add(Field\SelectField::create("template",
            $templates,
            array("required"=>true)
          )
        )
      ->end();
    }

  }

}