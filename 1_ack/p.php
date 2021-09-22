<?php
  
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

$queueName = 'superrd';
$exchangeName = 'superrd';
$config = [
    'host'=>'127.0.0.1',
    'vhost'=>'/',
    'port'=>5672,
    'login'=>'guest',
    'password'=>'guest'
];

$connection = new AMQPConnection($config);
$queueName = 'superrd';
$exchangeName = 'superrd';
$routeKey = 'superrd';
$message = 'task--';
$connection->connect() or die("Cannot connect to the broker!\n");
try {
        $channel = new AMQPChannel($connection);
        $exchange = new AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
 
        $queue = new AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
 
        $queue->bind($exchangeName, $routeKey);
 
        for($i=0 ; $i<100;$i++){
        $exchange->publish($message.$i,$routeKey);
        var_dump("[x] Sent $message $i");
        }
} catch (AMQPConnectionException $e) {
        var_dump($e);
        exit();
}
 $connection->disconnect();