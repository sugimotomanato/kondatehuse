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
<style>
body {
    margin: 0;
    padding: 0;
    background-image: url('images/haikei2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: "ヒラギノ角ゴ ProN", sans-serif;
}

/* 全体中央寄せ */
.container {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 40px;
}

/* 検索ボックス */
.search-box {
    width: 85%;
    max-width: 380px;
    margin-bottom: 10px;
}

.search-box input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #777;
    border-radius: 6px;
    background: #fff;
}

/* 検索ボタン */
.search-box button {
    margin-top: 5px;
    width: 100%;
    padding: 10px;
    background: #444;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
}

/* 白いカード（テーブル外枠） */
.table-card {
    width: 90%;
    max-width: 380px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
}

/* テーブル */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border-bottom: 1px solid #ccc;
    padding: 10px;
    text-align: left;
    font-size: 15px;
}

th {
    background: #f4f4f4;
    font-weight: bold;
}

tr:last-child td {
    border-bottom: none;
}
</style>
</head>
<body>

<div class="container">

    <!-- 検索ボックス -->
    <div class="search-box">
        <input type="text" id="keyword" placeholder="検索">
        <button id="searchBtn">検索</button>
    </div>

    <!-- 白いカード（検索結果エリア） -->
    <div class="table-card" id="tableArea">

        <table>
            <tr>
                <th>家族コード</th>
                <th>ユーザー名</th>
            </tr>

            <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row["parent_account"]) ?></td>
                <td><?= htmlspecialchars($row["user_name"]) ?></td>
            </tr>
            <?php endforeach; ?>

        </table>

    </div>

</div>

<script>
// AJAX検索処理
document.getElementById('searchBtn').addEventListener('click', function() {

    const keyword = document.getElementById('keyword').value;

    fetch('search.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'keyword=' + encodeURIComponent(keyword)
    })
    .then(res => res.text())
    .then(data => {
        // tableArea 内の HTML を完全に差し替え
        document.getElementById('tableArea').innerHTML = data;
    })
});
</script>

</body>
