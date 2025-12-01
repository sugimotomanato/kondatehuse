<?php
// --- 状態の決定 ---
// URLクエリから 'view' パラメータを取得します。
// パラメータが存在しない場合、デフォルトで 'local' (ローカル) に設定します。
$view = $_GET['view'] ?? 'local';

// --- ビューに応じた設定 ---
// グローバルビューかどうかを判定するフラグ
$isGlobal = ($view === 'global');

// ページのタイトルをビューに応じて設定
$pageTitle = $isGlobal ? 'U13検索結果画面(グローバル)' : 'U12検索結果画面(ローカル)';

// --- ボタンのスタイルクラス ---
// 'local' ボタンがアクティブな時のスタイル
$localBtnClass = ($view === 'local')
    ? 'bg-zinc-800 text-white' // アクティブ（暗い背景、白い文字）
    : 'bg-white text-zinc-800'; // 非アクティブ（白い背景、暗い文字）

// 'global' ボタンがアクティブな時のスタイル
$globalBtnClass = ($view === 'global')
    ? 'bg-zinc-800 text-white' // アクティブ
    : 'bg-white text-zinc-800'; // 非アクティブ
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Tailwind CSS を読み込みます -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- フォント (InterとNoto Sans JP) を読み込みます -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        /* カスタムスタイル */
        body {
            /* 読み込んだフォントを適用します */
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
            /* 背景色のフォールバック */
            background-color: #f0f0f0; 
        }
        
        /* 背景画像（固定） */
        .bg-pattern {
            /* 画像のURL（Unsplashから） */
            background-image: url('https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=1740&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* 背景をスクロールに追従させない */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -2; /* 一番後ろに配置 */
        }

        /* 背景のぼかしオーバーレイ */
        .bg-overlay {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.6); /* 半透明の白 */
            backdrop-filter: blur(8px); /* ぼかし効果 */
            -webkit-backdrop-filter: blur(8px);
            z-index: -1; /* 背景画像の上に配置 */
        }

        /* メインコンテンツのコンテナ */
        .main-container {
            position: relative;
            z-index: 1; /* 前面に配置 */
        }

        /* 星アイコンのスタイル */
        .star-icon {
            width: 20px;
            height: 20px;
            fill: white; /* 白で塗りつぶし */
        }
    </style>
</head>
<body class="antialiased">

    <!-- 背景要素 -->
    <div class="bg-pattern"></div>
    <div class="bg-overlay"></div>

    <!-- メインコンテンツコンテナ（スマホ幅） -->
    <div class="main-container min-h-screen max-w-md mx-auto">
        
        <!-- ヘッダー -->
        <header class="p-4 flex justify-between items-center">
            <!-- ページタイトルをPHPで動的に表示 -->
            <h1 class="text-sm text-zinc-700 font-medium"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <div class="flex space-x-2">
                <!-- 戻る・ホームボタン（リンク先は # にしています） -->
                 <a href="U06HOME.php" class="px-3 py-1 text-sm bg-gray-200/80 backdrop-blur-sm rounded-full text-zinc-700 hover:bg-gray-300 transition">ホーム</a>
            </div>
        </header>

        <!-- トグルボタン -->
        <div class="p-4 pt-2">
            <div class="grid grid-cols-2 gap-0 bg-white/70 backdrop-blur-sm p-1.5 rounded-full shadow-lg">
                <!-- グローバルボタン: ?view=global へのリンク -->
                <a href="U12KENSAKU.php?view=global" class="py-3 px-6 text-center text-lg font-bold rounded-full transition-all <?php echo $globalBtnClass; ?>">
                    グローバル
                </a>
                <!-- ローカルボタン: ?view=local へのリンク -->
                <a href="U12KENSAKU.php?view=local" class="py-3 px-6 text-center text-lg font-bold rounded-full transition-all <?php echo $localBtnClass; ?>">
                    ローカル
                </a>
            </div>
        </div>

        <!-- 検索結果グリッド -->
        <main class="p-4 grid grid-cols-2 gap-4">
            <?php
            // --- カード描画用のヘルパー関数 ---
            // $rank = 0 の場合は星を、 $rank > 0 の場合は順位バッジを表示します
            function renderCard($rank = 0) {
                ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all hover:shadow-lg">
                    <!-- 画像プレースホルダー -->
                    <div class="relative bg-gray-300 aspect-square w-full">
                        
                        <?php if ($rank > 0): ?>
                            <!-- 順位バッジ (ランクが1以上の場合) -->
                            <div class="absolute top-2 left-2 bg-amber-400 text-zinc-900 text-sm font-bold px-3 py-1 rounded-full shadow">
                                <?php echo $rank; ?>位
                            </div>
                        <?php else: ?>
                            <!-- 星アイコン (ランクが0の場合) -->
                            <div class="absolute top-2 right-2 bg-black/40 rounded-full p-1 shadow">
                                <svg class="star-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                  <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.17 5.215a1.125 1.125 0 00.95.713h5.5c1.168 0 1.64 1.493.793 2.193l-4.455 3.24a1.125 1.125 0 00-.364 1.29l1.688 5.215c.34 1.052-.87 1.95-1.782 1.333l-4.455-3.24a1.125 1.125 0 00-1.32 0l-4.455 3.24c-.91.617-2.122-.28-1.782-1.333l1.688-5.215a1.125 1.125 0 00-.364-1.29L.998 11.33c-.847-.7-0.375-2.193.793-2.193h5.5a1.125 1.125 0 00.95-.713l2.17-5.215z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        <?php endif; ?>

                        <!-- 画像領域（グレーのまま） -->
                        <div class="w-full h-full"></div>
                    </div>
                    <!-- カード情報 -->
                    <div class="p-3">
                        <h3 class="font-semibold text-zinc-800 text-base">ハンバーグ定食</h3>
                        <a href="#" class="text-sm text-cyan-600 hover:underline">さらにくわしく</a>
                    </div>
                </div>
                <?php
            }
            // --- カード描画 ---
            if ($isGlobal) {
                // グローバルビュー: 上位2件に「3位」バッジ
                renderCard(3); // 1枚目 (3位)
                renderCard(3); // 2枚目 (3位)
                renderCard(0); // 3枚目 (星)
                renderCard(0); // 4枚目 (星)
                renderCard(0); // 5枚目 (星)
                renderCard(0); // 6枚目 (星)
            } else {
                // ローカルビュー: 全て星
                renderCard(0); // 1枚目 (星)
                renderCard(0); // 2枚目 (星)
                renderCard(0); // 3枚目 (星)
                renderCard(0); // 4枚目 (星)
                renderCard(0); // 5枚目 (星)
                renderCard(0); // 6枚目 (星)
            }
            ?>
        </main>
    </div>

</body>
</html>
