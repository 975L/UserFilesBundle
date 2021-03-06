<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('c975_l_user_files');

        $rootNode
            ->children()
                ->scalarNode('site')
                ->end()
                ->scalarNode('logoutRoute')
                ->end()
                ->scalarNode('registration')
                    ->defaultTrue()
                ->end()
                ->scalarNode('gravatar')
                    ->defaultNull()
                ->end()
                ->arrayNode('hwiOauth')
                    ->prototype('scalar')->end()
                    ->defaultValue(array())
                ->end()
                ->booleanNode('databaseEmail')
                    ->defaultFalse()
                ->end()
                ->booleanNode('archiveUser')
                    ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
