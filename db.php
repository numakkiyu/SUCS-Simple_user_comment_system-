<?php
// 数据库参数
$host = 'localhost';
$dbName = 'comment';
$username = 'comment';
$password = 'bsbpRGWmCTJ7Hh4y';

try {
    // 创建PDO实例连接数据库
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    // 设置错误模式为异常
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

/**
 * 保存评论到数据库
 */
function saveComment($email, $comment, $ip, $ipAddress) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (email, comment, ip_address, ip_location) VALUES (:email, :comment, :ip, :ipLocation)");
        $stmt->execute([
            ':email' => $email,
            ':comment' => $comment,
            ':ip' => $ip,
            ':ipLocation' => $ipAddress
        ]);
    } catch (PDOException $e) {
        die("保存评论失败: " . $e->getMessage());
    }
}

/**
 * 获取所有评论
 */
function fetchComments() {
    global $pdo;

    try {
        $stmt = $pdo->query("SELECT * FROM comments ORDER BY submit_time DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("获取评论失败: " . $e->getMessage());
    }
}

/**
 * 删除评论
 */
function deleteComment($id) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        die("删除评论失败: " . $e->getMessage());
    }
}

function editComment($id, $comment) {
    global $pdo;
    // ... 实现编辑评论的代码 ...
}
/**
 * 编辑评论
 */
function fetchAllComments() { // 将 fetchComments 重命名为 fetchAllComments
    global $pdo;

    try {
        $stmt = $pdo->query("SELECT * FROM comments ORDER BY submit_time DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("获取评论失败: " . $e->getMessage());
    }
}

/**
 * 验证IP地址评论次数
 */
function checkIpLimit($ip) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE ip_address = :ip AND DATE(submit_time) = CURDATE()");
        $stmt->execute([':ip' => $ip]);
        $count = $stmt->fetchColumn();

        return $count < 3; // 允许每天最多3次评论
    } catch (PDOException $e) {
        die("检查IP限制失败: " . $e->getMessage());
    }
}
