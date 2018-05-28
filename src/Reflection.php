<?php
// +----------------------------------------------------------------------
// | 获取api接口注释
// +----------------------------------------------------------------------
// | 版权所有
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/JZhao1020/api-doc
// +----------------------------------------------------------------------

namespace Api\Doc;

class Reflection{
    private $params = array ();
    public $controller = []; //需要生成接口文档的类路径
    public $filter_method = ['__construct']; //忽略生成的类方法

    protected  $config = [
        'title'=>'APi接口文档',
        'version'=>'1.0.0',
        'copyright'=>'Powered By Zhangweiwei',
        'controller' => [],
        'filter_method'=>['_empty'],
        'return_format' => [
            'status' => "200/300/301/302",
            'message' => "提示信息",
        ]
    ];

    public function __construct($config = []){
        $this->config = array_merge($this->config, $config);

        if(isset($config['controller']))
            $this->controller =  $config['controller'];

        if(isset($config['filter_method']))
            $this->filter_method = array_merge($this->filter_method, $config['filter_method']);//忽略生成的类方法
    }

    /**
     * 使用 $this->name 获取配置
     * @access public
     * @param  string $name 配置名称
     * @return mixed    配置值
     */
    public function __get($name){
        return $this->config[$name];
    }

    /**
     * 设置验证码配置
     * @access public
     * @param  string $name  配置名称
     * @param  string $value 配置值
     * @return void
     */
    public function __set($name, $value){
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getList(){
        $list = [];
        if(!isset($this->controller))
            return [];

        foreach ($this->controller as $key => $val) {
            $reflection = new \ReflectionClass($val);
            $class_doc = $reflection->getDocComment();
            $class_doc = $this->parse($class_doc);

            //只允许生成public方法
            $method = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $action_doc = [];
            if(!isset($method))
                continue;

            foreach ($method as $action) {
                if (!in_array($action->name, $this->filter_method)) {
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
//                $value = $this->formatReturn(json_decode($value,true));
                $value = self::formatJson($value);
            }
            $array[$param] = $value;
        }

        return $array;
    }

    /**
     * 判断参数类型
     * @param $type
     * @return mixed
     */
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

    /**
     * 转换返回数据格式
     * @param string $json
     * @return string
     */
    public static function formatJson($json = '')
    {
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '&nbsp;';
        $newLine = "<br>";
        $prevChar = '';
        $outOfQuotes = true;
        for ($i = 0; $i <= $strLen; $i++) {
            $char = substr($json, $i, 1);
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
            } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            $result .= $char;
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            $prevChar = $char;
        }

        return $result;
    }
}