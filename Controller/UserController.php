<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use c975L\EmailBundle\Entity\Email;
use c975L\UserFilesBundle\Form\DeleteType;

class UserController extends Controller
{
//DASHBOARD
    /**
     * @Route("/dashboard",
     *      name="userfiles_dashboard")
     * @Method({"GET", "HEAD"})
     */
    public function dashboardAction()
    {
        //Gets user
        $user = $this->getUser();

        if ($user !== null) {
            //Removes challenge from session to avoid further validation problems
            $session = new Session();
            $session->remove('challenge');
            $session->remove('challengeResult');

            //Renders the dashboard
            return $this->render('@c975LUserFiles/pages/dashboard.html.twig', array(
                'user' => $user,
                ));
        } else {
            throw $this->createAccessDeniedException();
        }
    }

//SIGN OUT
    /**
     * @Route("/signout",
     *      name="userfiles_signout")
     * @Method({"GET", "HEAD"})
     */
    public function signoutAction()
    {
        //Gets the manager
        $em = $this->getDoctrine()->getManager();

        //Gets the user
        $user = $this->getUser();

        //Writes logout time
        if ($user !== null && $user != 'anon.') {
            $user->setLastLogout(new \DateTime());
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('fos_user_security_logout');
    }

//DELETE USER
    /**
     * @Route("/delete",
     *      name="userfiles_delete_account")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function deleteAccountAction(Request $request)
    {
        //Gets the user
        $user = $this->getUser();

        if ($user !== null && $user != 'anon.') {
            //Creates the form
            $form = $this->createForm(DeleteType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Gets the manager
                $em = $this->getDoctrine()->getManager();

                //Gets the translator
                $translator = $this->get('translator');

                //Creates email
                $subject = $translator->trans('label.delete_account', array(), 'userFiles');
                $body = $this->renderView('@c975LUserFiles/emails/deleteAccount.html.twig');
                $emailData = array(
                    'mailer' => $this->get('mailer'),
                    'subject' => $subject,
                    'sentFrom' => $this->getParameter('c975_l_email.sentFrom'),
                    'sentTo' => $user->getEmail(),
                    'sentCc' => null,
                    'replyTo' => $this->getParameter('c975_l_email.sentFrom'),
                    'body' => $body,
                    'ip' => $request->getClientIp(),
                    );
                $email = new Email();
                $email->setDataFromArray($emailData);

                //Persists Email in DB
                $em->persist($email);

                //Sends email
                $email->send();

                //Removes user
                $em->remove($user);

                //Flush DB
                $em->flush();

                //Creates flash
                $flash = $translator->trans('text.account_deleted', array(), 'userFiles');
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', $flash);

                //Sign out
                return $this->redirectToRoute('userfiles_signout');
            }

            return $this->render('@c975LUserFiles/pages/deleteAccount.html.twig', array(
                'form' => $form->createView(),
                ));
        }

        //Sign in
        return $this->redirectToRoute('fos_user_security_login');
    }
}