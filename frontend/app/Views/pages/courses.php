<?php
declare(strict_types=1);
$courses ??= [];
$canManageCourses ??= auth_has_any_role(...\App\Models\User::COURSE_MANAGER_ROLES);
require __DIR__ . '/../layouts/header.php';
?>
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-800"><?= e(tr('courses_page.title', 'Курсы лечения')) ?></h1>
            <p class="text-lg text-gray-600 mt-2"><?= e(tr('courses_page.subtitle', 'Онлайн-курсы терапии с упражнениями и материалами')) ?></p>
        </div>
        <?php if ($canManageCourses): ?>
        <a href="/course-create.php" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-medium text-white rounded-full gradient-cta hover:opacity-90 transition-opacity no-underline">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
            <?= e(tr('courses_page.create', 'Создать курс')) ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (empty($courses)): ?>
    <div class="text-center py-20 space-y-6">
        <div class="w-24 h-24 rounded-full gradient-icon-purple flex items-center justify-center mx-auto">
            <svg class="w-12 h-12 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800"><?= e(tr('courses_page.no_courses', 'Курсов пока нет')) ?></h2>
        <p class="text-gray-600"><?= e(tr('courses_page.no_courses_desc', 'Создайте первый курс лечения.')) ?></p>
        <?php if ($canManageCourses): ?>
        <a href="/course-create.php" class="inline-flex items-center gap-2 px-6 py-3 text-white font-medium rounded-full gradient-cta no-underline">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
            <?= e(tr('courses_page.create', 'Создать курс')) ?>
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($courses as $course):
            $media = $course['media'] ?? [];
            $thumb = null;
            foreach ($media as $m) { if ($m['file_type'] === 'image') { $thumb = $m['file_path']; break; } }
        ?>
        <div class="bg-white border-0 shadow-lg rounded-2xl overflow-hidden hover:shadow-xl transition-shadow">
            <?php if ($thumb): ?>
            <div class="aspect-video bg-gray-100 overflow-hidden">
                <img src="/<?= e($thumb) ?>" alt="" class="w-full h-full object-cover">
            </div>
            <?php else: ?>
            <div class="aspect-video gradient-hero flex items-center justify-center">
                <svg class="w-16 h-16 text-white/60" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
            </div>
            <?php endif; ?>
            <div class="p-5 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="text-lg font-semibold text-gray-800 line-clamp-2"><?= e($course['title']) ?></h3>
                    <?php if ($course['is_published']): ?>
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium bg-mint text-white rounded-full">Live</span>
                    <?php else: ?>
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-600 rounded-full">Draft</span>
                    <?php endif; ?>
                </div>
                <?php if ($course['description']): ?>
                <p class="text-sm text-gray-600 line-clamp-2"><?= e($course['description']) ?></p>
                <?php endif; ?>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <?php if ($course['target_sounds']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 border border-blue-soft text-blue-soft rounded-full"><?= e($course['target_sounds']) ?></span>
                    <?php endif; ?>
                    <?php if ((int)$course['lessons_count'] > 0): ?>
                    <span><?= (int)$course['lessons_count'] ?> <?= e(tr('courses_page.lessons', 'уроков')) ?></span>
                    <?php endif; ?>
                    <span><?= e($course['difficulty'] ?? '') ?></span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-400 pt-1 border-t border-gray-100">
                    <span><?= e(tr('courses_page.by', 'от')) ?> <?= e($course['author_name'] ?? '—') ?></span>
                    <span>•</span>
                    <span><?= date('d.m.Y', strtotime($course['created_at'])) ?></span>
                </div>
                <?php if (count($media) > 0): ?>
                <div class="flex items-center gap-1 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                    <?= count($media) ?> файл(ов)
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
