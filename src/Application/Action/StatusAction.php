<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\ConnectionChecker\ConnectionCheckerInterface;
use App\Application\ConnectionChecker\EntityManagerConnectionChecker;
use App\Application\ConnectionChecker\RedisConnectionChecker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class StatusAction
{
    private $checkers = [];

    public function __construct(
        EntityManagerConnectionChecker $entityManagerConnectionChecker,
        RedisConnectionChecker $redisConnectionChecker
    ) {
        $this->checkers[] = $entityManagerConnectionChecker;
        $this->checkers[] = $redisConnectionChecker;
    }

    /**
     * @Route(path="/status",name="app_status")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->query->get('local')) {
            return new JsonResponse([
                'message' => 'OK',
                'date' => $this->getServerDate(),
            ], 200);
        }

        [
            $ok,
            $statuses,
        ] = $this->generateComplexCheckResult();
        $response = [
            'status' => $ok ? 'OK' : 'DEGRADED',
            'date' => $this->getServerDate(),
            'messages' => $statuses,
        ];

        return new JsonResponse($response, $ok ? 200 : 503);
    }

    private function generateComplexCheckResult(): array
    {
        $ok = true;
        $messages = [];
        /** @var ConnectionCheckerInterface $checker */
        foreach ($this->checkers as $checker) {
            $message = [
                'description' => $checker->description(),
            ];

            if ($checker->check()) {
                $message['status'] = 'OK';
            } else {
                $message['status'] = 'FAIL';
                $ok = false;
            }

            $messages[] = $message;
        }

        return [$ok, $messages];
    }

    private function getServerDate(): string
    {
        return (new \DateTimeImmutable('now'))->format(DATE_ATOM);
    }
}
