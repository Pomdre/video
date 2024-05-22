<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class main extends Controller
{

function main() {
        $data = \DB::table('files')->inRandomOrder()->limit(20)->get();
        return view('main', ['static' => $data]);   
}

function sort() {
    $data = \DB::table('files')->orderBy('votes', 'desc')->limit(20)->get();
    return view('sort', ['static' => $data]);   
}

function all() {
    $data = \DB::table('files')->orderBy('id', 'desc')->get();
    return view('all', ['static' => $data]);   
}

function video(Request $request) {
    $data = $request->input('file');
    $votes = \DB::table('files')
        ->where('basename', $data)
        ->get();
    return view('video', ['video' => $data, 'votes' => $votes]);
}

function vote(Request $request) {
    $data = $request->input('file');
    \DB::table('files')
            ->where('basename', $data)
            ->increment('votes', 1);
    return redirect()->back();
}

}
