<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Server;
use App\User;
use Illuminate\Http\Request;

class DataController extends Controller
{

    /**
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/servers",
     *     summary="Get list of servers",
     *     produces={"application/json"},
     *     tags={"servers"},
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
     *                         @SWG\Items(ref="#/definitions/Server"),
     *                     ),
     *                 ),
     *             },
     *         )
     *     )
     * )
     */
    public function getServers() {
        return response()->api(Server::orderBy("name")->get());
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/servers/{id}",
     *     summary="Edit Server Entry",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="Server ID #", required=true, type="integer"),
     *     @SWG\Parameter(name="name", in="formData", description="Unique server name.", type="string"),
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
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             ref="#/definitions/OK"
     *        )
     *     )
     * )
     */
    public function postServers(Request $request, $id = null) {
        // Check name
        $name = $request->input("name");
        if (Server::where("name", $name)->first()) {
            return response()->conflict(); // Unique name check
        }

        if (!$id) {
            $server = new Server(['name' => $name]);
        } else {
            $server = Server::find($id);
            if (!$server) return response()->notfound();
            $server->name = $name;
        }
        $server->save();

        return response()->ok();
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Delete(
     *     path="/servers/{id}",
     *     summary="Delete a server",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="Server ID #", required=true, type="integer"),
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
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             ref="#/definitions/OK"
     *        )
     *     )
     * )
     */
    public function deleteServer(Request $request, $id) {
        $server = Server::find($id);
        if (!$server) return response()->notfound();
        $server->delete();
        return response()->ok();
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/users/{id}",
     *     summary="Get user details",
     *     produces={"application/json"},
     *     tags={"user"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="User ID #", type="integer"),
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
     *                         property="user",
     *                         ref="#/definitions/User",
     *                     ),
     *                 ),
     *             },
     *         )
     *     )
     * )
     */
    public function getUser(Request $request, $id = null) {
        if (!$id) $id = \Auth::user()->id;
        if (\Auth::user()->id != $id && \Auth::user()->hasRole("admin")) {
            return response()->forbidden();
        }

        $user = User::find($id);
        if (!$user) return response()->notfound();

        return response()->ok(['user' => $user]);
    }
}
