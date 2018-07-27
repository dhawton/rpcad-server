<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
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

    function getLogout() {
        if (!\Auth::check()) { return response()->unauthenticated(); }

        \Auth::logout();

        return response()->ok();
    }
}
