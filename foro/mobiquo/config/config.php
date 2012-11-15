<?php

defined('IN_MOBIQUO') or exit;

function get_mobiquo_config() 
{
    $config_file = './config/config.txt';
    file_exists($config_file) or exit('config.txt does not exists');
    
    if(function_exists('file_get_contents')){
        $tmp = file_get_contents($config_file);
    }else{
        $handle = fopen($config_file, 'rb');
        $tmp = fread($handle, filesize($config_file));
        fclose($handle);
    }
    
    // remove comments by /*xxxx*/ or //xxxx
    $tmp = preg_replace('/\/\*.*?\*\/|\/\/.*?(\n)/si','$1',$tmp);
    $tmpData = preg_split("/\s*\n/", $tmp, -1, PREG_SPLIT_NO_EMPTY);
    
    $mobiquo_config = array();
    foreach ($tmpData as $d){
        list($key, $value) = preg_split("/=/", $d, 2); // value string may also have '='
        $key = trim($key);
        $value = trim($value);
        if ($key == 'hide_forum_id')
        {
            $mobiquo_config[$key] = array();
            $mobiquo_config['hide_forum_str'] = '';
            $value = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
            count($value) and $mobiquo_config[$key] = $value and $mobiquo_config['hide_forum_str'] = join(',', $value);
        }
        elseif ($key == 'mod_function' || $key == 'conflict_mod')
        {
            $mobiquo_config[$key] = array();
            $value = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
            count($value) and $mobiquo_config[$key] = $value;
        }
        else
        {
            strlen($value) and $mobiquo_config[$key] = $value;
        }
    }
    
    return $mobiquo_config;
}

?>