<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>献立ホーム画面</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-pink': '#EC4899', 
                        'secondary-gray': '#D1D5DB', 
                        'accent-yellow': '#FFD700',
                        'light-bg': '#F9FAFB', 
                        'card-border': '#E5E7EB', 
                        'notify-red': '#EF4444', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        body, html { height: 100%; }
        .main-content {
            padding-bottom: 200px; 
            min-height: calc(100vh - 72px);
            background-image: url('haikei1.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: scroll; 
            background-color: transparent; 
        }
        .meal-card {
            width: 240px;
            height: 160px; 
            border-radius: 1rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            overflow: hidden;
            border: 1px solid #E5E7EB; 
            background-color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
        }
        .drawer {
            transition: transform 0.3s ease-out;
            transform: translateX(100%);
            width: 80%; 
        }
        .drawer.is-open { transform: translateX(0); }
        .notification-bell { position: relative; }
        .notification-bell.has-notification::after {
            content: '';
            position: absolute;
            top: 4px; right: 4px;
            width: 8px; height: 8px;
            background-color: #EF4444; 
            border-radius: 50%;
            border: 1px solid white; 
        }
        .user-icon-container {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .date-picker-menu {
            position: absolute;
            top: 100%; left: 0;
            z-index: 10;
            min-width: 120px;
        }

        /* 吹き出しのアニメーション */
        @keyframes popUp {
            0% { opacity: 0; transform: translate(-50%, 10px) scale(0.8); }
            100% { opacity: 1; transform: translate(-50%, 0) scale(1); }
        }
        .speech-bubble {
            animation: popUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            z-index: 100; /* 最前面に表示 */
        }
    </style>
</head>
<body class="bg-light-bg font-sans">

    <div class="main-content max-w-md mx-auto shadow-lg overflow-x-hidden relative">

        <header class="p-4 flex flex-row justify-between items-center sticky top-0 z-10 border-b border-gray-100 header-bg" style="background-color: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">ホーム</h1>
            <button id="menu-button" class="p-2 text-gray-600 hover:text-gray-800 rounded-full transition duration-150 ui-element-bg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>
        
        <div class="p-4 space-y-6 ui-element-bg">

            <section>
                <div class="flex items-center space-x-1 mb-3">
                    <h2 id="date-picker-trigger" class="text-xl font-bold text-primary-pink cursor-pointer relative">
                        今日 <span class="text-sm text-gray-700 ml-1">▼</span>
                        <div id="date-picker-menu" class="date-picker-menu bg-white border border-gray-200 rounded-lg shadow-xl hidden p-1">
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="今日">今日</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="先週">先週</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="先月">先月</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="翌年">翌年</button>
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="ランダム">ランダム</button>
                        </div>
                    </h2>
                    <h2 class="text-xl font-bold text-gray-700">の人気献立
                        <a href="U09RANKING.php" class="text-sm font-normal text-primary-pink ml-2 hover:underline">&gt;</a>
                    </h2>
                </div>

                <div id="popular-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="1">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('hanba-gu.jpg'); background-size: cover;"></div>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">ハンバーグ定食</h3>
                            <p class="text-xs text-gray-500">レシピや詳細</p>
                        </div>
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <button class="like-button p-0.5 text-secondary-gray transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-none stroke-current" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="2">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('karaage.jpg'); background-size: cover;"></div>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">唐揚げ定食</h3>
                            <p class="text-xs text-gray-500">レシピや詳細</p>
                        </div>
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <button class="like-button p-0.5 text-secondary-gray transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-none stroke-current" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="3">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('sashimi.jpg'); background-size: cover;"></div>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">刺身定食</h3>
                            <p class="text-xs text-gray-500">レシピや詳細</p>
                        </div>
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <button class="like-button p-0.5 text-secondary-gray transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-none stroke-current" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-bold mb-3 text-gray-700">お気に入り
                    <a href="U08OKINI.php" class="text-sm font-normal text-primary-pink ml-2 hover:underline">&gt;</a>
                </h2>
                <div id="favorite-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="4">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('gyouza.jpg'); background-size: cover;"></div>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">餃子定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <span class="text-xs font-bold text-gray-700">1位</span>
                            <button class="star-button p-0.5 text-secondary-gray transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-none stroke-current" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="5">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('saba.jpg'); background-size: cover;"></div>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">鯖定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <span class="text-xs font-bold text-gray-700">2位</span>
                            <button class="star-button p-0.5 text-secondary-gray transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-none stroke-current" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="6">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('nikunoyasai.jpg'); background-size: cover;"></div>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">肉の野菜炒め定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                        <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                            <span class="text-xs font-bold text-gray-700">3位</span>
                            <button class="star-button p-0.5 text-secondary-gray transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-none stroke-current" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-bold mb-3 text-gray-700">カレンダー
                    <a href="U07CARENDER.php" class="text-sm font-normal text-primary-pink ml-2 hover:underline">&gt;</a>
                </h2>
                <div id="calendar-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    <div class="flex-shrink-0 meal-card relative" data-meal-id="7">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('/sake.jpg'); background-size: cover;"></div>
                        <span class="absolute top-2 left-2 bg-white/80 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full shadow-md">本日2(火)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">【登録】鮭定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0 meal-card relative border-2 border-yellow-500" data-meal-id="8">
                        <div class="h-2/3 bg-gray-200" style="background-image: url('gyoutan.jpg'); background-size: cover;"></div>
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">本日2(火)</span>
                        <div class="p-2">
                            <h3 class="font-semibold text-gray-800 text-sm truncate">【提案】牛タン定食</h3>
                            <p class="text-xs text-gray-500">レシピや評価</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-8">
                <h2 class="text-xl font-bold mb-3 text-gray-700">今日の献立</h2>
                <div id="register-area" class="h-[300px] rounded-2xl shadow-xl flex justify-center items-center relative overflow-hidden bg-white" style="background-image: url('https://placehold.co/600x400/f0f0f0/333?text=Dining+Table+Image'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
                    <button id="register-button" onclick="window.location.href='U11TOUROKU.php'" class="relative w-10 h-10 bg-primary-pink/90 text-white rounded-full shadow-2xl shadow-primary-pink/50 flex items-center justify-center transition duration-300 transform hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </section>
        </div>

        <footer class="fixed bottom-0 left-0 right-0 max-w-md mx-auto border-t border-gray-200 shadow-2xl p-3 z-20 ui-element-bg bg-white/95 backdrop-blur-sm">
            <div class="flex flex-col space-y-3">
                
                <div class="flex items-end justify-start w-full">
                    
                    <div class="flex-shrink-0 text-center w-16 relative mr-2">
                        <div id="request-bubble" class="speech-bubble absolute -top-12 left-1/2 transform -translate-x-1/2 bg-white border border-gray-200 shadow-lg rounded-xl p-2 w-max max-w-[140px] hidden z-50 cursor-pointer" onclick="window.location.href='U10BYOUKI.php'">
                            <p id="request-text" class="text-xs font-bold text-gray-800 truncate text-center"></p>
                            <div class="absolute -bottom-1.5 left-1/2 transform -translate-x-1/2 rotate-45 w-3 h-3 bg-white border-r border-b border-gray-200"></div>
                        </div>

                        <a href="U10BYOUKI.php">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-primary-pink/10 border-2 border-primary-pink rounded-full transition duration-150 transform hover:scale-105 flex items-center justify-center">
                                <span id="my-reaction-emoji" role="img" aria-label="自分">😊</span>
                            </button>
                        </a>
                        <p id="my-reaction-name" class="text-xs font-medium text-primary-pink mt-1">自分</p>
                    </div>

                    <div id="reaction-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-3 pb-2 flex-grow">
                        <div class="flex-shrink-0 text-center w-16">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-gray-100 border-2 border-transparent rounded-full transition duration-150 hover:border-gray-300 flex items-center justify-center">
                                <span role="img" aria-label="名前">😥</span>
                            </button>
                            <p class="text-xs font-medium text-gray-500 mt-1">名前</p>
                        </div>
                        <div class="flex-shrink-0 text-center w-16">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-gray-100 border-2 border-transparent rounded-full transition duration-150 hover:border-gray-300 flex items-center justify-center">
                                <span role="img" aria-label="名前">😭</span>
                            </button>
                            <p class="text-xs font-medium text-gray-500 mt-1">名前</p>
                        </div>
                        <div class="flex-shrink-0 text-center w-16">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-gray-100 border-2 border-transparent rounded-full transition duration-150 hover:border-gray-300 flex items-center justify-center">
                                <span role="img" aria-label="名前">😠</span>
                            </button>
                            <p class="text-xs font-medium text-gray-500 mt-1">名前</p>
                        </div>
                        <div class="flex-shrink-0 text-center w-16">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-gray-100 border-2 border-transparent rounded-full transition duration-150 hover:border-gray-300 flex items-center justify-center">
                                <span role="img" aria-label="名前">😁</span>
                            </button>
                            <p class="text-xs font-medium text-gray-500 mt-1">名前</p>
                        </div>
                        <div class="flex-shrink-0 text-center w-16">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-gray-100 border-2 border-transparent rounded-full transition duration-150 hover:border-gray-300 flex items-center justify-center">
                                <span role="img" aria-label="名前">😁</span>
                            </button>
                            <p class="text-xs font-medium text-gray-500 mt-1">名前</p>
                        </div>
                    </div>
                </div>

                <div class="w-full px-1 pb-1">
                    <div class="search-container bg-white p-2 rounded-full shadow-inner border border-gray-200 flex items-center">
                        <button onclick="handleSearchClick()" class="p-2 ml-1 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="検索" 
                            class="w-full h-10 text-lg text-gray-700 bg-white border-none focus:ring-0 focus:outline-none placeholder-gray-500 ml-2"
                            onkeypress="if(event.key === 'Enter') handleSearchClick()"
                            autocomplete="off"
                        >
                    </div>
                    <div id="alertMessage" class="mt-1 text-center text-red-600 text-sm opacity-0 transition-opacity duration-300 h-4">
                        キーワードを入力してください。
                    </div>
                </div>
            </div>
        </footer>

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
                    <a href="U14LIST.php">買い物リスト</a>
                    <a href="#" class="block hover:text-primary-pink transition duration-150" onclick="showMessageBox('グループ削除画面へ遷移します。'); closeDrawer(); return false;">
                        グループ削除
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
        // --- 基本設定 ---
        let userName = "[自分の名前]";
        let currentEmoji = "😊"; 
        let hasNotification = true; 
        let userIconUrl = ""; 
        let currentSelection = "今日"; 

        const ICON_OPTIONS = {
            "デフォルト (灰色)": "",
            "アイコンA": "https://placehold.co/100x100/1e40af/ffffff?text=IconA",
            "アイコンB": "https://placehold.co/100x100/dc2626/ffffff?text=IconB",
            "アイコンC": "https://placehold.co/100x100/059669/ffffff?text=IconC"
        };
        const ICON_NAMES = Object.keys(ICON_OPTIONS);

        // 要素取得
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

        // --- ★初期化時にデータをロードする★ ---
        document.addEventListener('DOMContentLoaded', () => {
            userNameElement.textContent = userName;
            myReactionNameElement.textContent = userName;
            updateBellNotification();
            updateUserIcon();
            updatePopularHeading(currentSelection); 
            
            // U10BYOUKI.phpで保存したデータを読み込む
            loadUserData();
        });

        function loadUserData() {
            // localStorageからリクエストを取得
            const requestText = localStorage.getItem('userRequest');
            const bubble = document.getElementById('request-bubble');
            const textSpan = document.getElementById('request-text');

            if (requestText && requestText.trim() !== "") {
                textSpan.textContent = requestText;
                bubble.classList.remove('hidden'); // hiddenクラスを外して表示
            } else {
                bubble.classList.add('hidden');
            }

            // 状態（絵文字）もあれば反映
            const statusJson = localStorage.getItem('userStatus');
            if (statusJson) {
                try {
                    const statusData = JSON.parse(statusJson);
                    if (statusData.emoji) {
                        currentEmoji = statusData.emoji;
                        document.getElementById('my-reaction-emoji').textContent = currentEmoji;
                        updateUserIcon();
                    }
                } catch (e) {
                    console.error("Error parsing status:", e);
                }
            }
        }

        // --- ボタン制御ロジック ---

        // 1. LIKEボタン
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); 
                const btn = e.currentTarget;
                const svg = btn.querySelector('svg');
                const isLiked = btn.classList.toggle('text-primary-pink');
                btn.classList.toggle('text-secondary-gray', !isLiked);
                if (isLiked) {
                    svg.classList.add('fill-current');
                    svg.classList.remove('fill-none');
                    showMessageBox('いいねしました！');
                } else {
                    svg.classList.remove('fill-current');
                    svg.classList.add('fill-none');
                    showMessageBox('いいねを解除しました。');
                }
            });
        });

        // 2. STARボタン
        document.querySelectorAll('.star-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); 
                const btn = e.currentTarget;
                const svg = btn.querySelector('svg');
                const isStarred = btn.classList.toggle('text-accent-yellow');
                btn.classList.toggle('text-secondary-gray', !isStarred);
                if (isStarred) {
                    svg.classList.add('fill-current');
                    svg.classList.remove('fill-none');
                    svg.classList.remove('stroke-current');
                    showMessageBox('お気に入りに追加しました！');
                } else {
                    svg.classList.remove('fill-current');
                    svg.classList.add('fill-none');
                    svg.classList.add('stroke-current');
                    showMessageBox('お気に入りを解除しました。');
                }
            });
        });

        // --- その他の共通機能 ---

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

        // 検索機能
        window.handleSearchClick = function() {
            const input = document.getElementById('searchInput');
            const term = input ? input.value.trim() : "";
            const alertEl = document.getElementById('alertMessage');
            if(term === "") {
                alertEl.classList.remove('opacity-0');
                setTimeout(() => alertEl.classList.add('opacity-0'), 3000);
            } else {
                window.location.href = "U12KENSAKU.php";
            }
        };

        // ドロワー開閉
        menuButton.addEventListener('click', () => {
            drawer.classList.add('is-open');
            drawerBackdrop.classList.remove('hidden');
        });
        window.closeDrawer = function() {
            drawer.classList.remove('is-open');
            drawerBackdrop.classList.add('hidden');
            applicationNotification.classList.add('hidden');
        };

        // ユーザー情報編集
        window.editName = function() {
            const newName = prompt("新しい名前を入力してください:", userName);
            if (newName && newName.trim() !== "") {
                userName = newName.trim();
                userNameElement.textContent = userName;
                myReactionNameElement.textContent = userName;
            }
        };
        window.changeIconImage = function() {
            const selection = prompt(`新しいアイコン画像を選択してください:\n${ICON_NAMES.join(', ')}`, ICON_NAMES[0]);
            if (selection && ICON_OPTIONS.hasOwnProperty(selection)) {
                userIconUrl = ICON_OPTIONS[selection];
                updateUserIcon();
                showMessageBox(`アイコンを「${selection}」に変更しました。`);
            }
        };
        function updateUserIcon() {
            if (userIconUrl) {
                userIconBackground.style.backgroundImage = `url('${userIconUrl}')`;
                userIconBackground.style.backgroundColor = 'transparent';
            } else {
                userIconBackground.style.backgroundImage = 'none';
                userIconBackground.style.backgroundColor = '#D1D5DB';
            }
            userEmojiElement.textContent = currentEmoji ? currentEmoji : "";
            userEmojiElement.classList.toggle('opacity-0', !currentEmoji);
        }

        // 通知処理
        window.toggleApplicationNotification = function() {
            applicationNotification.classList.toggle('hidden');
        };
        window.handleApplication = function(action) {
            showMessageBox(`グループへの参加を「${action}」しました。`);
            applicationNotification.classList.add('hidden');
            hasNotification = false; 
            updateBellNotification();
        };
        function updateBellNotification() {
            bellButton.classList.toggle('text-yellow-500', hasNotification);
        }

        // カード遷移
        document.querySelectorAll('.meal-card').forEach(card => {
            card.addEventListener('click', (e) => {
                const id = e.currentTarget.getAttribute('data-meal-id');
                showMessageBox(`料理ID: ${id} の詳細画面へ遷移します。`);
            });
        });

        // 日付ピッカー
        datePickerTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            datePickerMenu.classList.toggle('hidden');
        });
        document.querySelectorAll('.date-option').forEach(opt => {
            opt.addEventListener('click', (e) => {
                updatePopularHeading(e.target.getAttribute('data-value'));
                datePickerMenu.classList.add('hidden');
            });
        });
        document.addEventListener('click', () => {
            if (!datePickerMenu.classList.contains('hidden')) datePickerMenu.classList.add('hidden');
        });
        function updatePopularHeading(val) {
            currentSelection = val;
            datePickerTrigger.childNodes[0].nodeValue = val + " "; 
        }

        // リアクションボタン（他のユーザー）
        document.querySelectorAll('.reaction-item').forEach(button => {
            // 自分のボタン（リンク付き）以外にイベントを設定
            if (!button.closest('a')) {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // リセット
                    document.querySelectorAll('.reaction-item').forEach(item => {
                        if(!item.closest('a')) { // 自分以外をリセット対象に
                            item.classList.remove('bg-primary-pink/10', 'border-primary-pink');
                            item.classList.add('bg-gray-100', 'border-transparent');
                            item.nextElementSibling.classList.remove('text-primary-pink');
                            item.nextElementSibling.classList.add('text-gray-500');
                        }
                    });
                    // アクティブ化
                    const btn = e.currentTarget;
                    btn.classList.remove('bg-gray-100', 'border-transparent');
                    btn.classList.add('bg-primary-pink/10', 'border-primary-pink');
                    btn.nextElementSibling.classList.remove('text-gray-500');
                    btn.nextElementSibling.classList.add('text-primary-pink');
                    
                    const name = btn.nextElementSibling.textContent;
                    showMessageBox(name + 'さんの献立リアクション履歴へ遷移します。');
                });
            }
        });
    </script>
</body>
</html>