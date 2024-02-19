<?php

namespace App\EventSubscriber\Book;

use App\Event\Book\BookCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class BookCreatedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BookCreatedEvent::class => ['onBookCreated']
        ];
    }

    public function onBookCreated(BookCreatedEvent $event)
    {
        $this->logger->info('Book created');
    }
}
