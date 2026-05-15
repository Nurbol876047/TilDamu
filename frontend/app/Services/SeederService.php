<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class SeederService
{
    public function seed(): void
    {
        $pdo = Database::connection();
        if (!$pdo) {
            return;
        }

        $count = (int) ($pdo->query('SELECT COUNT(*) AS cnt FROM children')->fetch()['cnt'] ?? 0);
        if ($count > 0) {
            return;
        }

        $pdo->exec(<<<'SQL'
INSERT INTO therapists (full_name, email, role) VALUES
('Алия Нурланова', 'aliya@example.com', 'Логопед-дефектолог')
SQL);

        // Create default user accounts (password: 123456)
        $hash = password_hash('123456', PASSWORD_BCRYPT, ['cost' => 12]);

        // Check if users table exists before seeding
        try {
            $pdo->query('SELECT 1 FROM users LIMIT 1');
            $userCount = (int) ($pdo->query('SELECT COUNT(*) AS cnt FROM users')->fetch()['cnt'] ?? 0);
            if ($userCount === 0) {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, therapist_id, created_at) VALUES ('Алия Нурланова', 'admin@tildamu.kz', '+7 700 000 00 00', :hash, 'admin', 1, NOW())");
                $stmt->execute(['hash' => $hash]);

                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, therapist_id, created_at) VALUES ('Алия Нурланова', 'therapist@tildamu.kz', '+7 700 000 00 01', :hash, 'therapist', 1, NOW())");
                $stmt->execute(['hash' => $hash]);

                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, created_at) VALUES ('Разработчик TilDamu', 'developer@tildamu.kz', '+7 700 000 00 02', :hash, 'developer', NOW())");
                $stmt->execute(['hash' => $hash]);
            }
        } catch (\PDOException $e) {
            // users table not yet created — skip
        }

        $pdo->exec(<<<'SQL'
INSERT INTO children (full_name, age, parent_name, parent_phone, status, notes) VALUES
('Анар К.', 6, 'Камила', '+7 700 111 22 33', 'Активен', 'Работа над звуками Р, Л, Ш'),
('Даниял М.', 5, 'Мадина', '+7 700 222 33 44', 'Активен', 'Нужна мягкая работа над плавностью речи'),
('Айша С.', 7, 'Самал', '+7 700 333 44 55', 'Ожидает', 'Контрольная диагностика через неделю')
SQL);

        $pdo->exec(<<<'SQL'
INSERT INTO assessments (public_id, child_id, overall_score, diagnosis, confidence, transcription, ai_summary, strengths_json, recommendations_json, sounds_json, raw_payload_json, created_at) VALUES
('seed-anar-1', 1, 72, 'Функциональная дислалия (легкая форма)', 89, 'рыба', 'Замечены трудности с Р, Л, Ш.',
 '["Хорошее произношение гласных","Четкая дикция при медленном темпе"]',
 '["Ежедневные упражнения на звук Р","Артикуляционная гимнастика утром"]',
 '[{"sound":"Р","severity":"средняя","correct":45},{"sound":"Л","severity":"легкая","correct":70},{"sound":"Ш","severity":"легкая","correct":75}]',
 '{"seed":true}', NOW()),
('seed-danial-1', 2, 58, 'Нарушение плавности речи (умеренное)', 84, 'самолет', 'Нужна консультация логопеда и упражнения на дыхание.',
 '["Хорошее понимание инструкции"]',
 '["Дыхательные упражнения","Упражнения на ритм речи"]',
 '[{"sound":"С","severity":"средняя","correct":52},{"sound":"З","severity":"легкая","correct":64}]',
 '{"seed":true}', NOW()),
('seed-aisha-1', 3, 85, 'Функциональная дислалия (легкая форма)', 91, 'чашка', 'Позитивная динамика, сохраняем план коррекции.',
 '["Хороший темп речи","Правильное произношение большинства слов"]',
 '["Сохранять домашнюю практику"]',
 '[{"sound":"Р","severity":"легкая","correct":82}]',
 '{"seed":true}', NOW())
SQL);

        $pdo->exec(<<<'SQL'
INSERT INTO exercise_templates (title, description, duration_minutes, difficulty, sound, stars, video_url, sort_order) VALUES
('Упражнение "Рычащий тигр"', 'Отработка звука Р с помощью игровой методики', 10, 'Легко', 'Р', 5, 'https://www.youtube.com/watch?v=5L0D4i4YJgQ', 1),
('Упражнение "Летящий самолет"', 'Развитие артикуляции для звука Л', 8, 'Средне', 'Л', 3, 'https://www.youtube.com/watch?v=Q5b2m4r5gH4', 2),
('Упражнение "Шипящая змея"', 'Правильное произношение звука Ш', 12, 'Легко', 'Ш', 4, 'https://www.youtube.com/watch?v=Rk7p3BqFf1g', 3)
SQL);
    }
}
