<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * @DiscriminatorMap(typeProperty="eventType", mapping={
 *    "review_created"=ReviewCreated::class,
 *    "review_needs_check"=ReviewNeedsCheck::class,
 *    "review_check_finished"=ReviewCheckFinished::class,
 *    "review_automated_checks_status_changed"=ReviewAutomatedChecksStatusChanged::class,
 * })
 */
interface EventInterface
{
    public function getReviewId(): string;

    public function getEventType(): string;
}
