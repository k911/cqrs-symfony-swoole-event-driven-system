<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * @DiscriminatorMap(typeProperty="eventType", mapping={
 *    "review_created"=ReviewCreated::class,
 *    "review_needs_check"=ReviewNeedsCheck::class,
 *    "review_check_finished"=ReviewCheckFinished::class,
 * })
 */
interface EventInterface
{
    public function getReviewId(): string;

    public function getEventType(): string;
}
