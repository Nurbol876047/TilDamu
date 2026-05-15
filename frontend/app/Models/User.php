<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    public const ROLE_PATIENT = 'patient';
    public const ROLE_THERAPIST = 'therapist';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESEARCHER = 'researcher';
    public const ROLE_DEVELOPER = 'developer';

    public const CANONICAL_ROLES = [
        self::ROLE_PATIENT,
        self::ROLE_THERAPIST,
        self::ROLE_ADMIN,
        self::ROLE_RESEARCHER,
        self::ROLE_DEVELOPER,
    ];

    public const LEGACY_ROLE_ALIASES = [
        'parent' => self::ROLE_PATIENT,
        'client' => self::ROLE_PATIENT,
    ];

    public const USER_TABLE_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DEVELOPER,
        self::ROLE_RESEARCHER,
    ];

    public const DATASET_MANAGER_ROLES = [
        self::ROLE_THERAPIST,
        self::ROLE_ADMIN,
        self::ROLE_DEVELOPER,
        self::ROLE_RESEARCHER,
    ];

    public const THERAPIST_PANEL_ROLES = [
        self::ROLE_THERAPIST,
        self::ROLE_ADMIN,
        self::ROLE_DEVELOPER,
        self::ROLE_RESEARCHER,
    ];

    public const COURSE_MANAGER_ROLES = [
        self::ROLE_THERAPIST,
        self::ROLE_ADMIN,
    ];

    public static function normalizeRole(?string $role): ?string
    {
        $role = strtolower(trim((string) $role));
        if ($role === '') {
            return null;
        }

        if (isset(self::LEGACY_ROLE_ALIASES[$role])) {
            return self::LEGACY_ROLE_ALIASES[$role];
        }

        return in_array($role, self::CANONICAL_ROLES, true) ? $role : null;
    }

    public static function adminCreatableRoles(): array
    {
        return self::CANONICAL_ROLES;
    }

    public function register(array $data): ?array
    {
        if (!$this->db()) {
            return null;
        }

        $role = self::normalizeRole((string) ($data['role'] ?? self::ROLE_PATIENT)) ?? self::ROLE_PATIENT;

        $stmt = $this->db()->prepare(
            'INSERT INTO users (full_name, email, phone, password_hash, role, avatar_url, created_at)
             VALUES (:full_name, :email, :phone, :password_hash, :role, :avatar_url, NOW())'
        );

        $stmt->execute([
            'full_name' => trim((string) $data['full_name']),
            'email' => strtolower(trim((string) $data['email'])),
            'phone' => $data['phone'] ?? null,
            'password_hash' => password_hash((string) $data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role' => $role,
            'avatar_url' => $data['avatar_url'] ?? null,
        ]);

        $userId = (int) $this->db()->lastInsertId();

        if ($role === self::ROLE_PATIENT && !empty($data['child_name'])) {
            $childAge = isset($data['child_age']) && is_numeric($data['child_age']) ? (int) $data['child_age'] : null;
            $child = (new Child())->firstOrCreate($data['child_name'], $childAge);

            if ($child && isset($child['id'])) {
                $this->db()->prepare('UPDATE users SET child_id = :child_id WHERE id = :id')
                    ->execute([
                        'child_id' => $child['id'],
                        'id' => $userId,
                    ]);
            }
        }

        return $this->findById($userId);
    }

    public function createByAdmin(array $data): ?array
    {
        if (!$this->db()) {
            return null;
        }

        $role = self::normalizeRole((string) ($data['role'] ?? self::ROLE_THERAPIST));
        if ($role === null) {
            return null;
        }

        $childId = null;
        $therapistId = null;
        if ($role === self::ROLE_PATIENT && !empty($data['child_name'])) {
            $childAge = isset($data['child_age']) && is_numeric($data['child_age']) ? (int) $data['child_age'] : null;
            $child = (new Child())->firstOrCreate(trim((string) $data['child_name']), $childAge);
            $childId = isset($child['id']) ? (int) $child['id'] : null;
        }

        if ($role === self::ROLE_THERAPIST) {
            $therapistId = $this->createTherapistProfile(
                trim((string) $data['full_name']),
                strtolower(trim((string) $data['email']))
            );
        }

        $stmt = $this->db()->prepare(
            'INSERT INTO users (full_name, email, phone, password_hash, role, child_id, therapist_id, avatar_url, is_active, created_at)
             VALUES (:full_name, :email, :phone, :password_hash, :role, :child_id, :therapist_id, :avatar_url, 1, NOW())'
        );

        $stmt->execute([
            'full_name' => trim((string) $data['full_name']),
            'email' => strtolower(trim((string) $data['email'])),
            'phone' => $data['phone'] ?? null,
            'password_hash' => password_hash((string) $data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role' => $role,
            'child_id' => $childId,
            'therapist_id' => $therapistId,
            'avatar_url' => $data['avatar_url'] ?? null,
        ]);

        return $this->findById((int) $this->db()->lastInsertId());
    }

    public function attempt(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!(int) ($user['is_active'] ?? 0)) {
            return null;
        }

        $storedPassword = (string) ($user['password_hash'] ?? '');
        $passwordInfo = password_get_info($storedPassword);
        $verified = false;
        $needsRehash = false;

        if (($passwordInfo['algo'] ?? 0) !== 0) {
            $verified = password_verify($password, $storedPassword);
            $needsRehash = $verified && password_needs_rehash($storedPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        } else {
            $verified = hash_equals($storedPassword, $password);
            $needsRehash = $verified;
        }

        if (!$verified) {
            return null;
        }

        if ($needsRehash) {
            $this->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]));
        }

        $this->db()?->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')
            ->execute(['id' => $user['id']]);

        return $this->normalizeUserRow($user);
    }

    public function all(): array
    {
        if (!$this->db()) {
            return [];
        }

        $rows = $this->db()->query('SELECT * FROM users ORDER BY created_at DESC, id DESC')->fetchAll() ?: [];
        return array_map(fn(array $row) => $this->normalizeUserRow($row), $rows);
    }

    public function findById(int $id): ?array
    {
        if (!$this->db()) {
            return null;
        }

        $stmt = $this->db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch() ?: null;
        return $this->normalizeUserRow($row);
    }

    public function findByEmail(string $email): ?array
    {
        if (!$this->db()) {
            return null;
        }

        $stmt = $this->db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => strtolower(trim($email))]);

        $row = $stmt->fetch() ?: null;
        return $this->normalizeUserRow($row);
    }

    public function emailExists(string $email): bool
    {
        if (!$this->db()) {
            return false;
        }

        $stmt = $this->db()->prepare('SELECT COUNT(*) AS cnt FROM users WHERE email = :email');
        $stmt->execute(['email' => strtolower(trim($email))]);

        return ((int) ($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function emailExistsForOther(string $email, int $userId): bool
    {
        if (!$this->db()) {
            return false;
        }

        $stmt = $this->db()->prepare('SELECT COUNT(*) AS cnt FROM users WHERE email = :email AND id != :id');
        $stmt->execute([
            'email' => strtolower(trim($email)),
            'id' => $userId,
        ]);

        return ((int) ($stmt->fetch()['cnt'] ?? 0)) > 0;
    }

    public function updateAvatar(int $id, string $avatarUrl): void
    {
        if (!$this->db()) {
            return;
        }

        $stmt = $this->db()->prepare('UPDATE users SET avatar_url = :avatar_url WHERE id = :id');
        $stmt->execute([
            'avatar_url' => $avatarUrl,
            'id' => $id,
        ]);
    }

    public function updateProfile(int $id, array $data): void
    {
        if (!$this->db()) {
            return;
        }

        $fields = [];
        $params = ['id' => $id];

        if (isset($data['full_name'])) {
            $fields[] = 'full_name = :full_name';
            $params['full_name'] = $data['full_name'];
        }

        if (isset($data['phone'])) {
            $fields[] = 'phone = :phone';
            $params['phone'] = $data['phone'];
        }

        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = password_hash((string) $data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (isset($data['avatar_url'])) {
            $fields[] = 'avatar_url = :avatar_url';
            $params['avatar_url'] = $data['avatar_url'];
        }

        if ($fields !== []) {
            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $this->db()->prepare($sql)->execute($params);
        }
    }

    public function updateByAdmin(int $id, array $data): ?array
    {
        if (!$this->db()) {
            return null;
        }

        $user = $this->findById($id);
        if (!$user) {
            return null;
        }

        $fields = [];
        $params = ['id' => $id];

        if (isset($data['full_name'])) {
            $fields[] = 'full_name = :full_name';
            $params['full_name'] = trim((string) $data['full_name']);
        }

        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params['email'] = strtolower(trim((string) $data['email']));
        }

        if (array_key_exists('phone', $data)) {
            $fields[] = 'phone = :phone';
            $params['phone'] = $data['phone'] !== '' ? $data['phone'] : null;
        }

        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = password_hash((string) $data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (!empty($data['role'])) {
            $role = self::normalizeRole((string) $data['role']);
            if ($role === null) {
                return null;
            }
            $fields[] = 'role = :role';
            $params['role'] = $role;

            if ($role === self::ROLE_THERAPIST && empty($user['therapist_id'])) {
                $therapistId = $this->createTherapistProfile((string) ($data['full_name'] ?? $user['full_name']), (string) ($data['email'] ?? $user['email']));
                $fields[] = 'therapist_id = :therapist_id';
                $params['therapist_id'] = $therapistId;
            }
        }

        if (array_key_exists('is_active', $data)) {
            $fields[] = 'is_active = :is_active';
            $params['is_active'] = (int) (bool) $data['is_active'];
        }

        if ($fields !== []) {
            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $this->db()->prepare($sql)->execute($params);
        }

        return $this->findById($id);
    }

    public function setActive(int $id, bool $active): void
    {
        if (!$this->db()) {
            return;
        }

        $stmt = $this->db()->prepare('UPDATE users SET is_active = :is_active WHERE id = :id');
        $stmt->execute([
            'is_active' => $active ? 1 : 0,
            'id' => $id,
        ]);
    }

    private function updatePasswordHash(int $id, string $passwordHash): void
    {
        if (!$this->db()) {
            return;
        }

        $this->db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id')
            ->execute([
                'password_hash' => $passwordHash,
                'id' => $id,
            ]);
    }

    private function createTherapistProfile(string $fullName, string $email): ?int
    {
        if (!$this->db()) {
            return null;
        }

        $stmt = $this->db()->prepare('INSERT INTO therapists (full_name, email, role, created_at) VALUES (:full_name, :email, :role, NOW())');
        $stmt->execute([
            'full_name' => $fullName,
            'email' => strtolower(trim($email)),
            'role' => 'Логопед',
        ]);

        return (int) $this->db()->lastInsertId();
    }

    private function normalizeUserRow(?array $row): ?array
    {
        if (!$row) {
            return null;
        }

        $row['role'] = self::normalizeRole((string) ($row['role'] ?? '')) ?? (string) ($row['role'] ?? self::ROLE_PATIENT);
        return $row;
    }
}
