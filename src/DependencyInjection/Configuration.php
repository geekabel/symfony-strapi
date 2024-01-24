<?php

declare(strict_types=1);

namespace Geekabel\SymfonyStrapi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfony_strapi');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('api_url')->isRequired()->info('The URL of the Strapi API.')->end()
                ->scalarNode('api_key')->isRequired()->end()
                ->arrayNode('authentication')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()
                        ->info('Whether authentication is globally enabled or not.')->end()
                        ->scalarNode('username')->defaultNull()->info('The username for authentication.')->end()
                        ->scalarNode('password')->defaultNull()->info('The password for authentication.')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}