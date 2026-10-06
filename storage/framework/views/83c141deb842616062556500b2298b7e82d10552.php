<?php $__env->startSection('title', $project->name); ?>
<?php $__env->startSection('page_title', $organization->name.' — '.$project->name); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h1 class="h4 mb-0"><?php echo e($project->name); ?></h1>
            <span class="badge <?php echo e($project->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e(ucfirst($project->status)); ?></span>
        </div>
        <p class="text-muted small mb-0 mt-1"><?php echo e($project->description ?? 'No description.'); ?></p>
        <small class="text-muted">Created by <?php echo e($project->creator?->name ?? '—'); ?> on <?php echo e($project->created_at->format('M j, Y')); ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('organizations.projects.index', $organization)); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> All projects
        </a>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', [\App\Models\Task::class, $organization])): ?>
            <a href="<?php echo e(route('organizations.projects.tasks.create', [$organization, $project])); ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New task
            </a>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $project)): ?>
            <a href="<?php echo e(route('organizations.projects.edit', [$organization, $project])); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-list-check me-1"></i> Tasks (<?php echo e($tasks->total()); ?>)</span>
        <form method="GET" action="<?php echo e(route('organizations.projects.show', [$organization, $project])); ?>" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="todo" <?php echo e(request('status') === 'todo' ? 'selected' : ''); ?>>To do</option>
                <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>In progress</option>
                <option value="done" <?php echo e(request('status') === 'done' ? 'selected' : ''); ?>>Done</option>
            </select>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assignee</th>
                    <th>Due date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('organizations.projects.tasks.show', [$organization, $project, $task])); ?>" class="text-decoration-none fw-semibold">
                                <?php echo e($task->title); ?>

                            </a>
                            <div class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($task->description ?? '', 60)); ?></div>
                        </td>
                        <td>
                            <span class="badge <?php echo e(['todo' => 'bg-secondary', 'in_progress' => 'bg-primary', 'done' => 'bg-success'][$task->status]); ?>">
                                <?php echo e(['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done'][$task->status]); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo e(['low' => 'bg-light text-dark border', 'medium' => 'bg-warning', 'high' => 'bg-danger'][$task->priority]); ?>">
                                <?php echo e(ucfirst($task->priority)); ?>

                            </span>
                        </td>
                        <td class="small"><?php echo e($task->assignee?->name ?? '—'); ?></td>
                        <td class="small <?php echo e($task->due_date && $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger fw-semibold' : 'text-muted'); ?>">
                            <?php echo e($task->due_date?->format('M j, Y') ?? '—'); ?>

                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="<?php echo e(route('organizations.projects.tasks.show', [$organization, $project, $task])); ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $task)): ?>
                                    <a href="<?php echo e(route('organizations.projects.tasks.edit', [$organization, $project, $task])); ?>" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $task)): ?>
                                    <form method="POST" action="<?php echo e(route('organizations.projects.tasks.destroy', [$organization, $project, $task])); ?>"
                                          onsubmit="return confirm('Delete task <?php echo e($task->title); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No tasks found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <?php echo e($tasks->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/projects/show.blade.php ENDPATH**/ ?>