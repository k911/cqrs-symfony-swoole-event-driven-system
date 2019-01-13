<?php

declare(strict_types=1);

namespace App\Domain\Review;

interface ReviewCommentRepositoryInterface
{
    public function findById(ReviewCommentId $commentId): ?ReviewComment;
}
