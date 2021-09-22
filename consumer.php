<?php
//申明连接参数
$config = [
    'host'=>'127.0.0.1',
    'vhost'=>'/',
    'port'=>5672,
    'login'=>'test',
    'password'=>'123456'
];

//连接broker,创建一个rabbitmq连接
$cnn = new AMQPConnection($config);

//抛出异常
if(!$cnn->connect()){
    echo "连接失败";
    exit();
}

//在连接内创建一个通道
$ch = new AMQPChannel($cnn);

//创建一个交换机
$ex = new AMQPExchange($ch);

//申明路由键
$routingKey = 'key_1';

//申明交换机名称
$exchangeName = 'exchange_1';

//设置交换机名称
$ex->setName($exchangeName);

//设置交换机的类型
$ex->setType(AMQP_EX_TYPE_DIRECT);

//设置交换机的持久
$ex->setFlags(AMQP_DURABLE);

//申明交换机
$ex->declareExchange();

//创建一个消息队列
$q = new AMQPQueue($ch);

//设置队列名称
$q->setName('queue_1');

//设置队列的持久
$q->setFlags(AMQP_DURABLE);

//申明消息队列
$q->declareQueue();

$q->bind($ex->getName(), $routingKey);

//接收消息并进行处理回调方法
function receive($envelope, $queue){
    sleep(1);
    echo $envelope->getBody()."\n";
}
$q->consume("receive");
