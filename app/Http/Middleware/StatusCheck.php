<?php

namespace App\Http\Middleware;

use Closure;

class StatusCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array     $status
     * @return mixed
     */
    public function handle($request, Closure $next, ...$status)
    {
        if (!\Auth::check()) return response()->forbidden();

        $valid = false;

        foreach($status as $s) {
            \Log::info("Checking $s...");
            switch($s) {
                case "admin":
                    if (\Auth::user()->hasRole("admin")) {
                        \Log::info("Has admin");
                        $valid = true;
                        break(2);
                    }
                    break;
                case "self":
                    if ($request->userid == \Auth::user()->id) {
                        \Log::info("Is self");
                        $valid = true;
                        break(2);
                    }
                    break;
                default:
                    if (\Auth::user()->department == $s) {
                        \Log::info("department matched");
                        $valid = true;
                        break(2);
                    }
                    break;
            }
        }

        if (!$valid) return response()->forbidden(['misc' => "Current department doesn't match list of required"]);

        return $next($request);
    }
}
