<?php
/**
 * 自动加载器 - 自动加载vendor目录下所有子目录的PHP文件
 * 
 * 该文件实现了一个简单的自动加载机制，可以自动加载vendor目录下的所有PHP文件
 * 使用了SPL自动加载注册和递归目录迭代器来扫描目录结构
 */

// 定义vendor目录的绝对路径
$vendorDir = __DIR__;

/**
 * 自动加载函数 - 根据类名查找并加载对应的PHP文件
 * 
 * @param string $className 需要加载的类名
 * @return void
 */
function autoloader($className) {
    global $vendorDir;
    
    // 将命名空间分隔符和类名中的下划线转换为目录分隔符
    $className = str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $className);
    
    // 构建可能的文件路径
    $possibleFile = $vendorDir . DIRECTORY_SEPARATOR . $className . '.php';
    
    // 如果文件存在，则包含它
    if (file_exists($possibleFile)) {
        require_once $possibleFile;
        return;
    }
    
    // 如果没有直接匹配，尝试在vendor目录下查找匹配的文件名
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($vendorDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    // 获取类名的最后部分（不含命名空间）
    $classBaseName = basename(str_replace('\\', '/', $className));
    
    // 遍历所有文件
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            // 如果文件名与类名匹配（不区分大小写）
            if (strtolower($file->getBasename('.php')) === strtolower($classBaseName)) {
                require_once $file->getPathname();
                return;
            }
        }
    }
};

// 注册自动加载函数
spl_autoload_register('autoloader');

/**
 * 预加载核心类文件
 * 
 * 只预加载必要的核心类文件，其他类将在需要时通过自动加载器加载
 * 
 * @return void
 */
function preloadCoreFiles() {
    global $vendorDir;
    
    // 定义需要预加载的核心类文件
    $coreFiles = [
        $vendorDir . '/Core/Request.php',
        $vendorDir . '/View/Template.php',
        $vendorDir . '/Controller/BaseController.php'
    ];
    
    // 加载核心类文件
    foreach ($coreFiles as $file) {
        if (file_exists($file)) {
            require_once $file;
        }
    }
};

// 执行核心类预加载
preloadCoreFiles();

// 加载配置文件
$configDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;

// 先加载默认配置文件
$defaultConfigFile = $configDir . 'default.inc.php';
if (file_exists($defaultConfigFile)) {
    // 包含默认配置文件，配置文件直接设置$config变量
    require_once $defaultConfigFile;
} else {
    // 如果默认配置文件不存在，初始化空配置
    $config = [];
}

// 检查自定义配置文件是否存在
$customConfigFile = $configDir . 'config.inc.php';
$isConfigExists = file_exists($customConfigFile);

// 如果存在自定义配置文件，则加载并覆盖默认配置
if ($isConfigExists) {
    // 包含自定义配置文件，配置文件中的设置会覆盖默认配置
    require_once $customConfigFile;
}

// 检查是否需要跳转到安装页面
// 排除安装控制器和静态资源请求
$currentController = isset($_GET['controller']) ? strtolower($_GET['controller']) : '';
$isInstallController = ($currentController === 'install');
$isAssetRequest = (strpos($_SERVER['REQUEST_URI'], '/assets/') !== false);

// 如果配置文件不存在，且不是安装控制器或静态资源请求，则跳转到安装页面
if (!$isConfigExists && !$isInstallController && !$isAssetRequest) {
    header('Location: ?controller=install');
    exit;
}

/**
 * 全局CSRF令牌函数 - 生成或获取CSRF令牌
 * 
 * 该函数是Core\Common::csrf_token()方法的全局包装器
 * 
 * @return string CSRF令牌
 */
if (!function_exists('csrf_token')) {
    function csrf_token() {
        return \Core\Common::csrf_token();
    }
}

/**
 * 全局CSRF令牌验证函数 - 验证CSRF令牌
 * 
 * 该函数是Core\Common::verify_csrf_token()方法的全局包装器
 * 
 * @param string $token 要验证的CSRF令牌
 * @return bool 如果令牌有效则返回true，否则返回false
 */
if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token) {
        return \Core\Common::verify_csrf_token($token);
    }
}

// 返回自动加载器函数名，以便可能的外部使用
return 'autoloader';