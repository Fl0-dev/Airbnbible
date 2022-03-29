<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class LoginVerifiedListener
{

    public function onAuthentificationSuccess(AuthenticationSuccessEvent $event)
    {
        dd('ok');
    }
}