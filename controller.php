<?php

namespace conpago;

class controller
{
    public $DB;
    public function __construct()
    {
        $this->DB = new \conpago\database();
    }
    public function view($arr = [])
    {
        $this->data['style'] = $this->data['style'] ?? [];
        $this->data['script'] = $this->data['script'] ?? [];
        $this->data['description'] = $arr['description'] ?? SITENAME;
        $this->data['keywords'] = $arr['keywords'] ?? SITENAME;
        $this->data['page'] = $arr['page'] ?? (debug_backtrace())[1]['function'];
        $this->data['view'] = strtolower($arr['view'] ?? substr(get_class($this), strrpos(get_class($this), '\\') + 1));

        return array_merge($this->data, $arr);
    }
    public function set($z)
    {
        foreach ($z as $v) {
            $variable = is_array($v) ? array_shift($v) : $v;
            $val = isset($_POST[$variable]) ? trim(htmlspecialchars($_POST[$variable])) : '';
            if (is_array($v))
                foreach ($v as $fu)
                    if (is_array($fu))
                        foreach ($fu as $fc) $val = call_user_func_array($fc, [$val]);
                    else
                        $val = call_user_func_array($fu, [$val]);
            $this->_req[clean($variable)] = $this->DB->escape($val);
        }
    }
    function setArray($z)
    {
        foreach ($z as $v)
            $this->_req[$v] = post($v);
    }
    function setFiles($z)
    {
        foreach ($z as $v)
            $this->_req[$v] = $_FILES[$v] ?? [];
    }
    function json($data, $t = false)
    {
        header('Content-Type: application/json');
        if ($t) die(json_encode($data, JSON_PRETTY_PRINT));
        $output = ['message' => $data, "error" => false];
        if (is_array($data))
            $output = isset($data['success']) ? ['message' => $data["success"], "error" => true] : ['message' => $data[0], 'field' => $data[1], "error" => false];
        die(json_encode($output, JSON_PRETTY_PRINT));
    }
    function check($arr = [])
    {
        $r = (object) $this->_req;
        $r->_errors = [];
        foreach ($arr as $v => $s)
            if ((isset($this->_req[$v]) && !empty($this->_req[$v]) ? $this->_req[$v] : false) == false) {
                $r->_errors[] = $s;
                $r->_fields[] = $v;
            }
        return $r;
    }
}
