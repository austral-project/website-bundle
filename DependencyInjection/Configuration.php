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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Austral Website Configuration.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class Configuration implements ConfigurationInterface
{
  /**
   * {@inheritdoc}
   */
  public function getConfigTreeBuilder(): TreeBuilder
  {
    $treeBuilder = new TreeBuilder('austral_website');

    $rootNode = $treeBuilder->getRootNode();
    $node = $rootNode->children();
    $node->scalarNode("protocol")->defaultValue("https")->end()
      ->booleanNode("compression_gzip")->end()
        ->arrayNode("form")
          ->addDefaultsIfNotSet()
          ->children()
            ->booleanNode("socialNetworkEnabled")->end()
          ->end()
        ->end();

    $node = $this->buildTemplate($node
      ->arrayNode('templates')
      ->arrayPrototype()
    );
    $node->end();
    return $treeBuilder;
  }

  /**
   * @param ArrayNodeDefinition $node
   *
   * @return mixed
   */
  protected function buildTemplate(ArrayNodeDefinition $node)
  {
    $node = $node
      ->children()
      ->booleanNode('isChoice')->defaultTrue()->end()
      ->scalarNode('path')->isRequired()->cannotBeEmpty()->end();
    return $node->end()->end()->end();
  }

  /**
   * @return array
   */
  public function getConfigDefault(): array
  {
    return array(
      "form"        =>  array(
        "socialNetworkEnabled"   =>  true,
      ),
      "compression_gzip"  =>  true,
      "protocol"    =>  "https",
      "languages"   =>  array(
        "to_path"     => false,
        "domains"     =>  array(
          "en"          =>  "www.website.local"
        )
      ),
      "templates"   =>  array(
        "sitemap"         =>  array(
          "isChoice"        =>  false,
          "path"            =>  "@AustralWebsite/Front/sitemap.xml.twig"
        ),
        "guideline"       =>  array(
          "isChoice"        =>  false,
          "path"            =>  "@AustralWebsite/Front/guideline.html.twig"
        ),
        "robots"          =>  array(
          "isChoice"        =>  false,
          "path"            =>  "@AustralWebsite/Front/robots.txt.twig"
        ),
        "default"            =>  array(
          "isChoice"        =>  true,
          "path"            =>  "@AustralWebsite/Front/page.html.twig"
        )
      )
    );
  }
}
