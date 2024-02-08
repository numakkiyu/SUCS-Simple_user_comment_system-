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