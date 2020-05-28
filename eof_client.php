<?php



#测试 eof方案 自定义包结束符 来解决粘包问题


use Swoole\Client;



$eof = '\r\n';


$client = new Client(SWOOLE_SOCK_TCP);

$client->set([
    //打开EOF_SPLIT检测  这个检测策略是遍历每一个字符,看是否等于eof标识,这个适合小数据量的场景.
    //'open_eof_split' => true,   
    //这个检测策略是当操作系统receive数据的时候,就检测一次包尾是否是eof标识,但是当一次接受到多个请求包的时候,就存在分包的问题 explode("\r\n", $data)[适合应答式的场景  ssh http]
    'open_eof_check'  => true,  
    'package_eof'    => $eof, //设置EOF
    'package_max_length' => 81920,
]);

if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit('connect tcp server fail');
}


$data = '你是一个大傻逼'.$eof;

// while (1) {
$client->send($data);
// }


//接受到的数据 也会带有结尾符. 需要自己去除
$res = $client->recv();

$res = explode($eof, $res);
var_dump($res);

$client->close();



