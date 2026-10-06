<?php $__env->startSection('title', 'Task'); ?>
<?php $__env->startSection('page_title', $organization->name.' — Task'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span><i class="bi bi-list-check me-1"></i> Task details</span>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('organizations.projects.show', [$organization, $project])); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Project
                    </a>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $task)): ?>
                        <a href="<?php echo e(route('organizations.projects.tasks.edit', [$organization, $project, $task])); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h1 class="h5 mb-0 me-auto"><?php echo e($task->title); ?></h1>
                    <span class="badge <?php echo e(['todo' => 'bg-secondary', 'in_progress' => 'bg-primary', 'done' => 'bg-success'][$task->status]); ?>">
                        <?php echo e(['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done'][$task->status]); ?>

                    </span>
                    <span class="badge <?php echo e(['low' => 'bg-light text-dark border', 'medium' => 'bg-warning', 'high' => 'bg-danger'][$task->priority]); ?>">
                        <?php echo e(ucfirst($task->priority)); ?> priority
                    </span>
                </div>

                <p class="text-muted"><?php echo e($task->description ?? 'No description.'); ?></p>

                <hr>

                <div class="row small">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Project</span>
                            <a href="<?php echo e(route('organizations.projects.show', [$organization, $project])); ?>" class="text-decoration-none"><?php echo e($project->name); ?></a>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Assigned to</span>
                            <span><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Due date</span>
                            <span><?php echo e($task->due_date?->format('M j, Y') ?? '—'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Created by</span>
                            <span><?php echo e($task->creator?->name ?? '—'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Created</span>
                            <span><?php echo e($task->created_at->format('M j, Y H:i')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Updated</span>
                            <span><?php echo e($task->updated_at->format('M j, Y H:i')); ?></span>
                        </div>
                    </div>
                </div>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $task)): ?>
                    <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                        <form method="POST" action="<?php echo e(route('organizations.projects.tasks.destroy', [$organization, $project, $task])); ?>"
                              onsubmit="return confirm('Delete task <?php echo e($task->title); ?>?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Delete task
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/tasks/show.blade.php ENDPATH**/ ?>