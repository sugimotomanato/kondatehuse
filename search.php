<?php
header('Content-Type: text/html; charset=UTF-8');

$keyword = $_POST['keyword'] ?? '';

// ------------------------------------------------------------
// DB接続
// ------------------------------------------------------------
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';                 // データベース名
 
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // 🔍 user_name のみを検索
    $sql = "
        SELECT parent_account_ID, parent_account, user_name
        FROM parent_account
        WHERE user_name LIKE :keyword
        ORDER BY parent_account_ID ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
    exit;
}
?>
<a href="U16ADMIN_HOME.php">戻る</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>家族コード</th>
        <th>ユーザー名</th>
    </tr>

    <?php if (!empty($results)): ?>
        <?php foreach ($results as $row): ?>
        <tr>
            <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['parent_account_ID']) ?></a></td>
            <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['parent_account']) ?></a></td>
            <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['user_name']) ?></a></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="3">該当データがありません。</td></tr>
    <?php endif; ?>
</table>

