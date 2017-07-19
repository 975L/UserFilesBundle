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
//This method has to be removed if https://github.com/FriendsOfSymfony/FOSUserBundle/pull/2587 is merged
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        $data['data'] = $this->setUserData();

        return $this->render('@FOSUser/Security/login.html.twig', $data);
    }

    /*
     * Method to override to send data to the template
     */
    public function setUserData()
    {
        return array(
            'site' => $this->getParameter('c975_l_user_files.site'),
            'registration' => $this->getParameter('c975_l_user_files.registration'),
            'hwiOauth' => $this->getParameter('c975_l_user_files.hwiOauth'),
        );
    }
}
