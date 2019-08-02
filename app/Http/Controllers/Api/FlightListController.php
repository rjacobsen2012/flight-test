<?php

namespace App\Http\Controllers\Api;

use App\Libraries\Flight;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FlightListController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Flight $flight
     * @return JsonResponse
     */
    public function index(Flight $flight)
    {
        return response()->json($flight->list());
    }

    /**
     * Display the specified resource.
     *
     * @param int $uuid
     * @param Flight $flight
     * @return JsonResponse
     */
    public function show($uuid, Flight $flight)
    {
        return response()->json($flight->show($uuid));
    }
}
