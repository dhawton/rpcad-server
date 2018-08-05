<?php

namespace App\Http\Controllers\API;

use App\Character;
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
     *     path="/accounts/{userid}/characters/{characterid}",
     *     summary="Get list of characters for user",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="userid", description="User ID, if 0 uses authenticated user.", in="path", type="integer"),
     *     @SWG\Parameter(name="characterid", description="Character ID, if not defined gets all.", in="path", type="integer"),
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
     *         response="409",
     *         description="Conflict, character doesn't belong to user",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Conflict"}},
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
     *                         property="characters",
     *                         type="array",
     *                         @SWG\Items(ref="#/definitions/Character"),
     *                     ),
     *                 ),
     *             },
     *         )
     *     )
     * )
     */
    public function getCharacters($userid, $characterid = null) {
        if ($userid == 0) { $user = \Auth::user(); }
        else {
            $user = User::find($userid);
            if (!$user) return response()->notfound(['misc' => 'User']);
        }

        if ($characterid) {
            $character = Character::find($characterid);
            if (!$character) return response()->notfound(['misc' => 'Character']);

            if ($character->user_id != $user->id) {
                return response()->conflict(['misc' => 'Character does not belong to user']);
            }
        } else {
            $character = Character::where("user_id", $user->id)->get();
        }
        return response()->ok(['characters' => $character]);
    }
}
