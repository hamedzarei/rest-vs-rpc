<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function recv()
    {
        return new JsonResponse([
            'data' => 'ok'
        ]);
    }
}
