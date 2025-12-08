<?php
// U05FAV.php: お気に入り一覧画面

// ==========================================================
// 1. データベース接続設定
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan'; 
$db_user = 'LAA1685019-kondatehausu'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019'; 

$favorite_recipes = [];
$user_name = "ゲスト"; 
$error_message = null;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1-1. お気に入りレシピの取得
    // (注意) 正確にはユーザーIDとお気に入りテーブルを結合する必要がありますが、
    // ここではデモとしてhert(いいね)が0より大きいレシピを取得します。
    $sql_fav = "SELECT recipe_id, title, hert, image_path FROM recipe WHERE hert > 0 ORDER BY hert DESC";
    $stmt_fav = $pdo->query($sql_fav);
    $favorite_recipes = $stmt_fav->fetchAll(PDO::FETCH_ASSOC);

    // 1-2. ユーザー情報の取得 (サイドメニュー用: ID=1固定、parent_accountテーブル参照)
    $stmt_user = $pdo->prepare("SELECT user_name, icon FROM parent_account WHERE parent_account_ID = ?");
    $stmt_user->execute([1]);
    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
    if ($user_data && !empty($user_data['user_name'])) {
        $user_name = htmlspecialchars($user_data['user_name']);
    }

} catch (PDOException $e) {
    // エラーハンドリング
    $error_message = "データベース接続またはデータ取得中にエラーが発生しました。DB情報をご確認ください。";
}

// --------------------------------------------------------------------------
// 2. データがない場合 または エラー時のダミーデータ
// --------------------------------------------------------------------------
if (empty($favorite_recipes)) {
    // 画像の雰囲気に合わせてダミーを複数用意
    for ($i = 1; $i <= 8; $i++) {
        $favorite_recipes[] = [
            'recipe_id' => $i,
            'title' => 'ハンバーグ定食',
            'hert' => 3, // ダミーのランク用
            'image_path' => '' // 空文字ならダミー画像を表示
        ];
    }
    if ($error_message) {
        $error_message .= " ダミーデータで表示しています。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans JP', sans-serif; }

        /* 背景画像設定 (2枚目の画像のようなボケた食卓背景) */
        .main-bg {
            /* ユーザーの画像にあった背景ファイル名を使用 */
            background-image: url('haikei2.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        /* 全体のオーバーレイ (半透明の白) */
        .content-overlay {
            background-color: rgba(255, 255, 255, 0.6); 
            min-height: 100vh;
            backdrop-filter: blur(3px); 
        }

        /* サイドメニュー (ドロワー) スタイル */
        .drawer {
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: 80%;
            max-width: 300px;
            background-color: white;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            z-index: 50;
        }
        .drawer.open { transform: translateX(0); }
        
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s;
            z-index: 40;
        }
        .drawer-overlay.open { opacity: 1; visibility: visible; }

        /* 期間選択メニュー (画像 a118fb.png の要素を再現) */
        .period-menu {
            writing-mode: vertical-rl;
            text-orientation: upright;
            position: fixed;
            left: 0;
            top: 15%; 
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 5px;
            border-radius: 0 8px 8px 0;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            z-index: 20;
        }
        .period-menu a {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            padding: 2px;
            font-weight: 500;
        }
        .period-menu a.active {
            font-weight: bold;
            color: #ef4444; /* red-500 */
        }
    </style>
</head>
<body class="main-bg">
    <div class="content-overlay">

        <header class="flex justify-between items-start px-4 pt-6 pb-2 sticky top-0 z-10 bg-white/50 backdrop-blur-sm">
            <div>
                <h1 class="text-3xl font-bold text-black drop-shadow-sm mb-1">お気に入り</h1>
                
                <div class="flex items-center bg-white/80 rounded px-1 py-0.5 w-max shadow-sm border border-gray-200">
                    <span class="text-sm font-bold text-gray-700 mr-1">今日</span>
                    <i class="fa-solid fa-caret-down text-gray-400 text-xs mr-1"></i>
                    <span class="text-sm font-bold text-black">の人気献立</span>
                </div>
            </div>

            <button id="menu-btn" class="text-black text-3xl focus:outline-none mt-1">
                <i class="fa-solid fa-bars"></i>
            </button>
        </header>
        
        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-4 my-4 rounded" role="alert">
                <p class="font-bold">エラー</p>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <div class="period-menu">
            <a href="#" class="active">ランダム</a>
            <a href="#">翌年</a>
            <a href="#">先月</a>
            <a href="#">先週</a>
        </div>

        <main class="p-4 pl-16 pb-20">
            <div class="grid grid-cols-2 gap-3">
                <?php foreach ($favorite_recipes as $index => $recipe): ?>
                    <?php 
                        $img_src = !empty($recipe['image_path']) ? htmlspecialchars($recipe['image_path']) : 'https://placehold.co/300x200/e2e8f0/94a3b8?text=No+Image';
                        // DBからhert数を取得し、今回はダミーで順位を3位固定とします（画像再現のため）
                        $rank = $recipe['hert'] ?? 3; 
                    ?>
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 relative">
                        <a href="U12DETAIL.php?recipe_id=<?php echo $recipe['recipe_id']; ?>" class="block h-28 w-full bg-gray-200">
                            <img src="<?php echo $img_src; ?>" alt="レシピ画像" class="w-full h-full object-cover">
                        </a>

                        <div class="absolute top-1 right-1 bg-white/90 backdrop-blur rounded-full px-2 py-0.5 text-[10px] font-bold shadow-sm text-gray-700">
                            3位 </div>

                        <button class="absolute top-20 right-2 w-7 h-7 bg-white rounded-full shadow-sm flex items-center justify-center z-10">
                            <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                        </button>

                        <div class="p-2 text-center">
                            <h3 class="font-bold text-sm text-gray-800 truncate mb-1">
                                <?php echo htmlspecialchars($recipe['title']); ?>
                            </h3>
                            <div class="flex justify-between items-center px-1">
                                <span class="text-[10px] text-gray-400">レシピや詳細</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

    </div>

    <div id="drawer-overlay" class="drawer-overlay"></div>
    <div id="drawer" class="drawer flex flex-col">
        <button id="close-drawer" class="absolute top-4 right-4 text-gray-500 text-2xl focus:outline-none">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="p-6 mt-8">
            <div class="mb-4 text-yellow-400 text-2xl"><i class="fa-solid fa-bell"></i></div>
            <div class="text-xs text-gray-500 mb-8">家族コード <span class="font-bold text-black text-sm">A12345</span></div>
            
            <div class="flex flex-col items-center mb-10">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-4xl shadow-inner mb-3">
                    <span>😷</span>
                </div>
                <h2 class="font-bold text-lg text-gray-800 border-b border-gray-300 pb-1 w-full text-center">
                    <?php echo $user_name; ?>
                </h2>
            </div>

            <nav class="flex-col space-y-4 text-gray-700 font-bold">
                <a href="U14LIST.php" class="block hover:text-red-500 transition">買い物リスト</a>
                <div class="h-px bg-gray-200 my-2"></div>
                <a href="U06HOME.php" class="block hover:text-red-500 transition">ホームへ戻る</a>
            </nav>
        </div>
    </div>

    <script>
        // ドロワー制御
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-drawer');
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawer-overlay');

        function toggleDrawer() {
            drawer.classList.toggle('open');
            overlay.classList.toggle('open');
        }

        menuBtn.addEventListener('click', toggleDrawer);
        closeBtn.addEventListener('click', toggleDrawer);
        overlay.addEventListener('click', toggleDrawer);
    </script>
</body>
</html>