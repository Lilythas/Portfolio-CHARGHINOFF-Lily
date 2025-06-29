<?php
session_start();
include 'config.php';

$action = $_GET['action'] ?? null;
if($action === 'logout'){
  session_destroy();
  header('Location: index.php');
  exit;
}

$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if($_POST['mode'] === 'login'){
    $email = $conn->real_escape_string($_POST['email']);
    $pwd = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows === 1){
      $user = $res->fetch_assoc();
      if(password_verify($pwd, $user['password'])){
        $_SESSION['user'] = $user;
        // Rediriger vers le menu principal
        header('Location: index.php');
        exit;
      } else {
        $message = 'Mot de passe incorrect';
      }
    } else {
      $message = 'Email introuvable';
    }
  } elseif($_POST['mode'] === 'register'){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pwdHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
    $stmt->bind_param('sss', $name, $email, $pwdHash);
    if($stmt->execute()){
      $message = 'Inscription réussie, connectez-vous.';
    } else {
      $message = 'Erreur : ' . $conn->error;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>connexion - Portfolio CHARGHINOFF</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="top-bar">
    <div class="logo"><a href="index.php">Portfolio CHARGHINOFF</a></div>
  </header>
  <main class="connexion-page">
    <?php if($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <div class="tabs">
      <button data-tab="login">Connexion</button>
      <button data-tab="register">Inscription</button>
    </div>
    <div class="tab-content">
      <form id="login" class="tab" method="post" style="display:none;">
        <input type="hidden" name="mode" value="login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
      </form>
      <form id="register" class="tab" method="post" style="display:none;">
        <input type="hidden" name="mode" value="register">
        <input type="text" name="name" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">S'inscrire</button>
      </form>
    </div>
  </main>
  <footer class="footer-menu">
    <a href="index.php">Accueil</a>
  </footer>
  <script>
    const tabs = document.querySelectorAll('.tabs button');
    const forms = document.querySelectorAll('.tab');
    tabs.forEach(btn => btn.addEventListener('click', () => {
      forms.forEach(f => f.style.display = 'none');
      document.getElementById(btn.dataset.tab).style.display = 'block';
    }));
    document.querySelector('.tabs button[data-tab="login"]').click();
  </script>
</body>
</html>
