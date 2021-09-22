# 限流

高并发

basic_qos($prefetchSize, $prefetchCount, $global)

## QOS

当不给$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag'])回执，就无法知道是否完成，就不会发送下一批消息。

