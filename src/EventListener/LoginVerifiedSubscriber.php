<?php

namespace App\EventListener;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginVerifiedSubscriber implements EventSubscriberInterface
{

    private RequestStack $request;
    private $router;

    public function __construct( RequestStack $request, RouterInterface $router)
    {
        $this->request = $request;
        $this->router =$router;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess'];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */  //utile pour typer $user
        $user = $event->getAuthenticationToken()->getUser();
        if(!$user->isVerified()) {
            $road = null;
            $requestRecup = $this->request->getSession()->get("_security.main.target_path");
            if($requestRecup) {
                $verifRequest = Request::create(
                    $requestRecup,
                    'Get'
                );
                $road = $this->router->matchRequest($verifRequest);

                if($road['_route'] != "app_verify_email") {
                    //$this->request->getSession()->getFlashBag()->add('warning','Mail à vérifier');
                    throw new AuthenticationException("Mail à vérifier !");
                }
            }


        }
    }
}