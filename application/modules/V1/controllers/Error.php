<?php

class ErrorController extends BaseController {

    //此处需要覆盖base的init
    public function init()
    {

    }

    //从2.1开始, errorAction支持直接通过参数获取异常
    public function errorAction(\Exception $exception) {
        return $this->response($exception->getCode(),$exception->getMessage());

    }
}