<?php
/**
 * Created by hisune.com
 * User: 446127203@qq.com
 * Date: 14-7-9
 * Time: 下午5:54
 */
namespace Tiny;

class Dispatch
{
    public $routes;
    private $routeRewrite = false; // 当前请求是否有路由重写

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function controller()
    {
        //session_start();
        if(!isset($_SESSION)){ session_start(); }
        headers_sent() OR header('Content-Type: text/html; charset=utf-8');

        list(Request::$controller, Request::$method, Request::$params) = $this->route();

        if(!class_exists(Request::$controller)){
            if(Config::$error404){
                Request::$controller = Config::$controller['0'] . '\\' . Config::$error404['0'];
                Request::$method = Config::$error404['1'];
                Request::$params = array();
            }else
                Error::print404();
        }else{
            $controllerInstance = new Request::$controller();
            $controllerInstance->initialize(Request::$method);

            if(Request::$params)
                call_user_func_array(array($controllerInstance, Request::$method), Request::$params);
            else
                $controllerInstance->{Request::$method}();
        }

        if(Config::config()->debug)
            \Tiny\Debug::Detail(
                array(
                    array('name' => 'Controller', 'value' => Request::$controller),
                    array('name' => 'Method', 'value' => Request::$method),
                    array('name' => 'params', 'value' => Request::$params),
                )
            );
    }

    /**
     * 路由分发处理
     * @return array controller, method, params
     */
    public function route()
    {
        $path = Url::pathInfo();
        $pathInfo = explode('/', $path);

        if($pathInfo['0'] == '') // 访问的是根目录
            return array(ucfirst(Config::$application) . '\\' . Config::$controller['0'] . '\\Index', 'index', array());
        else{ // 需要路由分发处理
            if($this->routes) { // 有配置路由规则
                if(isset($this->routes[$pathInfo['0']])){ // 普通的子模块目录重写
                    $class = isset($pathInfo['1']) ? preg_replace("/[^0-9a-z_]/i", '', $pathInfo['1']) : 'index';
                    $method = isset($pathInfo['2']) ? preg_replace("/[^0-9a-z_]/i", '', $pathInfo['2']) : 'index';
                    $controller = ucfirst(Config::$application) . '\\' . Config::$controller['0'] . '\\' . ucwords($this->routes[$pathInfo['0']]) . '\\' . ucwords($class);
                    $pathInfo && array_shift($pathInfo);
                    $pathInfo && array_shift($pathInfo);
                    $pathInfo && array_shift($pathInfo);
                    $this->routeRewrite = true;
                }else{ // 更高级的路由分发，需在路由规则里面返回controller, method, pathInfo
                    foreach ($this->routes as $k => $v) {
                        if(is_callable($v)){
                            $pattern = preg_replace('@\{(\w+)\}@', '(?<\1>[^/]+)', $k);
                            preg_match('@^' . $pattern . '$@', $path, $val);
                            if(!$val) continue;

                            $tok = array_filter(array_keys($val), 'is_string');
                            $val = array_map('urldecode', array_intersect_key(
                                $val,
                                array_flip($tok)
                            ));
                            $call = call_user_func_array($v, array($val));
                            $controller = ucfirst(Config::$application) . '\\' . Config::$controller['0'] . '\\' . $call['0']; // 返回自然键值的c,m,p
                            $method = $call['1'];
                            $pathInfo = $call['2'];

                            $this->routeRewrite = true;
                            break; // 只匹配第一个规则
                        }
                    }
                }
            }

            if(!$this->routeRewrite){ // 普通路由分发
                $controller = ucfirst(Config::$application) . '\\' . Config::$controller['0'] . '\\' . ucwords(preg_replace("/[^0-9a-z_]/i", '', $pathInfo['0']));
                $method = isset($pathInfo['1']) ? preg_replace("/[^0-9a-z_]/i", '', $pathInfo['1']) : 'index';
                $pathInfo && array_shift($pathInfo);
                $pathInfo && array_shift($pathInfo);
            }

            return array($controller, $method, $pathInfo);
        }
    }

}