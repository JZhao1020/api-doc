# api-doc

### 开源地址
https://github.com/JZhao1020/api-doc

## 1.安装
```shell
// 使用composer安装
composer require hao/api-doc
```

####2、配置参数
安装好扩展后在 application\extra\ 文件夹下会生成 doc.php 配置文件
在controller参数中添加对应的类
```
    'controller' => [
        'app\\api\\controller\\Demo'
    ]
```

####3、在相关接口类中增加注释参数( group 参数将接口分组，可选)
方法如下：返回参数支持数组及多维数组（参考test文件夹中的Index.php文件）

### 接口备注示例
返回数据格式，仅支持json数据格式
```
<?php
namespace app\reflection\controller;


/**
 * @title 文档index类
 */
class Index{
    public function __construct(){

    }

    /**
     * @title save方法
     * @description
     * @author 作者
     * @url http://www.baidu.com/save.html
     * @method POST
     *
     * @code 200 成功
     * @code 201 失败
     *
     * @param string name 名称 '' false
     * @param int age 年龄 '' false
     * @return {"code":200,"message":"666","data":{"param":1}}
     */
    public function save(){

    }

    /**
     * @title delete方法
     * @description ajax请求时，header带上Authorization信息；Authorization即为user_id token值(两个参数之间，用英文空格)
     * @author 作者
     * @url http://www.baidu.com/delete.html
     * @method POST
     *
     * @code 200 成功
     * @code 201 失败
     *
     * @param string name2 名称2 null false
     * @param int age2 年龄2 null false
     * @return {"code":400,"message":"111","data":[{"param":1}]}
     */
    public function delete(){

    }
}
```
####4、在浏览器访问http://你的域名/doc 查看接口文档

###问题
不少小伙伴反应，没有正常安装doc.php 配置文件，原因是你改过应用目录官方默认是application
如果没有生成doc.php 配置文件 你可以手动安装，直接在application（你修改的目录）里面创建extra文件夹，然后把扩展包中的vendor\hao\api-doc\src\config.php文件复制进去，并重命名为doc.php