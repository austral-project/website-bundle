<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Form\Type;

use Austral\FormBundle\Form\Type\FormType;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigValueByDomainInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Austral ValueByDomain Form Type.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ValueByDomainFormType extends FormType
{

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => ConfigValueByDomainInterface::class,
    ]);
  }

}