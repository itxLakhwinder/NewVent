<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\User;
use App\Models\Topic;
use App\Models\Journal;
use Illuminate\Support\Facades\Auth; 

class JournalController extends Controller
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $journals = Journal::with(['user'])->get();        
        return view('journals.list')->with([ "journals" => $journals]);
    }

    public function view($id)
    {
        $journal = Journal::where(['id' => $id])->first();
        return view('journals.view')->with([ "journal" => $journal]);
    }

    public function delete($id)
    {   
        Journal::where(['id' => $id])->delete();
        return redirect('/journals')->with('success', 'Journal delete successfully.');
    }

    public function disable($id)
    {   
        Journal::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/journals')->with('success', 'Journal disabled successfully.');
    }
    
    public function enable($id)
    {   
        Journal::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/journals')->with('success', 'Journal enabled successfully.');
    }

}
