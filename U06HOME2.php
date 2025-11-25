<?php
// U06HOME.php: ホーム画面 (1画面表示 & モーダル統合版)

// ==========================================================
// データベース接続設定 (前回と同じ)
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan'; 
$db_user = 'LAA1685019-kondatehausu'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019'; 

// ==========================================================
// データベースからのデータ取得 (前回と同じロジック)
// ==========================================================
$recipes = [];
$family_members = [];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. レシピの取得 (人気献立用: いいね数/hertが多い上位3件を取得)
    $stmt_recipes = $pdo->query("SELECT recipe_id, title, hert, group_id FROM recipe ORDER BY hert DESC LIMIT 3");
    $recipes = $stmt_recipes->fetchAll(PDO::FETCH_ASSOC);

    // 2. 家族メンバー（親アカウント）の取得 (フッターアイコン用)
    $stmt_users = $pdo->query("SELECT parent_account_ID, name, icon FROM parent_account");
    $db_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    
    $fixed_emojis = ['😀', '😎', '😥', '😭', '😨', '😊', '😜']; 
    $current_user_id = 1; // ログインユーザーをID=1と仮定

    foreach ($db_users as $i => $user) {
        $family_members[] = [
            'name' => htmlspecialchars($user['name']),
            'icon_emoji' => $fixed_emojis[$i % count($fixed_emojis)], 
            'is_self' => ($user['parent_account_ID'] == $current_user_id)
        ];
    }

} catch (PDOException $e) {
    // エラー時はダミーデータを使用
    if (empty($recipes)) {
        $recipes = [
            ['recipe_id' => 1, 'title' => 'ハンバーグ定食', 'hert' => 3],
            ['recipe_id' => 2, 'title' => 'カレーライス', 'hert' => 2],
            ['recipe_id' => 3, 'title' => 'オムライス', 'hert' => 1],
        ];
    }
    if (empty($family_members)) {
        $family_members = [
            ['name' => '自分', 'icon_emoji' => '😀', 'is_self' => true], 
            ['name' => '名前A', 'icon_emoji' => '😥', 'is_self' => false], 
        ];
    }
}

// 初期表示フィルター
$selected_filter = '今日'; 
// ==========================================================

// レシピカードを出力するヘルパー関数 (高さ調整)
function renderRecipeCard($recipe, $is_calendar = false) {
    $recipe_id = $recipe['recipe_id'] ?? $recipe['id'];
    $title = $recipe['title'] ?? 'タイトルなし';
    $hert = $recipe['hert'] ?? 0;
    
    $heart_icon = '<svg class="w-3 h-3 text-red-600 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';

    if ($is_calendar) {
        $top_right_overlay = '<div class="absolute top-1 right-1 bg-yellow-400 text-xs font-bold px-1.5 py-0.5 rounded-full shadow-lg">2(火)</div>';
        $bottom_right_info = ''; 
        $card_class = 'bg-yellow-50 shadow-inner shadow-yellow-200 border-yellow-300';
    } else {
        $top_right_overlay = '<div class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 bg-red-600 text-white text-xs font-bold rounded-full shadow-md">3位</div>';
        $bottom_right_info = '<div class="flex items-center text-xs text-gray-800 ml-1">' . $heart_icon . '<span class="ml-0.5">' . $hert . '</span></div>';
        $card_class = 'bg-white shadow-md border-gray-100';
    }

    // 【修正点】画像の高さを h-10 に縮小し、カード全体の高さを抑える
    $image_placeholder = '<div class="h-10 w-full bg-gray-300 flex items-center justify-center text-gray-500 text-xs">(写真)</div>';

    return '
        <a href="U12DETAIL.php?recipe_id=' . $recipe_id . '" class="block">
            <div class="rounded-xl overflow-hidden relative border ' . $card_class . '">
                ' . $image_placeholder . '
                
                ' . $top_right_overlay . '
                
                <div class="p-1 flex justify-between items-center text-center">
                    <p class="text-xs font-medium text-gray-800">' . htmlspecialchars($title) . '</p>
                    ' . $bottom_right_info . '
                </div>
            </div>
        </a>
    ';
}

// セクションを出力するヘルパー関数 (マージン調整)
function renderSection($title, $recipes, $is_calendar = false) {
    // 【修正点】垂直マージンを mb-4 に縮小
    echo '<div class="mb-4">';
    
    echo '<h2 class="text-base font-bold text-gray-800 mb-1 flex justify-between items-center">';
    echo '<span>' . htmlspecialchars($title) . '</span>';
    echo '<a href="#" class="text-xs text-red-600 font-medium ml-2"> > </a>';
    echo '</h2>';
    
    $grid_class = $is_calendar ? 'grid-cols-1' : 'grid-cols-3';

    echo '<div class="grid ' . $grid_class . ' gap-2">'; // gap-3 を gap-2 に縮小
    foreach ($recipes as $recipe) {
        echo renderRecipeCard($recipe, $is_calendar);
    }
    echo '</div>';
    echo '</div>';
}

// 家族アイコンを出力するヘルパー関数 (前回と同じ)
function renderFamilyIcon($member) {
    $is_self = $member['is_self'];
    $border_class = $is_self ? 'border-2 border-orange-500 shadow-md' : 'border border-gray-200 shadow-sm';
    $bg_class = $is_self ? 'bg-orange-100' : 'bg-gray-100';
    $text_color = $is_self ? 'text-orange-600 font-bold' : 'text-gray-600';
    $display_name = $is_self ? '自分' : $member['name']; 

    return '
        <div class="flex flex-col items-center text-center mx-1">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-3xl ' . $bg_class . ' ' . $border_class . '">
                ' . $member['icon_emoji'] . '
            </div>
            <span class="text-xs ' . $text_color . ' mt-1">' . htmlspecialchars($display_name) . '</span>
        </div>
    ';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U06ホーム</title>
　　<script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 【修正点】bodyとmainにFlexboxを適用し、スクロールを無効化 */
        body {
            font-family: sans-serif;
            margin: 0;
            /* Flexboxを適用 */
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden; /* スクロールを禁止 */
        }
        .main-container {
            /* main-containerが残りのスペースを埋め、内部にスクロールが発生しないようにする */
            flex-grow: 1;
            overflow: hidden; 
            padding-top: 8px; /* pt-2 を適用 */
            padding-bottom: 8px; 
            
            /* 背景画像の設定をmainに移動 */
            background-image: url('haikei1.jpg'); 
            background-attachment: fixed;;
            background-position: center;
            background-color: white; /* 背景画像がない場合のフォールバック */
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-red': '#090909ff', 
                    },
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const dateButton = document.getElementById('date-picker-button');
            const dateMenu = document.getElementById('date-picker-menu');
            const selectedText = document.getElementById('selected-date-text');
            const dateOptions = document.querySelectorAll('.date-option');
            
            // 【新規追加】右上のタブ（モーダル/サイドパネル）の要素
            const notifButton = document.getElementById('notification-button');
            const notifModal = document.getElementById('notification-modal');
            const settingsButton = document.getElementById('settings-button');
            const settingsPanel = document.getElementById('settings-panel');
            const closeSettings = document.getElementById('close-settings');


            // 1. 人気献立ドロップダウンのトグル
            dateButton.addEventListener('click', (event) => {
                event.stopPropagation(); 
                dateMenu.classList.toggle('hidden');
            });

            dateOptions.forEach(option => {
                option.addEventListener('click', () => {
                    selectedText.textContent = option.getAttribute('data-value'); 
                    dateMenu.classList.add('hidden');
                });
            });

            // 2. 右上タブの処理 (ページ移動なし)

            // 通知モーダルを開く
            notifButton.addEventListener('click', () => {
                notifModal.classList.remove('hidden');
            });

            // 通知モーダルを閉じる (背景クリックまたは閉じるボタン)
            notifModal.addEventListener('click', (event) => {
                if (event.target === notifModal) {
                    notifModal.classList.add('hidden');
                }
            });

            // 設定サイドパネルを開く
            settingsButton.addEventListener('click', () => {
                settingsPanel.classList.remove('translate-x-full');
            });

            // 設定サイドパネルを閉じる
            closeSettings.addEventListener('click', () => {
                settingsPanel.classList.add('translate-x-full');
            });

            // 3. メニュー外クリックでドロップダウンを閉じる
            document.addEventListener('click', (event) => {
                if (!dateButton.contains(event.target) && !dateMenu.contains(event.target)) {
                    dateMenu.classList.add('hidden');
                }
            });
        });
    </script>
</head>

<body class="bg-gray-50">

    <header class="flex-none bg-white shadow-sm px-4 py-3 flex justify-between items-center z-20">
        <h1 class="text-2xl font-bold text-gray-900">ホーム</h1>
        <div class="flex space-x-3">
            <div id="notification-button" class="text-gray-600 hover:text-gray-800 cursor-pointer">
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.93 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            </div>
            <div id="settings-button" class="text-gray-600 hover:text-gray-800 cursor-pointer">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </div>
        </div>
    </header>

    <main class="w-full max-w-sm mx-auto px-4 main-container flex flex-col justify-between">
        
        <div class="flex-none pt-2">
            <div class="mb-3">
                <div class="flex justify-start items-center mb-1">
                    <div class="relative">
                        <button id="date-picker-button" class="text-base font-bold text-gray-800 flex items-center hover:bg-gray-100 transition duration-150 p-1">
                            <span id="selected-date-text" class="text-red-600 border-b-2 border-red-600 pb-0.5"><?php echo htmlspecialchars($selected_filter); ?></span>
                            <span class="ml-1 font-normal text-gray-800">の人気献立</span>
                            <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="date-picker-menu" class="date-picker-menu absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-xl hidden p-1 z-30 min-w-max">
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="ランダム">ランダム</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="翌年">翌年</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="先月">先月</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="先週">先週</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="今日">今日</button>
                        </div>
                    </div>
                    <a href="#" class="text-xs text-red-600 font-medium ml-auto"> > </a>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <?php 
                    foreach ($recipes as $recipe) {
                        echo renderRecipeCard($recipe);
                    }
                    ?>
                </div>
            </div>

            <?php 

            $favorite_recipes_dummy = array_slice($recipes, 0, 2); 
            renderSection('お気に入り', $favorite_recipes_dummy);
            ?>

            <?php 
            $calendar_recipe_dummy = [
                ['recipe_id' => 4, 'title' => 'ハンバーグ定食', 'hert' => 0]
            ];
            renderSection('カレンダー', $calendar_recipe_dummy, true);
            ?>
        </div>


        <div class="text-center pt-2 pb-2">
            <h2 class="text-base font-bold text-gray-800 mb-2">今日の献立</h2>
            <div class="relative bg-gray-100 rounded-xl overflow-hidden shadow-lg p-4 flex items-center justify-center h-28">
                


                
                <a href="U11TOUROKU.php" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl hover:bg-gray-50 transition duration-150">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </a>
            </div>
            
            <p class="text-sm font-medium text-gray-800 mt-2">ハンバーグ定食</p>
        </div>

    </main>

    <footer class="flex-none bg-white p-3 border-t border-gray-200 shadow-2xl z-20">
        <div class="flex justify-start overflow-x-auto whitespace-nowrap mb-3 pb-1 -mx-2">
            <?php foreach ($family_members as $member): ?>
                <?php echo renderFamilyIcon($member); ?>
            <?php endforeach; ?>
        </div>
        
        <div class="relative">
            <input type="text" placeholder="検索" 
                   class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-full bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent text-sm">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </footer>


    <div id="notification-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-11/12 max-w-sm">
            <h2 class="text-xl font-bold mb-4">通知一覧 (の内容を表示)</h2>
            <p class="text-sm text-gray-600">新しいいいねが3件あります。</p>
            <p class="text-sm text-gray-600">ハンバーグ定食がカレンダーに追加されました。</p>
            <button onclick="document.getElementById('notification-modal').classList.add('hidden')" class="mt-4 text-red-600 font-medium">閉じる</button>
        </div>
    </div>

    <div id="settings-panel" class="fixed top-0 right-0 h-full w-64 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">メニュー</h2>
            <a href="#" class="block py-2 text-gray-700 hover:bg-gray-100 rounded">アカウント設定</a>
            <a href="#" class="block py-2 text-gray-700 hover:bg-gray-100 rounded">家族コード管理</a>
            <a href="#" class="block py-2 text-gray-700 hover:bg-gray-100 rounded">ヘルプ</a>
            <div class="h-px bg-gray-200 my-3"></div>
            <a href="#" class="block py-2 text-red-600 hover:bg-red-50 rounded">ログアウト</a>
        </div>
        <button id="close-settings" class="absolute top-2 left-2 text-gray-500 p-2 hover:bg-gray-100 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

</body>
</html>