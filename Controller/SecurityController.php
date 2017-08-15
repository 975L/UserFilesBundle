<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController
{
    protected function renderLogin(array $data)
    {
        $data['data'] = array(
            'site' => $this->getParameter('c975_l_user_files.site'),
            'registration' => $this->getParameter('c975_l_user_files.registration'),
            'hwiOauth' => $this->getParameter('c975_l_user_files.hwiOauth'),
        );

        return parent::renderLogin($data);
    }
}