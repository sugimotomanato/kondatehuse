<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']); // 1回表示したら消す
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
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

    <form action="U16ADMIN_HOME.php" method="post">
       <label for="system_ID">管理者ID</label>
       <p><input type="text" name="system_ID" id="system_ID" required></p>

       <label for="system_password">パスワード（半角英数8文字以上）</label>
       <p><input type="password" name="system_password" id="system_password" required></p>

       <button type="submit">ログイン</button>
    </form>

    <a href="U19ADMIN_MAKE.php">管理者登録はこちら</a>
       <a href="U25.php">パスワード再設定はこちら</a>

</body>
</html>

