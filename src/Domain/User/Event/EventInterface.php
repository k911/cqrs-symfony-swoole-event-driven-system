<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * @DiscriminatorMap(typeProperty="eventType", mapping={
 *    "user_created"=UserCreated::class,
 *    "user_email_changed"=UserEmailChanged::class,
 *    "user_password_changed"=UserPasswordChanged::class
 * })
 */
interface EventInterface
{
    public function getUserId(): string;

    public function getEventType(): string;
}
