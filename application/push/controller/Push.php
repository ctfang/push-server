<?php
/**
 * 推送管理
 * User: selden1992
 * Date: 2016/11/13
 * Time: 22:49
 */

namespace app\push\controller;


class Push
{
    /**
     * 签名
     * @param $connection
     * @param $arrData
     */
    public function __construct($connection,$arrData)
    {
        // $connection->close();
    }
    /**
     * 接受推送
     * @param $connection
     * @param $strData
     */
    public function toUser($connection, $strData)
    {
        if( isset(User::$uidList[$strData['toUser']]) ){
            User::sendUser(User::$uidList[$strData['toUser']], $strData['sms']);
            $connection->send( json_encode(array('errCode'=>0,'errData'=>'OK','data'=>'OK')) );
        }else{
            $connection->send( json_encode(array('errCode'=>4004,'errData'=>'没有用户映射','data'=>'')) );
        }
    }

}