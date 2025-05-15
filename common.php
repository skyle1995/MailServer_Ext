<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 核心函数库，不知道是干啥的就别乱动

// 定义常量
define('ERROR_ACCOUNT_NAME', '电子邮件地址格式不正确，请检查后再试！');
define('ERROR_ALL_FIELDS_REQUIRED', '所有字段参数都必须填写！');
define('ERROR_ADMIN_KEY_INVALID', '校验管理员秘钥失败！');
define('ERROR_CONFIG_ERROR', '配置错误，请联系管理员处理！');
define('ERROR_CSRF_TOKEN', '无效的CSRF令牌');
define('ERROR_EMAIL_NOT_FOUND', '提交邮箱不存在，请检查后再试！');
define('ERROR_INVALID_ACCOUNT', '账号包括小写字母和数字且长度不小于');
define('ERROR_INVALID_CREDENTIALS', '重置失败，邮箱账号或密码不正确！');
define('ERROR_INVALID_EMAIL', '请正确提交邮箱账号！');
define('ERROR_INVALID_HOST', '邮箱后缀不合法，请检查后再试！');
define('ERROR_INVALID_KEY', '请正确提交超级密钥！');
define('ERROR_INVALID_OLD_PASSWORD', '账号或密码错误，请重新尝试！');
define('ERROR_INVALID_PASSWORD', '密码包括大小写字母和数字且长度不小于8');
define('ERROR_LOGIN_FAILURE', '账号密码校验失败，状态异常！');
define('ERROR_REQUEST_FAILED', '请求失败');
define('ERROR_SAME_PASSWORD', '新密码与旧密码相同，取消修改！');
define('ERROR_PASSWORD_REG_DISABLED', '系统已禁止注册邮箱，请联系管理员处理！');
define('ERROR_PASSWORD_RESET_DISABLED', '系统已禁止重置密码，请联系管理员处理！');
define('ERROR_TOKEN_FAILURE', '发生错误，获取Token失败！');
define('ERROR_UNKNOWN', '未知错误');

define('SUCCESS_DELETE_MAILBOX', '删除邮箱成功！');
define('SUCCESS_PASSWORD_RESET', '重置密码成功！');
define('SUCCESS_REGISTER_MAILBOX', '注册邮箱成功！');

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

function isValidEmailDomain($email, $allowedDomains) {
    // 验证输入是否为空或格式不正确
    if (empty($email) || !is_array($allowedDomains) || empty($allowedDomains)) {
        return false;
    }

    // 提取邮箱的域名部分，并转换为小写
    $atPosition = strrpos($email, '@');
    if ($atPosition === false) {
        return false; // 邮箱格式不正确
    }
    // 获取邮箱地址中的域名部分，并转换为小写
    // $email 是用户的邮箱地址
    // $atPosition 是 "@" 符号在邮箱地址中的位置
    $emailDomain = strtolower(substr($email, $atPosition + 1));
    // 将允许的域名转换为小写并创建一个哈希表以提高查找效率
    $allowedDomainsLower = array_flip(array_map('strtolower', $allowedDomains));

    // 检查邮箱域名是否在允许的域名列表中
    return isset($allowedDomainsLower['@'.$emailDomain]);
}


/**
 * 根据邮箱域名或序号查找配置中的邮箱域名序号
 * @param array $hosts 邮箱域名列表
 * @param string|int $key 邮箱域名（如"@163.com"）或序号（从0开始）
 * @return string|null 返回序号（字符串类型）或null（如果未找到）
 */
function findEmailHostIndex(array $hosts, $key) {
    // 检查 hosts 是否为空数组
    if (empty($hosts)) {
        return null;
    }

    // 检查 key 是否为有效的邮箱域名
    if (is_string($key) && preg_match('/^@[\w.-]+$/', $key)) {
        $index = array_search($key, $hosts);
        if ($index !== false) {
            return (string)$index;
        }
        return null;
    }

    // 将字符串索引转换为整数
    $indexAsInt = (int)$key;

    // 获取 hosts 数组的长度
    $hostsLength = count($hosts);

    // 判断转换后的整数索引是否有效
    if ($indexAsInt >= 0 && $indexAsInt < $hostsLength) {
        return (string)$indexAsInt;
    }

    return null;
}

/**
 * 处理消息响应
 *
 * @param mixed $status 响应的状态码
 * @param string $msg 响应的消息
 */
function handleMsg($status, $msg) {
    exit(json_encode(array("status" => $status, "time" => time(), "msg" => $msg)));
}

/**
 * 生成CSRF令牌
 * 如果会话中不存在CSRF令牌，则创建一个长度为64位的随机令牌
 * 该令牌用于保护表单提交，以防止跨站请求伪造（CSRF）攻击
 * 
 * @return string 当前会话的CSRF令牌
 */
function generateCsrfToken() {
    if (!isset($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/**
 * 验证CSRF令牌
 * 比较给定的令牌是否与会话中的令牌相等
 * 使用hash_equals函数进行安全比较，以防止时间攻击
 * 
 * @param string $token 需要验证的CSRF令牌
 * @return bool 如果给定的令牌与会话中的令牌匹配，则返回true，否则返回false
 */
function validateCsrfToken($token) {
    return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

/**
 * 清理输入数据
 * 使用trim函数去除输入数据两端的空白字符，然后使用htmlspecialchars函数转换特殊字符为HTML实体
 * 这样可以防止XSS攻击，确保输入的数据在HTML环境中安全展示
 * 
 * @param string $input 需要清理的输入数据
 * @return string 清理后的输入数据
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * 验证表单输入字段
 * 检查每个指定的字段是否已提交且不为空
 * 如果任何一个字段未提交或为空，则终止脚本并返回错误信息
 * 
 * @param array $fields 需要验证的表单字段数组
 */
function validateFormInputs($p_data, $fields) {
    foreach ($fields as $field) {
        if (!isset($p_data[$field]) || trim($p_data[$field]) === '') {
            handleMsg(false, ERROR_ALL_FIELDS_REQUIRED);
        }
    }
}

/**
 * 检验账户名是否有效
 * 
 * 该函数通过正则表达式检查账户名是否仅包含小写字母和数字
 * 然后检查账户名的长度是否至少为指定长度
 * 
 * @param string $str 账户名字符串
 * @param int $length 账户名的最小长度
 * @return bool 如果账户名有效则返回true，否则返回false
 */
function AccountConfirmation($str, $length) {
    // 检查账户名是否仅由小写字母和数字组成
    if (preg_match('/[a-z0-9]+$/', $str)) {
        // 检查账户名长度是否至少为指定长度
        if(strlen($str) >= $length) return true;
        return false;
    } else {
        return false;
    }
}

/**
 * 检验密码是否符合复杂性要求
 * 
 * 该函数通过正则表达式检查密码是否至少包含一个大写字母、一个小写字母和一个数字
 * 还检查密码是否至少包含一个特殊字符（限于!@#.），并且长度至少为8个字符
 * 
 * @param string $str 密码字符串
 * @return bool 如果密码符合要求则返回true，否则返回false
 */
function PasswordConfirmation($str) {
    // 检查密码是否符合复杂性要求：至少一个大写字母、一个小写字母、一个数字和一个特殊字符（!@#.）
    if (preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[a-zA-Z0-9!@#.]+$/', $str)) {
        // 检查密码长度是否至少为8个字符
        if(strlen($str) >= 8) return true;
        return false;
    } else {
        return false;
    }
}

/**
 * 生成请求密钥数据
 *
 * 该函数根据给定的API密钥生成一个包含请求令牌和请求时间的数组
 * 请求令牌是通过对当前时间戳和API密钥的MD5值进行再次MD5处理生成的
 * 这种方式旨在提供一个简单的方法来验证请求的合法性和时间性
 *
 * @param string $apikey API密钥，用于生成请求令牌
 * @return array 包含请求令牌（request_token）和请求时间（request_time）的数组
 */
function get_key_data($apikey) {
    // 获取当前时间戳
    $now_time = time();
    
    // 返回包含请求令牌和请求时间的数组
    // 请求令牌通过将当前时间戳与API密钥的MD5值连接后再次进行MD5加密生成
    // 这样做可以确保每个请求的唯一性和时间性，用于验证请求的合法性和时效性
    return array(
        'request_token' => md5($now_time . md5($apikey)),
        'request_time' => $now_time,
    );
}


/**
 * 高级curl请求函数
 * 用于发起HTTP请求，包含多种可配置选项，以满足不同的请求需求
 * 
 * @param string $url 请求的URL地址
 * @param string $type 请求类型，默认为'GET'，支持'GET', 'POST', 'HEAD', 'PUT', 'OPTIONS', 'DELETE', 'TRACE', 'CONNECT'
 * @param string $data POST请求时发送的数据，默认为空
 * @param string $cookie 请求时携带的cookie信息，默认为空
 * @param string $userAgent 自定义的User-Agent信息，默认为空
 * @param string $encoding 请求编码，支持'all'表示发送所有支持的编码类型，默认为空
 * @param string $referer HTTP请求头中的Referer信息，特殊值'1'表示使用当前服务器主机名，默认为空
 * @param bool $nobody 启用时将不对HTML中的body部分进行输出，默认为false
 * @param bool $header_opt 启用时会将头文件的信息作为数据流输出，默认为false
 * @param array $header 自定义的HTTP头信息数组，默认为空数组
 * 
 * @return string 返回请求的结果
 */
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
 * @return string|null      返回对应的header值，如果未找到则返回null
 */
function getHeader(string $cont, string $name): ?string
{
    // 验证输入参数
    if (empty($cont) || empty($name)) {
        return null;
    }

    // 分割header和body
    $page = explode("\r\n\r\n", $cont, 2);
    if (count($page) < 2) {
        $headers = $page[0];
    } else {
        $headers = $page[0];
    }

    $ret = null;

    // 处理Set-Cookie和Cookie的特殊情况，因为它们可能有多个值
    if (strtolower($name) == "set-cookie" || strtolower($name) == "cookie") {
        // 正则取出对应键值，并使用 preg_quote 防止注入攻击
        $pattern = '/' . preg_quote($name, '/') . ': (.*?);/iU';
        preg_match_all($pattern, $headers, $res);
        $ret = implode(';', $res[1]);
    } else {
        // 遍历每一行，寻找对应的header键值
        foreach (explode("\r\n", $headers) as $val) {
            if (strpos($val, ": ") !== false) {
                list($key, $value) = explode(": ", $val, 2);
                if (strtolower($key) === strtolower($name)) {
                    $ret = $value;
                    break; // 找到后立即返回，避免不必要的循环
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
 * @return string|null
 */
function getCookie(string $cookie, string $name): ?string
{
    // 输入验证
    if (empty($cookie) || empty($name)) {
        return null;
    }

    try {
        // 解析 cookie 字符串为数组
        $cookies = [];
        parse_str(str_replace([';', ' '], '&', $cookie), $cookies);

        // 返回指定键名的值
        return $cookies[$name] ?? null;
    } catch (Exception $e) {
        // 异常处理
        error_log("Error parsing cookie: " . $e->getMessage());
        return null;
    }
}

/**
 * 合并Cookie信息，新的键值会覆盖旧的键值
 * @param string $cookie1   旧的cookie
 * @param string $cookie2   新的cookie
 * @return string
 */
function mergeCookie(string $cookie1, string $cookie2): string
{
    // 输入验证
    if (empty($cookie1) && empty($cookie2)) {
        return '';
    }

    // 清理输入
    $cookie1 = trim($cookie1);
    $cookie2 = trim($cookie2);

    // 初始化结果数组
    $cookie3 = [];

    // 辅助函数：解析单个cookie字符串为键值对数组
    function parseCookie(string $cookie): array {
        $result = [];
        foreach (explode(';', $cookie) as $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') !== false) {
                list($key, $value) = explode('=', $pair, 2);
                $result[trim($key)] = trim($value);
            }
        }
        return $result;
    }

    try {
        // 解析并合并两个cookie
        $cookie3 = array_merge(parseCookie($cookie1), parseCookie($cookie2));

        // 构建最终的cookie字符串
        $cookiePairs = [];
        foreach ($cookie3 as $key => $value) {
            if ($value !== 'deleted') {
                $cookiePairs[] = "$key=$value";
            }
        }

        return implode(';', $cookiePairs);
    } catch (Exception $e) {
        // 异常处理
        error_log("Error merging cookies: " . $e->getMessage());
        return '';
    }
}

/**
 * 从HTTP响应头字符串中提取HTTP状态码
 * 
 * @param string $headerString HTTP响应头字符串
 * @return int|null HTTP状态码，如果无法解析或输入无效则返回null
 */
function getHttpStatusCode($headerString) {
    // 输入验证
    if (!is_string($headerString) || trim($headerString) === '') {
        return null;
    }

    // 获取第一行，它包含了状态码
    $firstLine = explode("\r\n", $headerString, 2)[0] ?? '';

    // 使用正则表达式匹配状态码
    if (preg_match('/^HTTP\/[\d\.]+ (\d+)/', $firstLine, $matches)) {
        return (int)$matches[1];
    }

    // 返回null表示没有找到状态码
    return null;
}