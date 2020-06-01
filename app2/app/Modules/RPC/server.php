<?php
/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 5/27/20
 * Time: 1:27 PM
 */

namespace App\Modules\RPC;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class server
{
    public function __construct()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();

        $channel->queue_declare('rpc_queue', false, false, false, false);


        echo " [x] Awaiting RPC requests\n";

        $callback = function ($req) {
//            $n = intval($req->body);
//            echo ' [.] fib(', $n, ")\n";

            $msg = new AMQPMessage(
                'hello',
                array('correlation_id' => $req->get('correlation_id'))
            );

            $req->delivery_info['channel']->basic_publish(
                $msg,
                '',
                $req->get('reply_to')
            );
            echo " replying\n";
            $req->delivery_info['channel']->basic_ack(
                $req->delivery_info['delivery_tag']
            );
        };

        $channel->basic_qos(1000, 1000, null);
        $channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

//        $channel->close();
//        $connection->close();
    }
}

(new server());