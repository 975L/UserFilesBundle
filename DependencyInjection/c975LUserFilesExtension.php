<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class c975LUserFilesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $container->setParameter('c975_l_user_files.site', $processedConfig['site']);
        $container->setParameter('c975_l_user_files.logoutRoute', $processedConfig['logoutRoute']);
        $container->setParameter('c975_l_user_files.registration', $processedConfig['registration']);
        $container->setParameter('c975_l_user_files.gravatar', $processedConfig['gravatar']);
        $container->setParameter('c975_l_user_files.hwiOauth', $processedConfig['hwiOauth']);
        $container->setParameter('c975_l_user_files.databaseEmail', $processedConfig['databaseEmail']);
        $container->setParameter('c975_l_user_files.archiveUser', $processedConfig['archiveUser']);
    }
}
