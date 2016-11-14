<?php
/**
 * 连接用户管理
 * User: selden1992
 * Date: 2016/11/13
 * Time: 20:11
 */

namespace app\push\controller;


use Workerman\Lib\Timer;

class User
{
    /**
     * User constructor.
     * @param $connection
     * @param $arrData
     */
    public function __construct($connection, $arrData)
    {
        // 控制
        // $connection->close();
    }

    public static $uidList = array();
    /**
     * 新增用户映射
     * @param $connection
     * @param $strData
     */
    public function bind($connection,$strData)
    {
        if( $strData['user'] ){
            Timer::del($connection->auth_timer_id);// 验证成功，删除定时器，防止连接被关闭
            $connection->user = $strData['user'];
            self::$uidList[ $connection->user ] = $connection;
            $connection->send( json_encode(array('errCode'=>0,'errData'=>'OK','data'=>'OK')) );
        }else{
            $connection->send( json_encode(array('errCode'=>4004,'errData'=>'user 参数缺失','data'=>'')) );
        }
    }

    /**
     * 向uid发送信息
     * @param $connection
     * @param $strData
     */
    public static function sendUser($connection,$strData)
    {
        $connection->send( $strData );
    }

    /**
     * 删除映射
     * @param $connection
     */
    public static function delete($connection)
    {
        if( isset($connection->user) && isset(self::$uidList[$connection->user]) ){
            unset(self::$uidList[$connection->user]);
        }
    }
}