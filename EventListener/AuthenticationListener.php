<?php

namespace Cogipix\CogimixDeezerBundle\EventListener;

use Symfony\Component\Security\Core\Role\Role;

use Doctrine\Common\Persistence\ObjectManager;

use Cogipix\CogimixBundle\Events\AuthenticationEvent;

use Cogipix\CogimixBundle\Events\CogimixEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AuthenticationListener implements EventSubscriberInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om=$om;
    }

    public static function getSubscribedEvents()
    {
        return array(
                CogimixEvents::AUTHENTICATTION_SUCCESS => 'onAuthenticationSuccess');
    }

    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {

            $user=$event->getToken()->getUser();

            $deezerToken=$this->om->getRepository('CogimixDeezerBundle:DeezerToken')->findOneByUser($user);
            if($deezerToken){
                $user->addRole('ROLE_DEEZER');
                $event->getToken()->setAuthenticated(false);
            }
            $this->om->flush();

    }

}
