<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 5/27/20
 * Time: 1:33 PM
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RPCServer extends Command
{
    protected $signature = 'rpc:server';

    public function handle()
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();

        $channel->queue_declare('rpc_queue', false, false, false, false);

        function fib($n)
        {
            return 1;
//            if ($n == 0) {
//                return 0;
//            }
//            if ($n == 1) {
//                return 1;
//            }
//            return fib($n-1) + fib($n-2);
        }

        echo " [x] Awaiting RPC requests\n";
        $callback = function ($req) {
            $n = intval($req->body);
            echo ' [.] fib(', $n, ")\n";

            $msg = new AMQPMessage(
                (string) fib($n),
                array('correlation_id' => $req->get('correlation_id'))
            );

            $req->delivery_info['channel']->basic_publish(
                $msg,
                '',
                $req->get('reply_to')
            );
            $req->delivery_info['channel']->basic_ack(
                $req->delivery_info['delivery_tag']
            );
        };

        $channel->basic_qos(1000, 1000, null);
        $channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}