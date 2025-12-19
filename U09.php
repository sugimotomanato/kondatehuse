<?php
// U09_DB.php - 人気の献立ランキング（データベース接続版）
session_start();

// 1. データベース接続設定 (U06HOME.phpから流用)
// 1. データベース接続設定
$db_host = 'mysql320.phy.lolipop.lan'; // 画像通り（OK）
$db_user = 'LAA1685019';               // 画像の「ユーザー名」に修正しました
$db_pass = '6group';                   // パスワードはそのまま
$db_name = 'LAA1685019-kondatehausu';  // 画像の「データベース名」に修正しました 

// ローカル環境(XAMPP)とロリポップ環境の自動切り替え
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
}

$meals = [];
$error_message = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. 献立データを全て取得 (人気順のソートは未実装のため、一旦ID順)
    // 実際には、likesテーブルなどとJOINしてLIKE数でソートするのが理想的です。
    $sql = "SELECT recipe_id, title, image_path FROM recipe ORDER BY recipe_id DESC";
    $stmt = $pdo->query($sql);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = 'データベース接続エラー: ' . $e->getMessage();
    // 本番環境では詳細なエラーメッセージは表示しないようにする
}

// 3. ランダムなデータ生成用
$userNames = ["Yuki", "Taro", "Hanako", "Ken", "Mika", "Satoshi", "Rina", "Momoko", "Daiki", "Aya", "Ren", "Kaito", "Yui", "Sora", "Hiro", "Mai", "Jun"];
$fallbackImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 24 24' fill='%23555'%3E%3Crect width='100%25' height='100%25' fill='%23333'/%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z' fill='%23777'/%3E%3C/svg%3E";

// 簡易な疑似乱数関数（seedがないため、時間ベースで近似）
function getPseudoRandomInt($max, $seed) {
    return ($seed * 9301 + 49297) % 233280 % $max;
}

?>
<!DOCTYPE html>
<html lang="ja" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>人気の献立</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind Config (変更なし)
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary-pink': '#fa2d48', /* Apple Music Pink */
                        'glass-bg': 'rgba(28, 28, 30, 0.75)',
                        'dark-card': '#1c1c1e',
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* スタイルシート (変更なし) */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        body, html { height: 100%; margin: 0; background-color: #000; color: white; }
        .main-background {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background-color: #000;
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.9)), url('haikei1.jpg');
            background-size: cover;
            background-position: center;
        }
        .glass-effect {
            background-color: rgba(30, 30, 30, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .meal-card {
            width: 100%;
            background: transparent;
            cursor: pointer;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }
        .meal-card:active { transform: scale(0.96); }
        .card-image-container {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            background-color: #2c2c2e;
            position: relative;
        }
        .fade-in-image {
            opacity: 0;
            transition: opacity 0.5s ease-in;
        }
        .fade-in-image.loaded {
            opacity: 1;
        }
        .drawer {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            transform: translateX(100%);
            width: 85%; 
            background-color: #1c1c1e;
            color: white;
        }
        .drawer.is-open { transform: translateX(0); }
        input[type="month"] {
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fa2d48; 
            border-radius: 6px;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="main-background"></div>

    <div class="max-w-md mx-auto h-full overflow-y-auto hide-scrollbar relative pb-10">
        <header class="p-5 flex flex-row justify-between items-end sticky top-0 z-20 glass-effect">
            <div class="flex flex-col">
                <h1 class="text-3xl font-extrabold tracking-tight text-white mb-1">ランキング</h1>
                <div class="flex items-center space-x-2">
                    <input type="month" id="month-picker" value="2025-10">
                </div>
            </div>
            <button id="menu-button" class="p-2 text-primary-pink hover:text-white transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <div class="p-5">
            <div class="mb-6 border-b border-gray-700 pb-2">
                <h2 class="text-xl font-bold text-white">人気の献立</h2>
                <p class="text-gray-400 text-sm">データベースからの投稿</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-900 border border-red-700 text-white p-4 rounded-lg">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <div id="meals-container" class="grid grid-cols-2 gap-x-4 gap-y-8">
                <?php 
                // 4. DBから取得したデータを表示
                foreach($meals as $index => $meal):
                    $user = $userNames[getPseudoRandomInt(count($userNames), $meal['recipe_id'])];
                    $iconSeed = $meal['recipe_id'] + 999;
                    $userIconUrl = "https://i.pravatar.cc/150?u={$iconSeed}";
                    // 簡易的ないいね数
                    $likes = 10 + getPseudoRandomInt(500, $meal['recipe_id'] * 10);
                    // 画像パスの確認
                    $imageUrl = !empty($meal['image_path']) ? htmlspecialchars($meal['image_path'], ENT_QUOTES, 'UTF-8') : $fallbackImage;
                    $title = htmlspecialchars($meal['title'], ENT_QUOTES, 'UTF-8');
                ?>
                <div class="meal-card group" onclick="showMessageBox('<?= $title ?>の詳細へ (ID: <?= $meal['recipe_id'] ?>)')">
                    <div class="card-image-container">
                        <img src="<?= $imageUrl ?>" alt="<?= $title ?>" 
                            class="w-full h-full object-cover fade-in-image bg-dark-card"
                            onload="this.classList.add('loaded')"
                            onerror="this.src='<?= $fallbackImage ?>'">
                        <div class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-md rounded-full px-2 py-1 text-[10px] font-bold text-white flex items-center">
                            <span class="text-primary-pink mr-1">♥</span> <?= $likes ?>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="font-bold text-white text-sm leading-tight mb-1 truncate"><?= $title ?></h3>
                        <div class="flex items-center space-x-2">
                            <img src="<?= $userIconUrl ?>" 
                                class="w-5 h-5 rounded-full object-cover border border-gray-600 bg-dark-card"
                                onerror="this.src='https://i.pravatar.cc/150?u=fallback'">
                            <p class="text-xs text-gray-400 truncate"><?= $user ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($meals)): ?>
                    <p class="text-gray-400 col-span-2">献立データがありません。</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="drawer-backdrop" class="fixed inset-0 bg-black bg-opacity-60 z-30 hidden" onclick="closeDrawer()"></div>
    <div id="drawer" class="fixed top-0 right-0 h-full shadow-2xl z-40 drawer flex flex-col border-l border-gray-800">
        <div class="p-6">
            <div class="flex justify-between items-start mb-8">
                <button id="notification-bell-button" class="p-2 rounded-full bg-gray-800">
                    <span class="text-2xl">🔔</span>
                </button>
                <button class="text-gray-400 hover:text-white" onclick="closeDrawer()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-col items-center mb-10">
                <img src="https://i.pravatar.cc/150?u=my_profile_v3" class="w-24 h-24 rounded-full mb-3 object-cover border-2 border-primary-pink bg-dark-card" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns...'">
                <p class="text-xl font-bold text-white">[自分の名前]</p>
                <p class="text-sm text-gray-500 mt-1">家族コード: <span class="font-mono text-white">A12345</span></p>
            </div>

            <nav class="space-y-6 text-lg font-semibold flex flex-col">
                <a href="U14LIST.php" class="text-gray-300 hover:text-primary-pink transition">買い物リスト</a>
                <a href="U07HOME.php" class="text-gray-300 hover:text-primary-pink transition">ホームに戻る</a>
                <div class="h-px bg-gray-800 my-2"></div>
            </nav>
        </div>
    </div>

    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-[#1c1c1e] p-6 rounded-2xl shadow-2xl max-w-xs w-full text-center border border-gray-700">
            <p id="message-text" class="text-white font-semibold mb-6 text-lg"></p>
            <button onclick="closeMessageBox()" class="bg-primary-pink text-white w-full py-3 rounded-xl font-bold">OK</button>
        </div>
    </div>

    <script>
        // JSのデータ生成・レンダリングロジックはPHPで置き換えたため削除

        // ドロワーとメッセージ
        const drawer = document.getElementById('drawer');
        const drawerBackdrop = document.getElementById('drawer-backdrop');

        document.getElementById('menu-button').addEventListener('click', () => {
            drawer.classList.add('is-open');
            drawerBackdrop.classList.remove('hidden');
        });

        function closeDrawer() {
            drawer.classList.remove('is-open');
            drawerBackdrop.classList.add('hidden');
        }

        function showMessageBox(msg) {
            document.getElementById('message-text').textContent = msg;
            document.getElementById('message-box').classList.remove('hidden');
            document.getElementById('message-box').classList.add('flex');
        }
        function closeMessageBox() {
            document.getElementById('message-box').classList.add('hidden');
            document.getElementById('message-box').classList.remove('flex');
        }

        // 月選択の変更イベントはダミーのまま残す
        const monthPicker = document.getElementById('month-picker');
        monthPicker.addEventListener('change', () => {
            // DB接続版では月による再レンダリングはしないため、単にコンソールに出力するだけに留める
            console.log('月が変更されました: ' + monthPicker.value);
            // 実際にはここでAjaxリクエストを行い、新しい月のデータを取得・表示します
        });
    </script>
</body>
</html>