<?php

namespace App\Http\Controllers\API;

use App\Events\StatusChange;
use App\Http\Controllers\Controller;
use App\Server;
use App\User;
use Illuminate\Http\Request;

class CharacterController extends Controller
{

    /**
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/characters/{userid}",
     *     summary="Get list of characters for user",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="userid", description="User ID, if null uses authenticated user.", in="path", type="integer"),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Unauthorized"}},
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Forbidden",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Forbidden"}},
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not Found",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Not Found"}},
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(
     *                     ref="#/definitions/OK"
     *                 ),
     *                 @SWG\Schema(
     *                     type="object",
     *                     @SWG\Property(
     *                         property="servers",
     *                         type="array",
     *                         @SWG\Items(ref="#/definitions/Character"),
     *                     ),
     *                 ),
     *             },
     *         )
     *     )
     * )
     */
    public function getServers() {
        return response()->ok(['servers' => Server::orderBy("name")->get()]);
    }
}
