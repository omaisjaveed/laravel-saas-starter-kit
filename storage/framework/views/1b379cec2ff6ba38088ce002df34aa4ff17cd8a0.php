<?php $__env->startSection('title', 'Members'); ?>
<?php $__env->startSection('page_title', $organization->name.' — Members'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h1 class="h5 mb-0">Organization members</h1>
        <span class="text-muted small"><?php echo e($members->total()); ?> members</span>
    </div>
    <?php if($canManageMembers): ?>
        <a href="<?php echo e(route('organizations.users.invite', $organization)); ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Invite user
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <form method="GET" action="<?php echo e(route('organizations.users.index', $organization)); ?>" class="row g-2">
            <div class="col-sm-6 col-md-4">
                <input type="text" name="q" value="<?php echo e($search); ?>" class="form-control form-control-sm" placeholder="Search by name or email...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                <?php if($search): ?>
                    <a href="<?php echo e(route('organizations.users.index', $organization)); ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-sm"><?php echo e(strtoupper(substr($member->name, 0, 1))); ?></span>
                                <a href="<?php echo e(route('users.show', $member)); ?>" class="text-decoration-none fw-semibold"><?php echo e($member->name); ?></a>
                            </div>
                        </td>
                        <td class="text-muted small"><?php echo e($member->email); ?></td>
                        <td>
                            <span class="badge bg-<?php echo e(['owner' => 'danger', 'admin' => 'warning', 'manager' => 'info', 'member' => 'secondary'][$member->pivot->role] ?? 'secondary'); ?>">
                                <?php echo e(ucfirst($member->pivot->role)); ?>

                            </span>
                        </td>
                        <td class="text-muted small"><?php echo e($member->pivot->created_at?->format('M j, Y')); ?></td>
                        <td class="text-end">
                            <?php if($canManageMembers): ?>
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('organizations.users.edit', [$organization, $member->id])); ?>"
                                       class="btn btn-sm btn-outline-secondary" title="Edit / change role">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if($member->pivot->role !== 'owner'): ?>
                                        <form method="POST" action="<?php echo e(route('organizations.users.destroy', [$organization, $member->id])); ?>"
                                              onsubmit="return confirm('Remove <?php echo e($member->name); ?> from the organization?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No members found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <?php echo e($members->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/organizations/members.blade.php ENDPATH**/ ?>