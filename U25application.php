<?php
// データベース接続情報 (ご自身の環境に合わせて修正してください)
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019'; // ★正：LAA1685019
$db_pass = '6group'; // ★正：6group
$db_name = 'LAA1685019-kondatehausu'; // ★正：LAA1685019-kondatehausu

// 接続の試行
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("接続エラー: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $group_code = $_POST['group_code'] ?? '';
    $applicant_name = $_POST['applicant_name'] ?? '';

    // 必須チェック
    if (empty($group_code) || empty($applicant_name)) {
        die("グループコードと名前は必須です。");
    }

    // 1. family_codeが親アカウントテーブルに存在するか確認（任意だが推奨）
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM parent_account WHERE family_code = ?");
    $stmt_check->execute([$group_code]);
    if ($stmt_check->fetchColumn() == 0) {
        die("入力された家族コードは存在しません。");
    }

    // 2. 申請データを applications テーブルに挿入 (statusはデフォルトの0:Pending)
    $stmt_insert = $conn->prepare("INSERT INTO applications (family_code, applicant_name) VALUES (?, ?)");
    
    if ($stmt_insert->execute([$group_code, $applicant_name])) {
        echo "✅ 申請が完了しました。管理者の承認をお待ちください。";
    } else {
        echo "❌ 申請の送信中にエラーが発生しました。";
    }
} else {
    // フォーム経由ではないアクセスの場合
    echo "不正なアクセスです。";
}
?>