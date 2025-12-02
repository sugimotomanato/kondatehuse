<?php
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';         // データベース名
 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    try {
// DB接続（PDO）
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1) メールアドレスのユーザーを探す
    $stmt = $pdo->prepare("SELECT system_users_id FROM system WHERE email = :email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if ($user) {
   $token = bin2hex(random_bytes(32));
$tokenHash = password_hash($token, PASSWORD_DEFAULT); // 保存用ハッシュ
$expires = date("Y-m-d H:i:s", time() + 3600);

$stmt = $pdo->prepare("
    INSERT INTO password_resets (system_users_id, token, expires_at)
    VALUES (:uid, :token, :expires)
");
$stmt->execute([
    ':uid' => $user['system_users_id'],
    ':token' => $tokenHash,
    ':expires' => $expires
]);

$resetUrl = "https://あなたのドメイン/reset_password.php?token=" . $token;

    mail($email, "パスワード再設定", "以下のURLからパスワードを再設定してください:\n$resetUrl");
}

    echo "リセットリンクを送信しました。";

    } catch (PDOException $e) {
 error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }
}
