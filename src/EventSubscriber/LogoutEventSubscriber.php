<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Cache\CacheInterface;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private string $auth0Domain;
    private RouterInterface $router;
    private CacheInterface $userCache;

    public function __construct(string $auth0Domain, RouterInterface $router, CacheInterface $userCache)
    {
        $this->auth0Domain = $auth0Domain;
        $this->router = $router;
        $this->userCache = $userCache;
    }

    /**
     * @return array<class-string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $logoutEvent): void
    {
        $token = $logoutEvent->getToken();
        if (null !== $token && ($user = $token->getUser()) instanceof UserInterface) {
            $this->userCache->delete('user_' . $user->getUsername());
        }

        $logoutEvent->setResponse(
            new RedirectResponse(sprintf('%s/v2/logout?returnTo=%s', $this->auth0Domain, urlencode($this->router->generate('index', [], RouterInterface::ABSOLUTE_URL))))
        );
    }
}
