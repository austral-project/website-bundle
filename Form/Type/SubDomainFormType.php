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

use Austral\WebsiteBundle\Model\SubDomain;
use Austral\FormBundle\Form\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Austral SubDomain Form Type.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class SubDomainFormType extends FormType
{

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => SubDomain::class,
    ]);
  }

}