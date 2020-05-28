<?php

$client = new \Swoole\Client(SWOOLE_SOCK_TCP);

//开启包长度检测
$client->set([
    'open_length_check' => true,
    'package_length_type' => 'N',
    //length不包含包头
    'package_body_offset' => 23,
    'package_length_offset' => 0,
    //length 包含包头
    // 'package_body_offset' => 0,
    // 'package_length_offset' => 0,
    'package_max_length' => 81920,
]);

if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}



/*
    struct 
    {
        uint32 length 只包含了数据包体的长度
        uint32 uid 用户id
        char[15] u_name 用户名称
        data 包体
    }
 */



//打包数据 [长度包含了包头和包体]

/*
$uid = pack('N', 10001);

$uname = pack('a15', '陈林');

$data = '我是测试数据';

//$info = pack('a*', json_encode($data, JSON_UNESCAPED_UNICODE));
$info = pack('a30', $data);

$total = $uid.$uname.$info;

$total_length = strlen($total);
$length = pack('N', 4 + $total_length);


var_dump($total_length);

$client->send($length.$total);
*/

//[长度不包含包体]
$uid = pack('N', 10001);

$uname = pack('a15', '陈林');

$data = [
    'code' => 200,
    'msg' => '获取成功',
    'data' => 'dsagfdscxzfsafsdf',
    'secret' => 'test!@#123'
];

$info = pack('a*', json_encode($data, JSON_UNESCAPED_UNICODE));

$length = pack('N', strlen($info));

$client->send($length.$uid.$uname.$info);  //offest 4 + 4 + 15  = 23

$client->recv();
