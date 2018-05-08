<?php
// +----------------------------------------------------------------------
// | pay-php-sdk 操作文件的注释
// +----------------------------------------------------------------------
// | 版权所有
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：
// +----------------------------------------------------------------------

namespace Api\Doc;

class Reflection{
    private $params = array ();
    public $filter_method = ['__construct']; //忽略生成的类方法
    /**
     * @param $controller 需要生成接口文档的类路径
     * @param $filter_method 忽略生成的类方法
     * @return array
     * @throws \ReflectionException
     */
    public function doc($controller,$filter_method = []){
        $filter_method = array_merge($this->filter_method,$filter_method);//忽略生成的类方法
        $list = [];
        foreach ($controller as $key => $val) {
            $reflection = new \ReflectionClass($val);
            $class_doc = $reflection->getDocComment();
            $class_doc = $this->parse($class_doc);

            //只允许生成public方法
            $method = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $action_doc = [];
            foreach ($method as $action) {
                if (!in_array($action->name, $filter_method)) {
                    $doc_str = $action->getDocComment();
                    if ($doc_str) {
                        $action_doc[] = $this->parse($doc_str);
                    }
                }
            }

            $list[$key] = ['class_doc' => $class_doc, 'action_doc' => $action_doc];
        }
        return $list;
    }

    /**
     * 解析注释
     * @param string $doc
     * @return array
     */
    public function parse($doc = '') {
        if ($doc == '') {
            return $this->params;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return $this->params;

         $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return $this->params;

        $array = [];
        foreach ($lines[1] as $key => $val){
            $params = $this->parseLine($val);
            if(is_array($params)){
                $kk = array_keys($params);
                $vv = array_values($params);
                if ($kk[0] == 'param') {
                    $array['param'][] = $vv[0];
                } else if ($kk[0] == 'code') {
                    $array['code'][] = $vv[0];
                } else {
                    $array[$kk[0]] = $vv[0];
                }
            }
        }
        return $array;
    }

    /**
     * 解析成数组
     * @param $line
     * @return array|bool
     */
    private function parseLine($line) {
        // trim the whitespace from the line
        $line = trim ( $line );

        if (empty ( $line ))
            return false; // Empty line

        $array = [];
        if (strpos ( $line, '@' ) === 0) {
            if (strpos ( $line, ' ' ) > 0) {
                // Get the parameter name
                $param = substr ( $line, 1, strpos ( $line, ' ' ) - 1 );
                $value = substr ( $line, strlen ( $param ) + 2 ); // Get the value
            } else {
                $param = substr ( $line, 1 );
                $value = '';
            }
            if($param == 'param'){
                $param_arr = explode(' ',$value);
                $value = ['type' => $this->getParamType($param_arr[0]), 'name' => $param_arr[1], 'desc' => isset($param_arr[2]) ? $param_arr[2] : 'null', 'default' => isset($param_arr[3]) ? $param_arr[3] : 'null', 'require' => isset($param_arr[4]) ? $param_arr[4] : 'false'];
            }else if($param == 'code'){
                $param_arr = explode(' ',$value);
                $value = ['code' => $param_arr[0], 'message' => isset($param_arr[1]) ? $param_arr[1] : 'null'];
            }else if($param == 'return'){
                $value = json_decode($value,true);
            }
            $array[$param] = $value;
        }

        return $array;
    }

    private function getParamType($type){
        $typeMaps = [
            'string' => '字符串',
            'int' => '整型',
            'float' => '浮点型',
            'boolean' => '布尔型',
            'date' => '日期',
            'array' => '数组',
            'fixed' => '固定值',
            'enum' => '枚举类型',
            'object' => '对象',
        ];
        return array_key_exists($type,$typeMaps) ? $typeMaps[$type] : $type;
    }
}