<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AutoInstaller
{
    public function run(): void
    {
        if (!env('DB_AUTO_MIGRATE', true)) {
            return;
        }

        $pdo = Database::connection();
        if (!$pdo) {
            return;
        }

        $lockFile = storage_path('cache/install.lock');
        $this->runRoleMigration($pdo);

        if (is_file($lockFile)) {
            return;
        }

        $schemaFile = base_path('app/Database/schema.sql');
        if (is_file($schemaFile)) {
            $sql = file_get_contents($schemaFile) ?: '';
            if ($sql !== '') {
                $pdo->exec($sql);
            }
        }

        // Users table migration
        $usersMigration = base_path('app/Database/users_migration.sql');
        if (is_file($usersMigration)) {
            $sql = file_get_contents($usersMigration) ?: '';
            if ($sql !== '') {
                $pdo->exec($sql);
            }
        }
        $this->runRoleMigration($pdo);

        // Courses tables migration
        $coursesMigration = base_path('app/Database/courses_migration.sql');
        if (is_file($coursesMigration)) {
            $sql = file_get_contents($coursesMigration) ?: '';
            if ($sql !== '') {
                $pdo->exec($sql);
            }
        }

        if (env('DB_AUTO_SEED', true)) {
            (new SeederService())->seed();
        }

        if (!is_dir(dirname($lockFile))) {
            @mkdir(dirname($lockFile), 0775, true);
        }
        @file_put_contents($lockFile, 'installed at ' . date('c'));
    }

    private function runRoleMigration(\PDO $pdo): void
    {
        if (!$this->tableExists($pdo, 'users') || !$this->rolesNeedMigration($pdo)) {
            return;
        }

        $rolesMigration = base_path('app/Database/roles_migration.sql');
        if (!is_file($rolesMigration)) {
            return;
        }

        $sql = file_get_contents($rolesMigration) ?: '';
        if ($sql === '') {
            return;
        }

        try {
            $pdo->exec($sql);
        } catch (\PDOException $e) {
            Database::log('Roles migration failed: ' . $e->getMessage());
        }
    }

    private function tableExists(\PDO $pdo, string $table): bool
    {
        try {
            $stmt = $pdo->query('SHOW TABLES LIKE ' . $pdo->quote($table));
            return (bool) $stmt->fetchColumn();
        } catch (\PDOException) {
            return false;
        }
    }

    private function rolesNeedMigration(\PDO $pdo): bool
    {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'role'");
            $column = $stmt->fetch();
            $type = strtolower((string) ($column['Type'] ?? ''));
            if (!str_contains($type, 'patient') || !str_contains($type, 'developer') || !str_contains($type, 'researcher')) {
                return true;
            }

            $legacyRows = (int) ($pdo->query("SELECT COUNT(*) AS cnt FROM `users` WHERE `role` = 'parent'")->fetch()['cnt'] ?? 0);
            return $legacyRows > 0;
        } catch (\PDOException) {
            return true;
        }
    }
}
