<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Contract\QueryBusInterface;
use App\Application\Query\QueryStatus;
use App\Domain\SystemStatus\SystemStatus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class GetStatusAction
{
    private $queryBus;

    public function __construct(QueryBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
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
        /** @var SystemStatus $systemStatus */
        $systemStatus = $this->queryBus->handle(new QueryStatus(
            $request->query->get('local') ? true : false
        ));

        return new JsonResponse($systemStatus, $systemStatus->isOk() ? 200 : 503);
    }
}
