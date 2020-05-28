<?php

//tcp分包方案:  设置包结尾字符串[EOF]





$eof = '\r\n';
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("127.0.0.1", 9501);

//配置自定义包结尾
$serv->set(array(
    'log_level' => SWOOLE_LOG_TRACE,
    'trace_flags' => SWOOLE_TRACE_SERVER | SWOOLE_TRACE_HTTP2,
    //打开EOF_SPLIT检测  这个检测策略是遍历每一个字符,看是否等于eof标识,这个适合小数据量的场景.
    //'open_eof_split' => true,   
    //这个检测策略是当操作系统receive数据的时候,就检测一次包尾是否是eof标识,但是当一次接受到多个请求包的时候,就存在分包的问题 explode("\r\n", $data)[适合应答式的场景  ssh http]
    'open_eof_check'  => true,  
    'package_eof'    => $eof, //设置EOF
    'package_max_length' => 81920,
));



//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) use ($eof) {
   echo '接受到的数据为:'.$data.PHP_EOL;
   //解析数据
   //var_dump(unpack('Ndata_length/Nuid/a15uname/a*info', $data));

   //$msg = '我接受到数据了,通知你一下!'.$eof;

   //$serv->send($fd, $msg);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();

