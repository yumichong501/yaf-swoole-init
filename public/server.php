<?php
    //禁用错误报告
    //ini_set("display_errors", "On");
    //error_reporting(0);
    //error_reporting(E_ALL ^ E_NOTICE);
    ########################

    //swoole运行前取值
    define('APPLICATION_PATH', dirname(__FILE__) . "/..");
    $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/application.ini', ini_get('yaf.environ'));
    define("IS_VERSION_ON",$config["is_version_on"]);
    define("ZK_VERSION",$config["zk_version"]);
    //加载自定义路由
    $routeConfig = include_once APPLICATION_PATH.'/routes/'.strtolower($config["application.modules"]).'.php';
    Yaf_Registry::set('route_config', $routeConfig);

    //http服务
    if ($config["use_ssl"]){
        $http = new Swoole\Http\Server($config["swoole.host"], $config["swoole.port"],SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
    }else{
        $http = new Swoole\Http\Server($config["swoole.host"], $config["swoole.port"]);
    }
    //http基础配置
    $http->set([
        'ssl_cert_file' =>   $config["sslCertFile"],
        'ssl_key_file' =>   $config["sslKeyFile"],
        'open_http2_protocol' => $config["open_http2_protocol"],
        'reactor_num' => $config["swoole.reactor_num"],
        'worker_num' => $config["swoole.worker_num"],
        'daemonize' => $config["swoole.daemonize"]
    ]);
    unset($config);
    //启动加载
    $http->on('WorkerStart', function ($serv, $worker_id) {
        $application  = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
        $serv->application = $application;
    });
    //请求执行
    $http->on('request', function ($request, $response) use ($http) {
        $application = &$http->application;
        $request_uri = str_replace("/index.php", "", $request->server['request_uri']);

        $yaf_request = new Yaf_Request_Http($request_uri);
        $application->getDispatcher()->setRequest($yaf_request);

        Yaf_Registry::set('swoole_req', $request);
        Yaf_Registry::set('swoole_res', $response);

        // yaf 会自动输出脚本内容，因此这里使用缓存区接受交给swoole response 对象返回
        ob_start();
        $application->getDispatcher()->disableView();
        $application->bootstrap()->run();
        $data = ob_get_clean();
        $response->end($data);
    });

    $http->start();


