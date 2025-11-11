<?php
// エラーメッセージを初期化
$errors = [];
$code = '';
$name = '';
// ==========================================================
// データベース接続設定
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019-kondatehausu'; 

    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 管理者を取得
$stmt = $pdo->prepare("SELECT parent_account_ID, parent_account, user_name FROM parent_account");


          $stmt->execute();


    // 結果を配列で取得
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "DB接続エラー: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    table {
        border-collapse: collapse;
        width: 60%;
        margin: 20px auto;
    }
    th, td {
        border: 1px solid #aaa;
        padding: 8px 12px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
        body {
          background-image: url('images/haikei2.jpg');
          background-size: cover;      /* 画面全体にフィット */
          background-position: center; /* 中央に配置 */
          background-repeat: no-repeat;/* 繰り返さない */
        }
      
</style>
</head>
<body>
    <img src="kondatehuse/haikei2.jpg" alt="料理の写真" width="400" style="margin-top: 120px; margin-bottom: 120px;"><br>
    <h2 style="text-align:center;">ユーザー一覧</h2>
<input type="text" id="keyword" placeholder="入力">
<button id="searchBtn">検索</button>
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
        <tr><td colspan="3">データがありません。</td></tr>
    <?php endif; ?>

<script>
// 🔍 検索ボタンクリック時の処理
document.getElementById('searchBtn').addEventListener('click', function() {
    const keyword = document.getElementById('keyword').value;

    fetch('search.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'keyword=' + encodeURIComponent(keyword)
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('tableArea').innerHTML = data; // 結果を表エリアに差し替え
    })
    .catch(error => console.error('Error:', error));
});
</script>

</table>
</body>
</html>