## tcp协议 ##

 - 含义:

        基于**字节流式**数据,因此存在当接受数据的时候,可能**一次获取**到**多次请求的数据**,或者只获取到了**一次请求的一部分数据**.



----------


 - 解决方案:
     - 自定义eof数据结尾.
     
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
    
     - 包头和包体
     
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


