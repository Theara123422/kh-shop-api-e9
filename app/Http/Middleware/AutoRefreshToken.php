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
                // Token is expired, attempt to refresh the token
                $newToken = JWTAuth::refresh();
                
                // Add the new token to the response header
                return $this->setNewToken($newToken, $next($request));
            } catch (JWTException $e) {
                // Unable to refresh the token, send an error response
                return response()->json(['error' => 'Token has expired and cannot be refreshed'], 401);
            }
        } catch (JWTException $e) {
            // Token is invalid
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
        // Adding the refreshed token to the header of the response
        return $response->header('Authorization', 'Bearer ' . $newToken);
    }
}
