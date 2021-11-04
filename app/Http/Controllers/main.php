<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class main extends Controller
{

function main() {
        $data = \DB::table('files')->get()->random(20);
        return view('main', ['static' => $data]);
        
}

function video(Request $request) {
    $data = $request->input('file');
    return view('video', ['video' => $data]);
}
}
