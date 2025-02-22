<?php

namespace App\Http\Middleware;

use App\Traits\GeneralResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    use GeneralResponse;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $this->errorResponse('Unauthorized',403);
    }
}
