<?php

namespace App\Http\Controllers\Api;

use App\Domain\Common\VendorSystem;
use App\Http\Controllers\Controller;
use App\Domain\Movies\Contracts\MovieAggregator as MovieAggregatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(private readonly MovieAggregatorContract $movies) {}

    public function titles(Request $request): JsonResponse
    {
        /** @var object|null $jwt */
        $jwt = $request->attributes->get('jwt');
        $contextEnum = VendorSystem::tryFromInsensitive($jwt->context ?? null);
        $refresh = $request->boolean('refresh');
        ['titles' => $titles, 'atLeastOneOk' => $ok] = $this->movies->getAllTitles($contextEnum, $refresh);

        if (!$ok) {
            return response()->json(['status' => 'failure'], 503);
        }

        return response()->json($titles);
    }
}
