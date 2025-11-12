<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\LoginService;

final class SecurityController extends Controller {
    public function __construct(private readonly LoginService $loginService) {}

    /**
     * @throws BindingResolutionException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->loginService->authenticateAndIssueToken($data['login'], $data['password']);

        return $token
            ? response()->json(['status' => 'success', 'token' => $token])
            : response()->json(['status' => 'failure'], 401);
    }
}
