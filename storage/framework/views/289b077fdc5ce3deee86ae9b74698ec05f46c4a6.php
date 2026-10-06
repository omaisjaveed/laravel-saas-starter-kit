<?php $__env->startSection('title', 'Organizations'); ?>
<?php $__env->startSection('page_title', 'My Organizations'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0">Organizations</h1>
    <a href="<?php echo e(route('organizations.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create organization
    </a>
</div>

<div class="row g-3">
    <?php $__empty_1 = true; $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php if($organization->logo_path): ?>
                            <img src="<?php echo e(asset(\Illuminate\Support\Facades\Storage::url($organization->logo_path))); ?>"
                                 class="org-logo-lg" style="width:56px;height:56px;" alt="<?php echo e($organization->name); ?>">
                        <?php else: ?>
                            <span class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i></span>
                        <?php endif; ?>
                        <div>
                            <h2 class="h6 mb-0"><?php echo e($organization->name); ?></h2>
                            <span class="badge bg-light text-dark border"><?php echo e($organization->pivot->role ?? '—'); ?></span>
                        </div>
                    </div>

                    <p class="text-muted small mb-3"><?php echo e(\Illuminate\Support\Str::limit($organization->description ?? 'No description.', 90)); ?></p>

                    <div class="d-flex gap-3 small text-muted mb-3">
                        <span><i class="bi bi-people me-1"></i><?php echo e($organization->users_count); ?> members</span>
                        <span><i class="bi bi-kanban me-1"></i><?php echo e($organization->projects_count); ?> projects</span>
                        <span><i class="bi bi-list-check me-1"></i><?php echo e($organization->tasks_count); ?> tasks</span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('organizations.show', $organization)); ?>" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-eye me-1"></i> Open
                        </a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $organization)): ?>
                            <a href="<?php echo e(route('organizations.edit', $organization)); ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-gear"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <i class="bi bi-building" style="font-size: 3rem; color: #94a3b8;"></i>
                    <p class="text-muted mt-3 mb-0">No organizations yet. Create your first one.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="mt-4">
    <?php echo e($organizations->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/organizations/index.blade.php ENDPATH**/ ?>