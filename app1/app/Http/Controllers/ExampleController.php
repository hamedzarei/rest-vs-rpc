<?php

namespace App\Http\Controllers;

use App\Module\FibonacciRpcClient;
use GuzzleHttp\Client;
use PhpAmqpLib\Connection\AMQPStreamConnection;

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

    public function send()
    {
        $client = new Client();

        $client->get('app2:1213/recv');
    }

    public function sendRPC()
    {
        $fibonacci_rpc = new FibonacciRpcClient();
        $response = $fibonacci_rpc->call(30);
        echo ' [.] Got ', $response, "\n";
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }
}
