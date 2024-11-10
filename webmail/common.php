<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 核心函数库，不知道是干啥的就别乱动

function get_md5($s) {
    return md5($s);
}

function get_key_data($apikey) {
    $now_time = time();
    return array(
        'request_token' => get_md5($now_time . get_md5($apikey)),
        'request_time' => $now_time,
    );
}

function AccountConfirmation($str, $length) {
    if (preg_match('/[a-z0-9]+$/', $str)) {
        if(strlen($str) >= $length) return true;
        return false;
    } else {
        return false;
    }
}

function PasswordConfirmation($str) {
    if (preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[a-zA-Z0-9!@#.]+$/', $str)) {
        if(strlen($str) >= 8) return true;
        return false;
    } else {
        return false;
    }
}

function generateRandomPassword($length = 10) {
    $upperCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowerCase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $allCharacters = $upperCase . $lowerCase . $numbers;
    $randomString = '';
    
    // 确保字符串中至少包含一个大写字母、一个小写字母和一个数字
    $randomString .= $upperCase[rand(0, strlen($upperCase) - 1)];
    $randomString .= $lowerCase[rand(0, strlen($lowerCase) - 1)];
    $randomString .= $numbers[rand(0, strlen($numbers) - 1)];
    
    // 填充剩余的字符
    $remainingLength = $length - 3;
    for ($i = 0; $i < $remainingLength; $i++) {
        $randomString .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
    }
    
    // 打乱字符串中的字符顺序
    $randomString = str_shuffle($randomString);
    
    return $randomString;
}

function curl_senior(string $url, string $type = 'GET', string $data = '', string $cookie = '', string $userAgent = '', string $encoding = '', string $referer = '', bool $nobody = false, bool $header_opt = false, array $header = []): string
{
    // 初始化请求
    $ch = curl_init();

    // 设置请求的url
    curl_setopt($ch, CURLOPT_URL, $url);

    // 设置提交方式信息 GET POST HEAD PUT OPTIONS DELETE TRACE CONNECT
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,$type);

    // 设置提交内容
    if(!empty($data)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    // 设置header信息
    if(sizeof($header) == 0) {
        $header = [];
        $header[] = "Accept: */*";
        $header[] = "Accept-Language: zh-CN,zh;q=0.8";
        $header[] = "Connection: close";
        $header[] = "Connection: keep-alive";
        $header[] = "User-Agent: Mozilla/5.0 Chrome/99.0.4844.51 Safari/537.36 Edg/99.0.1150.36";
        $header[] = "referer: ".$url;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    // 设置cookie信息
    if(!empty($cookie)) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }

    // HTTP请求头中"Accept-Encoding: "的值，支持的编码有"identity"，"deflate"和"gzip"。如果为空字符串""，会发送所有支持的编码类型
    if(!empty($encoding)){
        if($encoding == "all"){
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }else{
            curl_setopt($ch, CURLOPT_ENCODING, $encoding);
        }
    }

    // 设置自定义User-Agent
    if(!empty($userAgent)){
        curl_setopt($ch, CURLOPT_USERAGENT,$userAgent);
    }

    // 在HTTP请求头中"Referer:"的内容（可自定义）
    if(!empty($referer)){
        if($referer==1){
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        }else{
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
    }

    // 启用时将不对HTML中的body部分进行输出
    if($nobody){
        curl_setopt($ch, CURLOPT_NOBODY,true);
    }

    // 启用时会将头文件的信息作为数据流输出
    if($header_opt){
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    }

    // 忽略SSL安全性
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // 设为true把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 禁止请求重定向
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    // 开始请求
    $ret = curl_exec($ch);

    // 关闭URL请求
    curl_close($ch);

    // 返回结果
    return $ret;
}

/**
 * 获取header中的参数信息
 * @param string $cont      未处理的header信息
 * @param string $name      提取的header键名
 * @return string
 */
function getHeader(string $cont, string $name): ?string
{
    // 分割header和body
    $page =  explode("\r\n\r\n",$cont,2);

    $ret = null;
    if(strtolower($name) == "set-cookie" or strtolower($name) == "cookie"){
        // 正则取出对应键值
        preg_match_all('/'.$name.': (.*);/iU', $page[0],  $res);
        $ret = implode(';', $res[1]);
    } else {
        foreach (explode("\r\n",$page[0]) as $val){
            if(strpos($val, ": ")){
                $obj = explode(": ",$val);
                if($obj[0] == $name){
                    $ret = $obj[1];
                }
            }
        }
    }
    return $ret;
}

/**
 * 获取cookie键值
 * @param string $cookie    完整cookie信息
 * @param string $name      取出cookie键名
 * @return string
 */
function getCookie(string $cookie, string $name): ?string
{
    $ret = null;
    $str = str_replace("; ", ";", $cookie);
    foreach (explode(";",$str) as $val){
        if(strpos($val, "=")){
            $obj = explode("=",$val);
            if($obj[0] == $name){
                $ret = $obj[1];
            }
        }
    }
    return $ret;
}

/**
 * 合并Cookie信息，新的键值会覆盖旧的键值
 * @param string $cookie1   旧的cookie
 * @param string $cookie2   新的cookie·
 * @return string
 */
function mergeCookie(string $cookie1, string $cookie2): string
{
    $cookie1 = str_replace("; ", ";", $cookie1);
    $cookie2 = str_replace("; ", ";", $cookie2);
    $cookie3 = [];

    // 将第一个cookie分割成数组
    foreach (explode(";",$cookie1) as $val){
        if(strpos($val, "=")){
            $obj = explode("=",$val);
            $cookie3[$obj[0]]=$obj[1];
        }
    }
    // 将第二个cookie分割成数组
    foreach (explode(";",$cookie2) as $val){
        if(strpos($val, "=")){
            $obj = explode("=",$val);
            $cookie3[$obj[0]]=$obj[1];
        }
    }

    $Cookie = null;
    foreach ($cookie3 as $key=>$val){
        if($val!="deleted"){
            $Cookie .= $key."=".$val.";";
        }
    }
    return substr($Cookie, 0, -2);
}

function getHttpStatusCode($headerString) {
    // 通过换行符分割头信息为单独的行
    $lines = explode("\r\n", $headerString);
    // 获取第一行，它包含了状态码
    $firstLine = $lines[0];
    // 使用正则表达式匹配状态码
    preg_match('/^HTTP\/[\d\.]+ (\d+)/', $firstLine, $matches);
    // 返回状态码，如果不存在则返回null
    return isset($matches[1]) ? (int)$matches[1] : null;
}

function CheckMailbox($imap, $username, $password){
    $state = true;
    
    $hostname = '{'.$imap.'/imap/ssl}INBOX';
    
    // 解析hostname获取host和port
    $host = preg_replace('/{(.*)}/', '$1', $hostname);
    $host = str_replace(array('imap/', 'ssl}', 'ssl}', 'imap/', '{'), '', $host);
    list($host, $port) = explode(':', $host);
    $port = $port ?: 143; // 默认端口为143
    
    // 创建socket
    $socket = stream_socket_client("ssl://$host:$port", $errno, $errstr, 30);
    if (!$socket) {
        die("$errstr ($errno)\n");
    }
    
    // 读取欢迎信息
    $response = fgets($socket, 512);
    // echo "Server: $response\n";
    
    // 登录
    fwrite($socket, "A001 LOGIN $username $password\r\n");
    $response = fgets($socket, 512);
    // echo "Login response: $response\n";
    if (strpos($response, 'OK') === false) $state = false;
    
    // 选择邮箱
    fwrite($socket, "A002 SELECT INBOX\r\n");
    $response = fgets($socket, 512);
    // echo "Select response: $response\n";
    if (strpos($response, 'FLAGS') === false) $state = false;
    
    // 获取邮箱状态
    fwrite($socket, "A003 STATUS INBOX (MESSAGES)\r\n");
    $response = fgets($socket, 512);
    // echo "Status response: $response\n";
    if (strpos($response, 'OK') === false) $state = false;
    
    // 登出
    fwrite($socket, "A004 LOGOUT\r\n");
    $response = fgets($socket, 512);
    // echo "Logout response: $response\n";
    if (strpos($response, 'EXISTS') === false) $state = false;
    
    // 关闭socket
    fclose($socket);
    
    return $state;
}

// 检查邮箱是否有效
function isValidEmailDomain($email, $allowedDomains) {
    // 提取邮箱的域名部分
    $emailDomain = substr(strrchr($email, '@'), 1);

    // 遍历允许的后缀数组
    foreach ($allowedDomains as $domain) {
        // 如果邮箱域名与允许的后缀匹配
        if (strtolower($emailDomain) === strtolower(substr($domain, 1))) {
            return true; // 返回true，表示邮箱域名有效
        }
    }

    return false; // 没有找到匹配的后缀，返回false
}

/**
 * 根据邮箱域名或序号查找配置中的邮箱域名序号
 * @param string|int $key 邮箱域名（如"@163.com"）或序号（从0开始）
 * @return string|null 返回序号（字符串类型）或null（如果未找到）
 */
function findEmailHostIndex($hosts, $key) {
    if (strpos($key, '@') === 0) {
        $index = array_search($key, $hosts);
        if ($index !== false) {
            return $index;
        } else {
            return null;
        }
    } else {
        if($key === "0"){
            return 0;
        }
        
        // 将字符串索引转换为整数
        $indexAsInt = (int)$key;
        if($indexAsInt === 0){
            return null;
        }

        // 获取hosts数组的长度
        $hostsLength = count($hosts);
        // 判断转换后的整数索引是否有效
        if ($indexAsInt >= 0 && $indexAsInt < $hostsLength) {
            return $indexAsInt;
        } else {
            return null;
        }
    }
}