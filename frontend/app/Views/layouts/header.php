<?php
$lang = current_language();
$t = t();
$pageTitle = $pageTitle ?? env('APP_NAME', 'TilDamu.kz');
$authUser = auth_user();
$avatarUrl = auth_avatar_url($authUser);
?>
<!DOCTYPE html>
<html lang="<?= $lang === 'kk' ? 'kk' : 'ru' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="/public/assets/img/tdm.png" type="image/png">
    <title><?= e($pageTitle) ?> — <?= e((string) env('APP_NAME', 'TilDamu.kz')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mint: '#A8E6CF',
                        'mint-light': '#C5F0DC',
                        'mint-dark': '#7FD8BE',
                        'blue-light': '#A8D8EA',
                        'blue-soft': '#7FB3D5',
                        beige: '#F5E6D3',
                        'beige-light': '#FBF5ED',
                        'purple-soft': '#D8BFD8',
                        'orange-soft': '#FDB777',
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    animation: {
                        'pulse-soft': 'pulse-soft 2s ease-in-out infinite',
                        'wave': 'wave 1s ease-in-out infinite',
                        'bounce-soft': 'bounce-soft 1s ease-in-out infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        'pulse-soft': {
                            '0%, 100%': { opacity: '1', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                        },
                        'wave': {
                            '0%, 100%': { transform: 'scaleY(0.5)' },
                            '50%': { transform: 'scaleY(1)' },
                        },
                        'bounce-soft': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        'float': {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .gradient-hero { background: linear-gradient(135deg, #A8E6CF, #7FB3D5, #D8BFD8); }
        .gradient-cta { background: linear-gradient(135deg, #7FB3D5, #A8E6CF); }
        .gradient-icon-blue { background: linear-gradient(135deg, #7FB3D5, #A8E6CF); }
        .gradient-icon-green { background: linear-gradient(135deg, #A8E6CF, #7FD8BE); }
        .gradient-icon-orange { background: linear-gradient(135deg, #FDB777, #F5E6D3); }
        .gradient-icon-purple { background: linear-gradient(135deg, #D8BFD8, #C4A5C4); }
        .gradient-card-blue { background: linear-gradient(135deg, #ffffff, #F0F9FF); }
        .gradient-card-green { background: linear-gradient(135deg, #ffffff, #F0FDF4); }
        .gradient-card-orange { background: linear-gradient(135deg, #ffffff, #FEF3F2); }
        .gradient-card-purple { background: linear-gradient(135deg, #ffffff, #FAF5FF); }
        .gradient-card-beige { background: linear-gradient(135deg, #FBF5ED, #ffffff); }
        .gradient-bg { background: linear-gradient(135deg, #FAFBFC, #F7FAFC, #EDF7F6); }
        .gradient-progress { background: linear-gradient(90deg, #7FB3D5, #A8E6CF); }
        .gradient-record-btn { background: linear-gradient(135deg, #7FB3D5, #A8E6CF); }
        .gradient-record-btn-active { background: linear-gradient(135deg, #FC8181, #F56565); }
    </style>
    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body class="min-h-screen gradient-bg">
<header class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <a href="/" class="flex items-center gap-3 no-underline">
                <div class="w-12 h-12 rounded-2xl gradient-icon-blue flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800"><?= e((string) env('APP_NAME', 'TilDamu.kz')) ?></h1>
                    <p class="text-sm text-gray-500"><?= e((string) $t['subtitle']) ?></p>
                </div>
            </a>
            <div class="flex items-center gap-3 flex-wrap justify-end">
                <!-- Language Selector -->
                <div class="relative" id="langDropdown">
                    <button id="langToggle" type="button" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium border border-gray-200 rounded-full hover:bg-gray-50 transition-colors text-gray-700">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                        <?= language_short(current_language()) ?>
                        <svg class="w-3 h-3 opacity-50" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="langMenu" class="hidden absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <?php foreach (available_languages() as $lc):
                            $isActive = $lc === current_language();
                        ?>
                        <a href="<?= e(lang_url($lc)) ?>" class="flex items-center gap-2 px-4 py-2 text-sm no-underline transition-colors <?= $isActive ? 'text-blue-soft font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                            <?= e(language_label($lc)) ?>
                            <?php if ($isActive): ?><svg class="w-3.5 h-3.5 ml-auto text-blue-soft" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (auth_check()): ?>
                <div class="flex items-center gap-2 bg-white/70 rounded-full px-2 py-1 border border-gray-100 shadow-sm">
                    <?php if ($avatarUrl): ?>
                        <img src="<?= e($avatarUrl) ?>" alt="avatar" class="w-9 h-9 rounded-full object-cover border border-white shadow-sm">
                    <?php else: ?>
                        <div class="w-9 h-9 rounded-full gradient-cta flex items-center justify-center text-white text-xs font-bold">
                            <?= e(auth_initials()) ?>
                        </div>
                    <?php endif; ?>
                    <span class="text-sm font-medium text-gray-700 hidden sm:inline max-w-[180px] truncate"><?= e(auth_name()) ?></span>
                    <a href="/logout.php" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-full hover:bg-gray-50 transition-colors no-underline" title="<?= e(tr('common.logout')) ?>">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        <span class="hidden sm:inline"><?= e(tr('common.logout')) ?></span>
                    </a>
                </div>
                <?php else: ?>
                <a href="/login.php" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-full gradient-cta hover:opacity-90 transition-opacity no-underline" title="Вход не обязателен для обычного пользователя">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                    <?= e(tr('common.login')) ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<?php if (empty($hideNav)): ?>
<nav class="bg-white/60 backdrop-blur-sm border-b border-gray-200/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex gap-2 overflow-x-auto py-3">
            <?php
            $navItems = [
                ['page' => 'home', 'href' => '/', 'label' => $t['home'], 'icon' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>', 'roles' => []],
                ['page' => 'diagnosis', 'href' => '/diagnosis.php', 'label' => $t['diagnosis'], 'icon' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>', 'roles' => []],
                ['page' => 'results', 'href' => '/results.php', 'label' => $t['results'], 'icon' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>', 'roles' => []],
                ['page' => 'recommendations', 'href' => '/recommendations.php', 'label' => $t['recommendations'], 'icon' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>', 'roles' => []],
                ['page' => 'therapist', 'href' => '/therapist.php', 'label' => $t['therapist'], 'icon' => '<path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/>', 'roles' => \App\Models\User::THERAPIST_PANEL_ROLES],
                ['page' => 'courses', 'href' => '/courses.php', 'label' => $t['courses'] ?? 'Courses', 'icon' => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>', 'roles' => []],
                ['page' => 'dataset', 'href' => '/dataset.php', 'label' => $t['dataset'] ?? 'Dataset', 'icon' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>', 'roles' => []],
                ['page' => 'dataset_history', 'href' => '/dataset-history.php', 'label' => $t['dataset_history'] ?? 'История датасета', 'icon' => '<path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l3 2"/>', 'roles' => \App\Models\User::DATASET_MANAGER_ROLES],
                ['page' => 'admin_users', 'href' => '/admin-users.php', 'label' => tr('admin_users_page.nav', 'Пользователи'), 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', 'roles' => \App\Models\User::USER_TABLE_ROLES],
            ];
            $currentRole = auth_role();
            foreach ($navItems as $item):
                if (!empty($item['roles']) && !auth_role_matches($currentRole, $item['roles'])) {
                    continue;
                }
                $active = is_active_page($item['page']);
                $btnClass = $active ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100';
            ?>
            <a href="<?= e($item['href']) ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors no-underline <?= $btnClass ?>">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $item['icon'] ?></svg>
                <?= e((string) $item['label']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>
<?php endif; ?>
<?php
$headerInteractionScript = <<<'JS'
<script>
document.addEventListener('DOMContentLoaded', () => {
    const langDropdown = document.getElementById('langDropdown');
    const langToggle = document.getElementById('langToggle');
    const langMenu = document.getElementById('langMenu');
    if (langToggle && langMenu) {
        langToggle.addEventListener('click', () => langMenu.classList.toggle('hidden'));
        document.addEventListener('click', (event) => {
            if (langDropdown && !langDropdown.contains(event.target)) {
                langMenu.classList.add('hidden');
            }
        });
    }
});
</script>
JS;
$extraScripts = ($extraScripts ?? '') . $headerInteractionScript;
?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
