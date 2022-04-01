<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginVerifierSubscriber implements EventSubscriberInterface
{
    private RequestStack $request;
    private $router;
    public function __construct(RequestStack $request, RouterInterface $router) {
        $this->request = $request;
        $this->router = $router;
    }
    
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            AuthenticationSuccessEvent::class => ['onAuthenticationSuccess'],
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event) {
        // dd($this->request);
        $user = $event->getAuthenticationToken()->getUser();

        if(!$user->isVerified()) {
            $targetRoute = null;
            $path = $this->request->getSession()->get('_security.main.target_path');
            if($path) {
                $verifRequest = Request::create(
                    $path,
                    'GET'
                );

                $targetRoute = $this->router->matchRequest($verifRequest)["_route"];

            }
            
            if($targetRoute != "app_verify_email") {
                throw new AuthenticationException("mail non vérifié");
            }
        }
    }
}
