<?php
//禁用错误报告
//ini_set("display_errors", "On");
//error_reporting(0);
//error_reporting(E_ALL ^ E_NOTICE);
/* 定义这个常量是为了在application.ini中引用*/
define('APPLICATION_PATH', dirname(__FILE__) . "/..");
$application = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
$application->getDispatcher()->disableView();
$application->bootstrap()->run();
