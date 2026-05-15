<?php

declare(strict_types=1);

$children ??= [];
$analyticsData ??= ['soundCounts' => [], 'diagnosisCounts' => []];
$dashboardStats ??= ['totalChildren' => 0, 'activeChildren' => 0, 'totalSessions' => 0, 'totalMessages' => 0];
$progressMap ??= [];
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>';
$therapistCopy = current_language() === 'kk' ? [
    'title' => 'Логопед панелі',
    'subtitle' => 'Пациенттерді басқару және прогресті бақылау',
    'admin' => 'Әкімші',
    'therapist' => 'Логопед-дефектолог',
] : [
    'title' => 'Панель логопеда',
    'subtitle' => 'Управление пациентами и мониторинг прогресса',
    'admin' => 'Администратор',
    'therapist' => 'Логопед-дефектолог',
];

require __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-800"><?= e($therapistCopy['title']) ?></h1>
            <p class="text-lg text-gray-600 mt-2"><?= e($therapistCopy['subtitle']) ?></p>
        </div>
        <div class="flex items-center gap-3">
            <?php if (auth_avatar_url()): ?>
            <img src="<?= e((string) auth_avatar_url()) ?>" alt="avatar" class="w-12 h-12 rounded-full object-cover border border-white shadow-sm">
            <?php else: ?>
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-soft to-mint flex items-center justify-center text-white font-bold text-lg"><?= e(auth_initials()) ?></div>
            <?php endif; ?>
            <div>
                <p class="font-semibold text-gray-800"><?= e(auth_name()) ?></p>
                <p class="text-sm text-gray-500"><?= e(auth_role_label()) ?></p>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid md:grid-cols-4 gap-6">
        <?php
        $totalChildren = (int) $dashboardStats['totalChildren'];
        $activeChildren = (int) $dashboardStats['activeChildren'];
        $totalSessions = (int) $dashboardStats['totalSessions'];
        $totalMessages = (int) $dashboardStats['totalMessages'];
        $stats = [
            ['icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', 'bg' => 'bg-blue-soft', 'gradient' => 'gradient-card-blue', 'title' => 'Всего детей', 'value' => (string) $totalChildren, 'sub' => 'Детские карты в базе'],
            ['icon' => '<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>', 'bg' => 'bg-mint', 'gradient' => 'gradient-card-green', 'title' => 'Активных', 'value' => (string) $activeChildren, 'sub' => 'Занимаются регулярно'],
            ['icon' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>', 'bg' => 'bg-orange-soft', 'gradient' => 'gradient-card-beige', 'title' => 'Диагностик', 'value' => (string) $totalSessions, 'sub' => 'Завершённых сессий'],
            ['icon' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>', 'bg' => 'bg-purple-soft', 'gradient' => 'gradient-card-purple', 'title' => 'Сообщения', 'value' => (string) $totalMessages, 'sub' => 'Вопросов от родителей'],
        ];
        foreach ($stats as $stat): ?>
        <div class="<?= $stat['gradient'] ?> border-0 shadow-lg rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full <?= $stat['bg'] ?> flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $stat['icon'] ?></svg>
                </div>
                <h3 class="font-semibold text-gray-700"><?= $stat['title'] ?></h3>
            </div>
            <p class="text-3xl font-bold text-gray-800"><?= $stat['value'] ?></p>
            <p class="text-sm text-gray-500 mt-1"><?= $stat['sub'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabs -->
    <div>
        <div class="flex justify-center mb-6">
            <div class="bg-gray-100 p-1 rounded-2xl inline-flex gap-1">
                <button data-tab="patients" id="tab-patients" class="tab-btn inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium rounded-xl transition-colors bg-white shadow text-gray-800">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Пациенты
                </button>
                <button data-tab="analytics" id="tab-analytics" class="tab-btn inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium rounded-xl transition-colors text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/></svg>
                    Аналитика
                </button>
                <button data-tab="ai-insights" id="tab-ai-insights" class="tab-btn inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium rounded-xl transition-colors text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    AI Рекомендации
                </button>
            </div>
        </div>

        <!-- Patients Tab -->
        <div id="content-patients" class="tab-content space-y-6">
            <!-- Search -->
            <div class="bg-white border-0 shadow-lg rounded-2xl p-4">
                <div class="flex gap-3 flex-wrap">
                    <div class="flex-1 min-w-[250px] relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" placeholder="Поиск по имени ребенка..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft" id="searchInput">
                    </div>
                    <button class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-full hover:bg-gray-50 transition-colors text-gray-700">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Фильтры
                    </button>
                </div>
            </div>

            <!-- Patient List -->
            <div class="grid lg:grid-cols-2 gap-6" id="patientList">
                <?php if (empty($children)): ?>
                <div class="col-span-2 text-center py-12 space-y-4">
                    <div class="w-20 h-20 rounded-full gradient-icon-blue flex items-center justify-center mx-auto">
                        <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Пациентов пока нет</h3>
                    <p class="text-gray-600">Когда ребёнок пройдёт первую диагностику, его карта появится здесь автоматически.</p>
                </div>
                <?php endif; ?>
                <?php foreach ($children as $child): ?>
                <div class="patient-card border-0 shadow-lg bg-white rounded-2xl overflow-hidden cursor-pointer transition-all hover:shadow-xl" data-name="<?= mb_strtolower($child['name']) ?>" data-child-id="<?= $child['id'] ?>">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800"><?= $child['name'] ?></h3>
                                <p class="text-sm text-gray-500"><?= $child['age'] ?> лет</p>
                            </div>
                            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full <?= $child['status'] === 'Активен' ? 'bg-mint text-white' : 'bg-gray-300 text-gray-700' ?>">
                                <?= $child['status'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Диагноз: <strong><?= $child['diagnosis'] ?></strong></p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($child['problemSounds'] as $sound): ?>
                                <span class="inline-block px-2 py-0.5 text-xs border border-blue-soft text-blue-soft rounded-full"><?= $sound ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-gray-600">Прогресс</span>
                                <span class="font-semibold text-blue-soft"><?= $child['progress'] ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="gradient-progress h-2 rounded-full transition-all" style="width: <?= $child['progress'] ?>%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t">
                            <span class="text-sm text-gray-500">Последняя сессия: <?= $child['lastSession'] ?></span>
                            <div class="flex gap-2">
                                <button class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                                </button>
                                <button class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Child Details Panel -->
            <div id="childDetails" class="hidden border-0 shadow-lg rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #F0F9FF, #ffffff);">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold">Детальная информация</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Progress Chart -->
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-4">Прогресс по диагностикам</h4>
                            <canvas id="progressChart" height="200"></canvas>
                        </div>
                        <!-- Assessment History for selected child -->
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-4">История диагностик</h4>
                            <div id="childAssessmentHistory" class="space-y-2">
                                <p class="text-sm text-gray-400">Выберите ребёнка для просмотра истории</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="bg-white rounded-xl p-4">
                        <h4 class="font-semibold text-gray-700 mb-3">Заметки логопеда</h4>
                        <textarea id="childNotes" class="w-full p-3 border border-gray-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-soft/50" rows="4" placeholder="Добавьте заметки о прогрессе ребенка..."></textarea>
                        <button id="saveNotesBtn" type="button" class="mt-3 px-4 py-2 text-sm font-medium bg-gray-900 text-white rounded-full hover:bg-gray-800 transition-colors">
                            Сохранить заметки
                        </button>
                        <span id="notesSaved" class="hidden ml-3 text-sm text-mint font-medium inline-flex items-center gap-1"><?= ui_icon('check', 'w-4 h-4') ?> <?= e(tr('therapist_page.notes_saved')) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div id="content-analytics" class="tab-content hidden space-y-6">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="border-0 shadow-lg bg-white rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-semibold">Паттерны ошибок</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="errorPatternsChart" height="300"></canvas>
                    </div>
                </div>
                <div class="border-0 shadow-lg bg-white rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-semibold">Распределение диагнозов</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="diagnosisChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights Tab -->
        <div id="content-ai-insights" class="tab-content hidden space-y-6">
            <div class="border-0 shadow-lg rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #F0F9FF, #ffffff);">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full gradient-icon-blue flex items-center justify-center">
                            <?= ui_icon('robot', 'w-5 h-5 text-white') ?>
                        </div>
                        <?= e(tr('therapist_page.ai_recommendations')) ?>
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php if (empty($children)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <p>Добавьте первого пациента и проведите диагностику — AI подготовит рекомендации.</p>
                    </div>
                    <?php else:
                    $borderColors = ['border-blue-soft', 'border-mint', 'border-purple-soft', 'border-orange-soft'];
                    $badgeColors = ['bg-mint', 'bg-orange-soft', 'bg-purple-soft', 'bg-blue-soft'];
                    foreach ($children as $idx => $insightChild):
                        $progress = (int) ($insightChild['progress'] ?? 0);
                        if ($progress >= 80) {
                            $insightText = 'Ребёнок показывает отличные результаты (' . $progress . '%). Рекомендуется провести финальную диагностику для возможного завершения терапии.';
                            $priority = 'Приоритет: Низкий';
                        } elseif ($progress >= 60) {
                            $insightText = 'Наблюдается стабильный прогресс. Рекомендуется увеличить интенсивность упражнений на проблемные звуки: ' . implode(', ', $insightChild['problemSounds'] ?: ['—']) . '.';
                            $priority = 'Приоритет: Средний';
                        } else {
                            $insightText = 'Прогресс требует внимания (' . $progress . '%). Рассмотрите изменение методики и добавление дыхательных упражнений.';
                            $priority = 'Приоритет: Высокий';
                        }
                    ?>
                    <div class="bg-white rounded-xl p-4 border-l-4 <?= $borderColors[$idx % count($borderColors)] ?>">
                        <h4 class="font-semibold text-gray-800 mb-2"><?= e($insightChild['name']) ?> — <?= $progress >= 80 ? 'Готовность к выпуску' : ($progress >= 60 ? 'Ускорение прогресса' : 'Требует внимания') ?></h4>
                        <p class="text-sm text-gray-600 mb-3"><?= e($insightText) ?></p>
                        <span class="inline-block px-3 py-1 text-xs font-medium <?= $badgeColors[$idx % count($badgeColors)] ?> text-white rounded-full">
                            <?= $priority ?>
                        </span>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$childrenJson = json_encode($children, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$analyticsJson = json_encode($analyticsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$progressMapJson = json_encode($progressMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$childNotesApi = json_encode(api_url('children/notes'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$extraScripts = <<<JS
<script>
const childrenData = {$childrenJson};
const analyticsData = {$analyticsJson};
const progressMap = {$progressMapJson};
let selectedChildId = null;

function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('bg-white', 'shadow', 'text-gray-800');
        el.classList.add('text-gray-500');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('bg-white', 'shadow', 'text-gray-800');
    activeTab.classList.remove('text-gray-500');
    if (tabName === 'analytics') initAnalyticsCharts();
}

function filterPatients() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.patient-card').forEach(card => {
        const name = card.dataset.name;
        card.style.display = name.includes(query) ? '' : 'none';
    });
}

let progressChart = null;
function selectChild(event, id) {
    selectedChildId = id;
    const panel = document.getElementById('childDetails');
    panel.classList.remove('hidden');
    document.querySelectorAll('.patient-card').forEach(c => c.classList.remove('ring-2', 'ring-blue-soft'));
    if (event && event.currentTarget) event.currentTarget.classList.add('ring-2', 'ring-blue-soft');

    const child = childrenData.find(item => Number(item.id) === Number(id));
    const ctx = document.getElementById('progressChart').getContext('2d');
    if (progressChart) progressChart.destroy();

    // Use real progress series from DB
    const series = progressMap[id] || [];
    const labels = series.map(p => p.label || '');
    const scores = series.map(p => Number(p.overall_score || 0));

    if (scores.length === 0) {
        scores.push(child ? Number(child.progress || 0) : 0);
        labels.push('Сейчас');
    }

    progressChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Балл',
                data: scores,
                borderColor: '#7FB3D5',
                backgroundColor: 'rgba(127, 179, 213, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#7FB3D5',
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: false, min: 0, max: 100, grid: { color: '#E5E7EB' } },
                x: { grid: { display: false } }
            }
        }
    });
    panel.querySelector('h3').textContent = child ? 'Детальная информация — ' + child.name : 'Детальная информация';

    // Load notes from child data
    const textarea = document.getElementById('childNotes');
    if (textarea && child) textarea.value = child.notes || '';
    document.getElementById('notesSaved').classList.add('hidden');

    // Build assessment history for this child
    const historyEl = document.getElementById('childAssessmentHistory');
    const childSeries = progressMap[id] || [];
    if (childSeries.length === 0) {
        historyEl.innerHTML = '<p class="text-sm text-gray-400">Диагностик пока не проводилось</p>';
    } else {
        historyEl.innerHTML = childSeries.map(s =>
            '<div class="flex items-center justify-between p-3 bg-white rounded-xl">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="w-8 h-8 rounded-full bg-blue-soft flex items-center justify-center">' +
                        '<svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>' +
                    '</div>' +
                    '<span class="text-sm">' + (s.label || '') + ' — Балл: ' + (s.overall_score || 0) + '/100</span>' +
                '</div>' +
            '</div>'
        ).join('');
    }

    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function saveNotes() {
    if (!selectedChildId) return;
    const notes = document.getElementById('childNotes').value;
    fetch({$childNotesApi}, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ child_id: selectedChildId, notes: notes })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            document.getElementById('notesSaved').classList.remove('hidden');
            setTimeout(() => document.getElementById('notesSaved').classList.add('hidden'), 2000);
        }
    })
    .catch(() => {});
}

let errorChart = null, diagChart = null;
function initAnalyticsCharts() {
    if (errorChart) return;
    const soundLabels = Object.keys(analyticsData.soundCounts || {});
    const soundValues = Object.values(analyticsData.soundCounts || {});
    const diagLabels = Object.keys(analyticsData.diagnosisCounts || {});
    const diagValues = Object.values(analyticsData.diagnosisCounts || {});

    const ctx1 = document.getElementById('errorPatternsChart').getContext('2d');
    if (soundLabels.length === 0) {
        ctx1.canvas.parentElement.innerHTML = '<p class="text-center text-gray-400 py-8">Данных пока нет. Проведите первую диагностику.</p>';
    } else {
        errorChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: soundLabels,
                datasets: [{ label: 'Ошибки', data: soundValues, backgroundColor: '#7FB3D5', borderRadius: 8 }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#E5E7EB' } }, x: { grid: { display: false } } }
            }
        });
    }

    const ctx2 = document.getElementById('diagnosisChart').getContext('2d');
    if (diagLabels.length === 0) {
        ctx2.canvas.parentElement.innerHTML = '<p class="text-center text-gray-400 py-8">Данных пока нет.</p>';
    } else {
        diagChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: diagLabels,
                datasets: [{ data: diagValues, backgroundColor: ['#7FB3D5', '#A8E6CF', '#FDB777', '#D8BFD8', '#C5F0DC'], borderWidth: 0 }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } } }
            }
        });
    }
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tab]').forEach((btn) => btn.addEventListener('click', () => switchTab(btn.dataset.tab)));
    document.getElementById('searchInput')?.addEventListener('input', filterPatients);
    document.querySelectorAll('[data-child-id]').forEach((card) => {
        card.addEventListener('click', (event) => selectChild(event, Number(card.dataset.childId)));
    });
    document.getElementById('saveNotesBtn')?.addEventListener('click', saveNotes);
    switchTab('patients');
});
</script>
JS;

require __DIR__ . '/../layouts/footer.php';
?>
