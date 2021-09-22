<?php

include(__DIR__ . '/../config.php');

// define('HOST', getenv('TEST_RABBITMQ_HOST') ? getenv('TEST_RABBITMQ_HOST') : '127.0.0.1');
// define('PORT', getenv('TEST_RABBITMQ_PORT') ? getenv('TEST_RABBITMQ_PORT') : 5672);
// define('USER', getenv('TEST_RABBITMQ_USER') ? getenv('TEST_RABBITMQ_USER') : 'guest');
// define('PASS', getenv('TEST_RABBITMQ_PASS') ? getenv('TEST_RABBITMQ_PASS') : 'guest');
// define('VHOST', '/');
//define('AMQP_DEBUG', getenv('TEST_AMQP_DEBUG') !== false ? (bool)getenv('TEST_AMQP_DEBUG') : false);
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);

$channel = $connection->channel();

$channel->queue_declare('qos_queue', false, true, false, false);

//第二个参数代表：每次只消费1条
$channel->basic_qos(null, 1, null);

function process_message($message)
{
    //消费完消息之后进行应答，告诉rabbit我已经消费了，可以发送下一组了
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    echo "finish\n";
}

$channel->basic_consume('qos_queue', '', false, false, false, false, 'process_message');

while ($channel->is_consuming()) {
    // After 10 seconds there will be a timeout exception.
    $channel->wait(null, false, 30000);
}