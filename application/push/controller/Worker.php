<?php

namespace app\push\controller;

use think\Config;
use think\worker\Server;
use Workerman\Lib\Timer;

class Worker extends Server
{
    protected $socket;

    public function __construct()
    {
        $this->socket = Config::get('workerman.socket');
        parent::__construct();
    }

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $arrData = json_decode($data, true);
        if (!$arrData['action']) {
            $connection->send('action 参数不存在');
        } else {
            $arrAction = explode('/', $arrData['action']);
            $class     = 'app\\push\\controller\\' . $arrAction[0];
            $func      = $arrAction[1];
            if (class_exists($class)) {
                $obj = new $class($connection,$arrData);
                if (method_exists($obj, $func)) {
                    $obj->$func($connection, $arrData['data']);
                } else {
                    $connection->send($func . '类的方法不存在');
                }
            } else {
                $connection->send($class . '类不存在');
            }
        }
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        // 临时给$connection对象添加一个auth_timer_id属性存储定时器id
        // 定时30秒关闭连接，需要客户端30秒内发送验证删除定时器
        $connection->auth_timer_id = Timer::add(30, function()use($connection){
            $connection->close();
        }, null, false);
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        User::delete($connection);
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
        $inner_text_worker = new \Workerman\Worker( Config::get('workerman.text') );
        $inner_text_worker->onMessage = function($connection, $data)
        {
            if( !is_array($data) ){
                $arrData = json_decode($data,true);
            }
            if( is_array($arrData) && !empty($arrData) ){
                $arrAction = explode('/', $arrData['action']);
                $class     = 'app\\push\\controller\\' . $arrAction[0];
                $func      = $arrAction[1];
                if (class_exists($class)) {
                    $obj = new $class($connection,$arrData);
                    if (method_exists($obj, $func)) {
                        $obj->$func($connection, $arrData['data']);
                    } else {
                        $connection->send($func . '类的方法不存在');
                    }
                } else {
                    $connection->send($class . '类不存在');
                }
            }else{
                $connection->send( json_encode(array('errCode'=>5004,'errData'=>'数据格式错误','data'=>'')) );
            }
        };
        $inner_text_worker->listen();
    }
}
