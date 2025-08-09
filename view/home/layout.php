<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : '邮件服务器插件'; ?></title>
    <!-- 网站图标 -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- FontAwesome 图标库 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                    <?php echo $content; ?>
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
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Layer弹出层 -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/layer/3.5.1/layer.js"></script>
    <!-- 粒子效果库 (CDN版本) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
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