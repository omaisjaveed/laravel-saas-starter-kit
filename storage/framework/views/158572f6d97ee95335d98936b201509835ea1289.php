<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Welcome'); ?> - <?php echo e(config('app.name')); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            min-height: 100vh;
        }
        .guest-card {
            max-width: 440px;
            width: 100%;
        }
        .guest-card .card { border: 0; border-radius: 1rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-4">
<div class="guest-card">
    <div class="text-center text-white mb-4">
        <i class="bi bi-boxes" style="font-size: 2.5rem;"></i>
        <h1 class="h4 mt-2 mb-0"><?php echo e(config('app.name')); ?></h1>
        <span class="small opacity-75">Laravel SaaS Starter Kit</span>
    </div>

    <div class="card shadow">
        <div class="card-body p-4">
            <?php echo $__env->make('partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel-saas\resources\views/layouts/guest.blade.php ENDPATH**/ ?>