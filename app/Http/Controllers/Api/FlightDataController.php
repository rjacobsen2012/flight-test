<?php

namespace App\Http\Controllers\Api;

use App\Libraries\Flight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FlightDataController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Flight $flight
     * @return void
     */
    public function store(Request $request, Flight $flight)
    {
        $flight->save($request);

        return response()->json(['status' => 'success']);
    }
}
