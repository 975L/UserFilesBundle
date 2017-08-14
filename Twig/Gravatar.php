<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Twig;

class Gravatar extends \Twig_Extension
{
    private $container;

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('gravatar_display', array($this, 'gravatarDisplay')),
        );
    }

    public function gravatarDisplay()
    {
        return $this->container->getParameter('c975_l_user_files.gravatar');
    }
}