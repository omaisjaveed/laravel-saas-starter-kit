<?php $__env->startSection('title', 'Projects'); ?>
<?php $__env->startSection('page_title', $organization->name.' — Projects'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h4 mb-0">Projects</h1>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', [\App\Models\Project::class, $organization])): ?>
        <a href="<?php echo e(route('organizations.projects.create', $organization)); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New project
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <form method="GET" action="<?php echo e(route('organizations.projects.index', $organization)); ?>" class="row g-2">
            <div class="col-sm-6 col-md-4">
                <input type="text" name="q" value="<?php echo e($search); ?>" class="form-control form-control-sm" placeholder="Search projects...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                <?php if($search): ?>
                    <a href="<?php echo e(route('organizations.projects.index', $organization)); ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Tasks</th>
                    <th>Created by</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('organizations.projects.show', [$organization, $project])); ?>" class="text-decoration-none fw-semibold">
                                <?php echo e($project->name); ?>

                            </a>
                            <div class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($project->description ?? '', 60)); ?></div>
                        </td>
                        <td>
                            <span class="badge <?php echo e($project->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e(ucfirst($project->status)); ?></span>
                        </td>
                        <td><?php echo e($project->tasks_count); ?></td>
                        <td class="small"><?php echo e($project->creator?->name ?? '—'); ?></td>
                        <td class="text-muted small"><?php echo e($project->created_at->format('M j, Y')); ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="<?php echo e(route('organizations.projects.show', [$organization, $project])); ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $project)): ?>
                                    <a href="<?php echo e(route('organizations.projects.edit', [$organization, $project])); ?>" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $project)): ?>
                                    <form method="POST" action="<?php echo e(route('organizations.projects.destroy', [$organization, $project])); ?>"
                                          onsubmit="return confirm('Delete project <?php echo e($project->name); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No projects found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <?php echo e($projects->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/projects/index.blade.php ENDPATH**/ ?>