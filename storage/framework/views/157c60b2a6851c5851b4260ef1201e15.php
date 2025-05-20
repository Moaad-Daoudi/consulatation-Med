<?php
    $logoStyle = request()->routeIs('dashboard')
        ? 'width: 100px; height: auto;'  // smaller on dashboard
        : 'width: 200px; height: auto;'; // default size
?>

<img src="<?php echo e(asset('images/logo1.png')); ?>" alt="My Logo" style="<?php echo e($logoStyle); ?>" <?php echo e($attributes); ?>>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/components/application-logo.blade.php ENDPATH**/ ?>