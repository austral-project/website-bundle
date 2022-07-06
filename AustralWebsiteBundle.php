<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle;
use Austral\WebsiteBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Austral Website Bundle.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class AustralWebsiteBundle extends Bundle
{

  /**
   * @param ContainerBuilder $container
   */
  public function build(ContainerBuilder $container)
  {
    parent::build($container);
    $container->addCompilerPass(new DoctrineResolveTargetEntityPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
  }

}
