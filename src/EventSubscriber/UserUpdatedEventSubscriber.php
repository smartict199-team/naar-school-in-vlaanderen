<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\UserUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class UserUpdatedEventSubscriber implements EventSubscriberInterface
{
    private CacheInterface $userCache;

    public function __construct(CacheInterface $userCache)
    {
        $this->userCache = $userCache;
    }

    /**
     * @return array<string, list<string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserUpdatedEvent::class => [
                'clearUserCache',
            ],
        ];
    }

    public function clearUserCache(UserUpdatedEvent $event): void
    {
        $this->userCache->delete(sprintf('user_%s', $event->getUserId()));
    }
}
