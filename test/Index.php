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