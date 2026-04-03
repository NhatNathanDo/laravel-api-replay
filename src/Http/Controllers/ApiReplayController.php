<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Storage\ApiReplay\Contracts\ApiLogRepositoryInterface;
use Storage\ApiReplay\Services\ReplayService;

class ApiReplayController extends Controller
{
    public function __construct(
        protected ApiLogRepositoryInterface $repository,
        protected ReplayService $replayService
    ) {}

    public function index(Request $request)
    {
        $logs = $this->repository->paginate(20);

        return view('api-replay::index', compact('logs'));
    }

    public function show(string $uuid)
    {
        $log = $this->repository->find($uuid);

        if (!$log) {
            abort(404);
        }

        return view('api-replay::show', compact('log'));
    }

    public function replay(Request $request, string $uuid)
    {
        try {
            $overrides = [
                'base_url' => $request->input('base_url'),
                'headers' => $request->input('headers', []),
                'dry_run' => $request->boolean('dry_run', false),
            ];

            $replay = $this->replayService->replay($uuid, $overrides);

            return response()->json([
                'success' => true,
                'replay' => $replay,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
