<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Services\AiClient;
use App\Services\AssessmentBuilder;
use App\Services\DiagnosisSessionService;

final class DiagnosisController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('pages.diagnosis', [
            'pageTitle' => (string) t('diagnosis'),
            'words' => (new DiagnosisSessionService())->words(),
        ]);
    }

    public function dataset(Request $request): void
    {
        $this->view('pages.dataset', [
            'pageTitle' => (string) t('dataset'),
            'words' => (new DiagnosisSessionService())->words(),
        ]);
    }

    public function datasetHistory(Request $request): void
    {
        auth_require(...User::DATASET_MANAGER_ROLES);

        $this->view('pages.dataset_history', [
            'pageTitle' => (string) t('dataset_history'),
        ]);
    }

    public function start(Request $request): never
    {
        $name = trim((string) ($request->all()['child_name'] ?? ''));
        $ageValue = $request->all()['child_age'] ?? null;
        $age = is_numeric($ageValue) ? (int) $ageValue : null;
        $disorderType = trim((string) ($request->all()['disorder_type'] ?? ''));
        (new DiagnosisSessionService())->start($name !== '' ? $name : 'Ребенок', $age, $disorderType);
        $this->json(['ok' => true]);
    }

    public function analyze(Request $request): never
    {
        $sessionService = new DiagnosisSessionService();
        if (!isset($_SESSION['diagnosis'])) {
            $sessionService->start('Ребенок', null);
        }

        $file = $request->file('audio');
        if (!$file || ($file['tmp_name'] ?? '') === '') {
            $this->json(['ok' => false, 'message' => 'Аудиофайл не получен.'], 422);
        }

        $word = trim((string) ($request->input('word') ?? ''));
        if ($word === '') {
            $this->json(['ok' => false, 'message' => 'Не передано контрольное слово.'], 422);
        }

        $child = [
            'full_name' => $_SESSION['diagnosis']['child_name'] ?? 'Ребенок',
            'age' => $_SESSION['diagnosis']['child_age'] ?? null,
        ];
        try {
            $analysis = (new AiClient())->analyzeSpeech($file['tmp_name'], $word, $child);
            $analysis['word'] = $word;
            $sessionService->addWordResult($analysis);
            $this->json(['ok' => true, 'analysis' => $analysis]);
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'message' => $e->getMessage()], 502);
        }
    }

    public function complete(Request $request): never
    {
        try {
            $assessment = (new AssessmentBuilder())->finalizeSession();
            $this->json([
                'ok' => true,
                'redirect' => '/results?assessment=' . urlencode((string) $assessment['public_id']),
                'assessment' => $assessment,
            ]);
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
