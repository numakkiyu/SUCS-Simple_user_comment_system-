<?php
session_start();

// 检查是否已登录
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: admin.php'); // 如果已登录，则重定向到管理员页面
    exit;
}

$loginError = '';

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 简单的硬编码登录验证（在实际应用中应使用更安全的方法）
    if ($username === 'szez' && $password === 'szez0901') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $loginError = '无效的用户名或密码';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员登录</title>
    <style>
        .login-container {
    width: 300px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 4px;
    background-color: #5cb85c;
    color: white;
    cursor: pointer;
}

button:hover {
    background-color: #4cae4c;
}

.error {
    color: red;
    margin-bottom: 15px;
}

    </style>
</head>
<body>
    <div class="login-container">
        <h2>管理员登录</h2>
        <form action="admin_login.php" method="post">
            <div class="form-group">
                <label for="username">用户名:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <?php if ($loginError): ?>
                <p class="error"><?php echo $loginError; ?></p>
            <?php endif; ?>
            <button type="submit">登录</button>
        </form>
    </div>
</body>
</html>
