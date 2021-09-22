# Ack - Comfirm

## 为什么消息会推送失败？

> 普通

```php
$msg = [
        'data'=>'消息_'.$i,
    ];
$ex->publish(json_encode($msg), $routingKey
    , AMQP_NOPARAM, array('delivery_mode' => 2));
```

没有确认：网络
producter连接mq失败,消息没有发送到mq
producter连接mq成功,但是发送到exchange失败
消息发送到exchange成功，但是路由到queue失败


### 发送失败处理

> producter连接mq失败，消息没有发送到mq

　  - 可以使用trycatch捕获异常，将消息保存到db中后续进行重发处理

> producter连接mq成功，但是发送到exchange失败

　　- 通过实现ConfirmCallback接口，对发送结果进行处理，根据ack来判断是否成功

　　　同时我们可以扩写correlationData类，因为correlationData只有一个ID属性，没有关于消息的属性，我们可以扩展这个类，在发送消息时，把想要的数据写入就可以了

> 消息发送到exchange成功但是路由到queue失败

　　- 可以通过实现ReturnCallback接口，对回退消息进行重发处理。

　　- 消息持久化还是创建队列的时候设置一下就行了，主要是为了防止rabbitmq宕机，rabbitmq重启后，会自己去寻找持久化的数据

---

> 确认

```php
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
```

### 例子

p.php生成消息，c1.php c2.php消费

---

如果Consumer数量很多或者希望每个Consumer同时只处理一个任务可以通过在Consumer中设置PrefetchCount来实现更加均匀的任务分发。

$channel = new AMQPChannel($connection);
$channel->setPrefetchCount(1);