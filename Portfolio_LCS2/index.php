<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portfolio CHARGHINOFF</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <header class="top-bar">
    <div class="logo">
      <a href="index.php">Portfolio CHARGHINOFF</a>
    </div>
    <div class="connexion-button">
      <?php if(isset($_SESSION['user'])): ?>
        <a href="connexion.php?action=logout">Déconnexion</a>
      <?php else: ?>
        <a href="connexion.php">Connexion</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="home">
    <div class="cards">
      <?php if(!isset($_SESSION['user'])): ?>
        <a href="connexion.php" class="card">Connexion / Inscription</a>
      <?php endif; ?>
      <?php if(isset($_SESSION['user'])): ?>
        <a href="moi.php" class="card">À propos de moi</a>
      <?php endif; ?>
      <a href="compétence.php" class="card">Compétences</a>
    </div>
  </main>

  <footer class="footer-menu" style="text-align: center;">
    <a href="index.php">Accueil</a>
  </footer>
</body>
</html>