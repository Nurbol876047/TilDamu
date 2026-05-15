<?php

declare(strict_types=1);

$users ??= [];
$roleOptions ??= auth_role_options();
$canManageUsers ??= false;
$error ??= null;
$success ??= null;
$old ??= [];

require __DIR__ . '/../layouts/header.php';
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl bg-white/80 p-6 shadow-sm border border-white sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-soft"><?= e(tr('admin_users_page.eyebrow', 'Доступы')) ?></p>
            <h1 class="mt-1 text-3xl font-bold text-gray-800"><?= e(tr('admin_users_page.title', 'Пользователи')) ?></h1>
            <p class="mt-2 text-gray-500"><?= e(tr('admin_users_page.subtitle', 'Администратор создаёт служебные аккаунты, разработчик может просматривать список.')) ?></p>
        </div>
        <div class="rounded-2xl bg-gray-900 px-5 py-4 text-white shadow">
            <p class="text-sm text-white/70"><?= e(tr('admin_users_page.total', 'Всего')) ?></p>
            <p class="text-3xl font-bold"><?= count($users) ?></p>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($canManageUsers): ?>
    <section class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800"><?= e(tr('admin_users_page.create_title', 'Создать пользователя')) ?></h2>
                <p class="text-sm text-gray-500"><?= e(tr('admin_users_page.create_hint', 'Клиенты могут регистрироваться сами, а служебные роли выдаёт администратор.')) ?></p>
            </div>
        </div>
        <form method="POST" action="/admin-users/store.php" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <label class="space-y-2">
                <span class="block text-sm font-medium text-gray-700"><?= e(tr('common.full_name')) ?></span>
                <input type="text" name="full_name" value="<?= e((string) ($old['full_name'] ?? '')) ?>" required class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
            </label>
            <label class="space-y-2">
                <span class="block text-sm font-medium text-gray-700"><?= e(tr('common.email')) ?></span>
                <input type="email" name="email" value="<?= e((string) ($old['email'] ?? '')) ?>" required class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
            </label>
            <label class="space-y-2">
                <span class="block text-sm font-medium text-gray-700"><?= e(tr('common.phone')) ?></span>
                <input type="text" name="phone" value="<?= e((string) ($old['phone'] ?? '')) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
            </label>
            <label class="space-y-2">
                <span class="block text-sm font-medium text-gray-700"><?= e(tr('common.password')) ?></span>
                <input type="password" name="password" required minlength="6" placeholder="<?= e(tr('auth.password_min')) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
            </label>
            <label class="space-y-2">
                <span class="block text-sm font-medium text-gray-700"><?= e(tr('admin_users_page.role', 'Роль')) ?></span>
                <select name="role" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
                    <?php foreach ($roleOptions as $role => $label): ?>
                    <option value="<?= e($role) ?>" <?= ($old['role'] ?? 'therapist') === $role ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="flex items-end">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">
                    <?= ui_icon('user', 'w-4 h-4') ?>
                    <?= e(tr('admin_users_page.create', 'Создать')) ?>
                </button>
            </div>
        </form>
    </section>
    <?php endif; ?>

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="flex flex-col gap-2 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-800"><?= e(tr('admin_users_page.table_title', 'Список пользователей')) ?></h2>
            <p class="text-sm text-gray-500">
                <?= e($canManageUsers ? tr('admin_users_page.admin_hint', 'Администратор может создавать, менять роли и отключать пользователей.') : tr('admin_users_page.readonly_hint', 'Режим просмотра: управление доступно только администратору.')) ?>
            </p>
        </div>

        <?php if ($users === []): ?>
        <div class="p-8 text-center text-sm text-gray-500"><?= e(tr('admin_users_page.empty_text', 'Пользователей пока нет.')) ?></div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4"><?= e(tr('common.full_name')) ?></th>
                        <th class="px-5 py-4"><?= e(tr('common.email')) ?></th>
                        <th class="px-5 py-4"><?= e(tr('admin_users_page.role', 'Роль')) ?></th>
                        <th class="px-5 py-4"><?= e(tr('admin_users_page.status', 'Статус')) ?></th>
                        <th class="px-5 py-4"><?= e(tr('admin_users_page.last_login', 'Последний вход')) ?></th>
                        <?php if ($canManageUsers): ?><th class="px-5 py-4"><?= e(tr('admin_users_page.actions', 'Действия')) ?></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($users as $user):
                        $role = auth_normalize_role((string) ($user['role'] ?? 'patient'));
                        $isActive = (bool) ($user['is_active'] ?? false);
                    ?>
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <?php $avatar = auth_avatar_url($user); ?>
                                <?php if ($avatar): ?>
                                <img src="<?= e($avatar) ?>" alt="avatar" class="h-10 w-10 rounded-full object-cover">
                                <?php else: ?>
                                <div class="grid h-10 w-10 place-items-center rounded-full gradient-cta text-xs font-bold text-white"><?= e(auth_initials((string) $user['full_name'])) ?></div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-semibold text-gray-800"><?= e((string) $user['full_name']) ?></p>
                                    <p class="text-xs text-gray-400">ID <?= (int) $user['id'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600"><?= e((string) $user['email']) ?></td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"><?= e(auth_role_label($role)) ?></span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= e($isActive ? tr('admin_users_page.active', 'Активен') : tr('admin_users_page.disabled', 'Отключён')) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">
                            <?= !empty($user['last_login_at']) ? e(date('d.m.Y H:i', strtotime((string) $user['last_login_at']))) : '—' ?>
                        </td>
                        <?php if ($canManageUsers): ?>
                        <td class="px-5 py-4">
                            <div class="flex min-w-[320px] flex-wrap gap-2">
                                <form method="POST" action="/admin-users/update.php" class="flex flex-wrap items-center gap-2">
                                    <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                    <input type="hidden" name="full_name" value="<?= e((string) $user['full_name']) ?>">
                                    <input type="hidden" name="email" value="<?= e((string) $user['email']) ?>">
                                    <input type="hidden" name="phone" value="<?= e((string) ($user['phone'] ?? '')) ?>">
                                    <select name="role" class="rounded-xl border border-gray-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
                                        <?php foreach ($roleOptions as $optionRole => $label): ?>
                                        <option value="<?= e($optionRole) ?>" <?= $role === $optionRole ? 'selected' : '' ?>><?= e($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="password" name="password" minlength="6" placeholder="<?= e(tr('admin_users_page.new_password', 'Новый пароль')) ?>" class="w-32 rounded-xl border border-gray-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-soft/40">
                                    <button type="submit" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800"><?= e(tr('common.save')) ?></button>
                                </form>
                                <form method="POST" action="/admin-users/toggle.php">
                                    <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                    <button type="submit" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                        <?= e($isActive ? tr('admin_users_page.disable', 'Отключить') : tr('admin_users_page.enable', 'Включить')) ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
