<?php

namespace conpago;

class core
{
    public $data;
    public function __construct($arr = [])
    {
        if (!isset($_SESSION['indicum']) || strlen($_SESSION['indicum']) != 32)
            $_SESSION['indicum'] = indicum();

        define('CSRF', $_SESSION['indicum']);

        $REQ = explode('/', $_GET['URL'] ?? []);

        $directory = 'controller';
        $controller = 'home';
        $method = 'index';

        if (isset($REQ[0]) && !empty($REQ[0])) {
            $controller = array_shift($REQ);
            if ($controller == CSRF) {
                $directory = 'response';
                $controller = array_shift($REQ);
            }
            if (count($REQ)) $method = array_shift($REQ);
        }

        if (isset($arr['controller']) && array_key_exists($controller, $arr['controller'])) {
            $controller = $arr['controller'][$controller];
        }

        if (isset($arr['method']) && array_key_exists($method, $arr['method'])) {
            $method = $arr['method'][$method];
        }

        $CLASS = "\\$directory\\$controller";

        $CLASS = class_exists($CLASS) ? $CLASS : '\\controller\\home';

        if (!class_exists($CLASS)) die('Page not found.');

        $CLASS = new $CLASS;

        $method = method_exists($CLASS, $method) ? $method : 'index';

        if (!method_exists($CLASS, $method)) die('Page not found.');

        $this->data = call_user_func_array([$CLASS, $method], array_values($REQ));
    }
    public function asset($arr, $extension = 'css' | 'js')
    {
        if (empty($this->data['view']) || empty($this->data['page'])) return;

        $data = $this->data;
        $extension = $extension == 'css' ? 'css' : 'js';
        $type = $extension == 'css' ? 'style' : 'script';
        $includes = [];
        if (isset($data[$type]) && count($data[$type]))
            foreach ($data[$type] as $asset)
                if (is_array($arr[$extension]) && array_key_exists($asset, $arr[$extension]))
                    $includes = array_merge($arr[$extension][$asset], $includes);
        if (isset($data['view']) && isset($data['page'])) {
            $includes[] = "/theme/$type/$data[view].$extension";
            $includes[] = "/theme/$type/$data[view]/$data[page].$extension";
        }
        $includes = array_unique($includes);
        foreach ($includes as $inc) {
            $inc = isExternal($inc) ? exist($inc) : cache($inc);
            if ($inc != false) echo $extension == 'css' ? '<link rel="stylesheet" href="' . $inc . '">' . PHP_EOL : '<script defer src="' . $inc . '"></script>' . PHP_EOL;
        }
    }
    public function content()
    {
        if (empty($this->data['view']) || empty($this->data['page'])) return;

        $data = $this->data;
        $FILE = DIR . "views/$data[view].php";
        if (isReadable($FILE)) require_once($FILE);
    }
}
