<?php
session_start();

$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("無効なアクセスです");
}

$token = $_POST['token'] ?? '';
$newPassword = $_POST['password'] ?? '';
$confirmPassword = $_POST['password_confirm'] ?? '';

// サーバー側でも一致チェック
if ($newPassword !== $confirmPassword) {
    exit("パスワードが一致しません。もう一度お試しください。");
}

if (strlen($newPassword) < 8) {
    exit("パスワードは8文字以上必要です");
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 有効期限内のトークンを取得
    $stmt = $pdo->prepare("SELECT * FROM `password_resets` WHERE `expires_at` >= NOW()");
    $stmt->execute();
    $tokens = $stmt->fetchAll();

    $userId = null;
    foreach ($tokens as $row) {
        if (password_verify($token, $row['token'])) {
            $userId = $row['system_users_id'];
            // 使用済みトークンは削除
            $stmt = $pdo->prepare("DELETE FROM `password_resets` WHERE `system_users_id` = :uid");
            $stmt->execute([':uid' => $userId]);
            break;
        }
    }

    if (!$userId) {
        exit("無効なトークンです");
    }

    // パスワード更新
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE `system` SET `password` = :pw WHERE `system_users_id` = :uid");
    $stmt->execute([
        ':pw' => $passwordHash,
        ':uid' => $userId
    ]);

} catch (PDOException $e) {
    exit("DBエラー");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>パスワード更新完了</title>
<style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: sans-serif;
    background: #f0f0f0;
}
.container {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
a {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
a:hover {
    background: #45a049;
}
</style>
</head>
<body>
<div class="container">
    <h2>パスワードが更新されました</h2>
    <p>ログイン画面から新しいパスワードでログインしてください。</p>
    <a href="U15ADMIN_LOGIN.php">ログイン画面へ</a>
</div>
</body>
</html>
