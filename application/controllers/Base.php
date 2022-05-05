<?php

/**
 * 基础控制器
 * Class BaseController
 */
class BaseController extends Yaf_Controller_Abstract
{
    public $params = [];
    /**
     * 里面做了自动加载自己的业务层
     * 控制器初始化
     */
    public function init()
    {
        $this->params = $this->getRawParam();
    }

    /**
     * 获取raw请求参数
     */
    public function getRawParam(){
        $rawContent = Yaf_Registry::get("swoole_req")->rawContent();
        return json_decode($rawContent,true);
    }

    /**
     * 通用信息返回
     * @param $code
     * @param string $message
     * @param array $data
     * @return mixed
     */
    public function response($code, $data = [], $message="")
    {
        if ($data === true || $data === false){
            $data = (object)[];
        }
        $message = $message?$message:"操作失败";
        return $this->setContent($code, $data,$message);
    }

    /**
     * 返回信息体
     * @param $code
     * @param $message
     * @param $data
     * @return mixed
     */
    private function setContent($code, $data, $message="操作失败")
    {
        $content = [
            'code' => (int)$code,
            'data' => $data,
            'message' => $message
        ];
        echo json_encode($content,JSON_UNESCAPED_UNICODE);
    }

    /**
     * swoole http2请求
     * @param $host
     * @param $port
     * @param $path
     * @param $param json格式
     */
    public function swoole_http2_post($host,$port,$path, $param = ''){
        $client = new Swoole\Coroutine\Http2\Client($host,$port, true);
        $client->set([
            'timeout' => -1,
        ]);
        $client->connect();
        $req2 = new Swoole\Http2\Request();
        $req2->method =  'POST';
        $req2->path = $path;
        $req2->headers = [
            'accept' => 'application/json',
            "Content-Type"=>"application/json"
        ];
        $req2->data = $param;
        $client->send($req2);
        $res2 = $client->recv();
        $rs = json_decode($res2->data, true);
        $client->close();

        return $rs;
    }

    //curl http2 post
    public function curl_http_post($url, $param = '',$is_ssl=1){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($is_ssl == 1)
            curl_setopt($ch, CURLOPT_HTTP_VERSION, 3); //3为http2
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen( $param)
            )
        );
        $result = curl_exec($ch); // 执行操作
        $rs = json_decode($result,true);

        curl_close($ch);
        return $rs;
    }


}
