<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\FlightListLibrary;
use Illuminate\Http\JsonResponse;

/**
 * Class FlightListController
 * @package App\Http\Controllers\Api
 */
class FlightListController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param FlightListLibrary $flightListLibrary
     * @return JsonResponse
     */
    public function index(FlightListLibrary $flightListLibrary)
    {
        return response()->json($flightListLibrary->getAll());
    }

    /**
     * Display the specified resource.
     *
     * @param int $uuid
     * @param FlightListLibrary $flightListLibrary
     * @return JsonResponse
     */
    public function show($uuid, FlightListLibrary $flightListLibrary)
    {
        return response()->json($flightListLibrary->get($uuid));
    }
}
