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
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Unauthorized"}},
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Return JSON Token.",
     *         @SWG\Schema(
     *             ref="#/definitions/OK"
     *        )
     *     )
     * )
     */
    function postIndex(Request $request) {
        \Log::info("Email: " . $request->input("email") . ", password: " . $request->input("password"));
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
     * @SWG\Post(
     *     path="/auth",
     *     summary="Login to CAD",
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
     *         description="Return JSON Token.",
     *         @SWG\Schema(
     *             ref="#/definitions/OK"
     *        )
     *     )
     * )
     */
    function getLogout() {
        if (!\Auth::check()) { return response()->unauthenticated(); }

        \Auth::logout();

        return response()->ok();
    }
}
