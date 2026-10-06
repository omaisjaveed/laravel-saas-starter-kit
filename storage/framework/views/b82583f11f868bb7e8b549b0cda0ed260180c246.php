<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php if(! $organization): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-5">
            <i class="bi bi-building" style="font-size: 3rem; color: #3b82f6;"></i>
            <h2 class="h4 mt-3">Welcome, <?php echo e(auth()->user()->name); ?>!</h2>
            <p class="text-muted">You are not a member of any organization yet.<br>Create your own organization or ask an owner to invite you.</p>
            <a href="<?php echo e(route('organizations.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create organization
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0"><?php echo e($organization->name); ?></h1>
            <span class="text-muted small">Organization dashboard</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('organizations.show', $organization)); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-building me-1"></i> Organization profile
            </a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', [\App\Models\Project::class, $organization])): ?>
                <a href="<?php echo e(route('organizations.projects.create', $organization)); ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> New project
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></span>
                    <div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['members']); ?></div>
                        <div class="text-muted small">Members</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-kanban"></i></span>
                    <div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['projects']); ?></div>
                        <div class="text-muted small">Projects</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-list-check"></i></span>
                    <div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['tasks']); ?></div>
                        <div class="text-muted small">Tasks</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-check2-all"></i></span>
                    <div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['tasks_done']); ?></div>
                        <div class="text-muted small">Tasks done</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><i class="bi bi-pie-chart me-1"></i> Task status</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span><span class="badge bg-secondary me-2">To do</span></span>
                        <strong><?php echo e($stats['tasks_todo']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span><span class="badge bg-primary me-2">In progress</span></span>
                        <strong><?php echo e($stats['tasks_in_progress']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span><span class="badge bg-success me-2">Done</span></span>
                        <strong><?php echo e($stats['tasks_done']); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><i class="bi bi-person-check me-1"></i> My open tasks</div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $myTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('organizations.projects.tasks.show', [$organization, $task->project_id, $task->id])); ?>"
                           class="d-block text-decoration-none text-reset border-bottom px-3 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold"><?php echo e(\Illuminate\Support\Str::limit($task->title, 40)); ?></span>
                                <span class="badge bg-light text-dark border"><?php echo e($task->project->name); ?></span>
                            </div>
                            <small class="text-muted">
                                <?php if($task->due_date): ?> Due <?php echo e($task->due_date->format('M j, Y')); ?> <?php else: ?> No due date <?php endif; ?>
                            </small>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-3 text-muted small text-center">No open tasks assigned to you</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><i class="bi bi-kanban me-1"></i> Recent projects</div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $recentProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('organizations.projects.show', [$organization, $project])); ?>"
                           class="d-block text-decoration-none text-reset border-bottom px-3 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold"><?php echo e($project->name); ?></span>
                                <span class="badge bg-light text-dark border"><?php echo e($project->tasks_count); ?> tasks</span>
                            </div>
                            <small class="text-muted">
                                <span class="badge <?php echo e($project->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e(ucfirst($project->status)); ?></span>
                            </small>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-3 text-muted small text-center">No projects yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom"><i class="bi bi-clock-history me-1"></i> Recent activity</div>
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
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/dashboard/index.blade.php ENDPATH**/ ?>