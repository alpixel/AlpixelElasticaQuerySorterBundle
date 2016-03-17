<?php

namespace Alpixel\Bundle\ElasticaQuerySorterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('alpixel_elastica_query_sorter');

        $rootNode
            ->children()
                ->arrayNode('views')
                    ->children()
                        ->scalarNode('clear_sort')
                            ->defaultvalue('AlpixelElasticaQuerySorterBundle:blocks:clear_sort.html.twig')
                        ->end()
                        ->scalarNode('sort_link')
                            ->defaultvalue('AlpixelElasticaQuerySorterBundle:blocks:sort_link.html.twig')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('item_per_page')
                    ->defaultvalue(25)
                ->end()
            ->end()
        ;


        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
