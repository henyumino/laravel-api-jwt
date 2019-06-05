<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutorial;

class TutorialController extends Controller
{
    public function index()
    {
        return Tutorial::with('comments')->get();
        // return Tutorial::all();
    }

    public function show($id)
    {   
        $tutorial = Tutorial::with('comments')->where('id', $id)->first();
        
        if(!$tutorial){
            return response()->json([
                'error' => 'id tutorial tidak ditemukan'
            ],404);
        }
        
        return $tutorial;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'  => 'required',
            'body'  => 'required',
        ]);

        $tutorial = $request->user()->tutorials()->create([
            'title'  => $request->json('title'),
            'slug'     => str_slug($request->json('title')),
            'body'  => $request->json('body'),
        ]);

        return $tutorial;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'  => 'required',
            'body'  => 'required',
        ]);

        $tut = Tutorial::find($id);
    
        // menguji ownership tutorial
        if($request->user()->id != $tut->user_id){
            return response()->json(['error' => 'tidak boleh mengedit tutorial ini'], 403);
        }

        $tut->title = $request->title;
        $tut->body = $request->body;
        $tut->save();

        return $tut;

    }

    public function destroy(Request $request, $id)
    {
        $tut = Tutorial::find($id);

        if($request->user()->id != $tut->user_id){
            return response()->json(['error' => 'tidak boleh menghapus tutorial ini'], 403);
        }

        $tut->delete();
       
        return response()->json([
            'success' => true, 
            'message' => 'berhasil menghapus'
        ], 200);
    }
}
