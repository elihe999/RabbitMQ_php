# 投递失败

## 如何百分百投递成功

### 1 消息落库打标

定时读取标记，标记为零的选取出来，循环读取重新发送。

#### 缺点

需要操作数据库

### 2 消息的延迟推送二次Ack

没有用数据库保存(没有用监听器)：更快

#### 缺点
