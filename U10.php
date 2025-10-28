<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U10BYOUKI | 状態設定</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 画像のクリーンなデザインに合わせた調整 */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
            /* body自体をflexコンテナとして、要素を中央に配置 */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* 上部に要素を寄せるため */
            padding: 20px;
            min-height: 100vh;
        }
        
        /* メインコンテナ - bodyのcenterに依存 */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center; /* これで子要素は水平方向に中央揃えされます */
            width: 100%;
            max-width: 380px; /* スマートフォンサイズに固定 */
        }

        /* アイコン列コンテナ (横長の白いカプセル) */
        .icon-row {
            padding: 12px 16px;
            background: white;
            border-radius: 40px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
            display: flex;
            gap: 12px;
            margin-top: 40px; 
            /* 左右のパディングを均等にするため、アイコン列自体も中央揃えを維持 */
        }

        .state-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            padding: 4px 0;
            border-radius: 8px;
            transition: transform 0.1s, opacity 0.1s;
            position: relative;
            user-select: none;
            width: 50px; 
        }
        
        .state-item:active {
            transform: scale(0.95);
            opacity: 0.8;
        }
        
        .state-item.selected .emoji-wrap {
            transform: scale(1.05);
            filter: drop-shadow(0 0 3px rgba(59, 130, 246, 0.5));
        }

        .emoji-wrap {
             transition: transform 0.2s;
        }

        .emoji {
            font-size: 38px; 
            line-height: 1;
        }

        .label {
            font-size: 11px;
            font-weight: 500;
            margin-top: 4px;
            color: #4a5568;
            white-space: nowrap;
        }

        /* 登録ボタンのスタイル調整 */
        .register-button {
            margin-top: 30px; 
            background-color: #38c172; 
            color: white;
            font-weight: 700;
            padding: 12px 60px; /* 幅を調整 */
            border-radius: 9999px;
            box-shadow: 0 4px 10px rgba(56, 193, 114, 0.4);
            transition: background-color 0.2s, transform 0.1s;
            /* 修正点：コンテナが中央揃えなので、ボタン自体はこれ以上調整不要 */
        }

        .register-button:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(56, 193, 114, 0.4);
        }

        /* リクエスト表示エリアの調整 */
        .request-indicator {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 150px; 
            margin-bottom: 20px;
        }

        /* リクエストの「もやもや」吹き出しの再現 */
        .request-bubble {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 6px 10px;
            font-size: 14px;
            color: #333;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.1s;
            position: relative;
            margin-bottom: 15px; 
        }
        
        /* 吹き出しのしっぽ */
        .request-bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 10px;
            height: 10px;
            background-color: white;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .user-icon-mock {
            width: 70px; 
            height: 70px;
            background-color: #cccccc;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* モーダルスタイル */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            display: none; 
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 350px;
        }
        
        .textarea-custom {
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            resize: none;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- コンテナを中央に配置 -->
    <div class="container">
        <!-- リクエストを書くエリア -->
        <div class="request-indicator">
            <div id="requestBubble" class="request-bubble" onclick="showRequestModal()">
                リクエストを書く
            </div>
            <div class="user-icon-mock"></div>
        </div>
        
        <!-- 状態選択アイコン列 -->
        <div class="icon-row">
            <div class="state-item" data-emoji="😷" data-label="病気">
                <span class="emoji-wrap"><span class="emoji">😷</span></span>
                <span class="label">病気</span>
            </div>
            <div class="state-item" data-emoji="🤕" data-label="怪我">
                <span class="emoji-wrap"><span class="emoji">🤕</span></span>
                <span class="label">怪我</span>
            </div>
            <div class="state-item" data-emoji="😥" data-label="悲しい">
                <span class="emoji-wrap"><span class="emoji">😥</span></span>
                <span class="label">悲しい</span>
            </div>
            <div class="state-item" data-emoji="😩" data-label="困った">
                <span class="emoji-wrap"><span class="emoji">😩</span></span>
                <span class="label">困った</span>
            </div>
            <div class="state-item" data-emoji="😐" data-label="普通">
                <span class="emoji-wrap"><span class="emoji">😐</span></span>
                <span class="label">普通</span>
            </div>
            <div class="state-item" data-emoji="😆" data-label="元気">
                <span class="emoji-wrap"><span class="emoji">😆</span></span>
                <span class="label">元気</span>
            </div>
        </div>

        <!-- 登録ボタン -->
        <button class="register-button" onclick="registerStatus()">登録</button>
    </div>

    <!-- リクエスト入力モーダル -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <h2 class="text-lg font-semibold mb-3">リクエストの入力</h2>
            <textarea id="requestInput" class="textarea-custom" placeholder="例：ハンバーグ定食、静かな環境が欲しい など"></textarea>
            <div class="flex justify-end gap-3 mt-4">
                <button class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="closeRequestModal()">キャンセル</button>
                <button class="px-4 py-2 text-sm text-white bg-blue-500 rounded-lg hover:bg-blue-600" onclick="saveRequest()">保存</button>
            </div>
        </div>
    </div>

    <script>
        // ローカルストレージキーの定義 (PHPとの連携をシミュレート)
        const STATUS_KEY = 'userStatus';
        const REQUEST_KEY = 'userRequest';

        let selectedEmoji = '';
        let selectedLabel = '';
        let currentRequest = localStorage.getItem(REQUEST_KEY) || '';

        // 初期表示時に関数を実行
        document.addEventListener('DOMContentLoaded', () => {
            loadInitialData();
            updateRequestBubble(currentRequest);
        });

        // 状態アイコンの選択ロジック
        const stateItems = document.querySelectorAll('.state-item');
        stateItems.forEach(item => {
            item.addEventListener('click', () => {
                // すべての選択状態を解除
                stateItems.forEach(i => i.classList.remove('selected'));
                
                // クリックされたアイテムを選択状態にする
                item.classList.add('selected');
                selectedEmoji = item.getAttribute('data-emoji');
                selectedLabel = item.getAttribute('data-label');
            });
        });

        // 初期データ（以前保存されていた状態）をロード
        function loadInitialData() {
            const savedStatus = localStorage.getItem(STATUS_KEY);
            const defaultEmoji = '😷'; // デフォルトは病気
            
            let initialEmoji = defaultEmoji;

            if (savedStatus) {
                try {
                    initialEmoji = JSON.parse(savedStatus).emoji;
                } catch (e) {
                    console.error("Error loading saved status:", e);
                }
            }

            // 初期絵文字を選択状態にする
            const initialItem = document.querySelector(`.state-item[data-emoji="${initialEmoji}"]`);
            if (initialItem) {
                initialItem.classList.add('selected');
                selectedEmoji = initialItem.getAttribute('data-emoji');
                selectedLabel = initialItem.getAttribute('data-label');
            } else {
                 // 該当するアイテムがない場合はデフォルトを選択
                document.querySelector(`.state-item[data-emoji="${defaultEmoji}"]`).classList.add('selected');
                selectedEmoji = defaultEmoji;
                selectedLabel = '病気';
            }
        }
        
        // リクエストバブルのテキストを更新する
        function updateRequestBubble(request) {
            const bubble = document.getElementById('requestBubble');
            if (request && request.trim() !== '') {
                // 画像に合わせて「ハンバーグ定食」をそのまま表示する例
                bubble.textContent = request.length > 10 ? request.substring(0, 10) + '...' : request;
            } else {
                bubble.textContent = 'リクエストを書く';
            }
        }

        // モーダルを表示
        function showRequestModal() {
            document.getElementById('requestInput').value = currentRequest;
            document.getElementById('requestModal').style.display = 'flex';
        }

        // モーダルを非表示
        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
        }

        // リクエストを保存
        function saveRequest() {
            const request = document.getElementById('requestInput').value.trim();
            currentRequest = request;
            localStorage.setItem(REQUEST_KEY, currentRequest);
            updateRequestBubble(currentRequest);
            closeRequestModal();
        }

        // 状態とリクエストを登録してホーム画面へ遷移
        function registerStatus() {
            // 選択された状態を保存
            const statusData = {
                emoji: selectedEmoji,
                label: selectedLabel
            };
            localStorage.setItem(STATUS_KEY, JSON.stringify(statusData));

            // U06HOME.htmlへ遷移
            window.location.href = 'U06HOME.php';
        }
    </script>
</body>
</html>
