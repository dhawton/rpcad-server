<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;
use Request;

class ResponseServiceProvider extends ServiceProvider
{
    public function boot(ResponseFactory $factory) {
        $factory->macro('malformed', function($data = []) use ($factory) {
            return response()->api(array_merge(generate_error("Malformed request"), $data), 400);
        });

        $factory->macro('conflict', function($data = []) use ($factory) {
            return response()->api(array_merge(generate_error("Conflict"), $data), 409);
        });

        $factory->macro('notfound', function($data = []) use ($factory) {
            return response()->api(array_merge(generate_error("Not found"), $data), 404);
        });

        $factory->macro('forbidden', function($data = []) use ($factory) {
            return response()->api(array_merge(generate_error("Forbidden"), $data), 403);
        });

        $factory->macro('unauthenticated', function($data = []) use ($factory) {
            return response()->api(array_merge(generate_error("Unauthorized"), $data), 401);
        });

        $factory->macro('ok', function ($data = []) use ($factory) {
            return response()->api(array_merge(['status' => 'OK'], $data), 200);
        });
        $factory->macro('api', function ($data, $status = 200, $headers = []) use ($factory) {

            return $factory->make(
                json_encode($data, JSON_NUMERIC_CHECK),
                $status,
                array_merge($headers, ['Content-Type' => 'application/json'])
            );
        });
    }

    public function register() {
        //
    }
}

/**
 * @param string $msg
 * @param bool $asArray
 * @return string|array
 */
function generate_error($msg, $asArray = true) {
    if ($asArray) {
        return [
            'status' => 'error',
            'msg' => $msg
        ];
    }
    return encode_json([
        'status' => 'error',
        'msg' => $msg
    ]);
}
