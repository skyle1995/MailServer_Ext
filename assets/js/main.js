/**
 * 自定义JavaScript脚本
 * 
 * 这个文件包含网站的自定义JavaScript功能
 */

// 当文档加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化提示框和弹出框（如果jQuery可用）
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
    
    // 禁用所有带有disabled类的按钮的默认点击行为
    var disabledButtons = document.querySelectorAll('.btn.disabled');
    for (var i = 0; i < disabledButtons.length; i++) {
        disabledButtons[i].addEventListener('click', function(e) {
            e.preventDefault();
            return false;
        });
    }
});

/**
 * 显示Layer弹出层消息
 * 
 * @param {string} message - 要显示的消息内容
 * @param {string} type - 消息类型（success, info, warning, danger）
 * @param {string} title - 弹出层标题
 */
var showModalMessage = (message, type, title) => {
    // 设置默认值
    type = type || 'success';
    title = title || '提示';
    
    // 根据类型设置图标
    var icon = 1; // 默认为成功图标
    
    if (type === 'success') {
        icon = 1; // 成功图标
    } else if (type === 'info') {
        icon = 0; // 信息图标
    } else if (type === 'warning') {
        icon = 3; // 警告图标
    } else if (type === 'danger') {
        icon = 2; // 错误图标
    }
    
    // 使用layer弹出层
     layer.open({
         type: 0, // 信息框
         title: title,
         content: message,
         icon: icon,
         btn: ['确定'],
         yes: function(index, layero) {
             // 点击确定按钮的回调
             layer.close(index);
         }
     });
};