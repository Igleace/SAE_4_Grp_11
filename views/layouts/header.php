<link rel="shortcut icon" href="/admin/ressources/favicon.png" type="image/x-icon">

<?php
    @session_start();
    $isUserLoggedIn = isset($_SESSION['userid']);
    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ;
?>


<!-- HEADER -->
<header>
    <a id="accueil" href="/?page=home">
        <img src="/assets/logo.png" alt="Logo de l'ADIIL">
    </a>
    <nav>
        <ul>
            <li>
                <a href="/?page=events">Événements</a>
            </li>
            <li>
                <a href="/?page=news">Actualités</a>
            </li>
            <li>
                <a href="/?page=shop">Boutique</a>
            </li>
            <li>
                <a href="/?page=grade">Grades</a>
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="/?page=agenda">Agenda</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="/?page=about">À propos</a>
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="/?page=account">Mon compte</a>
                </li>

                <?php if ($isAdmin): ?>
                  <li>
                      <a id="header_admin" href="/admin/admin.php">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else: ?>
                <li>
                    <a href="/?page=login">Se connecter</a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>
</header>
