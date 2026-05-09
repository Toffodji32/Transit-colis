<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginRedirectListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        // Rediriger les admins vers le dashboard admin
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
            $event->setResponse($response);
            return;
        }

        // Rediriger les agents entrepôt vers le dashboard entrepôt
        if (in_array('ROLE_ENTREPOT', $user->getRoles(), true)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_entrepot_dashboard'));
            $event->setResponse($response);
            return;
        }

        // Sinon, le comportement par défaut s'applique (redirection vers app_dashboard)
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onLoginSuccess', 10],
        ];
    }
}

