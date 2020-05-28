<?php


//tcp server  测试设置包格式  客户端和服务端是否可以正确的解析包数据


//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("127.0.0.1", 9501);
$serv->set([
    'log_level' => SWOOLE_LOG_TRACE,
    'trace_flags' => SWOOLE_TRACE_SERVER | SWOOLE_TRACE_HTTP2,
]);
$serv->set(array(
    'open_length_check' => true,
    'package_length_type' => 'N',
    //length不包含包头
    'package_body_offset' => 23,
    'package_length_offset' => 0,
    //length 包含包头
    // 'package_body_offset' => 0,
    // 'package_length_offset' => 0,
    'package_max_length' => 81920,
));

//包数据格式
/*
    struct 
    {
        uint32 length 只包含了数据包体的长度
        uint32 uid 用户id
        char[15] u_name 用户名称
        data 包体
    }
 */

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
   echo '接受到的数据为:'.$data.PHP_EOL;
   //解析数据
   var_dump(unpack('Ndata_length/Nuid/a15uname/a*info', $data));
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();