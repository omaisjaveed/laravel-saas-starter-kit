<?php $__env->startSection('title', 'Notifications'); ?>
<?php $__env->startSection('page_title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h4 mb-0">Notifications</h1>
    <?php if($unreadCount): ?>
        <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i> Mark all as read
            </button>
        </form>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="d-flex align-items-start gap-3 border-bottom px-3 py-3 <?php echo e($notification->read_at ? '' : 'bg-primary bg-opacity-10'); ?>">
                <span class="stat-icon <?php echo e($notification->read_at ? 'bg-light text-secondary' : 'bg-primary bg-opacity-25 text-primary'); ?>"
                      style="width:40px;height:40px;font-size:1.1rem;">
                    <?php switch($notification->data['type'] ?? ''):
                        case ('invitation'): ?><i class="bi bi-envelope-plus"></i><?php break; ?>
                        <?php case ('removal'): ?><i class="bi bi-envelope-x"></i><?php break; ?>
                        <?php case ('role_changed'): ?><i class="bi bi-shield-check"></i><?php break; ?>
                        <?php default: ?><i class="bi bi-bell"></i>
                    <?php endswitch; ?>
                </span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong class="small"><?php echo e($notification->data['title'] ?? 'Notification'); ?></strong>
                        <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                    </div>
                    <p class="small text-muted mb-1"><?php echo e($notification->data['message'] ?? ''); ?></p>
                    <?php if(! $notification->read_at): ?>
                        <form method="POST" action="<?php echo e(route('notifications.read', $notification->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-outline-primary py-0">Mark as read</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-muted p-5">
                <i class="bi bi-bell-slash" style="font-size: 2.5rem;"></i>
                <p class="mt-3 mb-0">No notifications yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <?php echo e($notifications->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/notifications/index.blade.php ENDPATH**/ ?>