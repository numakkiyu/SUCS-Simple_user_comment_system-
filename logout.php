<?php
session_start(); // 启动会话

// 清除所有会话变量
$_SESSION = array();

// 销毁会话
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // 销毁会话

// 重定向到登录页面或首页
header("Location: admin_login.php"); // 或者重定向到您的首页
exit;
