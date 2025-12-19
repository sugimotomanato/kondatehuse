<?php
// U08okini.php - お気に入りの献立リスト（データベース接続版）
session_start();

// 1. データベース接続設定 (U06HOME.phpから流用)
$db_host = 'mysql320.phy.lolipop.lan'; 
$db_user = 'LAA1685019';               
$db_pass = '6group';                   
$db_name = 'LAA1685019-kondatehausu';  

// ローカル環境(XAMPP)とロリポップ環境の自動切り替え
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
}

// -----------------------------------------------------------
// 2. 【Ajax POST リクエストの処理】 (お気に入り更新) - U06HOME.phpから移植
// U08okini.php自身にPOSTされた場合、DBのお気に入り状態を更新
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['recipe_id']) && isset($input['okini'])) {
            $recipe_id = $input['recipe_id'];
            $okini = $input['okini']; 

            // okiniの状態を更新
            $sql = "UPDATE recipe SET okini = :okini WHERE recipe_id = :recipe_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':okini', $okini, PDO::PARAM_INT);
            $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'お気に入り状態を更新しました']);
        } else {
            echo json_encode(['success' => false, 'message' => 'データが不足しています (IDまたはokini値)']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DBエラー: ' . $e->getMessage()]);
    }
    // POSTリクエスト処理後、ここでスクリプトを終了
    exit; 
}
// -----------------------------------------------------------


// 3. 【通常のページ表示 (GETリクエスト) の処理】
$meals = [];
$error_message = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // okini = 1 の献立のみを取得
    $sql = "SELECT recipe_id, title, image_path, okini FROM recipe WHERE okini = 1 ORDER BY recipe_id DESC";
    $stmt = $pdo->query($sql);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = 'データベース接続エラー: ' . $e->getMessage();
}

$fallbackImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 24 24' fill='%23555'%3E%3Crect width='100%25' height='100%25' fill='%23333'/%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z' fill='%23777'/%3E%3C/svg%3E";

?>
<!DOCTYPE html>
<html lang="ja" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入りの献立</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // U06HOME.phpのTailwind設定に合わせる
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    colors: {
                        'primary-pink': '#EC4899', 
                        'secondary-gray': '#D1D5DB', 
                        'accent-yellow': '#FFD700', // 星の色
                        'light-bg': '#F9FAFB', 
                        'card-border': '#E5E7EB',
                        'notify-red': '#EF4444', 
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* スタイルシート */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* U06HOME.phpに合わせて全体をライトモードのデザインに寄せる */
        body, html { 
            height: 100%; 
            margin: 0; 
            background-color: rgba(255, 255, 255, 0.6); /* light-bg */
            color: #1F2937; /* gray-800 */
        }
        .main-background {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background-image: url('haikei1.jpg'); /* U06HOME.phpの背景 */
            background-size: cover;
            background-position: center;
        }
        .header-bg {
            background-color: rgba(255,255,255,0.9); 
            backdrop-filter: blur(10px);
        }
        /* U06HOME.phpのカードデザインに合わせる */
        .meal-card {
            height: 160px; /* U06HOME.phpに合わせて高さ固定 */
            width: 100%;
            border-radius: 1rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            overflow: hidden;
            border: 1px solid #E5E7EB; /* card-border */
            background-color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            transition: transform 0.2s, opacity 0.3s, scale 0.3s; /* 削除時のアニメーション用 */
            display: flex;
            flex-direction: column;
        }
        .meal-card:hover { transform: translateY(-2px); box-shadow: 0 6px 10px -1px rgba(0, 0, 0, 0.15); }
        .meal-card:active { transform: scale(0.96); }

        .card-image-container {
            width: 100%;
            height: 66.66%; /* h-2/3 */
            overflow: hidden;
            background-color: #E5E7EB; /* gray-200 */
            position: relative;
        }

        .drawer {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            transform: translateX(100%);
            width: 85%; 
            background-color: #fff;
            color: #1F2937;
        }
        .drawer.is-open { transform: translateX(0); }
        input[type="month"] {
            background: #E5E7EB;
            border: none;
            color: #EC4899; 
            border-radius: 6px;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-light-bg font-sans">

    <div class="main-background"></div>

    <div class="max-w-md mx-auto h-full overflow-y-auto hide-scrollbar relative pb-10">
        <header class="p-4 flex flex-row justify-between items-center sticky top-0 z-20 border-b border-gray-200 header-bg">
            <div class="flex flex-col">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-800 mb-0">お気に入り</h1>
                <div class="flex items-center space-x-2">
                    <input type="month" id="month-picker" value="2025-10" class="mt-1">
                </div>
            </div>
            <button id="menu-button" class="p-2 text-gray-600 hover:text-gray-800 transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <div class="p-4">
            <div class="mb-6 border-b border-gray-300 pb-2">
                <h2 class="text-xl font-bold text-gray-700">お気に入りの献立リスト</h2>
                <p class="text-gray-500 text-sm">星マークをつけた献立</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <div id="meals-container" class="grid grid-cols-2 gap-x-4 gap-y-8">
                <?php 
                // 4. DBから取得したデータを表示
                $rank = 1; // 順位のカウンタを初期化
                foreach($meals as $index => $meal):
                    $recipe_id = htmlspecialchars($meal['recipe_id'], ENT_QUOTES, 'UTF-8');
                    $title = htmlspecialchars($meal['title'], ENT_QUOTES, 'UTF-8');
                    $imageUrl = !empty($meal['image_path']) ? htmlspecialchars($meal['image_path'], ENT_QUOTES, 'UTF-8') : $fallbackImage;
                    
                    // お気に入りリストなので常に星は付いた状態
                    $starColorClass = 'text-accent-yellow'; 
                    $fillClass = 'fill-current';
                ?>
                <div class="meal-card" data-meal-id="<?= $recipe_id ?>" onclick="window.location.href='U24SYOUSAI.php?id=<?= $recipe_id ?>'">
                    <div class="card-image-container" style="background-image: url('<?= $imageUrl ?>'); background-size: cover; background-position: center;">
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <span class="text-xs font-bold text-gray-700"><?php echo $rank++; ?>位</span> 
                            <button class="star-button p-0.5 <?php echo $starColorClass; ?> transition duration-150" data-okini="1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo $fillClass; ?>" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-2 h-1/3 flex flex-col justify-center">
                        <h3 class="font-semibold text-gray-800 text-sm truncate"><?= $title ?></h3>
                        <p class="text-xs text-gray-500">レシピや評価</p>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($meals)): ?>
                    <p class="text-gray-500 col-span-2 italic p-4">お気に入りの献立はまだありません。ホーム画面から星ボタンを押して追加してください。</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="drawer-backdrop" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden" onclick="closeDrawer()"></div>
    <div id="drawer" class="fixed top-0 right-0 h-full bg-white shadow-2xl z-40 drawer flex flex-col border-l border-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-start mb-8">
                <button id="notification-bell-button" class="p-2 rounded-full bg-gray-100">
                    <span class="text-2xl">🔔</span>
                </button>
                <button class="text-gray-600 hover:text-gray-800" onclick="closeDrawer()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-col items-center mb-10">
                <img src="https://i.pravatar.cc/150?u=my_profile_v3" class="w-24 h-24 rounded-full mb-3 object-cover border-2 border-primary-pink bg-gray-200" onerror="this.src='data:image/svg+xml,%3Csvg xmlns...'">
                <p class="text-xl font-bold text-gray-800">[自分の名前]</p>
                <p class="text-sm text-gray-500 mt-1">家族コード: <span class="font-mono text-gray-700">A12345</span></p>
            </div>

            <nav class="space-y-6 text-lg font-semibold flex flex-col">
                <a href="U14LIST.php" class="text-gray-700 hover:text-primary-pink transition">買い物リスト</a>
                <a href="U06HOME.php" class="text-gray-700 hover:text-primary-pink transition">ホームに戻る</a>
                <div class="h-px bg-gray-200 my-2"></div>
            </nav>
        </div>
    </div>

    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300" onclick="closeMessageBox()">
        <div class="bg-white p-6 rounded-xl shadow-2xl max-w-xs w-full text-center transform transition-transform duration-300" onclick="event.stopPropagation()">
            <p id="message-text" class="text-gray-800 font-semibold mb-4"></p>
            <button class="bg-primary-pink text-white px-4 py-2 rounded-lg font-bold hover:bg-primary-pink/80 transition duration-150" onclick="closeMessageBox()">OK</button>
        </div>
    </div>

    <script>
        // ドロワーとメッセージボックスの制御
        const drawer = document.getElementById('drawer');
        const drawerBackdrop = document.getElementById('drawer-backdrop');
        const monthPicker = document.getElementById('month-picker');

        document.getElementById('menu-button').addEventListener('click', () => {
            drawer.classList.add('is-open');
            drawerBackdrop.classList.remove('hidden');
        });

        function closeDrawer() {
            drawer.classList.remove('is-open');
            drawerBackdrop.classList.add('hidden');
        }

        function showMessageBox(message) {
            const box = document.getElementById('message-box');
            document.getElementById('message-text').textContent = message;
            box.classList.remove('hidden');
            box.classList.add('flex');
        }
        function closeMessageBox() {
            const box = document.getElementById('message-box');
            box.classList.remove('flex');
            box.classList.add('hidden');
        }

        // 月選択の変更イベント (ダミー)
        monthPicker.addEventListener('change', () => {
            console.log('月が変更されました: ' + monthPicker.value);
            showMessageBox('月の変更機能は未実装です。');
        });

        // ★U06HOME.phpから移植した star-button のクリックイベント処理 (お気に入り解除)★
        document.querySelectorAll('.star-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); 
                const btn = e.currentTarget;
                const card = btn.closest('.meal-card');
                const recipeId = card.getAttribute('data-meal-id');
                
                // お気に入りリストなので、ここでは「解除(0)」のみを想定
                const newOkiniValue = 0; 

                // サーバーにデータを送る (U08okini.php自身にPOST)
                fetch(location.href, { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        recipe_id: recipeId, 
                        okini: newOkiniValue 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 成功したらアニメーション後、リストからカードを削除
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            card.remove();
                            // リストが空になった場合のメッセージ表示 (簡易対応)
                            const container = document.getElementById('meals-container');
                            if (container.children.length === 0) {
                                container.innerHTML = '<p class="text-gray-500 col-span-2 italic p-4">お気に入りの献立はまだありません。ホーム画面から星ボタンを押して追加してください。</p>';
                            }
                        }, 300);

                        showMessageBox('お気に入りを解除しました。\n（このカードはリストから削除されました）');
                    } else {
                        console.error('エラー:', data.message);
                        showMessageBox('更新に失敗しました (' + data.message + ')');
                    }
                })
                .catch(error => {
                    console.error('通信エラー:', error);
                    showMessageBox('通信エラーが発生しました: ' + error.message);
                });
            });
        });
    </script>
</body>
</html>