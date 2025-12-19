<?php
header('Content-Type: text/html; charset=UTF-8');

$keyword = $_POST['keyword'] ?? '';
$keyword = trim($keyword); // 空白除去

$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // ★ 全フィールドを検索対象にする
    $sql = "
    SELECT parent_account_ID, family_code, user_name
    FROM parent_account
    WHERE 
        user_name LIKE :keyword OR
        family_code LIKE :keyword OR
        parent_account_ID LIKE :keyword
    ORDER BY parent_account_ID ASC
     ";

       $stmt = $pdo->prepare($sql);

// ★ 前方一致
       $stmt->bindValue(':keyword', $keyword . '%', PDO::PARAM_STR);

    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<tr><td colspan='3'>データ取得エラー</td></tr>";
    exit;
}

if ($results) {
    foreach ($results as $row) {
        $id = htmlspecialchars($row['parent_account_ID']);
        $family = htmlspecialchars($row['family_code']);
        $name = htmlspecialchars($row['user_name']);
        $link = 'U18ADMIN_DELEATE_LAST.php?id=' . urlencode($id);

        echo "
            <tr>
                <td><a href='{$link}'>{$id}</a></td>
                <td><a href='{$link}'>{$family}</a></td>
                <td><a href='{$link}'>{$name}</a></td>
            </tr>
        ";
    }
} else {
    echo "<tr><td colspan='3'>該当データがありません。</td></tr>";
}
?>
