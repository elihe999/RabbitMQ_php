<?php
//申明连接参数
$config = [
    'host'=>'127.0.0.1',
    'vhost'=>'/',
    'port'=>5672,
    'login'=>'guest',
    'password'=>'guest'
];

$cnn = new AMQPConnection($config);

//抛出异常
if(!$cnn->connect()){
    echo "连接失败";
    exit();
}
$cn = new AMQPChannel($cnn);
$ex = new AMQPExchange($cn);

$routingKey = 'key_1';
$exchangeName = 'exchange_1';
$ex->setName($exchangeName);
//设置交换机的类型
$ex->setType(AMQP_EX_TYPE_DIRECT);

//设置交换机的持久
$ex->setFlags(AMQP_DURABLE);

//申明交换机
$ex->declareExchange();

for($i=1;$i<=10;$i++){
    //消息内容
    $msg = [
        'data'=>'消息_'.$i,
    ];
    $ex->publish(json_encode($msg), $routingKey
        , AMQP_NOPARAM, array('delivery_mode' => 2));
}