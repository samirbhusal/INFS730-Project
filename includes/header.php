<?php
/**
 * Shared Header
 * -------------
 * Set $pageTitle before including this file.
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Project730';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | Project730</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>
<body>
