<?php
$errors = [];
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019-kondatehausu';
$db_pass = '6group';
$db_name = 'LAA1685019';

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

            // 管理者を取得
            $stmt = $pdo->prepare("SELECT * FROM system WHERE system_users_id = ?");
            $stmt->execute([$ID]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // ✅ 両方の比較方法を採用
            $isValid = false;
            if ($user) {
                // ハッシュ化されている場合
                if (password_verify($pass, $user['system_users_password'])) {
                    $isValid = true;
                }
                // 平文保存されている場合
                elseif ($pass === $user['system_users_password']) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                // ✅ ログイン成功時の処理
                header('Location: complete.php');
                exit();
            } else {
                // ❌ 失敗時
                header('Location: ./U15.php');
                exit();
            }

        } catch (PDOException $e) {
            echo "DB接続エラー: " . $e->getMessage();
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
</head>
<body>
        <h1>献立家</h1>
<a href="U17.php">ユーザ退会処理 ></a>

</body>
</html>