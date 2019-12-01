<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\SchoolSession;

class SchoolSessionController extends Controller
{   
    public function index(){
        $schoolSessions = SchoolSession::all();
        return view('sessions.index')->with('schoolSessions' , $schoolSessions);
    }

    public function show($id){
        $schoolSession = SchoolSession::where('id' , $id)->with('semesters')->get();
        return view('sessions.settings')->with('schoolSession' , $schoolSession);
    }

    public function create(){
        return view('settings.addsession');
    }

    public function store(Request $request){

        $data = $request->validate([
            'name' => 'required|string|max:40|unique:sessions',
        ]);

        try {
            SchoolSession::create($data);
            return back()->with('sessionAdded');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return back()->with('sessionNotAdded'); 
        }
       
    }
}