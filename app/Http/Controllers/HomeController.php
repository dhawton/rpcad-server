<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function postCad(Request $request) {
        $server = $request->input("server");
        $dept = $server->input("department");
        if ($server == -1 || $dept == -1) return redirect("/home");
        if (!\App\Server::find($server)) return redirect("/home");
        if (!\Auth::user()->hasRole($dept)) return redirect("/home");

        session()->put('server', $server);
        session()->put('dept', $dept);

        if(in_array($dept, ["state", "sheriff", "police"])) {
            return view('cad.leo');
        }
    }
}
