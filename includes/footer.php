<?php
/**
 * Shared Footer
 * -------------
 * Optionally set $footerScripts (array of script paths) before including.
 */
?>
<?php if (!empty($footerScripts)): ?>
    <?php foreach ($footerScripts as $src): ?>
        <script src="<?php echo e($src); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
