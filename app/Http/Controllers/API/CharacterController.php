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
    public function getCharacters($userid, $characterid = null)
    {
        if ($userid == 0) {
            $user = \Auth::user();
        } else {
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

    /**
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/accounts/{userid}/characters/{characterid}",
     *     summary="Create (characterid null or 0) or edit character",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="userid", description="User ID, if 0 uses authenticated user.", in="path", type="integer"),
     *     @SWG\Parameter(name="characterid", description="Character ID, if not defined, or 0, creates.", in="path", type="integer"),
     *     @SWG\Parameter(name="firstname", type="string", in="formData"),
     *     @SWG\Parameter(name="lastname", type="string", in="formData"),
     *     @SWG\Parameter(name="address", type="string", in="formData"),
     *     @SWG\Parameter(name="city", type="string", in="formData"),
     *     @SWG\Parameter(name="gender", type="string", in="formData", description="Male or Female"),
     *     @SWG\Parameter(name="datebirth", type="string", in="formData", description="Date of Birth (YYYY-MM-DD)"),
     *     @SWG\Parameter(name="race", type="string", in="formData"),
     *     @SWG\Parameter(name="haircolor", type="string", in="formData"),
     *     @SWG\Parameter(name="height_feet", type="string", in="formData"),
     *     @SWG\Parameter(name="height_inches", type="string", in="formData"),
     *     @SWG\Parameter(name="weight", type="string", in="formData"),
     *     @SWG\Parameter(name="photo", type="string", in="formData"),
     *     @SWG\Parameter(name="licensestatus", type="string", in="formData", description="Values 'ID Only', 'Learner's Permit', 'Valid', 'Suspended', 'Revoked'"),
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
    public function postCharacters(Request $request, $userid, $characterid = null)
    {
        if ($userid == 0) {
            $user = \Auth::user();
        } else {
            $user = User::find($userid);
            if (!$user) return response()->notfound(['misc' => 'User']);
        }

        if (!$characterid) {
            $character = new Character([
                'user_id' => $user->id,
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'gender' => $request->input('gender'),
                'datebirth' => $request->input('datebirth'),
                'race' => $request->input('race'),
                'haircolor' => $request->input('haircolor'),
                'height_feet' => $request->input('height_feet'),
                'height_inches' => $request->input('height_inches'),
                'weight' => $request->input('weight'),
                'photo' => $request->input('photo'),
                'licensestatus' => $request->input('licensestatus')
            ]);
            $character->save();
        } else {
            $character = Character::find($characterid);
            if (!$character) return response()->notfound(['misc' => 'Character']);
            if ($character->user_id != $user->id && !$user->hasRole('Admin')) {
                return response()->forbidden();
            }
            \DB::table('characters')->where('id', $characterid)->update([
                'user_id' => $user->id,
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'gender' => $request->input('gender'),
                'datebirth' => $request->input('datebirth'),
                'race' => $request->input('race'),
                'haircolor' => $request->input('haircolor'),
                'height_feet' => $request->input('height_feet'),
                'height_inches' => $request->input('height_inches'),
                'weight' => $request->input('weight'),
                'photo' => $request->input('photo'),
                'licensestatus' => $request->input('licensestatus')
            ]);

            $character = Character::find($characterid);

        }
        return response()->ok(['character' => $character]);
    }

    /**
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Delete(
     *     path="/accounts/{userid}/characters/{characterid}",
     *     summary="Create (characterid null or 0) or edit character",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="userid", description="User ID, if 0 uses authenticated user.", in="path", type="integer"),
     *     @SWG\Parameter(name="characterid", description="Character ID, if not defined, or 0, creates.", in="path", type="integer"),
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
     *             },
     *         )
     *     )
     * )
     */
    public function deleteCharacters($userid, $characterid)
    {
        if ($userid == 0) {
            $user = \Auth::user();
        } else {
            $user = User::find($userid);
            if (!$user) return response()->notfound(['misc' => 'User']);
        }

        $character = Character::find($characterid);
        if (!$character) return response()->notfound(['misc' => 'Character']);
        if ($character->user_id != $user->id && !$user->hasRole('Admin')) {
            return response()->forbidden();
        }
        $character->delete();
        return response()->ok();
    }
}
