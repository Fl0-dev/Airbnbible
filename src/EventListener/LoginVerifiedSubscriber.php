<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


class LoginVerifiedSubscriber implements EventSubscriberInterface{
    private $request;
    private $router;

    public function __construct(RequestStack $request, RouterInterface $router){
        $this->request = $request;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess'
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event){
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user->isVerified()){
            $targetPath = $this->request->getSession()->get('_security.main.target_path');

            if ($targetPath){
                $artificialRequest = Request::create(
                    $targetPath,
                    'GET'
                );
                $targetRoute = $this->router->matchRequest($artificialRequest)["_route"];

                if ($targetRoute != "app_verify_email"){
                    throw new AuthenticationException("Votre mail n'est pas vérifié");
                }
            }
        }
    }
}