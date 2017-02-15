<?php
/**
 * Created by sudesheng.
 * User: 2066815257@qq.com
 * Date: 15-7-17
 * Time: 下午3:45
 *
 * 简约为原则的高性能框架，包含：路由，简单权限验证，cookie，session，ORM，view，validation
 * 建议开启opcache：http://www.hisune.com/post/view/10/php-opcache-install-config-performance-test
 */
// define application name
define('APPLICATION', 'mxserver');

// Require bootstrap
require __DIR__ . '/../app/' . ucfirst(APPLICATION) . '/bootstrap/autoload.php';

// Run
try{

    $dispatch = new \Tiny\Dispatch(\Tiny\Config::route()->routes);
    $dispatch->controller();

}catch (Exception $e){

    \Tiny\Exception::exception($e);

}