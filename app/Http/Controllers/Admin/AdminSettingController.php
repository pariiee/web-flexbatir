<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    /**
     * Konfigurasi settings yang dikelola di halaman admin.
     * Format: [key, label, group, is_encrypted, type, placeholder]
     */
    private const MANAGED_SETTINGS = [
        [
            'key'          => 'ai_api_key',
            'label'        => 'OpenAgentic API Key',
            'group'        => 'ai',
            'is_encrypted' => true,
            'type'         => 'password',
            'placeholder'  => 'sk-...',
        ],
        [
            'key'          => 'ai_default_model',
            'label'        => 'Default AI Model',
            'group'        => 'ai',
            'is_encrypted' => false,
            'type'         => 'select',
            'options'      => [
                'claude-sonnet-4.5',
                'claude-sonnet-4.5-1m',
                'claude-sonnet-4.5-thinking',
                'deepseek-v4-flash',
                'minimax-m2.5',
            ],
            'placeholder'  => 'Pilih model default',
        ],
    ];

    public function index(): View
    {
        // Ambil nilai saat ini untuk setiap setting (dekripsi otomatis)
        $settings = collect(self::MANAGED_SETTINGS)->map(function (array $def) {
            $raw = Setting::get($def['key']);

            return array_merge($def, [
                // Untuk password/API key yang terenkripsi, jangan tampilkan nilai asli —
                // hanya tampilkan placeholder bintang jika sudah ada nilai
                'current_value' => ($def['is_encrypted'] && $raw)
                    ? str_repeat('•', 24)
                    : $raw,
                'has_value' => ! empty($raw),
            ]);
        });

        return view('admin.settings.index', [
            'settings'     => $settings,
            'managedDefs'  => self::MANAGED_SETTINGS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (self::MANAGED_SETTINGS as $def) {
            if ($def['type'] === 'select') {
                $options   = implode(',', $def['options']);
                $rules[$def['key']] = "nullable|in:{$options}";
            } else {
                $rules[$def['key']] = 'nullable|string|max:1000';
            }
        }

        $validated = $request->validate($rules);

        foreach (self::MANAGED_SETTINGS as $def) {
            $key   = $def['key'];
            $value = $validated[$key] ?? null;

            // Jika field password dikosongkan, jangan overwrite nilai yang ada
            if ($def['is_encrypted'] && empty($value)) {
                continue;
            }

            // Update label & group sekaligus
            $setting = Setting::firstOrCreate(['key' => $key], [
                'label' => $def['label'],
                'group' => $def['group'],
            ]);

            $setting->label = $def['label'];
            $setting->group = $def['group'];

            if (! empty($value)) {
                Setting::set($key, $value, $def['is_encrypted']);
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    /** Test koneksi AI via AJAX */
    public function testAi(Request $request): JsonResponse
    {
        try {
            $ai       = new AIService();
            $prompt   = $request->input('prompt', 'Halo! Balas dengan satu kalimat singkat.');
            $result   = $ai->ask($prompt);

            return response()->json([
                'success'    => true,
                'content'    => $result['content'],
                'model_used' => $result['model_used'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }
}
