<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : '邮件服务器插件安装'; ?></title>
    <!-- 网站图标 -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- 自定义样式 -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main>
        <div class="container">
            <div class="content-wrapper">
                <div class="logo-container">
                    <img src="assets/images/logo.svg" alt="Logo">
                </div>
                <div class="col-md-8">
                    <div class="panel panel-primary" style="max-width: 480px; margin: 0 auto; border-radius: 6px; box-shadow: none;">
                        <div class="panel-heading" style="text-align: center; border-radius: 6px 6px 0 0; border-bottom: 0px solid rgb(120, 194, 255);">
                            <h3 class="panel-title">系统安装</h3>
                        </div>
                        <div class="panel-body">
                            <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <strong>错误：</strong> <?php echo $error; ?>
                            </div>
                            <?php else: ?>
                            <form method="post" action="?controller=install&action=save">
                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                <div class="form-group">
                                    <label for="sitename">站点名称</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-globe"></i></span>
                                        <input type="text" class="form-control" id="sitename" name="sitename" placeholder="Anonymous Online Webmail" value="" required>
                                    </div>
                                    <p class="help-block">请输入您的站点名称，例如：Anonymous Online Webmail</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="panel">宝塔面板地址</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-link"></i></span>
                                        <input type="text" class="form-control" id="panel" name="panel" placeholder="https://127.0.0.1:8888" value="" required>
                                    </div>
                                    <p class="help-block">请输入您的宝塔面板访问地址，例如：https://127.0.0.1:8888</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="apikey">API接口密钥</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-key"></i></span>
                                        <input type="text" class="form-control" id="apikey" name="apikey" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="" required>
                                    </div>
                                    <p class="help-block">请在宝塔面板API接口中获取密钥</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="webmail">邮箱Webmail登录地址</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                                        <input type="text" class="form-control" id="webmail" name="webmail" placeholder="https://127.0.0.1" value="">
                                    </div>
                                    <p class="help-block">请输入您的Webmail访问地址，留空则不显示登录邮箱按钮</p>
                                </div>
                                <div class="form-group">
                                    <label>功能开关</label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="openRegister" <?php echo isset($config['openRegister']) && $config['openRegister'] ? 'checked' : ''; ?>> 开启注册邮箱功能
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="openReplace" <?php echo isset($config['openReplace']) && $config['openReplace'] ? 'checked' : ''; ?>> 开启修改密码功能
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nameLength">注册名称最低长度</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-text-width"></i></span>
                                        <input type="number" class="form-control" id="nameLength" name="nameLength" value="<?php echo isset($config['nameLength']) ? $config['nameLength'] : '5'; ?>" min="1" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="emailQuota">邮箱容量</label>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fas fa-hdd"></i></span>
                                                <input type="number" class="form-control" id="emailQuota" name="emailQuota" value="<?php echo isset($config['emailQuota']) ? $config['emailQuota'] : '5'; ?>" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <select class="form-control" name="emailQuotaUnit" style="height: 40px;">
                                                <option value="MB" <?php echo isset($config['emailQuotaUnit']) && $config['emailQuotaUnit'] === 'MB' ? 'selected' : ''; ?>>MB</option>
                                                <option value="GB" <?php echo isset($config['emailQuotaUnit']) && $config['emailQuotaUnit'] === 'GB' ? 'selected' : ''; ?>>GB</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="superKey">超级密钥</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-lock"></i></span>
                                        <input type="text" class="form-control" id="superKey" name="superKey" value="" required>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary" style="height: 40px;" onclick="generateRandomKey()"><i class="fas fa-random"></i> 随机生成</button>
                                        </span>
                                    </div>
                                    <p class="help-block">用于API对接认证，请设置复杂密钥并妥善保管</p>
                                </div>
                                <div class="form-group">
                                    <label for="exclude">域名过滤表（每行一个域名）</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-filter"></i></span>
                                        <textarea class="form-control" id="exclude" name="exclude" rows="5"></textarea>
                                    </div>
                                    <p class="help-block">这些域名将被隐藏或禁用</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="footer">底部版权信息</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-copyright"></i></span>
                                        <input type="text" class="form-control" id="footer" name="footer" placeholder="© <?php echo date('Y'); ?> Anonymous Online Webmail. 保留所有权利。" value="">
                                    </div>
                                    <p class="help-block">显示在网站底部的版权信息</p>
                                </div>
                                
                                <div class="form-group" style="text-align: center; margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-lg">保存配置并安装</button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <script>
                    /**
                     * 生成16位随机字符串（包含大小写英文字母和数字）
                     */
                    const generateRandomKey = () => {
                        // 定义可能的字符集
                        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                        let result = '';
                        
                        // 生成16位随机字符
                        for (let i = 0; i < 16; i++) {
                            const randomIndex = Math.floor(Math.random() * chars.length);
                            result += chars.charAt(randomIndex);
                        }
                        
                        // 设置到输入框
                        document.getElementById('superKey').value = result;
                    };
                    </script>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="footer">
        <div class="container">
            <p style="color: #757575; font-size: 13px; margin: 0;"><?php echo isset($footer) ? $footer : '© ' . date('Y') . ' 邮件服务器插件. 保留所有权利。'; ?></p>
        </div>
    </footer>
    
    <!-- jQuery (Bootstrap 的依赖) -->
    <script src="assets/js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- Layer弹出层 -->
    <script src="assets/layer/layer/layer.js"></script>
    <!-- 粒子效果库 (CDN版本) -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <!-- 自定义脚本 -->
    <script src="assets/js/main.js"></script>
    <!-- 初始化粒子效果 -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 创建粒子容器
        const particlesContainer = document.createElement('div');
        particlesContainer.id = 'particles-js';
        document.body.insertBefore(particlesContainer, document.body.firstChild);
        
        // 初始化粒子效果
        if (typeof particlesJS !== 'undefined') {
            particlesJS('particles-js', {
                particles: {
                    number: {
                        value: 100,
                        density: {
                            enable: true,
                            value_area: 1000
                        }
                    },
                    color: {
                        value: ["#2196F3", "#4CAF50", "#FF9800", "#9C27B0"]
                    },
                    shape: {
                        type: "circle",
                        stroke: {
                            width: 0,
                            color: "#000000"
                        }
                    },
                    opacity: {
                        value: 0.5,
                        random: false,
                        anim: {
                            enable: false,
                            speed: 1,
                            opacity_min: 0.1,
                            sync: false
                        }
                    },
                    size: {
                        value: 3,
                        random: true,
                        anim: {
                            enable: false,
                            speed: 40,
                            size_min: 0.1,
                            sync: false
                        }
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: "#2196F3",
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 3,
                        direction: "none",
                        random: true,
                        straight: false,
                        out_mode: "bounce",
                        bounce: true,
                        attract: {
                            enable: true,
                            rotateX: 600,
                            rotateY: 1200
                        }
                    }
                },
                interactivity: {
                    detect_on: "canvas",
                    events: {
                        onhover: {
                            enable: true,
                            mode: "repulse"
                        },
                        onclick: {
                            enable: true,
                            mode: "bubble"
                        },
                        resize: true
                    },
                    modes: {
                        grab: {
                            distance: 180,
                            line_linked: {
                                opacity: 1
                            }
                        },
                        bubble: {
                            distance: 300,
                            size: 60,
                            duration: 2,
                            opacity: 0.8,
                            speed: 3
                        },
                        repulse: {
                            distance: 150,
                            duration: 0.4
                        },
                        push: {
                            particles_nb: 6
                        },
                        remove: {
                            particles_nb: 2
                        }
                    }
                },
                retina_detect: true
            });
        } else {
            console.error('粒子效果需要particlesJS库支持');
        }
    });
    </script>
</body>
</html>