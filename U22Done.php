<?php 
ob_start();
session_start();

$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES);
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES);

    $na=$name;
    $pass=$password;
    $ma=$email;

    $n=$name;
    $p=$password;
    $m=$email;


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "メールアドレスの形式が間違っています。";
        header('Location: ./U20.php');
        exit();
    }

    try {
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user, $db_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // DB登録処理
        $stmt = $pdo->prepare("
            INSERT INTO system (system_users_name, system_users_password, email)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$name, $password, $email]);

          $stm = $pdo->prepare("
            SELECT * FROM `system` WHERE `email` = ?
        ");
                $stm->execute([$m]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);
        $ID=$user['system_users_id'];

        // ★ ロリポップ共用メールでメール送信する ★
              $mail = new PHPMailer();  


            $mail->isSMTP();
            $mail->Host       = 'smtp.lolipop.jp';
            $mail->SMTPAuth   = true;

            // 🔥 あなた専用 SMTP アカウント
            $mail->Username   = 'info@aso2301200.fem.jp';
            $mail->Password   = 'x5616zhF0Qc8G_-g';

            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';

            // 差出人（あなたのメール）
            $mail->setFrom('info@aso2301200.fem.jp', 'KondateHause 管理システム');

            // 宛先（登録ユーザー）
            $mail->addAddress($ma, $n);

            $mail->Subject = '管理者アカウント登録完了のお知らせ';
            $mail->Body = 
"{$name} 様

管理者アカウントの登録が完了しました。

━━━━━━━━━━━━━━
■ 登録情報
管理者ID：{$ID}
名前：{$name}
メールアドレス：{$email}
━━━━━━━━━━━━━━

ご利用ありがとうございます。
KondateHause 管理システム";

            $mail->send();

    } catch (PDOException $e) {
        error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U19ADMIN_MAKE.php');
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
<h1>管理者アカウント登録完了しました</h1>
<a href="U15ADMIN_LOGIN.php">戻る</a>
</body>
</html>

