<?php

namespace App\Http\Controllers\Api;

use App\Libraries\FlightDataLibrary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class FlightDataController
 * @package App\Http\Controllers\Api
 */
class FlightDataController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param FlightDataLibrary $flightDataLibrary
     * @return void
     */
    public function store(Request $request, FlightDataLibrary $flightDataLibrary)
    {
        $flightDataLibrary->save($request);

        return response()->json(['status' => 'success']);
    }
}
