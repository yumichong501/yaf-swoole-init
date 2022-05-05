<?php

/**
 * @name Bootstrap
 * @author {&$AUTHOR&}
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{

	public function _initConfig()
	{
		//把配置保存起来
		$arrConfig = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $arrConfig);
	}

    /**
     * 根据测服或者正服配置不同的错误级别
     */
    public function _initDebug()
    {
        // 如果是测服或者本地开发错误级别为显示所有错误，否则屏蔽所有错误
        if (Yaf_Registry::get('config')->application->env == 'develop') {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ALL & ~(E_NOTICE|E_WARNING));
        }
    }
	/**
	 * 加载Autoload
	 */
	function _initAutoload()
	{
		//ComposerAutoload
		$autoload = APPLICATION_PATH . '/vendor/autoload.php';
		if (file_exists($autoload)) {
			Yaf_Loader::import($autoload);
		}
	}
	public function _initPlugin(Yaf_Dispatcher $dispatcher)
	{

		//注册一个插件
		//$objSamplePlugin = new SamplePlugin();
		//$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher)
	{
		//在这里注册自己的路由协议,默认使用简单路由
        // 包含当前模块下面的路由配置文件
        $routeConfig = Yaf_Registry::get('route_config');
        Yaf_Dispatcher::getInstance()->getRouter()->addConfig(new Yaf_Config_Simple($routeConfig));
	}

	public function _initView(Yaf_Dispatcher $dispatcher)
	{
		//在这里注册自己的view控制器，例如smarty,firekylin
	}
}
