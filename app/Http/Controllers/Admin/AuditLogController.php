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
        
        // Build query
        $query = Activity::with(['causer.role', 'subject'])
            ->latest();

        // Search filter (user atau description)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('causer', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Action filter
        if ($action = $request->input('action')) {
            $query->where('event', $action);
        }

        // Role filter
        if ($roleId = $request->input('role_id')) {
            $query->whereHas('causer', function($q) use ($roleId) {
                $q->where('role_id', $roleId);
            });
        }

        // Get results (limit 100 untuk performa)
        $activities = $query->take(100)->get();

        // Transform to match view format
        $auditLogs = $activities->map(function($activity) {
            return [
                'id' => $activity->id,
                'action' => $activity->event,
                'role' => $activity->causer?->role?->name ?? 'System',
                'role_id' => $activity->causer?->role_id,
                'user' => $activity->causer?->name ?? 'System',
                'description' => $this->formatDescription($activity),
                'created_at' => $activity->created_at->toDateTimeString(),
            ];
        });

        // Count active filters
        $activeFiltersCount = collect(['search', 'action', 'role_id'])
            ->filter(fn($key) => $request->filled($key))
            ->count();

        // Build filter options
        $filterOptions = [
        [
            'name' => 'action',
            'placeholder' => 'Semua Aksi',
            'options' => collect([
                ['value' => '', 'label' => 'Semua Aksi'],
                ['value' => 'created', 'label' => 'CREATE'],
                ['value' => 'updated', 'label' => 'UPDATE'],
                ['value' => 'deleted', 'label' => 'DELETE'],
            ])
        ],
        [
            'name' => 'role_id',
            'placeholder' => 'Semua Role',
            'options' => collect([
                ['value' => '', 'label' => 'Semua Role']
                ])->merge(
                    $roles->map(fn($role) => [
                        'value' => (string) $role->id,
                        'label' => $role->name
                    ])
                )
            ]
        ];

        return view('admin.audit-log', compact(
            'roles',
            'auditLogs',
            'filterOptions',
            'activeFiltersCount'
        ));
    }

    /**
     * Format activity description untuk display
     */
    private function formatDescription(Activity $activity): string
    {
        $subject = $activity->subject;
        $causer = $activity->causer;
        $event = $activity->event;

        // Format berdasarkan subject type
        if ($activity->subject_type === 'App\Models\Batch') {
            $batchTitle = $activity->properties->get('attributes')['title'] ?? 
                         $activity->properties->get('old')['title'] ?? 
                         $subject?->title ?? 
                         'Unknown Batch';
            
            return match($event) {
                'created' => "Membuat batch baru: {$batchTitle}",
                'updated' => $this->getBatchUpdateDescription($activity),
                'deleted' => "Menghapus batch: {$batchTitle}",
                default => "{$event} batch: {$batchTitle}"
            };
        }

        if ($activity->subject_type === 'App\Models\User') {
            $userName = $activity->properties->get('attributes')['name'] ?? 
                       $activity->properties->get('old')['name'] ?? 
                       $subject?->name ?? 
                       'Unknown User';
            
            return match($event) {
                'created' => "Membuat user baru: {$userName}",
                'updated' => "Mengubah data user: {$userName}",
                'deleted' => "Menghapus user: {$userName}",
                default => "{$event} user: {$userName}"
            };
        }

        if ($activity->subject_type === 'App\Models\BatchParticipant') {
            return match($event) {
                'created' => "Menyetujui pendaftaran peserta",
                'updated' => "Mengubah status pendaftaran peserta",
                default => "{$event} participant"
            };
        }

        // Default description
        return $activity->description ?? ucfirst($event) . ' ' . class_basename($activity->subject_type ?? 'item');
    }

    /**
     * Get specific description untuk batch update
     */
    private function getBatchUpdateDescription(Activity $activity): string
    {
        $changes = $activity->properties->get('attributes', []);
        $old = $activity->properties->get('old', []);

        if (isset($changes['status']) && isset($old['status'])) {
            return "Mengubah status batch dari {$old['status']} menjadi {$changes['status']}";
        }

        if (isset($changes['title'])) {
            return "Update batch: {$changes['title']}";
        }

        return "Mengubah data batch";
    }
}