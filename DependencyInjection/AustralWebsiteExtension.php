<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


/**
 * Austral Website Extension.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class AustralWebsiteExtension extends Extension
{
  /**
   * {@inheritdoc}
   * @throws Exception
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $defaultConfig = $configuration->getConfigDefault();
    $config = array_replace_recursive($defaultConfig, $config);
    $container->setParameter('austral_website_config', $config);

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    $loader->load('parameters.yaml');
    $loader->load('services.yaml');
    $this->loadConfigToAustralBundle($container, $loader);
  }

  /**
   * @param ContainerBuilder $container
   * @param YamlFileLoader $loader
   *
   * @throws Exception
   */
  protected function loadConfigToAustralBundle(ContainerBuilder $container, YamlFileLoader $loader)
  {
    $bundlesConfigPath = $container->getParameter("kernel.project_dir")."/config/bundles.php";
    if(file_exists($bundlesConfigPath))
    {
      $contents = require $bundlesConfigPath;
      if(array_key_exists("Austral\AdminBundle\AustralAdminBundle", $contents))
      {
        $loader->load('austral_admin.yaml');
      }
      if(array_key_exists("Austral\EntityFileBundle\AustralEntityFileBundle", $contents))
      {
        $loader->load('austral_entity_file.yaml');
      }
      if(array_key_exists("Austral\FormBundle\AustralFormBundle", $contents))
      {
        $loader->load('austral_form.yaml');
      }
    }
  }

  /**
   * @return string
   */
  public function getNamespace(): string
  {
    return 'https://austral.app/schema/dic/austral_website';
  }

}
