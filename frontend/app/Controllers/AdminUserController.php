<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

final class AdminUserController extends Controller
{
    public function index(Request $request): void
    {
        auth_require(...User::USER_TABLE_ROLES);

        $this->view('pages.admin_users', [
            'pageTitle' => tr('admin_users_page.title', 'Пользователи'),
            'users' => (new User())->all(),
            'roleOptions' => auth_role_options(),
            'canManageUsers' => auth_is(User::ROLE_ADMIN),
            'error' => $_SESSION['admin_users_error'] ?? null,
            'success' => $_SESSION['admin_users_success'] ?? null,
            'old' => $_SESSION['admin_users_old'] ?? [],
        ]);

        unset($_SESSION['admin_users_error'], $_SESSION['admin_users_success'], $_SESSION['admin_users_old']);
    }

    public function store(Request $request): void
    {
        auth_require(User::ROLE_ADMIN);

        $data = $this->payload($request, true);
        $errors = $this->validate($data, true);
        $userModel = new User();

        if ($data['email'] !== '' && $userModel->emailExists($data['email'])) {
            $errors[] = tr('admin_users_page.email_exists', 'Пользователь с таким email уже существует.');
        }

        if ($errors !== []) {
            $_SESSION['admin_users_error'] = implode(' ', $errors);
            $_SESSION['admin_users_old'] = $data;
            redirect('/admin-users.php');
        }

        $user = $userModel->createByAdmin($data);
        if (!$user) {
            $_SESSION['admin_users_error'] = tr('admin_users_page.save_error', 'Не удалось сохранить пользователя.');
            $_SESSION['admin_users_old'] = $data;
            redirect('/admin-users.php');
        }

        $_SESSION['admin_users_success'] = tr('admin_users_page.created', 'Пользователь создан.');
        redirect('/admin-users.php');
    }

    public function update(Request $request): void
    {
        auth_require(User::ROLE_ADMIN);

        $userId = (int) $request->input('user_id', 0);
        $data = $this->payload($request, false);
        $errors = $this->validate($data, false);
        $userModel = new User();

        if ($userId <= 0) {
            $errors[] = tr('admin_users_page.not_found', 'Пользователь не найден.');
        }
        if ($data['email'] !== '' && $userModel->emailExistsForOther($data['email'], $userId)) {
            $errors[] = tr('admin_users_page.email_exists', 'Пользователь с таким email уже существует.');
        }

        if ($errors !== []) {
            $_SESSION['admin_users_error'] = implode(' ', $errors);
            redirect('/admin-users.php');
        }

        $updated = $userModel->updateByAdmin($userId, $data);
        if (!$updated) {
            $_SESSION['admin_users_error'] = tr('admin_users_page.save_error', 'Не удалось сохранить пользователя.');
            redirect('/admin-users.php');
        }

        $_SESSION['admin_users_success'] = tr('admin_users_page.updated', 'Пользователь обновлён.');
        redirect('/admin-users.php');
    }

    public function toggle(Request $request): void
    {
        auth_require(User::ROLE_ADMIN);

        $userId = (int) $request->input('user_id', 0);
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            $_SESSION['admin_users_error'] = tr('admin_users_page.not_found', 'Пользователь не найден.');
            redirect('/admin-users.php');
        }
        if ($userId === auth_id()) {
            $_SESSION['admin_users_error'] = tr('admin_users_page.self_disable_forbidden', 'Нельзя отключить собственный аккаунт.');
            redirect('/admin-users.php');
        }

        $userModel->setActive($userId, !((bool) ($user['is_active'] ?? false)));
        $_SESSION['admin_users_success'] = tr('admin_users_page.updated', 'Пользователь обновлён.');
        redirect('/admin-users.php');
    }

    private function payload(Request $request, bool $includePassword): array
    {
        $payload = [
            'full_name' => trim((string) $request->input('full_name', '')),
            'email' => trim((string) $request->input('email', '')),
            'phone' => trim((string) $request->input('phone', '')),
            'role' => (string) $request->input('role', User::ROLE_THERAPIST),
            'child_name' => trim((string) $request->input('child_name', '')),
            'child_age' => $request->input('child_age'),
        ];

        if ($includePassword || trim((string) $request->input('password', '')) !== '') {
            $payload['password'] = (string) $request->input('password', '');
        }

        return $payload;
    }

    private function validate(array $data, bool $passwordRequired): array
    {
        $errors = [];
        if (mb_strlen($data['full_name']) < 2) {
            $errors[] = tr('auth.enter_name');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = tr('auth.invalid_email');
        }

        $role = User::normalizeRole((string) ($data['role'] ?? ''));
        if ($role === null || !in_array($role, User::adminCreatableRoles(), true)) {
            $errors[] = tr('admin_users_page.invalid_role', 'Недопустимая роль пользователя.');
        }

        $password = (string) ($data['password'] ?? '');
        if ($passwordRequired && mb_strlen($password) < 6) {
            $errors[] = tr('auth.password_length');
        }
        if (!$passwordRequired && $password !== '' && mb_strlen($password) < 6) {
            $errors[] = tr('auth.password_length');
        }

        return $errors;
    }
}
