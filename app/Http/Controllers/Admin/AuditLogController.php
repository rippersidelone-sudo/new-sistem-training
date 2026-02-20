<?php
// app/Http/Controllers/Admin/AuditLogController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();

        // Build query — SELALU exclude event null agar tidak ada baris kosong
        $query = Activity::with(['causer.role', 'causer.branch', 'subject'])
            ->whereNotNull('event')        // ← FIX UTAMA: buang record tanpa event
            ->whereIn('event', ['created', 'updated', 'deleted']) // hanya event valid
            ->latest();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Action filter
        if ($action = $request->input('action')) {
            $query->where('event', $action);
        }

        // Role filter
        if ($roleId = $request->input('role_id')) {
            $query->whereHas('causer', function ($q) use ($roleId) {
                $q->where('role_id', $roleId);
            });
        }

        // Subject type filter
        if ($subjectType = $request->input('subject_type')) {
            $query->where('subject_type', 'like', "%{$subjectType}%");
        }

        // Date range filter
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $activities = $query->paginate(20)->withQueryString();

        // Transform
        $auditLogs = $activities->getCollection()->map(function ($activity) {
            $causer      = $activity->causer;
            $subject     = $activity->subject;
            $props       = $activity->properties;
            $attributes  = $props->get('attributes', []);
            $old         = $props->get('old', []);

            $lastSyncedAt = null;
            if ($subject && isset($subject->last_synced_at)) {
                $lastSyncedAt = $subject->last_synced_at;
            }

            return [
                'id'             => $activity->id,
                'event'          => $activity->event,
                'log_name'       => $activity->log_name,
                'subject_type'   => $activity->subject_type
                    ? class_basename($activity->subject_type)
                    : '-',
                'subject_id'     => $activity->subject_id,
                'subject_label'  => $this->getSubjectLabel($activity),
                'role'           => $causer?->role?->name ?? 'System',
                'user_name'      => $causer?->name ?? 'System',
                'user_email'     => $causer?->email ?? '-',
                'user_branch'    => $causer?->branch?->name ?? '-',
                'description'    => $this->formatDescription($activity),
                'changes'        => $this->formatChanges($attributes, $old),
                'changed_fields' => array_keys(array_diff_assoc($attributes, $old)),
                'last_synced_at' => $lastSyncedAt
                    ? \Carbon\Carbon::parse($lastSyncedAt)->format('d M Y, H:i')
                    : '-',
                'created_at'     => $activity->created_at->format('d M Y, H:i'),
                'updated_at'     => $activity->updated_at->format('d M Y, H:i'),
                'batch_uuid'     => $activity->batch_uuid,
            ];
        });

        $activities->setCollection($auditLogs);

        // Count active filters
        $activeFiltersCount = collect(['search', 'action', 'role_id', 'subject_type', 'date_from', 'date_to'])
            ->filter(fn($key) => $request->filled($key))
            ->count();

        $filterOptions = [
            [
                'name'        => 'action',
                'placeholder' => 'Semua Aksi',
                'options'     => collect([
                    ['value' => '', 'label' => 'Semua Aksi'],
                    ['value' => 'created', 'label' => 'CREATE'],
                    ['value' => 'updated', 'label' => 'UPDATE'],
                    ['value' => 'deleted', 'label' => 'DELETE'],
                ]),
            ],
            [
                'name'        => 'subject_type',
                'placeholder' => 'Semua Modul',
                'options'     => collect([
                    ['value' => '', 'label' => 'Semua Modul'],
                    ['value' => 'Batch', 'label' => 'Batch'],
                    ['value' => 'User', 'label' => 'User'],
                    ['value' => 'BatchParticipant', 'label' => 'Participant'],
                    ['value' => 'Category', 'label' => 'Category'],
                ]),
            ],
            [
                'name'        => 'role_id',
                'placeholder' => 'Semua Role',
                'options'     => collect([['value' => '', 'label' => 'Semua Role']])->merge(
                    $roles->map(fn($role) => [
                        'value' => (string) $role->id,
                        'label' => $role->name,
                    ])
                ),
            ],
        ];

        return view('admin.audit-log', compact(
            'roles',
            'activities',
            'filterOptions',
            'activeFiltersCount'
        ));
    }

    private function getSubjectLabel(Activity $activity): string
    {
        $props      = $activity->properties;
        $attributes = $props->get('attributes', []);
        $old        = $props->get('old', []);
        $subject    = $activity->subject;

        $name = $attributes['title']
            ?? $attributes['name']
            ?? $old['title']
            ?? $old['name']
            ?? $subject?->title
            ?? $subject?->name
            ?? null;

        return $name
            ? ($name . ($activity->subject_id ? " (#{$activity->subject_id})" : ''))
            : ($activity->subject_id ? "#{$activity->subject_id}" : '-');
    }

    private function formatDescription(Activity $activity): string
    {
        $subject = $activity->subject;
        $event   = $activity->event ?? 'unknown';

        if ($activity->subject_type === 'App\Models\Batch') {
            $batchTitle = $activity->properties->get('attributes')['title']
                ?? $activity->properties->get('old')['title']
                ?? $subject?->title
                ?? 'Unknown Batch';

            return match ($event) {
                'created' => "Membuat batch baru: {$batchTitle}",
                'updated' => $this->getBatchUpdateDescription($activity),
                'deleted' => "Menghapus batch: {$batchTitle}",
                default   => ucfirst($event) . " batch: {$batchTitle}",
            };
        }

        if ($activity->subject_type === 'App\Models\User') {
            $userName = $activity->properties->get('attributes')['name']
                ?? $activity->properties->get('old')['name']
                ?? $subject?->name
                ?? 'Unknown User';

            return match ($event) {
                'created' => "Membuat user baru: {$userName}",
                'updated' => "Mengubah data user: {$userName}",
                'deleted' => "Menghapus user: {$userName}",
                default   => ucfirst($event) . " user: {$userName}",
            };
        }

        if ($activity->subject_type === 'App\Models\BatchParticipant') {
            return match ($event) {
                'created' => "Menyetujui pendaftaran peserta",
                'updated' => "Mengubah status pendaftaran peserta",
                'deleted' => "Menghapus data peserta",
                default   => ucfirst($event) . " participant",
            };
        }

        // Fallback untuk subject type lain
        $subjectName = class_basename($activity->subject_type ?? 'item');
        return $activity->description
            ?? ucfirst($event) . ' ' . strtolower($subjectName);
    }

    private function getBatchUpdateDescription(Activity $activity): string
    {
        $changes = $activity->properties->get('attributes', []);
        $old     = $activity->properties->get('old', []);

        if (isset($changes['status'], $old['status'])) {
            return "Mengubah status batch dari {$old['status']} menjadi {$changes['status']}";
        }

        if (isset($changes['title'])) {
            return "Update batch: {$changes['title']}";
        }

        return "Mengubah data batch";
    }

    private function formatChanges(array $attributes, array $old): array
    {
        if (empty($old)) {
            return [];
        }

        $changes = [];
        $skip    = ['updated_at', 'created_at', 'deleted_at', 'password', 'remember_token'];

        foreach ($old as $field => $oldValue) {
            if (in_array($field, $skip)) {
                continue;
            }

            $newValue = $attributes[$field] ?? null;

            if ($oldValue !== $newValue) {
                $changes[] = [
                    'field' => str_replace('_', ' ', ucfirst($field)),
                    'old'   => $this->formatValue($oldValue),
                    'new'   => $this->formatValue($newValue),
                ];
            }
        }

        return $changes;
    }

    private function formatValue(mixed $value): string
    {
        if (is_null($value)) {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }
        if (is_array($value)) {
            return implode(', ', $value);
        }
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            try {
                return \Carbon\Carbon::parse($value)->format('d M Y, H:i');
            } catch (\Throwable) {
                // fall through
            }
        }
        return (string) $value;
    }
}