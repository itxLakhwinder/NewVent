<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\User;
use App\Models\Faq;
use App\Models\Page;


class FaqController extends Controller
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
        $faqs = Faq::get();
        return view('faqs.list')->with([ "faqs" => $faqs]);
    }

    public function view($id)
    {
        $faq = Faq::where(['id' => $id])->first();
        return view('faqs.view')->with([ "faq" => $faq]);
    }

    public function delete($id)
    {   
        Faq::where(['id' => $id])->delete();
        return redirect('/faqs')->with('success', 'FAQ delete successfully.');
    }

    public function store(Request $request)
    {          
        $faq = new Faq;
        $faq->question = $request->question;
        $faq->options = serialize($request->options);
        $faq->save();
        return redirect('/faqs')->with('success', 'FAQ added successfully.');
    }



    public function update(Request $request)
    {           
        $faq = Faq::find($request->id);
        $faq->question = $request->question;
        $faq->options = serialize($request->options);
        $faq->save();
        return redirect('/faqs')->with('success', 'FAQ updated successfully.');
    }

    public function disable($id)
    {   
        Faq::where(['id' => $id])->update(['status' => 1 ]);
        return redirect('/faqs')->with('success', 'FAQ disabled successfully.');
    }
    
    public function enable($id)
    {   
        Faq::where(['id' => $id])->update(['status' => 0 ]);
        return redirect('/faqs')->with('success', 'FAQ enabled successfully.');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function terms()
    {
        $text = Page::where('page', '=', 'terms')->first();
        return view('pages.terms')->with([ "text" => $text]);
    }

    public function termsSave(Request $request)
    {           
        $page = ($request->filled("id")) ? Page::find($request->id)  : new Page;
        $page->body = $request->body;
        $page->page = $request->page;
        $page->title = $request->title;
        $page->save();
        return redirect('/terms')->with('success', 'Page updated successfully.');
    }


    public function postingGuidelines()
    {
        $text = Page::where('page', '=', 'posting-guidelines')->first();
        return view('pages.posting-guidelines')->with([ "text" => $text]);
    }

    public function postingGuidelinesSave(Request $request)
    {           
        $page = ($request->filled("id")) ? Page::find($request->id)  : new Page;
        $page->body = $request->body;
        $page->page = $request->page;
        $page->title = $request->title;
        $page->save();
        return redirect('/guidelines')->with('success', 'Page updated successfully.');
    }

    public function privacyPolicy()
    {
        $text = Page::where('page', '=', 'privacy-policy')->first();
        return view('pages.privacy-policy')->with([ "text" => $text]);
    }

    public function privacyPolicySave(Request $request)
    {           
        $page = ($request->filled("id")) ? Page::find($request->id)  : new Page;
        $page->body = $request->body;
        $page->page = $request->page;
        $page->title = $request->title;
        $page->save();
        return redirect('/policy')->with('success', 'Page updated successfully.');
    }
}
