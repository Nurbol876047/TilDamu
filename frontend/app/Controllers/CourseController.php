<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Course;
use App\Models\User;

final class CourseController extends Controller
{
    public function index(Request $request): void
    {
        $publishedOnly = !auth_has_any_role(...User::COURSE_MANAGER_ROLES);
        $courses = (new Course())->all($publishedOnly);
        // Load media for each course
        $courseModel = new Course();
        foreach ($courses as &$c) {
            $c['media'] = $courseModel->getMedia((int) $c['id']);
        }
        unset($c);

        $this->view('pages.courses', [
            'pageTitle' => tr('courses_page.title', 'Курсы лечения'),
            'courses' => $courses,
            'canManageCourses' => auth_has_any_role(...User::COURSE_MANAGER_ROLES),
        ]);
    }

    public function create(Request $request): void
    {
        auth_require(...User::COURSE_MANAGER_ROLES);
        $this->view('pages.course_create', [
            'pageTitle' => tr('courses_page.create', 'Создать курс'),
            'error' => $_SESSION['course_error'] ?? null,
            'old' => $_SESSION['course_old'] ?? [],
        ]);
        unset($_SESSION['course_error'], $_SESSION['course_old']);
    }

    public function store(Request $request): void
    {
        auth_require(...User::COURSE_MANAGER_ROLES);

        $data = [
            'title' => trim((string) $request->input('title', '')),
            'description' => trim((string) $request->input('description', '')),
            'content' => trim((string) $request->input('content', '')),
            'target_sounds' => trim((string) $request->input('target_sounds', '')),
            'age_from' => (int) $request->input('age_from', 3),
            'age_to' => (int) $request->input('age_to', 10),
            'difficulty' => (string) $request->input('difficulty', 'Легко'),
            'lessons_count' => (int) $request->input('lessons_count', 0),
            'is_published' => $request->input('is_published') ? 1 : 0,
            'author_id' => auth_id(),
        ];

        if ($data['title'] === '') {
            $_SESSION['course_error'] = tr('courses_page.error_title', 'Введите название курса.');
            $_SESSION['course_old'] = $data;
            redirect('/course-create.php');
        }

        $courseModel = new Course();
        $courseId = $courseModel->create($data);

        if (!$courseId) {
            $_SESSION['course_error'] = tr('courses_page.error_db', 'Ошибка при создании курса.');
            $_SESSION['course_old'] = $data;
            redirect('/course-create.php');
        }

        // Handle media uploads
        $uploadDir = storage_path('uploads/courses/' . $courseId);
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }

        $files = $_FILES['media'] ?? null;
        if ($files && isset($files['name']) && is_array($files['name'])) {
            $maxSize = 50 * 1024 * 1024; // 50 MB
            for ($i = 0; $i < count($files['name']); $i++) {
                if (($files['error'][$i] ?? 4) !== 0) continue;
                if (($files['size'][$i] ?? 0) > $maxSize) continue;
                if (($files['tmp_name'][$i] ?? '') === '') continue;

                $origName = basename($files['name'][$i]);
                $safeName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                $destPath = $uploadDir . '/' . $safeName;
                $mime = $files['type'][$i] ?? 'application/octet-stream';

                if (move_uploaded_file($files['tmp_name'][$i], $destPath)) {
                    $courseModel->addMedia($courseId, [
                        'file_name' => $origName,
                        'file_path' => 'storage/uploads/courses/' . $courseId . '/' . $safeName,
                        'file_type' => Course::detectFileType($mime),
                        'file_size' => (int) ($files['size'][$i] ?? 0),
                        'mime_type' => $mime,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        redirect('/courses.php');
    }
}
