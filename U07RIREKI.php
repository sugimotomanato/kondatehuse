<?php
// =================================================================
// 1. DB接続設定とデータ取得
// =================================================================
// 提供された情報に基づいて設定
$host = 'mysql320.phy.lolipop.lan';
$db   = 'LAA1685019-kondatehausu'; 
$user = 'LAA1685019';       
$pass = '6group';           
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$recipes = []; 
$error_message = null;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 修正: 料理を完全にランダムに表示 (日にち指定なし)
    $stmt = $pdo->query('SELECT recipe_id, title, image_path FROM recipe ORDER BY RAND()');
    $recipes = $stmt->fetchAll();
    
} catch (\PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    $error_message = "料理データの取得中にエラーが発生しました。設定を確認してください。";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>献立ホーム画面</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Interフォントとカスタム設定
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-pink': '#000000', 
                        'secondary-gray': '#D1D5DB', 
                        'accent-yellow': '#FFD700', 
                        'light-bg': '#F9FAFB', 
                        'card-border': '#E5E7EB', 
                        'notify-red': '#EF4444', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                    }
                }
            }
        }
    </script>
    <style>
        /* スクロールバーを非表示にする（iOS/Android風） */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* 画面全体をモバイルの縦幅に合わせて最大化 */
        body, html {
            height: 100%;
        }

        /* 背景画像の設定 */
        .main-content {
            padding-bottom: 190px; 
            min-height: calc(100vh - 72px);
            background-image: url('haikei2.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: scroll; 
            background-color: transparent; 
        }
        
        /* UI要素の背景を半透明の白に変更し、背景を透けさせる */
        .ui-element-bg {
            /* スタイルを保持 */
        }

        /* ヘッダーの背景を調整 */
        .header-bg {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        /* 【修正点】カードの幅指定を解除し、高さを固定 */
        .meal-card {
            width: 100%; /* グリッドに幅を任せる */
            height: 160px; /* 高さを固定 */
            border-radius: 1rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06); 
            overflow: hidden;
            border: 1px solid #E5E7EB; 
            background-color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
        }

        /* サイドメニューのスタイル */
        .drawer {
            transition: transform 0.3s ease-out;
            transform: translateX(100%);
            width: 80%; 
        }
        .drawer.is-open {
            transform: translateX(0);
        }

        /* 通知ベルの赤いバッジ */
        .notification-bell {
            position: relative;
        }
        .notification-bell.has-notification::after {
            content: '';
            position: absolute;
            top: 4px; 
            right: 4px;
            width: 8px;
            height: 8px;
            background-color: #EF4444; 
            border-radius: 50%;
            border: 1px solid white; 
        }
        
        /* アイコン画像のコンテナスタイル */
        .user-icon-container {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="bg-light-bg font-sans">

    <div class="main-content max-w-md mx-auto shadow-lg overflow-x-hidden relative">

        <header class="p-4 flex flex-row justify-between items-center sticky top-0 z-10 border-b border-gray-100 header-bg">
            
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">献立一覧</h1>

            <button id="menu-button" class="p-2 text-gray-600 hover:text-gray-800 rounded-full transition duration-150 ui-element-bg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>
        
        <div class="p-4 space-y-6 ui-element-bg">

            <section class="mt-4"> 
                    <div id="calendar-grid" class="grid grid-cols-2 gap-4"> 
                        
                        <?php if ($error_message): ?>
                            <p class="text-red-500 font-bold col-span-2 text-center"><?php echo htmlspecialchars($error_message); ?></p>
                        <?php elseif (empty($recipes)): ?>
                            <p class="text-gray-500 col-span-2 text-center">登録された料理がありません。</p>
                        <?php else: ?>
                            <?php foreach ($recipes as $recipe): 
                                
                                // image_path が NULL または空の場合はプレースホルダーを使用
                                $image_url = !empty($recipe['image_path']) 
                                    ? htmlspecialchars($recipe['image_path']) 
                                    : "https://placehold.co/180x80/f0f0f0/333?text=" . urlencode($recipe['title']);
                                
                            ?>
                                <div class="meal-card relative" data-meal-id="<?php echo htmlspecialchars($recipe['recipe_id']); ?>">
                                    <div class="h-2/3 bg-gray-200" style="background-image: url('<?php echo $image_url; ?>'); background-size: cover; background-position: center;"></div>
                                    <div class="p-2">
                                        <h3 class="font-semibold text-gray-800 text-sm truncate">
                                            <?php echo htmlspecialchars($recipe['title']); ?>
                                        </h3>
                                        <p class="text-xs text-gray-500">レシピや評価</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                    </div>
                </section>
                </div>

    </div>

    <div id="drawer-backdrop" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden" onclick="closeDrawer()"></div>
    <div id="drawer" class="fixed top-0 right-0 h-full bg-white shadow-2xl z-40 drawer flex flex-col">
        <div class="flex-shrink-0">
            <div id="application-notification" class="hidden bg-gray-100 border-b border-gray-200 text-sm">
                <div class="flex justify-between items-center py-2 px-4">
                    <span class="text-gray-700">----から申請が届きました</span>
                    <div class="flex space-x-2">
                        <button class="text-sm text-green-600 font-bold" onclick="handleApplication('承認')">承認</button>
                        <button class="text-sm text-red-600 font-bold" onclick="handleApplication('拒否')">拒否</button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <button id="notification-bell-button" class="p-1 rounded-full notification-bell" onclick="toggleApplicationNotification()">
                        <span id="bell-icon" class="text-3xl">🔔</span>
                    </button>
                    <button class="text-gray-600 hover:text-gray-800" onclick="closeDrawer()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-8">家族コード <span class="font-bold text-gray-800">A12345</span></p>

                <div class="flex flex-col items-center mb-10">
                    <button id="user-icon-button" class="relative w-28 h-28 rounded-full shadow-md flex items-center justify-center mb-4 transition duration-150 user-icon-container" onclick="changeIconImage()">
                        <div id="user-icon-background" class="w-full h-full rounded-full bg-gray-300 transition-opacity duration-300"></div>
                        <div id="user-emoji" class="absolute text-5xl transition-opacity duration-300"></div>
                    </button>

                    <p id="user-name" class="text-lg font-bold text-gray-700 p-1 border-b border-gray-300 cursor-pointer hover:bg-gray-100 transition duration-150" onclick="editName()">
                        [自分の名前]
                    </p>
                </div>

                <nav class="space-y-6 text-gray-700 text-lg font-semibold">
                    <a href="U14.php" class="block hover:text-primary-pink transition duration-150">買い物リスト
                    </a>
                    <a href="#" class="block hover:text-primary-pink transition duration-150" onclick="showMessageBox('グループ削除画面へ遷移します。'); closeDrawer(); return false;">
                        グループ削除
                    </a>
                    <a href="U07.php" class="block hover:text-primary-pink transition duration-150">ホーム
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300" onclick="closeMessageBox()">
        <div class="bg-white p-6 rounded-xl shadow-2xl max-w-xs w-full text-center transform transition-transform duration-300" onclick="event.stopPropagation()">
            <p id="message-text" class="text-gray-800 font-semibold mb-4"></p>
            <button class="bg-primary-pink text-white px-4 py-2 rounded-lg font-bold hover:bg-primary-pink/80 transition duration-150" onclick="closeMessageBox()">OK</button>
        </div>
    </div>

    <script>
        let userName = "[自分の名前]";
        let currentEmoji = "😊"; 
        let hasNotification = true; 
        let userIconUrl = ""; 

        // ダミーのアイコン画像選択肢
        const ICON_OPTIONS = {
            "デフォルト (灰色)": "",
            "アイコンA": "https://placehold.co/100x100/1e40af/ffffff?text=IconA",
            "アイコンB": "https://placehold.co/100x100/dc2626/ffffff?text=IconB",
            "アイコンC": "https://placehold.co/100x100/059669/ffffff?text=IconC"
        };
        const ICON_NAMES = Object.keys(ICON_OPTIONS);

        // DOM要素
        const drawer = document.getElementById('drawer');
        const drawerBackdrop = document.getElementById('drawer-backdrop');
        const menuButton = document.getElementById('menu-button');
        const userNameElement = document.getElementById('user-name');
        const userEmojiElement = document.getElementById('user-emoji');
        const userIconBackground = document.getElementById('user-icon-background'); 
        const bellButton = document.getElementById('notification-bell-button');
        const applicationNotification = document.getElementById('application-notification');
        
        // ユーザー名、絵文字、通知の初期設定
        userNameElement.textContent = userName;
        updateBellNotification();
        updateUserIcon();

        // メッセージ表示関数 (alertの代替)
        function showMessageBox(message) {
            document.getElementById('message-text').textContent = message;
            document.getElementById('message-box').classList.remove('hidden');
            document.getElementById('message-box').classList.add('flex');
        }

        function closeMessageBox() {
            document.getElementById('message-box').classList.remove('flex');
            document.getElementById('message-box').classList.add('hidden');
        }

        // --- サイドメニュー関連の処理 ---
        menuButton.addEventListener('click', openDrawer);
        function openDrawer() {
            drawer.classList.add('is-open');
            drawerBackdrop.classList.remove('hidden');
        }
        function closeDrawer() {
            drawer.classList.remove('is-open');
            drawerBackdrop.classList.add('hidden');
            applicationNotification.classList.add('hidden');
        }

        // --- ユーザー設定関連の処理 ---
        function editName() {
            const newName = prompt("新しい名前を入力してください:", userName);
            if (newName !== null && newName.trim() !== "") {
                userName = newName.trim();
                userNameElement.textContent = userName;
            }
        }
        
        function changeIconImage() {
            const promptMessage = `新しいアイコン画像を選択してください:\n${ICON_NAMES.join(', ')}`;
            const selection = prompt(promptMessage, ICON_NAMES[0]);

            if (selection !== null && ICON_OPTIONS.hasOwnProperty(selection)) {
                userIconUrl = ICON_OPTIONS[selection];
                updateUserIcon();
                showMessageBox(`アイコン画像を「${selection}」に変更しました。`);
            } else if (selection !== null) {
                showMessageBox("無効な選択です。");
            }
        }
        function updateUserIcon() {
            if (userIconUrl) {
                userIconBackground.style.backgroundImage = `url('${userIconUrl}')`;
                userIconBackground.style.backgroundColor = 'transparent';
            } else {
                userIconBackground.style.backgroundImage = 'none';
                userIconBackground.style.backgroundColor = '#D1D5DB'; 
            }
            if (currentEmoji) {
                userEmojiElement.textContent = currentEmoji;
                userEmojiElement.classList.remove('opacity-0');
            } else {
                userEmojiElement.textContent = "";
                userEmojiElement.classList.add('opacity-0');
            }
        }

        // --- 通知関連の処理 ---
        function updateBellNotification() {
            if (hasNotification) {
                bellButton.classList.add('has-notification');
                bellButton.classList.add('text-yellow-500'); 
            } else {
                bellButton.classList.remove('has-notification');
                bellButton.classList.remove('text-yellow-500');
            }
        }
        function toggleApplicationNotification() {
            if (applicationNotification.classList.contains('hidden')) {
                applicationNotification.classList.remove('hidden');
            } else {
                applicationNotification.classList.add('hidden');
            }
        }
        function handleApplication(action) {
            showMessageBox(`グループへの参加を「${action}」しました。`);
            applicationNotification.classList.add('hidden');
            hasNotification = false; 
            updateBellNotification();
        }

        // --- ホーム画面の機能 ---
        
        // 1. 献立カード（.meal-card）をクリックしたら料理詳細画面へ遷移
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.meal-card');
            if (card) {
                const mealId = card.getAttribute('data-meal-id');
                showMessageBox(`料理ID: ${mealId} の詳細画面へ遷移します。`);
            }
        });

        // 2. 検索バーがクリックされたときの処理 (ダミーコード)
        function handleSearchClick() {
             showMessageBox('検索画面へ遷移します。');
        }

    </script>
</body>
</html>