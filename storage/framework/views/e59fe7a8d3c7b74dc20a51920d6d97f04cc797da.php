<?php $__env->startSection('title', 'Verify email'); ?>

<?php $__env->startSection('content'); ?>
<h2 class="h5 mb-1">Verify your email</h2>
<p class="text-muted small mb-4">
    Thanks for signing up! Before getting started, could you verify your email address by clicking
    on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
</p>

<?php if(session('verification-link-sent')): ?>
    <div class="alert alert-success small">A new verification link has been sent to your email address.</div>
<?php endif; ?>

<div class="d-grid gap-2">
    <?php if(session('resend_available', true)): ?>
        <form method="POST" action="<?php echo e(route('verification.resend')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-envelope-arrow-up me-1"></i> Resend verification email
            </button>
        </form>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>