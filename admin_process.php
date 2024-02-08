<?php
require_once 'db.php'; // 确保这里正确地引入了db.php

session_start();

// 验证管理员身份
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'szez') {
    header('Location: admin_login.php'); // 重定向到登录页面
    exit;
}

// 检查请求类型
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        // 处理删除评论请求
        $id = $_POST['id'];
        deleteComment($id);
    } elseif (isset($_POST['edit']) && isset($_POST['id']) && isset($_POST['comment'])) {
        // 处理编辑评论请求
        $id = $_POST['id'];
        $comment = $_POST['comment'];
        editComment($id, $comment);
    }

    // 重定向回管理员页面
    header('Location: admin.php');
    exit;
}

// 获取所有评论
$comments = fetchAllComments();

// ... 其余的HTML和PHP代码 ...
