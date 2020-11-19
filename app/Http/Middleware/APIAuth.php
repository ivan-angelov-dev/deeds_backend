<?php

namespace App\Http\Middleware;

use App\Http\Utils\Utils;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Http\Parser\AuthHeaders;

class APIAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $chain = JWTAuth::parser()->getChain();

        foreach ($chain as $item) {
            if ($item instanceof AuthHeaders) {
                $item->setHeaderName('X-API-TOKEN');
                $item->setHeaderPrefix('deeds');
            }
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            $request->merge(['user' => $user]);

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return Utils::makeResponse([], config('constants.response-message.invalid-token'));
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return Utils::makeResponse([], config('constants.response-message.token-expired'));
            }else{
                return Utils::makeResponse([], config('constants.response-message.token-not-found'));

            }
        }
        return $next($request);
    }
}
