<?php
$errors = [];
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019-kondatehausu'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ID = $_POST['system_ID'] ?? '';
    $pass = $_POST['system_password'] ?? '';

    if (empty($ID) || empty($pass)) {
        $errors[] = "IDまたはパスワードが未入力です。";
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // IDで検索（数値のIDを使う）
            $stmt = $pdo->prepare("SELECT * FROM system WHERE system_users_id = ?");
            $stmt->execute([$ID]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $isValid = false;

            if ($user && !empty($user['system_users_password'])) {
                $stored_pass = $user['system_users_password'];

                // まず password_verify() でチェック（ハッシュ対応）
                if (password_verify($pass, $stored_pass)) {
                    $isValid = true;
                }
                // password_verify が false のとき、平文一致をチェック
                elseif ($pass === $stored_pass) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                // ログイン成功
                header('Location: complete.php');
                exit();
            } else {
                // ログイン失敗
                header('Location: ./U15ADMIN_LOGIN.php');
                exit();
            }

        } catch (PDOException $e) {
            echo "DB接続エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
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