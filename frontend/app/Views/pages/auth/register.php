<?php

declare(strict_types=1);

$error ??= null;
$old ??= [];
$hideNav = true;

require __DIR__ . '/../../layouts/header.php';
?>

<div class="min-h-[75vh] flex items-center justify-center py-12">
    <div class="w-full max-w-2xl space-y-8">
        <div class="text-center space-y-3">
            <div class="w-20 h-20 rounded-3xl gradient-icon-blue flex items-center justify-center mx-auto shadow-lg">
                <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800"><?= e(tr('auth.register_title')) ?></h1>
            <p class="text-gray-500"><?= e(tr('auth.register_subtitle')) ?></p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
            <p class="text-sm text-red-600 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                <?= e($error) ?>
            </p>
        </div>
        <?php endif; ?>

        <form method="POST" action="/register" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-xl p-8 space-y-6" id="registerForm">
            <div class="grid md:grid-cols-[140px_1fr] gap-5 items-start rounded-2xl border border-dashed border-gray-300 p-5 bg-gray-50">
                <div class="w-32 h-32 rounded-3xl overflow-hidden bg-white border border-gray-200 flex items-center justify-center shadow-sm mx-auto md:mx-0">
                    <img id="registerAvatarPreview" src="" alt="preview" class="hidden w-full h-full object-cover">
                    <div id="registerAvatarPlaceholder" class="text-gray-400 text-center px-3">
                        <div class="mx-auto w-fit"><?= ui_icon('camera-plus', 'w-9 h-9') ?></div>
                        <div class="text-xs mt-2"><?= e(tr('auth.avatar_title')) ?></div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2"><?= e(tr('auth.avatar_title')) ?> <span class="text-gray-400">(<?= e(tr('common.optional')) ?>)</span></label>
                        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-100">
                            <?= ui_icon('camera-plus', 'w-4 h-4') ?>
                            <span><?= e(tr('auth.upload_avatar')) ?></span>
                            <input id="registerAvatarInput" type="file" name="avatar" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500"><?= e(tr('auth.avatar_hint')) ?></p>
                    <p id="registerAvatarName" class="text-xs text-gray-400"></p>
                </div>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                <input type="hidden" name="role" value="patient" class="register-role-input">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 text-blue-soft"><?= ui_icon('user', 'w-5 h-5') ?></div>
                    <div>
                        <p class="font-semibold text-gray-800"><?= e(tr('auth.role_patient', 'Клиент / родитель')) ?></p>
                        <p class="mt-1 text-sm text-gray-600"><?= e(tr('auth.patient_notice', 'Регистрация открыта для клиентов. Администраторы, разработчики и логопеды создаются администратором.')) ?></p>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700"><?= e(tr('common.full_name')) ?></label>
                    <input type="text" name="full_name" value="<?= e((string) ($old['full_name'] ?? '')) ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700"><?= e(tr('common.phone')) ?></label>
                    <input type="text" name="phone" value="<?= e((string) ($old['phone'] ?? '')) ?>" class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700"><?= e(tr('common.email')) ?></label>
                    <input type="email" name="email" value="<?= e((string) ($old['email'] ?? '')) ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700"><?= e(tr('common.password')) ?></label>
                    <input type="password" name="password" required minlength="6" placeholder="<?= e(tr('auth.password_min')) ?>" class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700"><?= e(tr('auth.password_confirm')) ?></label>
                    <input type="password" name="password_confirm" required minlength="6" class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
                </div>
                <div id="childAgeWrap" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700"><?= e(tr('auth.child_age')) ?></label>
                    <input type="number" name="child_age" min="1" max="18" value="<?= e((string) ($old['child_age'] ?? '')) ?>" class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
                </div>
            </div>

            <div id="childFields" class="space-y-2">
                <label class="block text-sm font-medium text-gray-700"><?= e(tr('auth.child_name')) ?></label>
                <input type="text" name="child_name" value="<?= e((string) ($old['child_name'] ?? '')) ?>" class="w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-soft/50 focus:border-blue-soft transition-colors">
            </div>

            <button type="submit" class="w-full py-3 text-white font-medium rounded-2xl gradient-cta hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                <?= e(tr('common.register')) ?>
            </button>
        </form>

        <p class="text-center text-sm text-gray-500">
            <?= e(tr('auth.has_account')) ?>
            <a href="/login" class="text-blue-soft font-medium hover:underline"><?= e(tr('common.login')) ?></a>
        </p>
    </div>
</div>

<?php
$extraScripts = <<<JS
<script>
function toggleChildFields() {
    const role = document.querySelector('input[name="role"]:checked')?.value || document.querySelector('input[name="role"]')?.value || 'patient';
    const childFields = document.getElementById('childFields');
    const childAgeWrap = document.getElementById('childAgeWrap');
    const childNameInput = document.querySelector('input[name="child_name"]');

    if (role === 'patient' || role === 'parent' || role === 'client') {
        childFields.classList.remove('hidden');
        childAgeWrap.classList.remove('hidden');
        childNameInput?.setAttribute('required', 'required');
    } else {
        childFields.classList.add('hidden');
        childAgeWrap.classList.add('hidden');
        childNameInput?.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.register-role-input').forEach((radio) => radio.addEventListener('change', toggleChildFields));
    toggleChildFields();

    const input = document.getElementById('registerAvatarInput');
    const preview = document.getElementById('registerAvatarPreview');
    const placeholder = document.getElementById('registerAvatarPlaceholder');
    const fileName = document.getElementById('registerAvatarName');

    input?.addEventListener('change', (event) => {
        const [file] = event.target.files || [];
        if (!file) {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            preview.src = '';
            fileName.textContent = '';
            return;
        }

        fileName.textContent = file.name;
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target?.result || '';
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
});
</script>
JS;
require __DIR__ . '/../../layouts/footer.php';
?>
