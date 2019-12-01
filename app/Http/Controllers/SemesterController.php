<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Semester;

class SemesterController extends Controller
{
    public function store(Request $request){

        
        $validator = Validator::make($request->all() , [
            'name' => 'required|string|max:30',
            'session_id' => 'required|integer',
        ]);

        if($validator->fails()){

           return response()->json( [ 'error' => $validator->errors() ]);

        }

        //continue

        //check for unique if semester with session exists
        $num = Semester::where([
            'name' => $request->name,
            'session_id' => $request->session_id,
            ])->count();

        if($num == 0){

            Semester::create([
                'name' => $request->name,
                'session_id' => $request->session_id,
            ]);

            return response()->json( [ 'success' => 'semester created'] );

        }

        if($num > 0){
            return response()->json( ['semester_exists' => 'this session already has a ' .strtoupper($request->name) .' semester' ]);
        }


    }
}