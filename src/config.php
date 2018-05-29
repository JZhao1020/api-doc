<?php
return [
    'is_ip' => true,//true：开启ip认证；false：关闭ip认证
    'ips' => [
        //允许浏览的ip地址
        '127.0.0.1'
    ],
    'controller' => [
        //需要生成文档的类
        'app\reflection\controller\Index',
    ],
    'filter_method' => [
        //过滤 不解析的方法名称
        '_empty'
    ],
];
