<?php $__env->startSection('title', $organization->name); ?>
<?php $__env->startSection('page_title', $organization->name); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php if($organization->logo_path): ?>
                <img src="<?php echo e(asset(\Illuminate\Support\Facades\Storage::url($organization->logo_path))); ?>"
                     class="org-logo-lg" alt="<?php echo e($organization->name); ?>">
            <?php else: ?>
                <span class="stat-icon bg-primary bg-opacity-10 text-primary" style="width:96px;height:96px;font-size:2.5rem;border-radius:1rem;">
                    <i class="bi bi-building"></i>
                </span>
            <?php endif; ?>

            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h4 mb-0"><?php echo e($organization->name); ?></h1>
                    <span class="badge bg-light text-dark border"><?php echo e(auth()->user()->roleIn($organization) ?? 'guest'); ?></span>
                </div>
                <p class="text-muted small mb-1 mt-1">
                    <?php echo e($organization->description ?? 'No description.'); ?>

                </p>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <?php if($organization->email): ?><span><i class="bi bi-envelope me-1"></i><?php echo e($organization->email); ?></span><?php endif; ?>
                    <?php if($organization->website): ?><span><i class="bi bi-globe me-1"></i><a href="<?php echo e($organization->website); ?>" target="_blank" class="text-decoration-none"><?php echo e($organization->website); ?></a></span><?php endif; ?>
                    <span><i class="bi bi-hash me-1"></i><?php echo e($organization->slug); ?></span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $organization)): ?>
                    <a href="<?php echo e(route('organizations.edit', $organization)); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-gear me-1"></i> Settings
                    </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageMembers', $organization)): ?>
                    <a href="<?php echo e(route('organizations.users.index', $organization)); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-people me-1"></i> Members
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-primary"><?php echo e($stats['members']); ?></div>
                <div class="text-muted small">Members</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-success"><?php echo e($stats['projects']); ?></div>
                <div class="text-muted small">Projects</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-warning"><?php echo e($stats['tasks']); ?></div>
                <div class="text-muted small">Tasks</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-1"></i> Members</span>
                <a href="<?php echo e(route('organizations.users.index', $organization)); ?>" class="small">Manage</a>
            </div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('users.show', $member)); ?>" class="d-flex align-items-center gap-2 text-decoration-none text-reset border-bottom px-3 py-2">
                        <span class="avatar-sm"><?php echo e(strtoupper(substr($member->name, 0, 1))); ?></span>
                        <div class="flex-grow-1">
                            <div class="small fw-semibold"><?php echo e($member->name); ?></div>
                            <small class="text-muted"><?php echo e($member->email); ?></small>
                        </div>
                        <span class="badge bg-light text-dark border"><?php echo e($member->pivot->role); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-3 text-muted small text-center">No members</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-1"></i> Recent activity</span>
                <a href="<?php echo e(route('activity.index')); ?>" class="small">View all</a>
            </div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-start gap-2 border-bottom px-3 py-2">
                        <i class="bi bi-dot text-primary fs-4"></i>
                        <div>
                            <div class="small">
                                <strong><?php echo e($activity->user?->name ?? 'System'); ?></strong>
                                <?php echo e($activity->description ?? $activity->action); ?>

                            </div>
                            <small class="text-muted"><?php echo e($activity->created_at->diffForHumans()); ?></small>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-3 text-muted small text-center">No activity yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/organizations/show.blade.php ENDPATH**/ ?>