<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIメモ帳 (Gemini搭載)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* (CSSは変更ありません) */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background-color: #f4f5f7;
        }
        #memo-textarea {
            resize: none;
            min-height: 75vh; 
            font-size: 1rem;
            line-height: 1.7;
            white-space: pre-wrap;
            overflow-y: auto;
        }
        #memo-textarea::-webkit-scrollbar {
            width: 8px;
        }
        #memo-textarea::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 4px;
        }
        .gemini-button {
            @apply bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold py-2 px-4 rounded-lg transition duration-150 text-sm shadow-sm flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed;
        }
        .spinner {
            @apply animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500;
            border-color: #6366f1;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            border-width: 2px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="mx-auto max-w-3xl">
        <div class="bg-white shadow-lg rounded-2xl p-4 md:p-6">
            
            <!-- ★★★ ヘッダーエリア (変更点) ★★★ -->
            <div class="flex flex-wrap justify-between items-center mb-4 border-b pb-3 gap-y-3">
                <h1 class="text-2xl font-bold text-gray-800">メモ帳</h1>
                
                <!-- 右側のコントロールをグループ化 -->
                <div class="flex items-center space-x-4">
                    
                    <!-- ★★★ ホームに戻るボタン (追加) ★★★ -->
                    <a 
                        href="U06HOME.php" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-xl transition duration-150 text-sm flex items-center shadow-sm"
                        aria-label="ホームに戻る"
                    >
                        <!-- ホームアイコン -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        ホーム
                    </a>
                    <!-- ★★★★★★★★★★★★★★★★★★★★★ -->

                    <!-- Geminiローディングスピナー (通常は非表示) -->
                    <div id="gemini-loading" class="hidden">
                        <div class="spinner" role="status">
                            <span class="sr-only">読み込み中...</span>
                        </div>
                    </div>
                    <!-- 保存ステータス表示エリア -->
                    <div id="status-display" class="text-sm font-medium text-gray-500 flex items-center transition-opacity duration-300">
                        <!-- JSによって内容が挿入されます -->
                    </div>
                </div>
            </div>
            <!-- ★★★★★★★★★★★★★★★★★★★★★★★ -->


            <!-- Gemini API 機能ボタンエリア (変更なし) -->
            <div id="gemini-controls" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4 p-3 bg-gray-50 rounded-xl border">
                
            </div>

            <!-- メモ帳本体 (変更なし) -->
            <textarea 
                id="memo-textarea" 
                class="w-full p-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 outline-none transition duration-150 shadow-inner bg-gray-50"
                placeholder="ここにメモを入力してください... 「トピック」を入力して「アイデア出し」ボタンを押すこともできます。"
            ></textarea>

            <!-- クリアボタン (変更なし) -->
            <div class="mt-4 text-right">
                <button id="clear-button" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-xl transition duration-150 text-sm shadow-md">
                    メモを全消去
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript (変更なし) -->
    <script>
        // === DOM要素の取得 ===
        const memoTextarea = document.getElementById('memo-textarea');
        const statusDisplay = document.getElementById('status-display');
        const clearButton = document.getElementById('clear-button');
        
        // Gemini関連のDOM要素
        const geminiControls = document.getElementById('gemini-controls');
        const geminiButtons = geminiControls.querySelectorAll('button');
        const btnContinue = document.getElementById('btn-continue');
        const btnSummarize = document.getElementById('btn-summarize');
        const btnBrainstorm = document.getElementById('btn-brainstorm');
        const geminiLoading = document.getElementById('gemini-loading');

        // === ローカルストレージ保存機能 ===
        let statusTimer;

        /**
         * ステータスを表示し、一定時間後に非表示にする
         * @param {string} text - 表示するテキスト
         * @param {string} colorClass - Tailwindのテキスト色クラス
         * @param {number | null} duration - 自動で消えるまでの時間(ms)。nullの場合は消えない。
         */
        function showStatus(text, colorClass, duration = 1500) {
            clearTimeout(statusTimer);
            
            let iconSvg = '';
            if (colorClass === 'text-green-600') {
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
            } else if (colorClass === 'text-red-600') {
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
            }
            
            statusDisplay.innerHTML = iconSvg + `<span class="${colorClass}">${text}</span>`;
            statusDisplay.classList.remove('opacity-0');

            if (duration !== null) {
                statusTimer = setTimeout(() => {
                    statusDisplay.classList.add('opacity-0');
                }, duration);
            }
        }

        /**
         * ページ読み込み時の処理
         */
        window.addEventListener('load', () => {
            try {
                const savedMemo = localStorage.getItem('mySimpleMemo');
                if (savedMemo !== null) {
                    memoTextarea.value = savedMemo;
                }
                showStatus('読み込み完了', 'text-gray-500');
            } catch (e) {
                console.error('メモの読み込みに失敗しました:', e);
                memoTextarea.placeholder = 'メモの読み込み中にエラーが発生しました。';
            }
        });

        /**
         * テキストエリア入力時の処理 (ローカルストレージへの保存)
         */
        memoTextarea.addEventListener('input', () => {
            try {
                localStorage.setItem('mySimpleMemo', memoTextarea.value);
                showStatus('保存済み', 'text-green-600');
            } catch (e) {
                console.error('メモの保存に失敗しました:', e);
                showStatus('保存エラー', 'text-red-600');
            }
        });

        /**
         * クリアボタンクリック時の処理
         */
        clearButton.addEventListener('click', () => {
            if (window.confirm('本当にすべてのメモを消去しますか？この操作は元に戻せません。')) {
                try {
                    memoTextarea.value = '';
                    localStorage.removeItem('mySimpleMemo');
                    showStatus('消去しました', 'text-red-600');
                } catch (e) {
                    console.error('メモの消去に失敗しました:', e);
                    showStatus('消去エラー', 'text-red-600');
                }
            }
        });

        // === Gemini API 機能 ===

        // APIキー (空文字列のままにしておくと、Canvas環境が自動的にキーを挿入します)
        const apiKey = ""; 
        const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;

        /**
         * API呼び出し中のUI制御
         * @param {boolean} isLoading - ローディング中かどうか
         */
        function setGeminiLoading(isLoading) {
            if (isLoading) {
                geminiLoading.classList.remove('hidden');
                geminiButtons.forEach(button => button.disabled = true);
            } else {
                geminiLoading.classList.add('hidden');
                geminiButtons.forEach(button => button.disabled = false);
            }
        }

        /**
         * 指数関数的バックオフを用いた fetch リトライ
         */
        async function fetchWithBackoff(url, options, maxRetries = 3) {
            let delay = 1000; // 1秒から開始
            for (let i = 0; i < maxRetries; i++) {
                try {
                    const response = await fetch(url, options);
                    if (response.ok) {
                        return await response.json();
                    } else if (response.status === 429 || response.status >= 500) {
                        // スロットリングまたはサーバーエラーの場合、リトライ
                        console.warn(`API request failed (status ${response.status}). Retrying in ${delay}ms...`);
                        await new Promise(resolve => setTimeout(resolve, delay));
                        delay *= 2; // 次のディレイを2倍に
                    } else {
                        // クライアントエラー (400系) はリトライしない
                        throw new Error(`API request failed with status ${response.status}: ${await response.text()}`);
                    }
                } catch (error) {
                    if (i === maxRetries - 1) throw error; // 最後のリトライでも失敗
                    await new Promise(resolve => setTimeout(resolve, delay));
                    delay *= 2;
                }
            }
            throw new Error('API request failed after all retries.');
        }


        /**
         * Gemini API を呼び出す共通関数
         * @param {string} systemPrompt - システムプロンプト (AIの役割指示)
         * @param {string} userQuery - ユーザーの入力 (現在のメモ内容)
         */
        async function callGemini(systemPrompt, userQuery) {
            setGeminiLoading(true);
            showStatus('AIが応答中...', 'text-blue-600', null);

            const payload = {
                contents: [{ parts: [{ text: userQuery }] }],
                systemInstruction: {
                    parts: [{ text: systemPrompt }]
                },
            };

            try {
                const result = await fetchWithBackoff(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const text = result.candidates?.[0]?.content?.parts?.[0]?.text;

                if (!text) {
                    throw new Error('AIからの応答が空です。');
                }
                
                showStatus('AI応答完了', 'text-green-600');
                setGeminiLoading(false);
                return text; // 生成されたテキストを返す

            } catch (error) {
                console.error('Gemini API呼び出しエラー:', error);
                showStatus(`AIエラー: ${error.message}`, 'text-red-600', 5000);
                setGeminiLoading(false);
                return null; // エラー時は null を返す
            }
        }

        /**
         * テキストエリアにAIの応答を追記する
         * @param {string} header - 見出し (例: "--- 要約 ---")
         * @param {string} content - AIが生成した内容
         */
        function appendToMemo(header, content) {
            const currentText = memoTextarea.value;
            // headerが空でない場合のみ、見出しを追加
            const headerText = header ? `\n\n${header}\n` : "\n";
            const newText = `${currentText}${headerText}${content}\n`;
            
            memoTextarea.value = newText;
            // 手動で'input'イベントを発火させてローカルストレージに保存
            memoTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            // スクロールを一番下に移動
            memoTextarea.scrollTop = memoTextarea.scrollHeight;
        }

        // --- ボタンのイベントリスナー設定 ---

        /**
         * 「続きを書く」ボタン
         */
        btnContinue.addEventListener('click', async () => {
            const currentText = memoTextarea.value;
            if (currentText.trim().length < 10) {
                showStatus('10文字以上入力してください', 'text-red-600');
                return;
            }
            
            const systemPrompt = "あなたはプロのライターです。以下の文章の続きを、文脈を維持しながら自然な流れで作成してください。";
            const generatedText = await callGemini(systemPrompt, currentText);
            
            if (generatedText) {
                // 「続きを書く」場合は、見出しなしでそのまま追記
                appendToMemo("", generatedText);
            }
        });

        /**
         * 「要約する」ボタン
         */
        btnSummarize.addEventListener('click', async () => {
            const currentText = memoTextarea.value;
            if (currentText.trim().length < 50) {
                showStatus('要約するには50文字以上必要です', 'text-red-600');
                return;
            }

            const systemPrompt = "あなたは優秀な編集者です。以下の文章を読み、重要なポイントを3つの箇条書きで簡潔に要約してください。";
            const generatedText = await callGemini(systemPrompt, currentText);
            
            if (generatedText) {
                appendToMemo("--- AIによる要約 ---", generatedText);
            }
        });

        /**
         * 「アイデア出し」ボタン
         */
        btnBrainstorm.addEventListener('click', async () => {
            const currentText = memoTextarea.value;
            if (currentText.trim().length === 0) {
                showStatus('アイデアのトピックを何か入力してください', 'text-red-600');
                return;
            }

            // メモ全体をトピックとして扱う
            const systemPrompt = "あなたはクリエイティブなプランナーです。以下のトピックについて、5つのユニークなアイデアをリスト形式（例: 1. アイデアA）で提案してください。";
            const generatedText = await callGemini(systemPrompt, currentText);

            if (generatedText) {
                appendToMemo("--- AIによるアイデア ---", generatedText);
            }
        });

    </script>
</body>
</html>

