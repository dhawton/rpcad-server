<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Server;
use App\User;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function getServers() {
        return response()->ok(['servers' => Server::orderBy("name")->get()]);
    }

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

    public function deleteServer(Request $request, $id) {
        $server = Server::find($id);
        if (!$server) return response()->notfound();
        $server->delete();
        return response()->ok();
    }

    public function getUser(Request $request, $id) {
        if (\Auth::user()->id != $id && \Auth::user()->hasRole("admin")) {
            return response()->forbidden();
        }

        $user = User::find($id);
        if (!$user) return response()->notfound();

        return response()->ok(['user' => $user]);
    }
}
