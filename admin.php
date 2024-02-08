<?php
require_once 'db.php'; // 引入数据库连接文件
session_start();

// 简单的管理员验证
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'szez') {
    header('Location: admin_login.php'); // 重定向到登录页面
    exit;
}

// 获取所有评论
$comments = fetchAllComments();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员页面</title>
    <style>
        .admin-container {
    width: 80%;
    margin: 20px auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

    </style>
</head>
<body>
    <div class="admin-container">
        <h2>评论管理</h2>
        <a href="logout.php">退出登录</a>
        <table>
            <thead>
                <tr>
                    <th>邮箱</th>
                    <th>评论</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($comment['email']); ?></td>
                    <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                    <td>
                        <form action="admin_process.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                            <input type="text" name="comment" value="<?php echo htmlspecialchars($comment['comment']); ?>">
                            <button type="submit" name="delete">删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
