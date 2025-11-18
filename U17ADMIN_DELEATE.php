<?php
// ==========================================================
// DB接続
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8",
        $db_user,
        $db_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT parent_account_ID, parent_account, user_name FROM parent_account ORDER BY parent_account_ID ASC");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "DB接続エラー: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ユーザー一覧</title>
</head>
<body>

<h2 style="text-align:center;">ユーザー一覧</h2>

<input type="text" id="keyword" placeholder="ユーザー名で検索">
<button id="searchBtn">検索</button>

<a href="U16ADMIN_HOME.php">戻る ></a>

<!-- 🔻検索結果を置き換えるエリア -->
<div id="tableArea">
<table border="1">
    <tr>
        <th>ID</th>
        <th>家族コード</th>
        <th>ユーザー名</th>
    </tr>

    <?php foreach ($results as $row): ?>
    <tr>
        <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['parent_account_ID']) ?></a></td>
        <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['parent_account']) ?></a></td>
        <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['user_name']) ?></a></td>
    </tr>
    <?php endforeach; ?>
</table>
</div>

<script>
// 🔍検索ボタン押下イベント
document.getElementById('searchBtn').addEventListener('click', function() {

    const keyword = document.getElementById('keyword').value;

    fetch('search.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'keyword=' + encodeURIComponent(keyword)
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('tableArea').innerHTML = data;
    })
    .catch(err => console.log(err));
});
</script>
</body>
</html>
