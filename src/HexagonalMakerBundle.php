<?php

namespace AdrienLbt\HexagonalMakerBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Class HexagonalMakerBundle
 * @package AdrienLbt\HexagonalMakerBundle
 */
class HexagonalMakerBundle extends AbstractBundle
{
    /**
     * Créer "l'arbre" de configuration du bundle
     *
     * @param DefinitionConfigurator $definition
     * @return void
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        // @phpstan-ignore-next-line
        $definition->rootNode()
            ->children()
                ->scalarNode('application_path')->defaultValue('Application')->end()
                ->scalarNode('domain_path')->defaultValue('Domain')->end()
                ->scalarNode('infrastructure_path')->defaultValue('Infrastructure')->end()
            ->end()
        ;
    }

    /**
     * Charge les services du bundle
     *
     * @param array $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     * @return void
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $container->parameters()->set('hexagonal_maker.application_path', $config['application_path']);
        $container->parameters()->set('hexagonal_maker.domain_path', $config['domain_path']);
        $container->parameters()->set('hexagonal_maker.infrastructure_path', $config['infrastructure_path']);
        $container->import('../config/services.yaml');
    }
}
