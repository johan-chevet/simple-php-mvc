<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($tite) ? htmlspecialchars($title) . '-' . APP_NAME : APP_NAME  ?></title>
    <link rel="stylesheet" href="<?= BASE_URL . "/static/css/style.css" ?>">
    <?php if (isset($stylesheets)): ?>
        <?php foreach ($stylesheets as $style_path): ?>
            <link rel="stylesheet" href="<?= BASE_URL . "/static/css/" . $style_path ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <a href="<?php echo BASE_URL . '/'; ?>" title="retour à l'accueil">Accueil</a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo url('planning'); ?>">Planning</a></li>
                <li><a href="<?php echo url('reserve'); ?>">Réserver</a></li>
                <?php if (Core\SessionManager::is_logged()): ?>
                    <li><a href="<?php echo url('profile/update'); ?>">Profil</a></li>
                    <li><a href="<?php echo url('logout'); ?>">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="<?php echo url('login'); ?>">Connexion</a></li>
                    <li><a href="<?php echo url('register'); ?>">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <?= $content ?? '' ?>
</body>

</html>