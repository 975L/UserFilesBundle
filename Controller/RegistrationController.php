<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        //Redirects if registration is disabled
        if ($this->getParameter('c975_l_user_files.registration') !== true) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return parent::registerAction($request);
    }

    //Redirects to login if the confirmation token is expired/invalid (in case user click the confirm link more than once)
    //https://github.com/FriendsOfSymfony/FOSUserBundle/issues/2106
    public function confirmAction(Request $request, $token)
    {
        try {
            return parent::confirmAction($request, $token);
        } catch (NotFoundHttpException $e) {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }
}
