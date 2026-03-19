<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class main extends Controller
{

function main() {
        $data = \DB::table('files')->inRandomOrder()->limit(20)->get();
        $people = \DB::table('people')->orderBy('name')->get();
        return view('main', ['static' => $data, 'people' => $people]);
}

function sort() {
    $data = \DB::table('files')->orderBy('votes', 'desc')->limit(20)->get();
    $people = \DB::table('people')->orderBy('name')->get();
    return view('sort', ['static' => $data, 'people' => $people]);
}

function all() {
    $data = \DB::table('files')->orderBy('id', 'desc')->get();
    $people = \DB::table('people')->orderBy('name')->get();
    return view('all', ['static' => $data, 'people' => $people]);
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

function person(Request $request) {
    $personId = $request->input('id');
    $person = \DB::table('people')->where('id', $personId)->first();
    if (!$person) {
        return redirect('/');
    }
    $data = \DB::table('files')
        ->join('file_person', 'files.id', '=', 'file_person.file_id')
        ->where('file_person.person_id', $personId)
        ->orderBy('files.id', 'desc')
        ->select('files.*')
        ->get();
    $people = \DB::table('people')->orderBy('name')->get();
    return view('person', ['static' => $data, 'person' => $person, 'people' => $people]);
}

function people() {
    $people = \DB::table('people')
        ->leftJoin('file_person', 'people.id', '=', 'file_person.person_id')
        ->select('people.*', \DB::raw('COUNT(file_person.file_id) as video_count'))
        ->groupBy('people.id', 'people.name', 'people.face_encoding', 'people.thumbnail', 'people.created_at', 'people.updated_at')
        ->orderByDesc('video_count')
        ->get();
    return view('people', ['people' => $people]);
}

function renamePerson(Request $request) {
    $personId = $request->input('id');
    $name = $request->input('name');
    \DB::table('people')
        ->where('id', $personId)
        ->update(['name' => $name]);
    return redirect()->back();
}

}
