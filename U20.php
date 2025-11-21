<?php
session_start();
// ハッシュを読み込む

if ($_SERVER["REQUEST_METHOD"] === "POST") {
define('SYSTEM_PASSWORD_HASH', 'aso230'); // ここに保存


    $pass = trim($_POST['system_password'] ?? '');

    // パスワード検証
    if (password_verify($pass, SYSTEM_PASSWORD_HASH)) {

        // セッションにログイン状態を保存
        $_SESSION['logged_in'] = true;

       

    } else {
        $error = "パスワードが違います。";
         // ログイン失敗
        header("Location: ./U19ADMIN_MAKE.php");
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
</head>
<body>
  <form action="U20" method="post">
        <label for="">
        管理者名
        <p><input type="text" name="ID" id=""></p>
        </label>
        <label for="">
        パスワード（半角英数8文字以上）
       <p><input type="password" name="passward" id=""></p> 
        </label>
        <label for="">
        メールアドレス
       <p><input type="email" name="email" id=""></p> 
        </label>
        <button type="submit">登録</button>
</body>
</html>