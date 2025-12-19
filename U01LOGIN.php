<?php
session_start();

$error = "";

// --------------------------------------------------
// 基本設定
// --------------------------------------------------
// 本番用（ロリポップ）
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';              // ←ここを修正 (短くする)
$db_pass = '6group'; 
$db_name = 'LAA1685019-kondatehausu'; // ←ここを修正 (長くする)

// パソコン(XAMPP)用の上書き設定
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $db_host = '127.0.0.1';
    $db_user = 'root';
    $db_pass = '';
    // ここにphpMyAdmin通りの名前を入れています
    $db_name = 'LAA1685019-kondatehausu'; 
}

// ...（以下続き） = 'LAA1685019-kondatehausu'; 

// --------------------------------------------------
// データベース接続（診断機能付き）
// --------------------------------------------------
// エラー報告を有効にする
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $mysqli->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // 【診断】接続に失敗した場合の特別処理
    if ($_SERVER['SERVER_NAME'] === 'localhost') {
        // データベース名を指定せずにサーバーにだけ接続してみる
        try {
            $check_con = new mysqli($db_host, $db_user, $db_pass);
            $result = $check_con->query("SHOW DATABASES");
            $db_list = [];
            while ($row = $result->fetch_row()) {
                $db_list[] = $row[0];
            }
            
            // 診断結果の表示
            echo "<div style='background:#fee; border:2px solid red; padding:20px; font-family:sans-serif;'>";
            echo "<h2 style='color:red;'>接続エラー診断</h2>";
            echo "<p>プログラムは「<strong>{$db_name}</strong>」というデータベースを探しましたが、見つかりませんでした。</p>";
            echo "<hr>";
            echo "<h3>現在、このパソコン(XAMPP)にあるデータベース一覧:</h3>";
            echo "<ul>";
            foreach ($db_list as $db) {
                // 名前が一致しているかチェックして強調表示
                if ($db === $db_name) {
                    echo "<li><strong>{$db}</strong> (←あるはずです！なぜ！？)</li>";
                } else {
                    echo "<li>{$db}</li>";
                }
            }
            echo "</ul>";
            echo "<p>※上記リストの中に、作りたいデータベースの名前が含まれていない場合、phpMyAdminで作成した場所が違う可能性があります。</p>";
            echo "<p>※リストにある場合、大文字・小文字がコードと完全に一致しているか確認してください。</p>";
            echo "</div>";
            exit; // 処理をここで止める
        } catch (Exception $ex) {
            die("サーバー自体に接続できませんでした: " . $ex->getMessage());
        }
    } else {
        // 本番環境のエラー
        die("データベース接続失敗: " . $e->getMessage());
    }
}

// --------------------------------------------------
// ログイン処理
// --------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["user_name"] ?? "";
    $input_code = $_POST["parent_account"] ?? "";

    if ($name && $input_code) {
        $sql = "SELECT * FROM parent_account WHERE family_code = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $input_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $_SESSION["user_name"] = $name; 
            $_SESSION["family_code"] = $row["family_code"];
            $_SESSION["parent_account_ID"] = $row["parent_account_ID"];
            header("Location: U06HOME.php");
            exit;
        } else {
            $error = "家族コードが正しくありません。";
        }
        $stmt->close();
    } else {
        $error = "すべての項目を入力してください。";
    }
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>献立家　ログイン</title>
<style>
    body {
        font-family: "Hiragino Sans", "Hiragino Kaku Gothic ProN", Meiryo, sans-serif;
        background-image: url('MMM.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #e8e4d9; 
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        color: #333;
    }
    .container {
        width: 300px;
        text-align: center;
        padding-top: 20px;
        padding-bottom: 20px;
    }
    .logo-plate {
        background-color: #dccbba;
        border: 1px solid #c0b0a0;
        border-radius: 6px;
        padding: 15px 0;
        width: 180px;
        margin: 0 auto 40px auto;
        font-size: 32px;
        font-family: "Hiragino Mincho ProN", "Yu Mincho", serif;
        font-weight: 900;
        letter-spacing: 2px;
        color: #1a1a1a;
        box-shadow: 3px 3px 8px rgba(0,0,0,0.2);
    }
    .intercom-body {
        background-color: #555352;
        width: 200px;
        height: 280px;
        border-radius: 15px;
        margin: 0 auto 30px auto;
        padding: 20px;
        box-sizing: border-box;
        box-shadow: 5px 5px 15px rgba(0,0,0,0.4), inset 1px 1px 2px rgba(255,255,255,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .intercom-screen {
        background-color: #3e3d3d;
        height: 110px;
        border-radius: 10px;
        box-shadow: inset 3px 3px 6px rgba(0,0,0,0.5);
        border: 1px solid #2a2a2a;
        margin-bottom: 15px;
        position: relative;
    }
    .intercom-screen::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60%;
        height: 70%;
        border-radius: 8px;
        border: 2px solid #303030;
    }
    .submit-button {
        background-color: #333130;
        color: #ccc;
        border: none;
        width: 100%;
        height: 100px;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        font-size: 13px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 15px;
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .submit-button:hover {
        background-color: #3a3837;
        color: #fff;
    }
    .submit-button:active {
        transform: translateY(2px);
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    .input-group {
        text-align: left;
        margin-bottom: 15px;
    }
    label {
        display: block;
        font-size: 12px;
        color: #444;
        margin-bottom: 5px;
        margin-left: 4px;
    }
    input[type="text"] {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 6px;
        background-color: #fdfdfd;
        box-shadow: inset 1px 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.05);
        box-sizing: border-box;
        font-size: 14px;
        color: #333;
    }
    .error {
        color: #cc0000;
        font-size: 12px;
        margin-bottom: 10px;
        font-weight: bold;
    }
    .link {
        margin-top: 25px;
        font-size: 14px;
        font-weight: bold;
    }
    .link a {
        color: #000;
        text-decoration: none;
    }
    .admin-link {
        margin-top: 40px;
        font-size: 13px;
    }
    .admin-link a {
        color: #999;
        text-decoration: none;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="container">
        <div class="logo-plate">献立家</div>
        <form method="POST" action="">
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="intercom-body">
                <div class="intercom-screen"></div>
                <button type="submit" class="submit-button">申請する</button>
            </div>
            <div class="input-group">
                <label>名前</label>
                <input type="text" name="user_name" placeholder="名前を入力" value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>">
            </div>
            <div class="input-group">
                <label>家族コードの入力（6文字英数以上）</label>
                <input type="text" name="parent_account" placeholder="家族コードを入力">
            </div>
        </form>
        <div class="link">
            <a href="U02MAKE.php">未登録の方はこちら</a>
        </div>
    </div>
</body>
</html>