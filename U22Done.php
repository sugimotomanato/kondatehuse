<?php 
ob_start();
session_start();

$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';                 // データベース名

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_regenerate_id(true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);
    $email    = trim($_POST['email']);

    if ($name === '' || $email === '' || $password === '') {
    $_SESSION['error'] = '全ての項目を入力してください。';
    header('Location: ./U20.php');
    exit();
}

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "メールアドレスの形式が間違っています。";
        header('Location: ./U20.php');
        exit();
    }
    
if (mb_strlen($password) < 8) {
    $_SESSION['error'] = 'パスワードは8文字以上で入力してください。';
    header('Location: ./U20.php');
    exit();
}

    try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 

        // パスワードをハッシュ化（必須）
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // DB登録
        $stmt = $pdo->prepare("INSERT INTO `system` (`system_users_name`, `system_users_password`, `email`)
            VALUES (?, ?, ?)");
        $stmt->execute([$name, $hash, $email]);

        // 登録されたユーザーID
        $ID = $pdo->lastInsertId();

        // メール送信
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.lolipop.jp';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@aso2301200.fem.jp';
        $mail->Password   = 'x5616zhF0Qc8G_-g';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        $mail->setFrom('info@aso2301200.fem.jp', 'KondateHause 管理システム');
        $mail->addAddress($email, $name);

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

<p>
ログインはこちらから：<br>
<a href='https://aso2301200.fem.jp/U15ADMIN_LOGIN.php'>https://aso2301200.fem.jp/U15ADMIN_LOGIN.php</a>
</p>

ご利用ありがとうございます。
KondateHause 管理システム";

        $mail->send();

    } catch (Exception $e) {
    error_log('メール送信エラー: ' . $mail->ErrorInfo);
    $_SESSION['error'] = 'メール送信に失敗しました。再度お試しください。';
            header('Location: ./U19ADMIN_MAKE.php');
        exit();
    } catch (PDOException $e) {
        error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U19ADMIN_MAKE.php');
        exit();
    }
}else{
        header('Location: ./U19ADMIN_MAKE.php');
    exit();
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

