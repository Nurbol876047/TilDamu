<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Assessment;
use App\Models\Child;
use App\Models\User;

final class TherapistController extends Controller
{
    public function index(Request $request): void
    {
        auth_require(...User::THERAPIST_PANEL_ROLES);

        $assessmentModel = new Assessment();
        $childModel = new Child();

        $rows = $assessmentModel->byChildSummary();
        $children = array_map(function (array $child) {
            $sounds = json_decode((string) ($child['sounds_json'] ?? '[]'), true) ?: [];
            return [
                'id' => (int) $child['id'],
                'name' => $child['name'],
                'age' => (int) ($child['age'] ?? 0),
                'status' => $child['status'] ?? 'Активен',
                'lastSession' => $child['last_session'] ? date('d.m.Y', strtotime((string) $child['last_session'])) : '—',
                'progress' => (int) ($child['progress'] ?? 0),
                'diagnosis' => $child['diagnosis'] ?? '—',
                'problemSounds' => array_map(fn($item) => $item['sound'] ?? '', $sounds),
                'notes' => $child['notes'] ?? '',
            ];
        }, $rows);

        $analytics = $assessmentModel->analytics();

        // Build real progress series for each child (for charts)
        $progressMap = [];
        foreach ($children as $child) {
            $progressMap[$child['id']] = $assessmentModel->progressSeries($child['id'], 10);
        }

        // Real dashboard stats
        $totalChildren = count($children);
        $activeChildren = count(array_filter($children, fn($c) => ($c['status'] ?? '') === 'Активен'));
        $platformStats = $assessmentModel->platformStats();
        $totalMessages = $childModel->totalChatMessages();

        $this->view('pages.therapist', [
            'pageTitle' => (string) t('therapist'),
            'children' => $children,
            'analyticsData' => $analytics,
            'progressMap' => $progressMap,
            'dashboardStats' => [
                'totalChildren' => $totalChildren,
                'activeChildren' => $activeChildren,
                'totalSessions' => $platformStats['totalAssessments'],
                'totalMessages' => $totalMessages,
            ],
        ]);
    }

    public function saveNotes(Request $request): never
    {
        auth_require(...User::THERAPIST_PANEL_ROLES);
        $data = $request->all();
        $childId = (int) ($data['child_id'] ?? 0);
        $notes = trim((string) ($data['notes'] ?? ''));
        if ($childId > 0) {
            (new Child())->updateNotes($childId, $notes);
        }
        $this->json(['ok' => true]);
    }
}
