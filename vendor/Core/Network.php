<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Core;

/**
 * Network类 - 提供网络请求和HTTP处理功能
 * 
 * 该类包含了处理HTTP请求、解析响应头、处理Cookie等网络相关功能
 */
class Network {
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
    public static function curlSenior(string $url, string $type = 'GET', string $data = '', string $cookie = '', string $userAgent = '', string $encoding = '', string $referer = '', bool $nobody = false, bool $header_opt = false, array $header = []): string
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
    public static function getHeader(string $cont, string $name): ?string
    {
        // 验证输入参数
        if (empty($cont) || empty($name)) {
            return null;
        }

        // 分割header和body
        $page = explode("\r\n\r\n", $cont, 2);
        $headers = $page[0] ?? '';
        
        // 转换为小写以进行不区分大小写的比较
        $nameLower = strtolower($name);
        $ret = null;

        // 处理Set-Cookie和Cookie的特殊情况，因为它们可能有多个值
        if ($nameLower === "set-cookie" || $nameLower === "cookie") {
            // 正则取出对应键值，并使用 preg_quote 防止注入攻击
            // 修改正则表达式以匹配可能没有分号结尾的情况
            $pattern = '/' . preg_quote($name, '/') . ': ([^\r\n]*)/i';
            if (preg_match_all($pattern, $headers, $res)) {
                $ret = implode(';', $res[1]);
            }
        } else {
            // 遍历每一行，寻找对应的header键值
            foreach (explode("\r\n", $headers) as $val) {
                if (strpos($val, ": ") !== false) {
                    [$key, $value] = explode(": ", $val, 2);
                    if (strtolower($key) === $nameLower) {
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
     * @return string|null      返回cookie值，如果未找到或发生错误则返回null
     */
    public static function getCookie(string $cookie, string $name): ?string
    {
        // 输入验证
        if (empty($cookie) || empty($name)) {
            return null;
        }

        try {
            // 使用更可靠的方式解析cookie
            $cookies = [];
            $cookieParts = explode(';', $cookie);
            
            foreach ($cookieParts as $part) {
                $part = trim($part);
                if (strpos($part, '=') !== false) {
                    [$key, $value] = explode('=', $part, 2);
                    $cookies[trim($key)] = trim($value);
                }
            }
            
            // 返回指定键名的值
            return $cookies[$name] ?? null;
        } catch (\Exception $e) {
            // 异常处理
            error_log("Error parsing cookie: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 解析单个cookie字符串为键值对数组
     * @param string $cookie    cookie字符串
     * @return array            解析后的键值对数组
     */
    public static function parseCookie(string $cookie): array {
        $result = [];
        if (empty($cookie)) {
            return $result;
        }
        
        foreach (explode(';', $cookie) as $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') !== false) {
                [$key, $value] = explode('=', $pair, 2);
                $result[trim($key)] = trim($value);
            }
        }
        return $result;
    }

    /**
     * 合并Cookie信息，新的键值会覆盖旧的键值
     * @param string $cookie1   旧的cookie
     * @param string $cookie2   新的cookie
     * @return string           合并后的cookie字符串
     */
    public static function mergeCookie(string $cookie1, string $cookie2): string
    {
        // 输入验证
        if (empty($cookie1) && empty($cookie2)) {
            return '';
        }

        // 清理输入
        $cookie1 = trim($cookie1);
        $cookie2 = trim($cookie2);

        try {
            // 解析并合并两个cookie
            $mergedCookies = array_merge(self::parseCookie($cookie1), self::parseCookie($cookie2));

            // 构建最终的cookie字符串
            $cookiePairs = [];
            foreach ($mergedCookies as $key => $value) {
                // 跳过已删除的cookie
                if ($value !== 'deleted' && $value !== '') {
                    $cookiePairs[] = "$key=$value";
                }
            }

            return implode('; ', $cookiePairs);
        } catch (\Exception $e) {
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
    public static function getHttpStatusCode(string $headerString): ?int 
    {
        // 输入验证
        if (trim($headerString) === '') {
            return null;
        }

        // 获取第一行，它包含了状态码
        $firstLine = explode("\r\n", $headerString, 2)[0] ?? '';

        // 使用正则表达式匹配状态码
        if (preg_match('/^HTTP\/[\d\.]+ (\d+)/', $firstLine, $matches)) {
            return (int)$matches[1];
        }

        // 尝试匹配其他可能的HTTP响应格式
        if (preg_match('/(\d{3})/', $firstLine, $matches)) {
            $code = (int)$matches[1];
            // 验证状态码是否在有效范围内(100-599)
            if ($code >= 100 && $code < 600) {
                return $code;
            }
        }

        // 返回null表示没有找到状态码
        return null;
    }
}