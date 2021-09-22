<?php
 
/*
 * 发布-订阅-c1c2
 * create by superrd
 */

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

// include(__DIR__ . './../config.php');

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
$connection->connect() or die("Cannot connect to the broker!\n");
$channel = new AMQPChannel($connection);
// prefetch
$channel->setPrefetchCount(1);

$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE);
$exchange->declareExchange();
 
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declareQueue();
$routeKey = 'superrd';
$queue->bind($exchangeName, $routeKey);
//阻塞模式接收消息
 
echo "Message:\n";
while(True){
        $queue->consume('processMessage');
//自动ACK应答
        //$queue->consume('processMessage', AMQP_AUTOACK);
}
 
$conn->disconnect();
/*
* 消费回调函数
* 处理消息
*/
function processMessage($envelope, $q) {
    $msg = $envelope->getBody();
    sleep(1);  //sleep1秒模拟任务处理
    echo $msg."\n"; //处理消息
    $q->ack($envelope->getDeliveryTag()); //手动发送ACK应答
}