<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class AccountController extends APIController
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/account/{id}",
     *     summary="Modify account",
     *     produces={"application/json"},
     *     tags={"account"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="User ID, if null uses authenticated user", type="integer"),
     *     @SWG\Parameter(name="email", in="formData", description="Change email address", type="string"),
     *     @SWG\Parameter(name="current_password", in="formData", description="Current account password, used for password changes", type="string"),
     *     @SWG\Parameter(name="new_password", in="formData", description="Change password, requires current_password", type="string"),
     *     @SWG\Parameter(name="identifier", in="formData", description="Change identifier", type="integer"),
     *     @SWG\Parameter(name="identifier_type", in="formData", description="Change identifier type (ie, Civ, LEO, etc)", type="string"),
     *     @SWG\Response(
     *         response="400",
     *         description="Malformed request (usually missing field)",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Malformed Request"}},
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
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
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Not Found"}},
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Conflict, usually for invalid current passwords, duplicate identifiers within same identifier type",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Conflict"}},
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
    function postIndex(Request $request, $id = null) {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'identifier' => 'integer',
            'identifier_type' => 'max:255',
            'password' => 'min:8'
        ]);
        if ($validator->fails()) {
            return response()->malformed();
        }
        if (!$id) { $id = \Auth::user()->id; }
        if ($id != \Auth::user()->id && !\Auth::user()->hasRole('admin')) {
            return response()->forbidden();
        }
        if ($id != \Auth::user()->id) {
            $user = User::find($id);
        } else {
            $user = \Auth::user();
        }

        if (!$user) return response()->notfound();

        if ($request->has("new_password")) {
            if (!$request->has("current_password")) return response()->malformed();
            if (!\Hash::check($request->input("current_password"), $user->password))
                return response()->forbidden();
            $user->password = \Hash::make($request->input("new_password"));
        }

        if ($request->has("email")) {
            $u = User::where("email", $request->input("email"))->first();
            if ($u) return response()->conflict();

            $user->email = $request->input("email");
        }

        if ($request->has("identifier")) {
            if ($request->has("identifier_type")) {
                $it = $request->input("identifier_type");
            } else {
                $it = $user->identifier_type;
            }
            $u = User::where("identifier_type", $it)->where("identifier", $request->has("identifier"))->first();
            if ($u) return response()->conflict();
            $user->identifier = $request->input("identifier");
            $user->identifier_type = $it;
        }

        $user->save();
        return response()->ok();
    }

    /**
     * @param int|null $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/account/{id}",
     *     summary="Get account info",
     *     produces={"application/json"},
     *     tags={"account"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="User ID, if null uses authenticated account", type="integer"),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
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
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Not found"}},
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             allOf={
     *             @SWG\Schema(
     *                 ref="#/definitions/OK"
     *             ),
     *             @SWG\Schema(
     *                 type="object",
     *                 @SWG\Property(property="user", type="object", ref="#/definitions/User"),
     *             )
     *             }
     *        )
     *     )
     * )
     */
    function getIndex($id = null) {
        if ($id) { $user = User::find($id); }
        else { $user = \Auth::user(); }

        if (!$user) return response()->notfound();

        return response()->ok(['user' => $user]);
    }

    /**
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Delete(
     *     path="/account/{id}",
     *     summary="Delete account",
     *     produces={"application/json"},
     *     tags={"account"},
     *     security={"session"},
     *     @SWG\Parameter(name="id", in="path", description="User ID", type="integer"),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
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
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Not found"}},
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK Response",
     *         @SWG\Schema(
     *             allOf={
     *             @SWG\Schema(
     *                 ref="#/definitions/OK"
     *             )
     *             }
     *        )
     *     )
     * )
     */
    function deleteIndex($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->notfound();
        }

        if ($user->status != "Offline") {
            event(new StatusChange($user->server_id, [
                'id' => $user->id,
                'status' => 'Offline',
                'department' => null,
                'division' => null
            ]));
        }

        $user->delete();

        return response()->ok();
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Post(
     *     path="/account/new",
     *     summary="Create account",
     *     produces={"application/json"},
     *     tags={"account"},
     *     security={"session"},
     *     @SWG\Parameter(name="email", in="formData", description="Email address", type="string"),
     *     @SWG\Parameter(name="password", in="formData", description="Password", type="string"),
     *     @SWG\Parameter(name="identifier", in="formData", description="Identifier", type="integer"),
     *     @SWG\Parameter(name="identifier_type", in="formData", description="Identifier type (ie, Civ, LEO, etc)", type="string"),
     *     @SWG\Parameter(name="name", in="formData", description="Display name", type="string"),
     *     @SWG\Response(
     *         response="400",
     *         description="Malformed request (usually missing field)",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Malformed Request"}},
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated",
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
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Not Found"}},
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Conflict, usually for invalid current passwords, duplicate identifiers within same identifier type",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status"="error","msg"="Conflict"}},
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
    function postNew(Request $request, $id = null) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'identifier' => 'required|integer',
            'identifier_type' => 'max:255',
            'password' => 'required|min:8'
        ]);
        if ($validator->fails()) {
            return response()->malformed();
        }

        if ($id != \Auth::user()->id && !\Auth::user()->hasRole('admin')) {
            return response()->forbidden();
        }

        $u = User::where('email', $request->input("email"))->first();
        if ($u) return response()->conflict();

        $u = User::where('identifier_type', $request->input("identifier_type"))
            ->where("identifier", $request->input("identifier"))->first();
        if ($u) return response()->conflict();

        $user = new User([
            'email' => $request->input("email"),
            'identifier' => $request->input('identifier'),
            'identifier_type' => $request->input('identifier_type'),
            'password' => \Hash::make($request->input("password")),
            'name' => $request->input("name")
        ]);
        $user->save();
        return response()->ok();
    }
}
