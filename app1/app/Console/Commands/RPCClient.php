<?php

/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 6/1/20
 * Time: 9:39 AM
 */
namespace App\Console\Commands;


use App\Module\FibonacciRpcClient;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RPCClient extends Command
{
    protected $signature = 'rpc:client';

    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function handle()
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

//    public function call()
//    {
//        $this->response = null;
//        $this->corr_id = uniqid();
//
//        $msg = new AMQPMessage(
//            'hello',
//            array(
//                'correlation_id' => $this->corr_id,
//                'reply_to' => $this->callback_queue
//            )
//        );
//        $this->channel->basic_publish($msg, '', 'rpc_queue');
//        while (!$this->response) {
//            $this->channel->wait();
//        }
//        return intval($this->response);
//    }
}