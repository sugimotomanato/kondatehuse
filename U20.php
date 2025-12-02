<?php
ob_start();
session_start();// ハッシュを読み込む
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']); // 1回表示したら消す

if ($_SERVER["REQUEST_METHOD"] === "POST") {
 $hash=   password_hash("aso230", PASSWORD_DEFAULT);//ここに保存
try{

    $pass = trim($_POST['system_password'] ?? '');

    // パスワード検証
    if (password_verify($pass, $hash)) {
        // セッションにログイン状態を保存
        $_SESSION['logged_in'] = true;
    } else {
        $_SESSION['error'] = "パスワードが違います。";
         // ログイン失敗
        header("Location: ./U19ADMIN_MAKE.php");
        exit();
    }

}catch (PDOException $e) {
 error_log("エラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
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
    <title>管理者登録フォーム</title>
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
  .error {
          color: red;
          font-size: 18px;
          margin: 10px 0;
        }
      </style>
</head>
<body>
        <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

<h2>管理者登録</h2>

<form action="U21Confirm.php" method="post">

    <label for="admin-id">管理者名</label>
    <p><input type="text" name="name" id="admin-id"
              value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES); ?>"
              required></p>

    <label for="password">パスワード（半角英数8文字以上）</label>
    <p><input type="password" name="password" id="password" minlength="8"
    value="<?= htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES); ?>"
              required></p>

    <label for="email">メールアドレス</label>
    <p><input type="email" name="email" id="email"
              value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>"
              required></p>

    <button type="submit">確認する</button>
</form>

</body>
</html>

