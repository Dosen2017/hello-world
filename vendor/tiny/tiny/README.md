Hisune Tiny Frame
=========

目录结构(注意某些目录的首字母大写)：
========
.
├── app                         // Hisune Tiny Frame的应用程序，里每个目录代表一个应用程序
│   └── App1                    // App1 code，目录下的推荐目录配置如下：
│       ├── bootstrap           // bootstrap文件，及自定义函数库
│       ├── config              // 配置文件
│       ├── Controller          // Controller继承\Tiny\Controller
│       │   ├── Admin           // 模块举例
│       │   │   └── Index.php   // 子模块Controller
│       │   └── Index.php       // 无模块Controller
│       ├── Model               // Model继承\Tiny\Model
│       ├── var                 // 日志及缓存目录
│       └── view                // 视图文件
├── app1                        // 举例app
│   └── index.php               // app1应用的入口文件
└── vendor                      // composer包，当前只有Hisune Tiny Frame包

只允许app1目录暴露，多应用只需加app2， app3等。

配置
========
config.php配置举例：
###return array(
    'debug' => false, // 是否开启调试模式
    'flag' => 'xxoo', // session唯一标识
    'show_error' => false, // 是否显示错误
    'timezone' => 'PRC', // 时区
    'token' => false, // 自动加token
    'database' => array(
        'dns' => "mysql:host=127.0.0.1;port=3306;dbname=recharge;charset=UTF8", // 主从分离用逗号','隔开
        'username' => 'root',
        'password' => '',
        'prefix' => '',
        'separate' => false, // 主从分离
        'rand_read' => false, // 随机读取
        'log_queries' => false, // 是否记录所有请求
    ),
);

route.php配置举例：
###return array(
    'routes' => array('admin' => 'admin'), // /admin开头的请求Dispatch到Application\Controller\Admin，其他请求自动Dispatch到Controller\Class
);

About
========
Created by [lyx](http://hisune.com)