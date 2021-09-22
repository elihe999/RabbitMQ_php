# 幂等性

## 案例

```php
function process_message($message)
{
    if ($message->body == 'good') {
        $message->ack();
    } else {
        $message->nack(true);
    }

    // Send a message with the string "quit" to cancel the consumer.
    if ($message->body === 'quit') {
        $message->getChannel()->basic_cancel($message->getConsumerTag());
    }
}
```

如果是good，代表已经接收到了；否则再发一次

basic_nack.php

## 解决方案

唯一键
