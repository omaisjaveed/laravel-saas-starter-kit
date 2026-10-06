<?php $__env->startSection('title', 'Edit Member'); ?>
<?php $__env->startSection('page_title', $organization->name.' — Edit member'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <i class="bi bi-person-gear me-1"></i> Edit member
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('organizations.users.update', [$organization, $member->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" value="<?php echo e(old('name', $member->name)); ?>"
                               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" value="<?php echo e($member->email); ?>" class="form-control" disabled>
                        <div class="form-text">Email cannot be changed here.</div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                <?php echo e($member->pivot->role === 'owner' ? 'disabled' : ''); ?>>
                            <option value="member" <?php echo e(old('role', $member->pivot->role) === 'member' ? 'selected' : ''); ?>>Member</option>
                            <option value="manager" <?php echo e(old('role', $member->pivot->role) === 'manager' ? 'selected' : ''); ?>>Manager</option>
                            <option value="admin" <?php echo e(old('role', $member->pivot->role) === 'admin' ? 'selected' : ''); ?>>Admin</option>
                            <?php if($member->pivot->role === 'owner'): ?>
                                <option value="owner" selected>Owner</option>
                            <?php endif; ?>
                        </select>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php if($member->pivot->role === 'owner'): ?>
                            <div class="form-text">The owner role cannot be changed.</div>
                        <?php endif; ?>
                    </div>

                    <?php if($member->pivot->role === 'owner'): ?>
                        <input type="hidden" name="role" value="owner">
                        <div class="alert alert-warning small">
                            <i class="bi bi-shield-lock me-1"></i> This member is the owner. The owner role cannot be changed.
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" <?php if($member->pivot->role === 'owner'): ?> disabled <?php endif; ?>>
                            <i class="bi bi-check-lg me-1"></i> Save changes
                        </button>
                        <a href="<?php echo e(route('organizations.users.index', $organization)); ?>" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/organizations/edit-member.blade.php ENDPATH**/ ?>