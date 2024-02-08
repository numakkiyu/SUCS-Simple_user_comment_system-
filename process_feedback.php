<?php
require_once 'db.php'; // 引入数据库连接和操作的函数

// 获取IP地址的地理位置
function getIpLocation($ip) {
    // 设置新的 API URL
    $apiUrl = "https://tenapi.cn/v2/getip";

    // 准备 POST 请求的数据
    $postData = http_build_query(['ip' => $ip]);

    // 配置一个 POST 请求的上下文
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData,
        ]
    ]);

    // 向新的 API 发起 POST 请求
    $response = file_get_contents($apiUrl, false, $context);
    $data = json_decode($response, true);

    // 检查响应中是否包含所需数据
    if (isset($data['code']) && $data['code'] == 200 && isset($data['data'])) {
        // 获取位置信息
        $area = $data['data']['area'] ?? '未知位置';

        return $area;
    }

    return '未知位置';
}

// 检查表单是否提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取用户数据
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;
    $ip = $_SERVER['REMOTE_ADDR'];

    // 获取IP地址的地理位置
    $ipLocation = getIpLocation($ip);

    // 验证邮箱
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '无效的邮箱地址。';
    } elseif (strlen($comment) < 5) {
        // 验证评论长度
        $error = '评论内容至少需要5个字符。';
    } else {
        // 检查IP地址评论次数限制
        if (!checkIpLimit($ip)) {
            $error = '您已达到每日评论次数限制。';
        } else {
            // 保存数据到数据库
            saveComment($email, $comment, $ip, $ipLocation); // 现在传递了四个参数
            header('Location: index.php'); // 重定向回首页
            exit;
        }
    }
}

// 如果有错误，回到首页并显示错误信息
if (isset($error)) {
    header("Location: index.php?error=" . urlencode($error));
    exit;
}
