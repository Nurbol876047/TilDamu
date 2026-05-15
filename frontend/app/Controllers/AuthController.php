<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Services\AvatarService;

final class AuthController extends Controller
{
    public function showLogin(Request $request): void
    {
        if (auth_check()) {
            redirect('/');
        }

        $this->view('pages.auth.login', [
            'pageTitle' => tr('common.login'),
            'error' => $_SESSION['auth_error'] ?? null,
            'old_email' => $_SESSION['auth_old_email'] ?? '',
        ]);

        unset($_SESSION['auth_error'], $_SESSION['auth_old_email']);
    }

    public function login(Request $request): void
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $avatarFile = $request->file('avatar');

        $avatarService = new AvatarService();
        $avatarError = $avatarService->validate($avatarFile);

        if ($email === '' || $password === '') {
            $_SESSION['auth_error'] = tr('auth.fill_all');
            $_SESSION['auth_old_email'] = $email;
            redirect('/login.php');
        }

        if ($avatarError) {
            $_SESSION['auth_error'] = $avatarError;
            $_SESSION['auth_old_email'] = $email;
            redirect('/login.php');
        }

        $userModel = new User();
        $user = $userModel->attempt($email, $password);
        if (!$user) {
            $_SESSION['auth_error'] = tr('auth.invalid_credentials');
            $_SESSION['auth_old_email'] = $email;
            redirect('/login.php');
        }

        if ($avatarFile && ($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $avatarUrl = $avatarService->store($avatarFile);
            if ($avatarUrl === null) {
                $_SESSION['auth_error'] = tr('auth.avatar_upload_error');
                $_SESSION['auth_old_email'] = $email;
                redirect('/login.php');
            }
            $userModel->updateAvatar((int) $user['id'], $avatarUrl);
        }

        $this->storeUserSession($user);

        $intended = (string) ($_SESSION['auth_intended'] ?? '');
        unset($_SESSION['auth_intended']);

        if ($intended !== '') {
            redirect($intended);
        }

        $redirect = match (User::normalizeRole((string) $user['role'])) {
            User::ROLE_ADMIN, User::ROLE_DEVELOPER, User::ROLE_RESEARCHER => '/admin-users.php',
            User::ROLE_THERAPIST => '/therapist.php',
            default => '/results.php',
        };
        redirect($redirect);
    }

    public function showRegister(Request $request): void
    {
        if (auth_check()) {
            redirect('/');
        }

        $this->view('pages.auth.register', [
            'pageTitle' => tr('common.register'),
            'error' => $_SESSION['auth_error'] ?? null,
            'old' => $_SESSION['auth_old'] ?? [],
        ]);

        unset($_SESSION['auth_error'], $_SESSION['auth_old']);
    }

    public function register(Request $request): void
    {
        $data = [
            'full_name' => trim((string) $request->input('full_name', '')),
            'email' => trim((string) $request->input('email', '')),
            'phone' => trim((string) $request->input('phone', '')),
            'password' => (string) $request->input('password', ''),
            'password_confirm' => (string) $request->input('password_confirm', ''),
            'role' => (string) $request->input('role', User::ROLE_PATIENT),
            'child_name' => trim((string) $request->input('child_name', '')),
            'child_age' => $request->input('child_age'),
        ];

        $avatarFile = $request->file('avatar');
        $avatarService = new AvatarService();

        $errors = [];
        if ($data['full_name'] === '') {
            $errors[] = tr('auth.enter_name');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = tr('auth.invalid_email');
        }
        if (mb_strlen($data['password']) < 6) {
            $errors[] = tr('auth.password_length');
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = tr('auth.password_mismatch');
        }
        $role = User::normalizeRole($data['role']);
        if ($role !== User::ROLE_PATIENT) {
            $role = User::ROLE_PATIENT;
        }
        $data['role'] = $role;
        if ($data['role'] === User::ROLE_PATIENT && $data['child_name'] === '') {
            $errors[] = tr('auth.enter_child_name');
        }

        $avatarError = $avatarService->validate($avatarFile);
        if ($avatarError) {
            $errors[] = $avatarError;
        }

        $userModel = new User();
        if ($data['email'] !== '' && $userModel->emailExists($data['email'])) {
            $errors[] = tr('auth.email_exists');
        }

        if ($errors !== []) {
            $_SESSION['auth_error'] = implode(' ', $errors);
            $_SESSION['auth_old'] = $data;
            redirect('/register.php');
        }

        if ($avatarFile && ($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $avatarUrl = $avatarService->store($avatarFile);
            if ($avatarUrl === null) {
                $_SESSION['auth_error'] = tr('auth.avatar_upload_error');
                $_SESSION['auth_old'] = $data;
                redirect('/register.php');
            }
            $data['avatar_url'] = $avatarUrl;
        }

        $user = $userModel->register($data);
        if (!$user) {
            $_SESSION['auth_error'] = tr('auth.register_error');
            $_SESSION['auth_old'] = $data;
            redirect('/register.php');
        }

        $this->storeUserSession($user);

        redirect('/results.php');
    }

    public function logout(Request $request): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_name']);
        session_regenerate_id(true);
        redirect('/login.php');
    }

    private function storeUserSession(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = User::normalizeRole((string) ($user['role'] ?? '')) ?? User::ROLE_PATIENT;
        $_SESSION['user_name'] = (string) $user['full_name'];
    }
}
