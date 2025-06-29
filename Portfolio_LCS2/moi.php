<?php
session_start();
include 'config.php';
if(!isset($_SESSION['user'])){
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portfolio CHARGHINOFF - À propos de moi</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="top-bar">
    <div class="logo"><a href="index.php">Portfolio CHARGHINOFF</a></div>
    <div class="connexion-button">
      <?php if(isset($_SESSION['user'])): ?>
        <a href="connexion.php?action=logout">Déconnexion</a>
      <?php else: ?>
        <a href="connexion.php">Connexion</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="about">
    <h2>À propos de moi</h2>
    <section class="bio">
      <img src="profil.jpg" alt="Avatar de Lily Charghinoff">
      <h4>Je m'appelle Lily Charghinoff, née le 6 novembre 2006 à Saint-Maur-des-Fossés.</h4>
      <h4>Actuellement en bachelor en informatique à l'ESGI d'Aix-en-Provence.</h4>
      <h3>Diplômes :</h3>
      <ul>
        <li>Bachelor : En cours...</li>
        <li>Baccalauréat : Admise</li>
      </ul>
      <h3>Contact :</h3>
      <ul>
        <li>Email : <a href="mailto:lily.charghinoff@gmail.com">lily.charghinoff@gmail.com</a></li>
        <li>Téléphone : 06.32.40.35.89</li>
      </ul>
    </section>
  </main>

  <footer class="footer-menu">
    <a href="index.php">Accueil</a>
    <?php if(isset($_SESSION['user'])): ?>
      <a href="connexion.php?action=logout">Déconnexion</a>
    <?php endif; ?>
  </footer>
</body>
</html>
