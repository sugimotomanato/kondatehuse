<?php
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';         // データベース名
 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    try {
// DB接続（PDO）
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1) メールアドレスのユーザーを探す
    $stmt = $pdo->prepare("SELECT `system_users_id` FROM `system` WHERE `email` = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2) トークン生成（安全なランダム値）
        $token = bin2hex(random_bytes(32));

        // 3) トークンをDBに保存（有効期限つき）
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, token, expires_at)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))
        ");
        $stmt->execute([$user['id'], $token]);

        // 4) リセット用URLを生成
        $url = "https://example.com/reset_password.php?token=" . $token;

        // 5) メール送信（簡易例）
        mail($email, "パスワード再設定", "以下のURLからパスワードを再設定してください：\n\n$url");
    }

    echo "リセットリンクを送信しました。";

    } catch (PDOException $e) {
 error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }
}
