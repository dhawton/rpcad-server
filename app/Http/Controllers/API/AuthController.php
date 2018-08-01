<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;


class AuthController extends APIController
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/auth",
     *     summary="Login to CAD",
     *     produces={"application/json"},
     *     tags={"auth"},
     *     @SWG\Parameter(name="email", in="formData", description="User email address", type="string"),
     *     @SWG\Parameter(name="password", in="formData", description="User password", type="string"),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Unauthorized"}},
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             ref="#/definitions/OK"
     *        )
     *     )
     * )
     */
    function postIndex(Request $request) {
        if (\Auth::attempt([
            'email' => $request->input("email"),
            'password' => $request->input("password"),
            'active' => 1])
        ) {
            return response()->ok();
        } else {
            return response()->unauthenticated();
        }
    }

    /**

     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/auth/logout",
     *     summary="Logout from CAD",
     *     produces={"application/json"},
     *     tags={"auth"},
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Unauthorized"}},
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             ref="#/definitions/OK"
     *        )
     *     )
     * )
     */
    function getLogout() {
        if (!\Auth::check()) { return response()->unauthenticated(); }

        \Auth::user()->division = null;
        \Auth::user()->department = null;
        \Auth::user()->status = "Offline";
        \Auth::user()->server_id = null;
        \Auth::user()->save();

        \Auth::logout();

        return response()->ok();
    }
}
