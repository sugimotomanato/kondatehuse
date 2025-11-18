<?php
ob_start();
session_start();
// ================================================
// ロリポップ MySQL 接続テスト（本番環境用）
// ================================================
 
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';         // データベース名
 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$ID = trim($_POST['system_ID'] ?? '');
$pass = trim($_POST['system_password'] ?? '');

// ★ 半角英数字でなければエラー
if (!preg_match('/^[a-zA-Z0-9]+$/', $ID)) {
    $_SESSION['error'] = "IDは半角英数字で入力してください。";
    header('Location: ./U15ADMIN_LOGIN.php');
    exit();
}

    if ($ID === '' || $pass === '') {
     $_SESSION['error'] = "IDとパスワードを入力してください。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
 

  $stmt = $pdo->prepare("SELECT * FROM `system` WHERE BINARY `system_users_id` = ?");
        $stmt->execute([$ID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $isValid = false;
        if ($user && !empty($user['system_users_password'])) {
            $stored_pass = trim($user['system_users_password']);
            if (password_verify($pass, $stored_pass) || $pass === $stored_pass) {
                $isValid = true;
            }
        }

        if ($isValid) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $ID;
        } else {
        $_SESSION['error'] = "ID またはパスワードが違います。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
        }

 
} catch (PDOException $e) {
 error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
          background-image: url('images/haikei2.jpg');
          background-size: cover;      /* 画面全体にフィット */
          background-position: center; /* 中央に配置 */
          background-repeat: no-repeat;/* 繰り返さない */
        }
      </style>
</head>
<body>
    <img src="haikei2.jpg" alt="料理の写真" width="400" style="margin-top: 120px; margin-bottom: 120px;"><br>
        <h1>献立家</h1>
<a href="U17ADMIN_DELEATE.php">ユーザ退会処理 ></a>

</body>
</html>