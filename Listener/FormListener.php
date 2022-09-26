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