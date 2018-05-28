# api-doc

### 开源地址
https://github.com/JZhao1020/api-doc

### 使用示例
```php
/**
     * @title 接口名称
     * @description 备注说明
     * @author 作者
     * @url 接口url
     * @method POST|GET（请求类型）
     *
     * @code 200 成功
     * @code 201 失败
     *
     * @param string name 名称 null false
     * @param int age 年龄 null false
     * @return {"code":200,"message":"666","data":[]}
     */
    public function save(){

    }
```

## 安装
```shell
// 使用composer安装
composer require hao/api-doc
```
