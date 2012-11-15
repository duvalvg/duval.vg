<?php

error_reporting(E_ALL & ~E_NOTICE);

if (isset($_GET['checkip']))
{
    print do_post_request(array('ip' => 1));
}
else
{
    echo 'Tapatalk push notification test: <b>';
    $return_status = do_post_request(array('test' => 1));
    
    if ($return_status === '1')
        echo 'Success</b>';
    else
        echo 'Failed</b><br />'.$return_status;
}

function do_post_request($data)
{
    $push_url = 'http://push.tapatalk.com/push.php';
    
    $response = 'CURL is disabled and PHP option "allow_url_fopen" is OFF. You can enable CURL or turn on "allow_url_fopen" in php.ini to fix this problem.';
    if (function_exists('curl_init'))
    {
        $ch = curl_init($push_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
    }
    elseif (ini_get('allow_url_fopen'))
    {
        $params = array('http' => array(
            'method' => 'POST',
            'content' => http_build_query($data, '', '&'),
        ));
        
        $ctx = stream_context_create($params);
        $fp = @fopen($push_url, 'rb', false, $ctx);
        if (!$fp) return false;
        $response = @stream_get_contents($fp);
    }
    
    return $response;
}