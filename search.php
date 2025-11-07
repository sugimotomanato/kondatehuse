<?php
// エラーメッセージを初期化
$errors = [];
$code = '';
$name = '';
$complete_page = 'complete.php'; 

// ==========================================================
// データベース接続設定 (ロリポップ情報とDB設計を統合)
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019-kondatehausu'; // 画像から推測される正しいユーザー名
$db_pass = '6group'; // 画像のパスワードから推測
$db_name = 'LAA1685019'; // 画像のDB名から推測

$keyword = $_POST['keyword'] ?? '';

try {
    $pdo = new PDO($dsn, $user, $password);
    $sql = "SELECT parent_account_ID, parent_account, user_name FROM parent_account WHERE user_name LIKE :parent_account_ID ORDER BY parent_account_ID ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
    exit;
}
?>

<table>
    <tr>
        <th>ID</th>
        <th>家族コード</th>
        <th>ユーザー名</th>
    </tr>
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $row): ?>
            <tr>
              <td>
                    <a href="U18.php?id=<?= urlencode($row['parent_account_ID']) ?>">
                        <?= htmlspecialchars($row['parent_account_ID'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </td>
                <td>
                    <a href="U18.php?id=<?= urlencode($row['parent_account_ID']) ?>">
                        <?= htmlspecialchars($row['parent_account'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </td>
                <td>
                    <a href="U18.php?id=<?= urlencode($row['parent_account_ID']) ?>">
                        <?= htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="3">該当データがありません。</td></tr>
    <?php endif; ?>
</table>
