<?php

namespace App\Http\Controllers\API;

use App\Events\StatusChange;
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
        return response()->ok(['servers' => Server::orderBy("name")->get()]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/servers/{id}",
     *     summary="Edit Server Entry",
     *     produces={"application/json"},
     *     tags={"servers"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="Server ID #, if not provided creates a new server entry", type="integer"),
     *     @SWG\Parameter(name="name", in="formData", description="Unique server name.", required=true, type="string"),
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
     * @param int $id
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
     *         response="404",
     *         description="Not Found",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Not Found"}},
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
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/users/{userid}",
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

    /**
     * @param Request $request
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/cad/status/{userid}",
     *     summary="Get or set user details, if you change status to offline, the remaining parameters are set to null automatically whether defined explicitly or not.  Values of null will be set when status is 'Offline' or when a logout is processed.",
     *     produces={"application/json"},
     *     tags={"user"},
     *     security={"session"},
     *     @SWG\Parameter(name="userid", in="path", description="User ID #, if not provided, defaults to logged in user", type="integer"),
     *     @SWG\Parameter(name="department", in="formData", description="Department user is in, IE: Civilian, Police, Sheriff, Highway, USCG", type="string"),
     *     @SWG\Parameter(name="server", in="formData", description="Server ID the user is in", type="integer"),
     *     @SWG\Parameter(name="division", in="formData", description="Division to associate user with", type="string"),
     *     @SWG\Parameter(name="status", in="formData", description="Status for user, valid options: 'Available', 'Busy', 'Out of Service', 'Offline'", type="string"),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *         @SWG\Schema(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/error"),
     *                 @SWG\Schema(type="object",@SWG\Property(property="misc",type="string",description="Extra data for debugging")),
     *             },
     *         ),
     *         examples={"application/json":{"status"="error","msg"="Bad Request","misc"="Invalid status supplied"}},
     *     ),
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
     *                         property="user",
     *                         ref="#/definitions/User",
     *                     ),
     *                 ),
     *             },
     *         )
     *     )
     * )
     */
    public function postUserStatus(Request $request, $id = null) {
        if (!$id) $id = \Auth::user()->id;

        $user = User::find($id);
        if (!$user) return response()->notfound();

        // Check if user going offline, if so, assume setting everything to null
        if ($user->status != "Offline" && $request->input("status") == "Offline") {
            event(new StatusChange($user->server_id, [
                'id' => $user->id,
                'status' => 'Offline',
                'department' => null,
                'division' => null
            ]));
            $user->status = "Offline";
            $user->signed_on = null;
            $user->server_id = null;
            $user->department = null;
            $user->division = null;
            $user->save();
            return response()->ok(['user' => $user]);
        }

        if ($request->has("status")) {
            if (!in_array($request->input("status"), ['Available', 'Busy', 'Offline', 'Out of Service'])) return response()->conflict(['misc' => 'Invalid status']);
            if ($user->status == "Offline" && $request->input("status") != "Offline") {
                $user->signed_on = \Carbon\Carbon::now();
            }
            $user->status = $request->input("status");
        }
        if ($request->has("department")) {
            $user->department = $request->input("department");
        }
        if ($request->has("division")) {
            $user->division = $request->input("division");
        }
        if ($request->has("server")) {
            if ($user->server_id != null) {
                // Sign user off old CAD for server
                event(new StatusChange($user->server_id, [
                    'id' => $user->id,
                    'status' => 'Offline',
                    'department' => null,
                    'division' => null
                ]));
            }
            $user->server_id = $request->input("server");
        }

        $user->save();

        event(new StatusChange($user->server_id, [
            'id' => $user->id,
            'status' => $user->status,
            'department' => $user->department,
            'division' => $user->division
        ]));

        return response()->ok(['user' => $user]);
    }
}
