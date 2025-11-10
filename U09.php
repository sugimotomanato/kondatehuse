<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>献立ホーム画面</title>
    <!-- Tailwind CSS CDNを読み込み -->
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
            padding-bottom: 190px; /* フッターの高さに合わせる */
            min-height: calc(100vh - 72px);
            
            /* 【修正点】ローカル実行用に相対パスに戻しました。*/
            /* HTMLファイルと同じフォルダに haikei.jpg を置いてください。*/
            background-image: url('haikei2.jpg'); 
            
            background-size: cover;
            background-position: center;
            background-attachment: scroll; 
            background-color: transparent; 
        }
        
        /* UI要素の背景を半透明の白に変更し、背景を透けさせる */
        .ui-element-bg {
            
            
        }

        /* カードの背景（半透明の白に統一） */
        .meal-card-bg {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
        }

        /* ヘッダーの背景を調整 */
        .header-bg {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        /* カードの見た目を統一 */
        .meal-card {
            width: 240px;
            height: 160px; 
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

        /* 日付選択ドロップダウンのスタイル */
        .date-picker-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 10;
            min-width: 120px;
        }
    </style>
</head>
<body class="bg-light-bg font-sans">

    <!-- メインコンテンツラッパー -->
    <div class="main-content max-w-md mx-auto shadow-lg overflow-x-hidden relative">

        <!-- ヘッダーエリア: ホームを左に寄せます -->
        <header class="p-4 flex flex-row justify-between items-center sticky top-0 z-10 border-b border-gray-100 header-bg">
            
            <!-- ホームタイトル (左寄せ) -->
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">お気に入り</h1>

            <!-- ハンバーガーメニュー (右端) - ドロワー開閉用 -->
            <button id="menu-button" class="p-2 text-gray-600 hover:text-gray-800 rounded-full transition duration-150 ui-element-bg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>
        
        <!-- メインスクロールエリア -->
        <div class="p-4 space-y-6 ui-element-bg">

<input type="month" id="month" name="month" value="2025-10" min="2000-01" max="2030-12">
</form>
<script>
 // 送信時の値は "2025-10" になる（必要ならスラッシュに変換）
 document.querySelector('form').addEventListener('submit', e => {
   e.preventDefault();
   const v = document.getElementById('month').value; // "2025-10"
   console.log(v);
   console.log(v.replace('-', '/')); // "2025/10"
 });
</script>
                    
                    
                </div>

                <section>
               
                <div id="calendar-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    
                    <!-- カード 1 (日付表示) -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="7">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+1'); background-size: cover;"></div>
                        <!-- 日付バッジ -->
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">1(月)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 2 (日付表示と赤いハイライト) -->
                    <div class="flex-shrink-0 meal-card relative border-2 border-yellow-500" data-meal-id="8">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+2'); background-size: cover;"></div>
                        <!-- 日付バッジ (赤色) -->
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">2(火)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 3 -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="9">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+3'); background-size: cover;"></div>
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">3(水)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. お気に入り (水平横スクロール) -->
            <section>
               
                <div id="calendar-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    
                    <!-- カード 1 (日付表示) -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="7">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+1'); background-size: cover;"></div>
                        <!-- 日付バッジ -->
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">1(月)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 2 (日付表示と赤いハイライト) -->
                    <div class="flex-shrink-0 meal-card relative border-2 border-yellow-500" data-meal-id="8">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+2'); background-size: cover;"></div>
                        <!-- 日付バッジ (赤色) -->
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">2(火)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 3 -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="9">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+3'); background-size: cover;"></div>
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">3(水)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. カレンダー (水平横スクロール) -->
            <section>
               
                <div id="calendar-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    
                    <!-- カード 1 (日付表示) -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="7">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+1'); background-size: cover;"></div>
                        <!-- 日付バッジ -->
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">1(月)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 2 (日付表示と赤いハイライト) -->
                    <div class="flex-shrink-0 meal-card relative border-2 border-yellow-500" data-meal-id="8">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+2'); background-size: cover;"></div>
                        <!-- 日付バッジ (赤色) -->
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">2(火)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 3 -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="9">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+3'); background-size: cover;"></div>
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">3(水)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>
                </div>
            </section>

            <section>
               
                <div id="calendar-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    
                    <!-- カード 1 (日付表示) -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="7">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+1'); background-size: cover;"></div>
                        <!-- 日付バッジ -->
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">1(月)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 2 (日付表示と赤いハイライト) -->
                    <div class="flex-shrink-0 meal-card relative border-2 border-yellow-500" data-meal-id="8">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+2'); background-size: cover;"></div>
                        <!-- 日付バッジ (赤色) -->
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">2(火)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>

                    <!-- カード 3 -->
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="9">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('https://placehold.co/240x106/f0f0f0/333?text=Date+3'); background-size: cover;"></div>
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">3(水)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>
                </div>
            </section>


           
        </div>

        
                </div>

                <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索画面へ遷移</title>
    <!-- Tailwind CSSの読み込み -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* フォントの指定 */
        body { font-family: "Inter", sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-start justify-center min-h-screen pt-40 md:pt-64">

    <div class="w-full max-w-xl px-4">
        <!-- 
            検索バーのコンテナ: 
            白背景、カプセル型の角丸 (rounded-full)、柔らかな影 (shadow-xl)、Flexでアイコンと入力を配置 
        -->
        
        <div id="alertMessage" class="mt-4 text-center text-red-600 opacity-0 transition-opacity duration-300">
            キーワードを入力してください。
        </div>
    </div>

    <script>
        /**
         * 検索ボタンのクリック、またはEnterキー押下時に実行される関数。
         * 検索キーワードを取得し、U13KENSAKU.phpへ遷移します。
         */
        function handleSearchClick() {
            const inputElement = document.getElementById('searchInput');
            const searchTerm = inputElement.value.trim();
            const alertElement = document.getElementById('alertMessage');
            
            // 1. 入力値が空でないかチェック
            if (searchTerm === "") {
                // エラーメッセージを表示
                alertElement.classList.remove('opacity-0');
                
                // 3秒後にメッセージを非表示にする
                setTimeout(() => {
                    alertElement.classList.add('opacity-0');
                }, 3000);
                
                return; // 処理を中断
            }

            // 2. U13KENSAKU.phpへ画面遷移する
            // 💡 補足: 検索キーワードをURLパラメータとして渡す場合は以下の形式を使います。
            // const destinationUrl = `U13KENSAKU.php?q=${encodeURIComponent(searchTerm)}`;
            
            const destinationUrl = "U13KENSAKU.php";
            
            console.log(`U13KENSAKU.phpへ遷移を開始します... (検索語: ${searchTerm})`);
            
            // 3. 画面遷移を実行
            window.location.href = destinationUrl;
        }

    </script>
</body>
</html>

            </div>
        </footer>

    </div>

    <!-- サイドメニュー (ドロワー) -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden" onclick="closeDrawer()"></div>
    <div id="drawer" class="fixed top-0 right-0 h-full bg-white shadow-2xl z-40 drawer flex flex-col">
        <div class="flex-shrink-0">
            <!-- 申請通知エリア (初期は非表示) -->
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
                <!-- 通知ベルとメニュー閉じるボタン -->
                <div class="flex justify-between items-start mb-6">
                    <!-- 通知ベル -->
                    <button id="notification-bell-button" class="p-1 rounded-full notification-bell" onclick="toggleApplicationNotification()">
                        <span id="bell-icon" class="text-3xl">🔔</span>
                    </button>
                    <!-- メニュー閉じるボタン -->
                    <button class="text-gray-600 hover:text-gray-800" onclick="closeDrawer()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                
                <!-- 家族コード -->
                <p class="text-sm text-gray-600 mb-8">家族コード <span class="font-bold text-gray-800">A12345</span></p>

                <!-- ユーザーアイコンと名前エリア -->
                <div class="flex flex-col items-center mb-10">
                    <!-- アイコンと絵文字のコンテナ -->
                    <button id="user-icon-button" class="relative w-28 h-28 rounded-full shadow-md flex items-center justify-center mb-4 transition duration-150 user-icon-container" onclick="changeIconImage()">
                        <!-- アイコンの背景を div に分離し、画像/灰色を設定 -->
                        <div id="user-icon-background" class="w-full h-full rounded-full bg-gray-300 transition-opacity duration-300"></div>
                        <!-- 絵文字 (中央に重ねて表示、アイコンより小さい) -->
                        <div id="user-emoji" class="absolute text-5xl transition-opacity duration-300"></div>
                    </button>

                    <!-- 名前 (クリックで編集可能) -->
                    <p id="user-name" class="text-lg font-bold text-gray-700 p-1 border-b border-gray-300 cursor-pointer hover:bg-gray-100 transition duration-150" onclick="editName()">
                        [自分の名前]
                    </p>
                </div>

                <!-- メニューリスト -->
                <nav class="space-y-6 text-gray-700 text-lg font-semibold">
                    <a href="U14LIST.php">買い物リスト
                    </a>
                    <a href="#" class="block hover:text-primary-pink transition duration-150" onclick="showMessageBox('グループ削除画面へ遷移します。'); closeDrawer(); return false;">
                        グループ削除
                    </a>
                    <nav class="space-y-6 text-gray-700 text-lg font-semibold">
                    <a href="U07HOME.php">ホーム
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- カスタムメッセージボックス (alertの代替) -->
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
        let currentSelection = "今日"; 

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
        const myReactionNameElement = document.getElementById('my-reaction-name');
        const userEmojiElement = document.getElementById('user-emoji');
        const userIconBackground = document.getElementById('user-icon-background'); 
        const bellButton = document.getElementById('notification-bell-button');
        const applicationNotification = document.getElementById('application-notification');
        const datePickerTrigger = document.getElementById('date-picker-trigger'); 
        const datePickerMenu = document.getElementById('date-picker-menu');      
        
        // ユーザー名、絵文字、通知、日付選択の初期設定
        userNameElement.textContent = userName;
        myReactionNameElement.textContent = userName;
        updateBellNotification();
        updateUserIcon();
        updatePopularHeading(currentSelection); 

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
                myReactionNameElement.textContent = userName; // 名前同期
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


        // --- ホーム画面の機能修正 ---
        
        // 1. 献立カード（.meal-card）をクリックしたら料理詳細画面に遷移
        document.querySelectorAll('.meal-card').forEach(card => {
            card.addEventListener('click', (e) => {
                const mealId = e.currentTarget.getAttribute('data-meal-id');
                showMessageBox(`料理ID: ${mealId} の詳細画面へ遷移します。`);
            });
        });

        // 2. 見出しの「へ移動」部分をクリックしたら詳細画面に遷移
        document.getElementById('popular-detail-link').addEventListener('click', () => {
            showMessageBox(`${currentSelection}の人気献立の詳細画面へ遷移します。`);
        });
        document.getElementById('favorite-detail-link').addEventListener('click', () => {
            showMessageBox('お気に入り献立の詳細画面へ遷移します。');
        });
        document.getElementById('calendar-detail-link').addEventListener('click', () => {
            showMessageBox('カレンダーの詳細画面へ遷移します。');
        });


        // 3. 日付ピッカー機能
        function toggleDatePickerMenu() {
            datePickerMenu.classList.toggle('hidden');
        }

        function updatePopularHeading(selection) {
            currentSelection = selection;
            // '今日' の部分を置き換え
            datePickerTrigger.childNodes[0].nodeValue = selection; 
            showMessageBox(`おかえりなさい！`);
        }

        // トリガーボタンにイベントリスナーを追加
        datePickerTrigger.addEventListener('click', toggleDatePickerMenu);

        // メニューアイテムにイベントリスナーを追加
        document.querySelectorAll('.date-option').forEach(button => {
            button.addEventListener('click', (e) => {
                const selection = e.target.getAttribute('data-value');
                updatePopularHeading(selection);
                closeDatePickerMenu();
            });
        });
        
        // メニュー外をクリックしたら閉じる
        document.addEventListener('click', (e) => {
            if (!datePickerTrigger.contains(e.target) && !datePickerMenu.contains(e.target)) {
                closeDatePickerMenu();
            }
        });

        function closeDatePickerMenu() {
            datePickerMenu.classList.add('hidden');
        }


        // 既存の機能 (トグル、登録、検索、リアクション)
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); // カード遷移をブロック
                const isLiked = e.currentTarget.classList.toggle('text-primary-pink');
                e.currentTarget.classList.toggle('text-secondary-gray', !isLiked);
                const svg = e.currentTarget.querySelector('svg');
                svg.classList.toggle('fill-current', isLiked);
                svg.classList.toggle('fill-none', !isLiked);
                svg.classList.toggle('stroke-current', !isLiked);
                if (isLiked) { showMessageBox('いいねしました！'); } else { showMessageBox('いいねを解除しました。'); }
            });
        });

        document.querySelectorAll('.star-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); // カード遷移をブロック
                const isStarred = e.currentTarget.classList.toggle('text-accent-yellow');
                e.currentTarget.classList.toggle('text-secondary-gray', !isStarred);
                e.currentTarget.querySelector('svg').classList.toggle('fill-current', isStarred);
                e.currentTarget.querySelector('svg').classList.toggle('fill-none', !isStarred);
                e.currentTarget.querySelector('svg').classList.toggle('stroke-current', !isStarred);
                if (isStarred) { showMessageBox('お気に入りに追加しました！'); } else { showMessageBox('お気に入りを解除しました。'); }
            });
        });

        // 検索バーがクリックされたときの処理
        function handleSearchClick() {
             showMessageBox('検索画面へ遷移します。');
        }

        document.querySelectorAll('.reaction-item').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelectorAll('.reaction-item').forEach(item => {
                    item.classList.remove('bg-primary-pink/10', 'border-primary-pink');
                    item.classList.add('bg-gray-100', 'border-transparent');
                    item.nextElementSibling.classList.remove('text-primary-pink');
                    item.nextElementSibling.classList.add('text-gray-500');
                });

                e.currentTarget.classList.remove('bg-gray-100', 'border-transparent');
                e.currentTarget.classList.add('bg-primary-pink/10', 'border-primary-pink');
                e.currentTarget.nextElementSibling.classList.remove('text-gray-500');
                e.currentTarget.nextElementSibling.classList.add('text-primary-pink');

                const name = e.currentTarget.nextElementSibling.textContent;
                showMessageBox(name + 'さんの献立リアクション履歴へ遷移します。');
            });
        });

    </script>
</body>
</html>
