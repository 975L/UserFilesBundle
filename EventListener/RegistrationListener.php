<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityManager;

class RegistrationListener implements EventSubscriberInterface
{
    protected $em;
    protected $router;

    public function __construct(EntityManager $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
            FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm',
        );
    }

    //Adds different data to registration
    public function onRegistrationSuccess(FormEvent $event)
    {
        $form = $event->getForm()->getData();

        $session = new Session();
        $sessionChallengeResult = (string) $session->get('challengeResult');

        //Sets the date of creation
        $form->setCreation(new \DateTime());
    }

    //Adds different data to registration completed
    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        //Adds the picture
        $user->setAvatar('https://www.gravatar.com/avatar/' . hash('md5', strtolower(trim($user->getEmail()))) . '?s=128&d=mm&r=g');
        $this->em->persist($user);

        //Flush DB
        $this->em->flush();
    }

    //Redirects confirmed user to the dashboard
    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $event->setResponse(new RedirectResponse($this->router->generate('userfiles_dashboard')));
    }
}