<?php $__env->startSection('title', 'Activity Logs'); ?>
<?php $__env->startSection('page_title', 'Activity Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h4 mb-0">Activity logs</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <form method="GET" action="<?php echo e(route('activity.index')); ?>" class="row g-2">
            <?php if($organizations->isNotEmpty()): ?>
                <div class="col-sm-4 col-md-3">
                    <select name="organization" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All organizations</option>
                        <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($org->id); ?>" <?php echo e(request('organization') == $org->id ? 'selected' : ''); ?>><?php echo e($org->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-sm-4 col-md-3">
                <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All actions</option>
                    <?php $__currentLoopData = ['user.login', 'user.logout', 'user.registered', 'user.created', 'user.invited', 'user.removed', 'user.role_changed', 'organization.created', 'organization.updated', 'organization.deleted', 'project.created', 'project.updated', 'project.deleted', 'task.created', 'task.updated', 'task.deleted', 'profile.updated']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($action); ?>" <?php echo e(request('action') === $action ? 'selected' : ''); ?>><?php echo e($action); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Description</th>
                    <th>User</th>
                    <th>Organization</th>
                    <th>IP</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <span class="badge <?php echo e(str_starts_with($log->action, 'user.') ? 'bg-info' : (str_starts_with($log->action, 'organization.') ? 'bg-danger' : (str_starts_with($log->action, 'project.') ? 'bg-primary' : 'bg-success'))); ?> bg-opacity-75">
                                <?php echo e($log->action); ?>

                            </span>
                        </td>
                        <td class="small"><?php echo e($log->description ?? '—'); ?></td>
                        <td class="small"><?php echo e($log->user?->name ?? 'System'); ?></td>
                        <td class="small"><?php echo e($log->organization?->name ?? 'Platform'); ?></td>
                        <td class="text-muted small"><?php echo e($log->ip_address ?? '—'); ?></td>
                        <td class="text-muted small" title="<?php echo e($log->created_at->format('M j, Y H:i:s')); ?>"><?php echo e($log->created_at->diffForHumans()); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No activity recorded</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <?php echo e($logs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/activity/index.blade.php ENDPATH**/ ?>