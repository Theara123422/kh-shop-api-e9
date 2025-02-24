<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpFoundation\Response;

class AutoRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Attempt to parse the token and authenticate the user
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            try {
                // Ensure the token can be refreshed before attempting to refresh
                if (!JWTAuth::getToken()) {
                    return response()->json(['error' => 'Token is required'], 401);
                }

                // Refresh the token
                $newToken = JWTAuth::refresh();

                // Attach the new token and continue
                return $this->setNewToken($newToken, $next($request));
            } catch (JWTException $e) {
                return response()->json(['error' => 'Token has expired and cannot be refreshed'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Proceed with the request if token is valid
        return $next($request);
    }

    /**
     * Add the refreshed token to the response headers.
     */
    protected function setNewToken($newToken, $response): Response
    {
        // Check if response is JSON, then add the token
        if (method_exists($response, 'header')) {
            return $response->header('Authorization', 'Bearer ' . $newToken);
        }

        return response()->json($response->original)->header('Authorization', 'Bearer ' . $newToken);
    }
}
