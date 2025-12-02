<?php
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';         // データベース名

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        try {
// DB接続（PDO）
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$token = $_POST['token'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT system_users_id, expires_at FROM password_resets WHERE token = :token");
$stmt->execute([':token' => $token]);
$row = $stmt->fetch();

if (!$row || strtotime($row['expires_at']) < time()) {
    die("トークンが無効または期限切れです");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE system SET system_users_password = :pass WHERE system_users_id = :id");
$stmt->execute([
    ':pass' => $hashed,
    ':id' => $row['system_users_id']
]);

$stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
$stmt->execute([':token' => $token]);


    } catch (PDOException $e) {
 error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }
    
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
             <style>
body {
    margin: 0;
    padding: 0;
    background-image: url('haikei2.jpg'); /* ← これが背景画像！ */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: "ヒラギノ角ゴ ProN", sans-serif;
    text-align: center;
}
      </style>
</head>
<body>
    <h1>パスワードを更新しました。ログインしてください。</h1>
    <a href="U15ADMIN_LOGIN.php">ログインへ</a>
</body>
</html>