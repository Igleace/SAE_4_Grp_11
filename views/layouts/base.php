<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <title><?php echo $title ?? 'Accueil'; ?></title>

    <link rel="stylesheet" href="/assets/css/general_style.css">
    <link rel="stylesheet" href="/assets/css/header_style.css">
    <link rel="stylesheet" href="/assets/css/footer_style.css">
    <link rel="stylesheet" href="/assets/css/bubble.css">
    <?php if (isset($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="/assets/css/<?php echo $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="body_margin">
    <?php require_once __DIR__ . '/header.php'; ?>
    <div id="page-container">
        <?php include $view; ?>
    </div>
    <?php require_once __DIR__ . '/footer.php'; ?>

    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="/assets/js/<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>