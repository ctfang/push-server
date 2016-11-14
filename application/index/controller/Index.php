<?php
namespace app\index\controller;


class Index
{
    public function index()
    {
        echo " 发送监听标识：<br>".json_encode(
            array(
                'action' => 'User/bind',
                'data' => array(
                    'user' => '123',
                )
            ),
            JSON_UNESCAPED_UNICODE
        );
        echo " <br>发送推送：<br>".json_encode(
                array(
                    'action' => 'Push/toUser',
                    'data' => array(
                        'toUser' => '123',
                        'sms' => '字符串格式',
                    ),
                    'appid' => '应用id',
                    'noncestr' => '随机',
                    'timestamp' => time(),
                    'signature'=>'签名，',
                ),
                JSON_UNESCAPED_UNICODE
            );
    }
    public function connect()
    {
        return view();
    }
}
