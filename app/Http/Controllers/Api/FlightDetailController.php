<?php

namespace App\Http\Controllers\Api;

use App\Libraries\FlightDetailLibrary;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Class FlightDetailController
 * @package App\Http\Controllers\Api
 */
class FlightDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param FlightDetailLibrary $flightDetailLibrary
     * @return JsonResponse
     */
    public function index(FlightDetailLibrary $flightDetailLibrary)
    {
        return response()->json($flightDetailLibrary->getAll());
    }

    /**
     * Display the specified resource.
     *
     * @param $uuid
     * @param FlightDetailLibrary $flightDetailLibrary
     * @return JsonResponse
     */
    public function show($uuid, FlightDetailLibrary $flightDetailLibrary)
    {
        return response()->json($flightDetailLibrary->get($uuid));
    }
}
