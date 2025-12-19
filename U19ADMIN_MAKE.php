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
    <title>管理者新規作成認証</title>
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

        <h1>管理者専用パスワードを入力してください</h1>

            <form action="U20.php" method="post">
        <input type="password" name="system_password" required>
        <button type="submit">次へ</button>
    </form>
</form>
</body>
</html>