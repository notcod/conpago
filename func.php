<?php

function indicum($indicum = '')
{
    if ($indicum != '') return substr(preg_replace('/[^a-zA-Z0-9]+/', '', $indicum), 0, 32);
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < 32; $i++)
        $indicum .= $characters[random_int(0, 61)];
    return $indicum;
}
function clean($i)
{
    return preg_replace('/[^a-zA-Z0-9_]+/', '', str_replace('-', '_', $i));
}
function isExternal($url)
{
    $parse = parse_url($url);
    return !empty($parse['host']) && strcasecmp($parse['host'], isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
}
function isReadable($FILE){
    return create($FILE) && is_file($FILE) && is_readable($FILE) && filesize($FILE) != 0;
}
function cache($url)
{
    $FILE = DIR . $url;
    if (isReadable($FILE))
        return $url . '?' . md6(filemtime($FILE));
    return false;
}
function section($data)
{
    if(empty($data['view']) || empty($data['page'])) return;
    
    $FILE = DIR . "views/$data[view]/$data[page].php";
    if (isReadable($FILE))
        require_once($FILE);
}
function create($FILE)
{
    if (!file_exists($FILE)) {
        $path = explode('/', $FILE);
        array_pop($path);
        $path = implode('/', $path);
        if (!file_exists($path))
            mkdir($path, 0777, true);
        $f = fopen($FILE, "a") or die("Unable to open file! -> " . $FILE);
        fclose($f);
        chmod($FILE, 0777);
    }
    return file_exists($FILE);
}

function exist($url)
{
    $get = substr($url, 0, 4) != 'http' ? url().$url : $url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $get);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result !== FALSE ? $url : false;
}
function md6($q)
{
    return strlen($q) > 0 ? substr(clean(base64_encode(md5($q))), 6, 6) : '';
}
function url()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 0) . "/";
}


// function pathFile($p)
// {
//     if (defined("CREATE_FILE")) return false;
//     $p = str_replace("\\", "/", $p);
//     $d = explode('/', $p);
//     unset($d[count($d) - 1]);
//     createPath(implode('/', $d));
//     $f = fopen($p, "a") or die("Unable to open file! -> " . $p);
//     fclose($f);
//     return $p;
// }
// function uncache($f)
// {
//     $FILE_NAMES = explode('/', $f);
//     $FILE_name = array_pop($FILE_NAMES);
//     $FILE_NAMES = implode('/', $FILE_NAMES);

//     $FILE = DIR . $FILE_NAMES . "/" . md5($FILE_name . ".min.js") . '.Lighter.js';
//     if (is_file($FILE) && is_readable($FILE) && filesize($FILE) != 0)
//         return $FILE_NAMES . "/" . md5($FILE_name . ".min.js") . '.Lighter.js' . '?' . md6(filemtime($FILE));

//     $FILE = DIR . $FILE_NAMES . "/" . md5($FILE_name . ".min.css") . '.Lighter.css';
//     if (is_file($FILE) && is_readable($FILE) && filesize($FILE) != 0)
//         return $FILE_NAMES . "/" . md5($FILE_name . ".min.css") . '.Lighter.css' . '?' . md6(filemtime($FILE));

//     $FILE = DIR . $f;
//     if (is_file($FILE) && is_readable($FILE) && filesize($FILE) != 0)
//         return $f . '?' . md6(filemtime($FILE));

//     return false;
// }
// function getFile($f, $data = [])
// {
//     if (!file_exists(DIR . $f)) pathFile(DIR . $f);
//     if ($f == ".php") return false;
//     if ($f == ".js") return false;
//     if ($f == ".css") return false;
//     if (extension($f) == "php")
//         require_once(DIR . $f);
//     else
//         return uncache($f);
// }

// function createPath($path)
// {
//     $path = str_replace("\\", "/", $path);
//     if (is_dir($path)) return true;
//     echo "\n";
//     $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
//     $return = createPath($prev_path);
//     return ($return && is_writable($prev_path)) ? mkdir($path) : false;
// }

// function extension($d)
// {
//     $ex = explode(".", $d);
//     return end($ex);
// }

