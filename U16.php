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
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 管理者を取得
            $stmt = $pdo->prepare("SELECT * FROM system WHERE system_users_id = ?");
            $stmt->execute([$ID]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $isValid = false;

            if ($user && !empty($user['system_users_password'])) {

                $stored_pass = $user['system_users_password'];

                // ① ハッシュの場合のみ password_verify 実行
                if (strlen($stored_pass) > 50 && password_verify($pass, $stored_pass)) {
                    $isValid = true;
                }
                // ② 平文の場合（同一文字列で完全一致したときのみ許可）
                elseif ($pass === $stored_pass) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                header('Location: complete.php');
                exit();
            } else {
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
    <div style="text-align: center;">
    <img src="haikei2.jpg" alt="料理の写真" width="400" style="margin-top: 120px; margin-bottom: 120px;"><br>
        <h1>献立家</h1>
<a href="U17.php">ユーザ退会処理 ></a>
</div>

</body>
</html>