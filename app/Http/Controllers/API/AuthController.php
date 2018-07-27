<?php
namespace App\Http\Controllers\API\AuthController;

use App\Http\Controllers\Controller;
use Auth;
use Request;

class AuthController extends Controller
{
    function postIndex(Request $request) {
        if (Auth::attempt(['email' => $request->input("email"), 'password' => $request->input("password"), 'active' => 1])) {
            return response()->ok();
        } else {
            return response()->unauthenticated();
        }
    }
}
