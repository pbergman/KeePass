<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */
namespace KeePass;

use \Symfony\Component\Config\Definition\ConfigurationInterface;
use \Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration implementation validation the global config file
 *
 * Class    Configuration
 * @package KeePass
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {

        $treeBuilder   = new TreeBuilder();

        $keePassConfig = $treeBuilder->root('KeePass');

        $keePassConfig->children()
                            ->append($this->addKeePassNode())
                            ->scalarNode('cache_folder')
                                ->defaultValue('@data_folder@/cache')
                            ->end()
                            ->scalarNode('data_folder')
                                ->defaultValue('%HOME%/.KeePassCli')
                            ->end()
                            ->scalarNode('app_folder')
                                ->defaultValue(realpath(sprintf('%s/../',dirname(__FILE__))))
                            ->end()
                        ->end();

        return $treeBuilder;
    }

    private function addKeePassNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('keepass');

        $node->addDefaultsIfNotSet()
             ->children()
                ->scalarNode('mono')
                    ->defaultValue(trim(`which mono`))
                ->end()
                ->scalarNode('database')
                    ->isRequired()
                ->end()
                ->scalarNode('kps')
                    ->defaultValue('@app_folder@/bin/KeePass/KPScript.exe')
                ->end()
             ->end()
          ->end();

        return $node;

    }

}
