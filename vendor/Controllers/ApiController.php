<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Controllers;

use Core\Request;
use Core\Validator;
use Core\Common;
use Core\Network;

// 会话已在入口文件中启动

/**
 * ApiController类 - 处理API请求
 * 
 * 该类提供了API接口，用于处理各种请求并返回JSON格式的响应
 * 只返回JSON格式数据，不渲染任何HTML内容
 */
class ApiController extends BaseController {
    /**
     * 构造函数
     * 
     * @param array $config 配置数组
     */
    public function __construct($config = []) {
        parent::__construct($config);
        // 设置响应类型为JSON
        $this->setResponseType(self::RESPONSE_TYPE_JSON);
    }
    
    /**
     * 处理错误
     * 
     * @param string $message 错误消息
     * @param int $statusCode HTTP状态码
     */
    protected function error($message, $statusCode = 400) {
        $this->jsonResponse([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }
    
    /**
     * 覆盖父类的display方法
     * 确保API控制器不会渲染任何HTML内容，而是返回JSON数据
     * 
     * @param string $template 模板名称
     * @param array $data 传递给模板的数据
     */
    public function display($template = null, $data = []) {
        // 不渲染模板，而是返回JSON数据
        parent::display($template, $data);
    }
    
    /**
     * 默认动作 - 返回API状态
     */
    public function indexAction() {
        $this->jsonResponse([
            'status' => 'success',
            'message' => 'API服务正常运行',
            'version' => '1.0.0'
        ]);
    }
    
    /**
     * 处理邮箱注册请求
     * 通过AJAX方式提交注册信息并返回JSON响应
     */
    public function registerAction() {
        // 检查注册功能是否开放
        if (!isset($this->config['openRegister']) || !$this->config['openRegister']) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '注册功能当前未开放'
            ], 403);
        }
        
        // 检查是否为POST请求
        if (!$this->isPost()) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '请求方法不正确'
            ], 405);
        }
        
        // 验证CSRF令牌
        $token = $this->getPostParam('csrf_token');
        if (!verify_csrf_token($token)) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'CSRF令牌验证失败，请刷新页面重试'
            ], 400);
        }
        
        // 获取表单数据
        $username = $this->getPostParam('username');
        $domain = $this->getPostParam('domain');
        $fullName = $this->getPostParam('full_name');
        $password = $this->getPostParam('password');
        $confirmPassword = $this->getPostParam('confirm_password');
        $captcha = $this->getPostParam('captcha');
        
        // 验证表单数据
        $validator = new Validator();
        $minNameLength = isset($this->config['nameLength']) ? $this->config['nameLength'] : 3;
        $validator->validate($username, 'required|min:' . $minNameLength . '|username', '账号名不能为空|账号名长度不能少于' . $minNameLength . '个字符|用户名必须以小写字母开头，且只能包含小写字母和数字');
        $validator->validate($domain, 'required', '后缀不能为空');
        $validator->validate($fullName, 'required', '昵称不能为空');
        $validator->validate($password, 'required|min:8', '密码不能为空|密码长度不能少于8个字符');
        $validator->validate($confirmPassword, 'required|same:' . $password, '确认密码不能为空|两次输入的密码不一致');
        $validator->validate($captcha, 'required', '验证码不能为空');
        
        // 验证验证码
        if (!isset($_SESSION['captcha']) || strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '验证码错误'
            ], 400);
        }
        
        // 如果验证失败，返回错误信息
        if ($validator->hasErrors()) {
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => '表单验证失败',
                'message' => $validator->getErrors()
            ], 400);
        }
        
        // 检查域名是否在排除列表中
        if (isset($this->config['exclude']) && is_array($this->config['exclude']) && in_array($domain, $this->config['exclude'])) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '该域名不允许注册'
            ], 403);
        }
        
        // 构建完整邮箱地址
        $email = $username . '@' . $domain;
        
        // 调用宝塔API创建邮箱
        $apiUrl = $this->config['panel'] . '/plugin?action=a&name=mail_ext&s=add_mailbox';
        
        // 获取邮箱容量设置
        $emailQuota = isset($this->config['emailQuota']) ? $this->config['emailQuota'] : 5;
        $emailQuotaUnit = isset($this->config['emailQuotaUnit']) ? $this->config['emailQuotaUnit'] : 'GB';
        
        // 构建API请求参数，直接合并请求密钥数据和邮箱参数
        $p_data = Common::getKeyData($this->config["apikey"]) + array(
            'username' => $email, // 使用完整的邮箱地址
            'password' => $password,
            'full_name' => $fullName,
            'quota' => $emailQuota . ' ' . $emailQuotaUnit, // 格式为"数字 单位"，如"5 GB"或"500 MB"
            'is_admin' => 0,
            'active' => 1
        );
        
        // 发送API请求
        $apiResponse = Network::curlSenior($apiUrl, 'POST', http_build_query($p_data));
        
        return $this->textResponse($apiResponse);

        // 解析响应
        $response = json_decode($apiResponse, true);
        if (!$response) {
            $response = ['status' => false, 'msg' => '无法解析API响应', 'response' => $apiResponse];
        }
        
        // 解析API响应
        if (isset($response['status']) && $response['status'] || 
            isset($response['code']) && $response['code'] == 200 || 
            isset($response['data']['status']) && $response['data']['status']) {
            // 注册成功
            $successMsg = '';
            if (isset($response['msg'])) {
                $successMsg = $response['msg'];
            } elseif (isset($response['data']['msg'])) {
                $successMsg = $response['data']['msg'];
            } else {
                $successMsg = '邮箱账户创建成功！';
            }
            
            return $this->jsonResponse([
                'status' => 'success',
                'message' => $successMsg,
                'data' => [
                    'email' => $email
                ]
            ]);
        } else {
            // 注册失败
            $errorMsg = '';
            if (isset($response['msg'])) {
                $errorMsg = $response['msg'];
            } elseif (isset($response['data']['msg'])) {
                $errorMsg = $response['data']['msg'];
            } else {
                $errorMsg = '创建邮箱账户失败，请稍后再试或联系管理员。';
            }
            
            return $this->jsonResponse([
                'status' => 'error',
                'message' => $errorMsg
            ], 500);
        }
    }
    
    /**
     * 处理修改密码请求
     * 通过AJAX方式提交修改密码信息并返回JSON响应
     */
    public function replaceAction() {
        // 检查注册功能是否开放
        if (!isset($this->config['openReplace']) || !$this->config['openReplace']) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '改密功能当前未开放'
            ], 403);
        }

        // 检查是否为POST请求
        if (!$this->isPost()) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '请求方法不正确'
            ], 405);
        }
        
        // 验证CSRF令牌
        $token = $this->getPostParam('csrf_token');
        if (!verify_csrf_token($token)) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'CSRF令牌验证失败，请刷新页面重试'
            ], 400);
        }
        
        // 获取表单数据
        $username = $this->getPostParam('username');
        $domain = $this->getPostParam('domain');
        $password = $this->getPostParam('password');
        $newPassword = $this->getPostParam('new_password');
        $confirmPassword = $this->getPostParam('confirm_password');
        $captcha = $this->getPostParam('captcha');
        
        // 验证表单数据
        $validator = new Validator();
        $minNameLength = isset($this->config['nameLength']) ? $this->config['nameLength'] : 3;
        $validator->validate($username, 'required|min:' . $minNameLength . '|username', '账号名不能为空|账号名长度不能少于' . $minNameLength . '个字符|用户名必须以小写字母开头，且只能包含小写字母和数字');
        $validator->validate($domain, 'required', '后缀不能为空');
        $validator->validate($password, 'required', '当前密码不能为空');
        $validator->validate($newPassword, 'required|min:8', '新密码不能为空|新密码长度不能少于8个字符');
        $validator->validate($confirmPassword, 'required|same:' . $newPassword, '确认密码不能为空|两次输入的密码不一致');
        $validator->validate($captcha, 'required', '验证码不能为空');
        
        // 验证验证码
        if (!isset($_SESSION['captcha']) || strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => '验证码错误'
            ], 400);
        }
        
        // 如果验证失败，返回错误信息
        if ($validator->hasErrors()) {
            return $this->jsonResponse([
                'status' => 'error',
                'errors' => '表单验证失败',
                'message' => $validator->getErrors()
            ], 400);
        }
        
        // 构建完整邮箱地址
        $email = $username . '@' . $domain;
        
        // 调用宝塔API更新邮箱密码
        $apiUrl = $this->config['panel'] . '/plugin?action=a&name=mail_ext&s=update_password';
        
        // 构建API请求参数
        $p_data = Common::getKeyData($this->config["apikey"]) + array(
            'username' => $email,
            'password' => $password,
            'new_password' => $newPassword
        );
        
        // 发送API请求
        $apiResponse = Network::curlSenior($apiUrl, 'POST', http_build_query($p_data));
        
        // 解析响应
        $response = json_decode($apiResponse, true);
        if (!$response) {
            $response = ['status' => false, 'msg' => '无法解析API响应', 'response' => $apiResponse];
        }
        
        // 解析API响应
        if (isset($response['status']) && $response['status'] || 
            isset($response['code']) && $response['code'] == 200 || 
            isset($response['data']['status']) && $response['data']['status']) {
            // 修改密码成功
            $successMsg = '';
            if (isset($response['msg'])) {
                $successMsg = $response['msg'];
            } elseif (isset($response['data']['msg'])) {
                $successMsg = $response['data']['msg'];
            } else {
                $successMsg = '密码修改成功！';
            }
            
            return $this->jsonResponse([
                'status' => 'success',
                'message' => $successMsg,
                'data' => [
                    'email' => $email
                ]
            ]);
        } else {
            // 修改密码失败
            $errorMsg = '';
            if (isset($response['msg'])) {
                $errorMsg = $response['msg'];
            } elseif (isset($response['data']['msg'])) {
                $errorMsg = $response['data']['msg'];
            } else {
                $errorMsg = '修改密码失败，请稍后再试或联系管理员。';
            }
            
            return $this->jsonResponse([
                'status' => 'error',
                'message' => $errorMsg
            ], 500);
        }
    }
    
    /**
     * 获取可用域名列表API
     * 返回可用的邮箱域名列表，排除配置中指定的域名
     */
    public function domainsAction() {
        // 调用宝塔API获取域名列表
        $url = $this->config["panel"] . '/plugin?action=a&name=mail_ext&s=get_domain';
        $p_data = Common::getKeyData($this->config["apikey"]);
        
        // 发送API请求
        $result = Network::curlSenior($url, 'POST', http_build_query($p_data));
        $response = json_decode($result, true);
        
        // 处理API响应
        if ($response && isset($response['status']) && $response['status'] && isset($response['data'])) {
            $domains = $response['data'];
            
            // 检查是否有需要排除的域名
            if (isset($this->config['exclude']) && is_array($this->config['exclude']) && !empty($this->config['exclude'])) {
                // 过滤掉需要排除的域名
                $domains = array_filter($domains, function($domain) {
                    return !in_array($domain, $this->config['exclude']);
                });
                
                // 重新索引数组
                $domains = array_values($domains);
            }
            
            return $this->jsonResponse([
                'status' => 'success',
                'data' => $domains
            ]);
        }
        
        // 如果API请求失败，返回错误信息
        return $this->jsonResponse([
            'status' => 'error',
            'message' => '无法获取可用后缀列表，请稍后再试或联系管理员。'
        ], 500);
    }
}