<?php


class TestController extends BaseController
{

    public function indexAction(){

        return $this->response(0, "ok", $this->params);
    }
}