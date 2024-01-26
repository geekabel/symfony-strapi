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
                ->scalarNode('api_url')
                    ->info('The URL of the Strapi API.')
                    ->defaultValue('')
                ->end()
                ->scalarNode('api_key')
                    ->defaultValue('')
                ->end()
                ->arrayNode('authentication')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Whether authentication is globally enabled or not.')
                        ->end()
                        ->scalarNode('username')
                            ->info('The username for authentication.')
                            ->defaultValue('')
                        ->end()
                        ->scalarNode('password')
                            ->info('The password for authentication.')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}