<?php
return [
    'title' => "APi接口文档",  //文档title
    'version'=>'1.0.0', //文档版本
    'copyright'=>'Powered By Zhangweiwei', //版权信息
    'controller' => [
        //需要生成文档的类
        'app\reflection\controller\Index',
        'app\reflection\controller\User',
    ],
    'filter_method' => [
        //过滤 不解析的方法名称
        '_empty'
    ],
];
