<?php
declare(strict_types=1);
namespace Geekabel\SymfonyStrapi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonyStrapiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('symfony_strapi.api_url', $config['api_url']);
        $container->setParameter('symfony_strapi.api_key', $config['api_key']);
        $container->setParameter('symfony_strapi.authentication.enabled', $config['authentication']['enabled']);
        $container->setParameter('symfony_strapi.authentication.username', $config['authentication']['username']);
        $container->setParameter('symfony_strapi.authentication.password', $config['authentication']['password']);
    }
}