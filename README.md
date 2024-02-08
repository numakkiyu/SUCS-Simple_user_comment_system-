# PHP+MySQL的简单用户评论系统 SUCS(Simple user comment system)
----
### 概述

#### 项目简介
本项目是采用*PHP*+*MySQL*的轻量化用户评论系统，主要是以轻便为主，页面采用 iframe嵌入式 来运行，不用重新构建，直接使用

#### 项目信息
##### 项目作者：北海的佰川
##### GitHub项目地址：https://github.com/numakkiyu/SUCS-Simple_user_comment_system-
##### 原文章地址：https://me.tianbeigm.cn/archives/SUCS-Simple_user_comment_system-
##### 北海的佰川保留所有权益，开源免费使用

#### 代码讲解
在本篇文章中，我们将分析和解释上面所给出的一些PHP代码，这些代码用于构建一个用户反馈和评论系统。我们将逐段讲解代码的功能和实现原理，讨论与每个代码片段相关的关键概念和用法。

----
### index.php
我们从主要的代码文件`index.php`开始讲解。这个文件负责展 评论系统 的PHP页面，并处理用户提交的评论数据。

#### 代码查看

```php
<?php
require_once 'db.php';
require_once 'process_feedback.php';

// 获取HTTP头部的Referer值
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// 解析来源域名
$refererHost = parse_url($referer, PHP_URL_HOST);

$whitelist = ['白名单域名', 'www.baidu.com', 'comment.x.com']; //不需要添加http（https），不支持泛域名，一定要添加这个 用户反馈和评论系统 的页面域名（本页域名），以及需要嵌入的网站页面地址

// 获取引用iframe嵌入页面的域名
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$refererHost = parse_url($referer, PHP_URL_HOST);

// 检查是否有引用页
if ($refererHost != '填写你的嵌入页面域名') {
    // 如果是直接访问（没有Referer）；填写嵌入iframe网站页面地址
    if (empty($referer)) {
        echo '请您访问 <a href="你的嵌入网站页面网址">你的嵌入网站页面网址</a> 获取完整服务';
        exit;
    } else { // 如果是从其他网站嵌入
        // 检查引用页是否在白名单中
        if (!in_array($refererHost, $whitelist)) {
            header('Content-Security-Policy: frame-ancestors \'none\'');
            echo '嵌入验证失败，您无法嵌入该页面，请访问 <a href="https://web.hypcvgm.top">https://web.hypcvgm.top</a>获取完整服务，或者联系管理员提供域名验证。';
            exit;
        }
    }
}
// 检查IP地址和提交次数
$ip = $_SERVER['REMOTE_ADDR'];
$canSubmit = checkIpLimit($ip);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户反馈</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* 公共样式 */
        .feedback-container {
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: containerFadeIn 1s;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="email"], textarea {
            width: 60%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f8f8f8;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* 电脑端样式 */
        @media screen and (min-width: 769px) {
            .feedback-container {
                width: 70%;
                max-width: 800px;
            }

            .comments {
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
                max-height: 395px;
                overflow-y: auto;
            }

            .comment {
                background-color: #f8f8f8;
                padding: 15px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .comment p {
                margin: 5px 0;
            }

            .comment-ip {
                text-align: right;
                color: #777;
                font-size: 12px;
            }
        }

        /* 移动端样式 */
        @media screen and (max-width: 768px) {
            .feedback-container {
                width: 90%;
            }

            .comments {
                max-height: 350px;
                overflow-y: auto;
            }

            .comment {
                background-color: #e9f5ff;
                padding: 15px;
                border-radius: 15px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .comment p {
                font-size: 14px;
            }

            .comment-ip {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="feedback-container">
        <h2>用户评论&反馈</h2>
        <h5>一个IP只能评论三次，超过三次需要等24小时之后才能评论。</h5>
        <?php if ($canSubmit): ?>
            <form action="process_feedback.php" method="POST">
                <div class="form-group">
                    <label for="email">邮箱:</label>
                    <input type="email" name="email" id="email" required placeholder="输入邮箱地址">
                </div>
                <div class="form-group">
                    <label for="comment">评论:</label>
                    <textarea name="comment" id="comment" required minlength="5" placeholder="一个IP只能评论三次，评论至少要输入五个字"></textarea>
                </div>
                <button type="submit" name="submit">提交</button>
            </form>
        <?php else: ?>
            <p>您已达到今日评论次数限制，请24小时后再试。</p>
        <?php endif; ?>

       <h3>评论&反馈</h3>
<div class="comments">
    <?php
    // 显示评论
    $comments = fetchComments();
    foreach ($comments as $comment) {
        echo "<div class='comment'>";
        echo "<p><small>评论用户：" . htmlspecialchars($comment['email']) . "</small></p>";
        echo "<p></p>"; 
        echo "<div class='comment-content'>";
        echo "<p><b>" . htmlspecialchars($comment['comment']) . "</b></p>"; 
        echo "</div>";
        echo "<p class='comment-ip'><small>用户IP: " . htmlspecialchars($comment['ip_address']) . "</small></p>";
        // 显示 IP 位置
        if (isset($comment['ip_location'])) {
            echo "<p class='comment-ip-location'><small>IP位置: " . htmlspecialchars($comment['ip_location']) . "</small></p>";
        }
        echo "</div>";
    }
    ?>
</div>
    </div>
</body>
</html>
```

#### 文件依赖
在代码的第2行和3行，我们引入了`db.php`和`process_feedback.php`，这两个文件包含了数据库连接和操作的函数，以及对表单提交的处理函数。

#### 检查引用页
在代码的第11行到23行，我们检查了引用页面，也就是用户从哪个页面链接进入本页面。我们首先获取并解析了HTTP头中的`Referer`值，然后将其与一个白名单进行比较。如果引用页面不在白名单中，我们将输出一条错误消息，要求用户访问指定的页面才能获得完整的服务。如果没有引用页面，我们将输出一条消息，要求用户访问指定的页面来获取完整服务。

#### 检查IP地址和提交次数
在代码的第29行和30行，我们获取了用户的IP地址，并调用了`checkIpLimit`函数来检查IP地址的提交次数。如果用户已经达到了每日评论次数限制，我们将输出一条消息，告知用户需要等待24小时后才能再次评论。如果用户可以提交评论，我们将显示一个评论表单供用户填写。

#### 显示评论与提交评论
在代码的第59行到87行，我们显示了已经提交的评论。我们首先调用了`fetchComments`函数获取数据库中的评论数据，然后使用循环将每条评论数据输出到HTML页面中。在输出评论时，我们使用了`htmlspecialchars`函数对评论内容和用户信息进行了转义，以避免XSS漏洞。

### process_feedback.php
下一步，我们来看看`process_feedback.php`文件，它处理了表单提交和保存评论数据的操作。

#### 代码查看

```php
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
```

#### 获取用户数据和IP地址地理位置
在代码的第15行到21行，我们从`$_POST`数组中获取用户提交的邮箱地址、评论内容和IP地址，并调用了`getIpLocation`函数获取IP地址的地理位置信息。

#### 验证用户数据和评论次数限制
在代码的第24行到45行，我们验证了用户提交的邮箱地址和评论内容的有效性。如果邮箱地址无效或评论内容不符合要求，我们将输出相应的错误消息。然后，我们使用`checkIpLimit`函数检查用户的IP地址评论次数是否超过限制。如果超过限制，我们将输出错误消息。

#### 保存评论数据并重定向
在代码的第49行到56行，如果通过了上述验证，我们将调用`saveComment`函数将评论数据保存到数据库中，并使用`header`函数将用户重定向回首页。

### db.php
接下来，我们来看看`db.php`文件，其中包含了与数据库连接和操作相关的函数。

#### 代码查看

```php
<?php
// 数据库参数
$host = 'localhost'; // 数据库服务器地址
$dbName = ''; // 数据库名
$username = ''; // 数据库用户名
$password = ''; // 数据库密码


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

```

#### 数据库连接和实例
在代码的第13行到18行，我们首先定义了数据库连接的参数，并使用这些参数创建了一个`PDO`实例，来实现与数据库的连接。这里使用了`try...catch`语句来处理可能发生的异常。

#### 保存评论数据到数据库
在代码的第23行到31行，我们定义了`saveComment`函数，用于将评论数据保存到数据库的`comments`表中。我们首先使用`prepare`方法来预处理插入语句，然后执行`execute`方法将数据插入到数据库中。

#### 获取所有评论数据
在代码的第35行到44行，我们定义了`fetchComments`函数，用于获取数据库中的所有评论数据。我们使用`query`方法执行查询语句，然后使用`fetchAll`方法获取查询结果。

#### 检查IP地址评论次数限制
在代码的第50行到61行，我们定义了`checkIpLimit`函数，用于检查指定IP地址的评论次数是否超过限制。我们使用`prepare`方法预处理查询语句，然后执行`execute`方法查询数据库。最后，我们使用`fetchColumn`方法获取查询结果。

### admin_login.php
现在，我们来看看`admin_login.php`文件，用于管理员登录的页面。

#### 代码查看

```php
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

```

#### 检查是否已登录
在代码的第9行到14行，我们首先检查用户是否已经登录。如果用户已经登录，我们会将其重定向到管理员页面。

#### 处理登录请求
在代码的第16行到44行，我们处理了登录请求。我们从`$_POST`数组中获取用户名和密码，然后通过简单的硬编码方式进行验证。如果验证通过，我们将设置相应的会话变量，并将用户重定向到管理员页面。如果验证失败，我们将输出错误消息。

### admin.php
现在，我们来看看`admin.php`文件，用于管理员登录的页面。

#### 代码查看

```php
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

```

#### 文件依赖和管理员验证
在代码的第2行，我们引入了`db.php`文件，以便与数据库建立连接。然后，在第3行，我们开始了会话。

接下来，我们使用简单的方式验证用户是否是管理员。如果用户没有登录或用户名与管理员用户名不匹配，我们将重定向用户到登录页面，并使用`exit`函数终止后续代码的执行。

#### 获取所有评论数据
在代码的第8行，我们调用了`fetchAllComments`函数，获取数据库中的所有评论数据，并将其存储在`$comments`数组中。

#### 显示评论和操作
在代码的第13行，我们开始构建管理员页面的HTML内容。首先，我们显示一个标题和一个退出登录的链接。

然后，我们使用一个表格来展示评论数据。表格的头部包含了"邮箱"、"评论"和"操作"三个列。在每个数据行中，我们将评论的邮箱和评论内容显示在对应的列中，并为每条评论添加了一个删除按钮。

删除按钮使用了一个表单，其中包含了一个隐藏的输入字段来存储评论的ID，以及一个文本输入框用于编辑评论内容。用户可以通过编辑文本输入框中的内容来修改评论，并点击删除按钮将评论从数据库中删除。

### admin_process.php
现在，我们来看看`admin_process.php`文件，用于管理员登录的页面。

#### 代码查看

```php
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

```

#### 文件依赖和管理员验证
在代码的第2行，我们引入了`db.php`文件，以便与数据库建立连接。然后，在第4行开始了会话。

接下来，我们使用简单的方式验证用户是否是管理员。如果用户没有登录或用户名与管理员用户名不匹配，我们将重定向用户到登录页面，并使用`exit`函数终止后续代码的执行。

#### 检查请求类型和处理请求
在代码的第9行，我们检查了请求的类型。如果请求的类型是POST，我们将进一步处理请求。

首先，我们检查是否有一个`delete`字段和一个`id`字段被提交。如果是，我们从`$_POST`数组中获取评论的ID，并调用`deleteComment`函数将该评论从数据库中删除。

接下来，我们检查是否有一个`edit`字段、一个`id`字段和一个`comment`字段被提交。如果是，我们从`$_POST`数组中获取评论的ID和修改后的评论内容，并调用`editComment`函数来更新数据库中的评论内容。

#### 重定向回管理员页面
无论是删除评论还是编辑评论，我们最后都将用户重定向回管理员页面，并使用`exit`函数终止后续代码的执行。

#### 获取所有评论数据
在代码的第19行，我们再次调用了`fetchAllComments`函数，获取数据库中的所有评论数据，并将其存储在`$comments`数组中。

### logout.php
最后，我们来看看`logout.php`文件，用于退出登录的操作。

#### 代码查看

```
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

```

#### 清除会话变量和销毁会话
在代码的第5行到16行，我们首先清除了所有会话变量，然后销毁了会话。这样可以确保用户的会话信息被完全清除。

#### 重定向到登录页面或首页
在代码的第19行到22行，我们将用户重定向到登录页面或首页，以确保他们完成了注销操作。

### MySQL数据库
下面就是展示MySQL数据库的SQL文件是一个数据库文件，其中包含了创建和填充一个名为`comments`的表的SQL语句。

####代码展示

```sql
-- MySQL dump 10.13  Distrib 5.7.40, for Linux (x86_64)
--
-- Host: localhost    Database: comment
-- ------------------------------------------------------
-- Server version	5.7.40-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `ip_location` varchar(255) DEFAULT NULL,
  `submit_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,'1540585339@qq.com','测试语句','120.229.33.106','中国广东深圳市 移动','2024-01-22 21:51:27');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'comment'
--

--
-- Dumping routines for database 'comment'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-08 20:27:14

```

#### 创建`comments`表
在文件的第22行开始，我们可以看到创建`comments`表的SQL语句。这个表包含了以下列：
- `id`：自增的整型字段，作为主键。
- `email`：存储评论者的电子邮件地址的字符串字段。
- `comment`：存储评论内容的文本字段。
- `ip_address`：存储评论者的IP地址的字符串字段。
- `ip_location`：存储评论者的IP所属地理位置的字符串字段（可选）。
- `submit_time`：存储评论提交时间的日期时间字段，默认值为当前时间。

在第28行，我们可以看到通过`ENGINE=InnoDB`指定了使用InnoDB引擎，并设置了自增起始值为54。

#### 填充`comments`表
在文件的第34-40行，我们可以看到向`comments`表插入了一条记录的SQL语句。这条记录包含了一些示例数据，包括一个测试邮箱地址、评论内容、评论者的IP地址、IP所属地理位置以及提交时间。

----

### 注意事项

#### 1、上面的邮箱没有`SMTP`验证，你们可以自行添加
#### 2、上面的 评论系统 采用嵌入式使用，请不要直接使用，CSS布局是采用响应式
#### 3、本代码是以简单小巧为主，删除了安全部分，请你们自行添加，如果导致数据库丢失，后果自负
