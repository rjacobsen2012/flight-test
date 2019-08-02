<?php

namespace App\Http\Controllers\Api;

use App\Libraries\Flight;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class FlightDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Flight $flight
     * @return JsonResponse
     */
    public function index(Flight $flight)
    {
        return response()->json($flight->listDetail());
    }

    /**
     * Display the specified resource.
     *
     * @param $uuid
     * @param Flight $flight
     * @return JsonResponse
     */
    public function show($uuid, Flight $flight)
    {
        return response()->json($flight->showDetail($uuid));
    }
}
