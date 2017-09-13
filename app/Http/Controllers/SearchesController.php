<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Categories;
use App\Search;
use App\Advertisement;
use DB;

// Illuminate\Support\Facades\...
use Auth, Validator, Input;

class SearchesController extends Controller
{
    private $searchRules;

    public function __construct()
    {
        // $this->middleware('auth');

        // Only service type is required to search with
        $this->searchRules = array(
            'service' => 'required'//,
            // 'location' => 'required_without_all:service,quote_min,quote_max,rating',
            // 'quote_min' => 'required_without_all:service,location,quote_max,rating',
            // 'quote_max' => 'required_without_all:service,location,quote_max,rating',
            // 'rating' => 'required_without_all:service,location,quote_min,quote_max,rating',
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('searches.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Advertisement::orderBy('service','asc')->lists('service','service')->all();
        return view('searches.create')->with('categories',$categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * https://stackoverflow.com/questions/23401365/laravel-at-least-one-field-required-validation
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->searchRules);

        //Create search
        $search = new Search;
        $search->service = $request->input('service');
        $search->location = $request->input('location');
        $search->quote_min = $request->input('quote_min');
        $search->quote_max = $request->input('quote_max');
        $search->rating = $request->input('rating');
        // Since searches are serverside, we will use id of 0 to store guest searches
        $search->user_id = Auth::user() ? auth()->user()->id : 0;   
        $search->save();

        return redirect('/searches');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Advertisement::orderBy('service','asc')->lists('service','service')->all();
        $search = Search::find($id);
        //$service = Categories::where('category','=',$search->service)->first();
        return view('searches.edit')->with('search', $search)->with('categories', $categories);//->with('service',$service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, $this->searchRules);

        //Update search
        $search = Search::find($id);
        $search->service = $request->input('service');
        $search->location = $request->input('location');
        $search->quote_min = $request->input('quote_min');
        $search->quote_max = $request->input('quote_max');
        $search->rating = $request->input('rating');
        $search->user_id = Auth::user() ? auth()->user()->id : 0;   
        $search->save();

        return redirect('/searches');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $search = Search::find($id);
        $search->delete();
        return redirect('/profile')->with('success', 'Search Deleted');
    }
}
