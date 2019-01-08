<?php

declare(strict_types=1);

namespace App\Domain\Review;

interface ReviewRepositoryInterface
{
    public function findById(ReviewId $reviewId): ?Review;
}
