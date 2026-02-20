<script>
// Global data untuk modal (hanya 1x render)
window.rolesData = @json($roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name]));
window.branchesData = @json($branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name]));
</script>