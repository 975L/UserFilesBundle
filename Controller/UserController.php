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

            //Defines toolbar
            $tools  = $this->renderView('@c975LUserFiles/tools.html.twig', array(
                'type' => 'dashboard',
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'userfiles',
            ))->getContent();

            //Renders the dashboard
            return $this->render('@c975LUserFiles/pages/dashboard.html.twig', array(
                'user' => $user,
                'data' => array('gravatar' => $this->getParameter('c975_l_user_files.gravatar')),
                'toolbar' => $toolbar,
                'dashboards' => $this->getParameter('c975_l_toolbar.dashboards'),
                ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
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

        //Invalidates the session
        $session = new Session();
        $session->invalidate();

        //Calls user's defined functions if overriden
        $this->signoutUserFunction();

        return $this->redirectToRoute($this->getParameter('c975_l_user_files.logoutRoute'));
    }

    /*
     * Override this function in your Controller to add you own actions to signoutAction
     */
    public function signoutUserFunction()
    {
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

                //Calls user's defined method if overriden
                $this->deleteAccountUserDefinedMethod();

                //Creates email
                $subject = $translator->trans('label.delete_account', array(), 'userFiles');
                $body = $this->renderView('@c975LUserFiles/emails/deleteAccount.html.twig');
                $emailData = array(
                    'subject' => $subject,
                    'sentFrom' => $this->getParameter('c975_l_email.sentFrom'),
                    'sentTo' => $user->getEmail(),
                    'sentCc' => null,
                    'replyTo' => $this->getParameter('c975_l_email.sentFrom'),
                    'body' => $body,
                    'ip' => $request->getClientIp(),
                    );
                $emailService = $this->get(\c975L\EmailBundle\Service\EmailService::class);
                $emailService->send($emailData, $this->getParameter('c975_l_user_files.databaseEmail'));

                //Archives user
                if ($this->getParameter('c975_l_user_files.archiveUser') === true) {
                    $this->archiveUserDefinedMethod();
                }

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
                'data' => array('gravatar' => $this->getParameter('c975_l_user_files.gravatar')),
            ));
        }

        //Sign in
        return $this->redirectToRoute('fos_user_security_login');
    }

    /*
     * Override this method in your Controller to add you own actions to deleteAccountAction
     */
    public function deleteAccountUserDefinedMethod()
    {
    }

    /*
     * Override this method in your Controller to add you own actions to archiveUser
     */
    public function archiveUserDefinedMethod()
    {
        //Gets the connection
        $conn = $this->getDoctrine()->getManager()->getConnection();

        //Calls the stored procedure
        $query = 'CALL sp_UserFiles_UserArchive("' . $this->getUser()->getId() . '");';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $stmt->closeCursor();
    }
}
