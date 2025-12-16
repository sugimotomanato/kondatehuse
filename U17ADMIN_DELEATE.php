<?php
ob_start();
session_start();// ハッシュを読み込む
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']); // 1回表示したら消す
// ==========================================================
// DB接続
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT parent_account_ID, family_code, user_name FROM parent_account ORDER BY parent_account_ID ASC");
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
    background-image: url('haikei2.jpg'); /* ← これが背景画像！ */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: "ヒラギノ角ゴ ProN", sans-serif;
    text-align: center;
}

/* 全体中央寄せ */
.container {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* 検索ボックスエリア */
.search-box {
    width: 85%;
    max-width: 380px;
    margin-top: 120px;    /* ← ここで背景の上部に余白 */
    margin-bottom: 20px;
}

.search-box input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #777;
    border-radius: 6px;
    background: #fff;
}

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

/* 白カードエリア */
.table-card {
    width: 90%;
    max-width: 380px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    margin-bottom: 50px;
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
}

tr:last-child td {
    border-bottom: none;
}
  .error {
          color: red;
          font-size: 18px;
          margin: 10px 0;
        }
</style>


<body>
        <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
<div class="container">
         <h1>献立家(管理者専用)<h1>
<a href="U16ADMIN_HOME.php?id=<?= 0 ?>">戻る</a>
    <!-- 検索ボックス -->
    <div class="search-box">
        <input type="text" id="keyword" placeholder="検索">
        <button id="searchBtn">検索</button>
    </div>

    <!-- 白カード テーブル -->
    <div class="table-card" id="tableArea">

      <table>
    <tr>
        <th>ID</th>
        <th>家族コード</th>
        <th>ユーザー名</th>
    </tr>
<tbody id="tableBody"> 
    <?php foreach ($results as $row): ?>
    <tr>
        <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['parent_account_ID']) ?></a></td>
        <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['family_code']) ?></a></td>
        <td><a href="U18ADMIN_DELEATE_LAST.php?id=<?= urlencode($row['parent_account_ID']) ?>"><?= htmlspecialchars($row['user_name']) ?></a></td>
    </tr>
    <?php endforeach; ?>
</table>

    </div>

</div>


<script>
// AJAX検索
document.getElementById('searchBtn').addEventListener('click', function() {
    const keyword = document.getElementById('keyword').value;

    fetch('search.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'keyword=' + encodeURIComponent(keyword)
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('tableBody').innerHTML = data;
    });
});

</script>

</body>
</html>