<?php

namespace App\Services\ExternalAPI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('external_api.base_url');
        $this->apiKey  = config('external_api.api_key');
        $this->timeout = config('external_api.timeout', 30);
    }

    // ============================================================
    // CORE HTTP METHODS
    // ============================================================

    /**
     * GET request ke API
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->retry(
                    config('external_api.retry_attempts', 3),
                    config('external_api.retry_delay', 1000)
                )
                ->get($this->buildUrl($endpoint), $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                    'status'  => $response->status(),
                ];
            }

            // Log error response dari API
            Log::warning('External API error response', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'status'  => $response->status(),
                'message' => $this->getErrorMessage($response->status()),
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('External API connection failed', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'status'  => 0,
                'message' => 'Tidak dapat terhubung ke API. Periksa koneksi internet Anda.',
            ];

        } catch (\Exception $e) {
            Log::error('External API unexpected error', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'status'  => 0,
                'message' => 'Terjadi kesalahan tidak terduga: ' . $e->getMessage(),
            ];
        }
    }

    // ============================================================
    // SPECIFIC ENDPOINT METHODS
    // ============================================================

    /**
     * Get all branches dari API
     */
    public function getBranches(): array
    {
        return $this->get(
            config('external_api.endpoints.branches')
        );
    }

    /**
     * Get all teachers dari API
     */
    public function getTeachers(): array
    {
        return $this->get(
            config('external_api.endpoints.teachers')
        );
    }

    /**
     * Get teachers by branch dari API
     */
    public function getTeachersByBranch(int $branchExternalId): array
    {
        $endpoint = str_replace(
            '{branch_id}',
            $branchExternalId,
            config('external_api.endpoints.branch_teachers')
        );

        return $this->get($endpoint);
    }

    /**
     * Get all skills dari API
     */
    public function getSkills(): array
    {
        return $this->get(
            config('external_api.endpoints.skills')
        );
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Build full URL dari endpoint
     */
    private function buildUrl(string $endpoint): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * Get headers untuk setiap request
     */
    private function getHeaders(): array
    {
        return [
            'X-API-Key'    => $this->apiKey,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get human-readable error message berdasarkan HTTP status
     */
    private function getErrorMessage(int $status): string
    {
        return match($status) {
            401 => 'API Key tidak valid atau tidak memiliki akses.',
            403 => 'Akses ditolak oleh API.',
            404 => 'Endpoint API tidak ditemukan.',
            422 => 'Data yang dikirim tidak valid.',
            429 => 'Terlalu banyak request. Coba lagi nanti.',
            500 => 'Server API sedang bermasalah. Coba lagi nanti.',
            503 => 'API sedang tidak tersedia. Coba lagi nanti.',
            default => "Terjadi kesalahan (HTTP {$status}).",
        };
    }

    /**
     * Test koneksi ke API
     * Digunakan untuk validasi API Key
     */
    public function testConnection(): array
    {
        $result = $this->getBranches();

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Koneksi ke API berhasil!',
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Koneksi ke API gagal.',
        ];
    }
}