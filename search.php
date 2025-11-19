<?php
header('Content-Type: text/html; charset=UTF-8');

$keyword = $_POST['keyword'] ?? '';

$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';                 // データベース名
 
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    echo "<tr><td colspan='3'>接続エラー: " . $e->getMessage() . "</td></tr>";
    exit;
}

if (!empty($results)) {
    foreach ($results as $row) {
        echo "<tr>
            <td><a href='U18ADMIN_DELEATE_LAST.php?id=" . urlencode($row['parent_account_ID']) . "'>" . htmlspecialchars($row['parent_account_ID']) . "</a></td>
            <td><a href='U18ADMIN_DELEATE_LAST.php?id=" . urlencode($row['parent_account_ID']) . "'>" . htmlspecialchars($row['parent_account']) . "</a></td>
            <td><a href='U18ADMIN_DELEATE_LAST.php?id=" . urlencode($row['parent_account_ID']) . "'>" . htmlspecialchars($row['user_name']) . "</a></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='3'>該当データがありません。</td></tr>";
}
?>
