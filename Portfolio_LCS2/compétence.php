<?php
//=============================================================================
// Fichier : compétence.php
//=============================================================================
session_start();
include 'config.php';  // $conn
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php'); exit;
}
$userId   = (int)$_SESSION['user_id'];
$userRole = $_SESSION['role'];
$message  = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['mode'] ?? '') {
        case 'add_skill':
            if ($userRole === 'admin') {
                $name = $conn->real_escape_string(trim($_POST['skill_name']));
                if ($name !== '') {
                    $conn->query("INSERT INTO skills (name) VALUES ('$name')");
                    $message = 'Compétence ajoutée.';
                }
            }
            break;
        case 'update_user_skill':
            $skillId = (int)$_POST['skill_id'];
            $level   = $conn->real_escape_string($_POST['level']);
            // Vérifier le niveau autorisé
            $valid = ['débutant','intermédiaire','avancé','expert'];
            if (in_array($level, $valid)) {
                // Upsert
                $stmt = $conn->prepare(
                    "INSERT INTO user_skills (user_id, skill_id, level)
                     VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE level = VALUES(level)"
                );
                $stmt->bind_param('iis', $userId, $skillId, $level);
                $stmt->execute();
                $message = 'Niveau mis à jour.';
            }
            break;
        case 'delete_skill':
            if ($userRole === 'admin') {
                $skillId = (int)$_POST['skill_id'];
                $conn->query("DELETE FROM skills WHERE id=$skillId");
                $message = 'Compétence supprimée.';
            }
            break;
    }
}

// Lecture des compétences
$skills = [];
$res = $conn->query(
    "SELECT s.id, s.name, us.level
     FROM skills s
     LEFT JOIN user_skills us
       ON s.id = us.skill_id AND us.user_id = $userId
     ORDER BY s.name"
);
while ($row = $res->fetch_assoc()) {
    $skills[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Mes compétences</title></head>
<body>
<h1>Compétences</h1>
<?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>

<?php if ($userRole === 'admin'): ?>
<h2>Ajouter une compétence</h2>
<form method="post">
    <input type="hidden" name="mode" value="add_skill">
    <label>Nom : <input type="text" name="skill_name" required></label>
    <button type="submit">Ajouter</button>
</form>
<?php endif; ?>

<table border="1">
    <tr><th>Compétence</th><th>Niveau</th><th>Action</th></tr>
    <?php foreach ($skills as $sk): ?>
    <tr>
      <td><?= htmlspecialchars($sk['name']) ?></td>
      <td>
        <form method="post" style="display:inline;">
          <input type="hidden" name="mode" value="update_user_skill">
          <input type="hidden" name="skill_id" value="<?= $sk['id'] ?>">
          <select name="level" onchange="this.form.submit()">
            <option value="">--</option>
            <?php foreach (['débutant','intermédiaire','avancé','expert'] as $lvl): ?>
              <option value="<?= $lvl ?>" <?= $sk['level']===$lvl?'selected':'' ?>><?= $lvl ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td>
        <?php if ($userRole === 'admin'): ?>
        <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer?');">
          <input type="hidden" name="mode" value="delete_skill">
          <input type="hidden" name="skill_id" value="<?= $sk['id'] ?>">
          <button type="submit">Supprimer</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="projets.php">Gérer mes projets</a></p>
</body>
</html>

<?php
//=============================================================================
// Fichier : projets.php
//=============================================================================
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }
$userId   = (int)$_SESSION['user_id'];
$userRole = $_SESSION['role'];
actions:
$action = $_GET['action'] ?? 'list';
$message = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['mode'] ?? '') {
        case 'add':
        case 'edit':
            $title       = $conn->real_escape_string(trim($_POST['title']));
            $description = $conn->real_escape_string(trim($_POST['description']));
            $link_raw    = trim($_POST['link']);
            $link        = filter_var($link_raw, FILTER_VALIDATE_URL) ? $conn->real_escape_string($link_raw) : '';
            // Upload image si présent
            $imageName = '';
            if (!empty($_FILES['image']['name'])) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
                    $imageName = uniqid('proj_') . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$imageName);
                }
            }
            if ($_POST['mode']==='add') {
                $stmt = $conn->prepare(
                    "INSERT INTO projects (user_id,title,description,image,link) VALUES (?,?,?,?,?)"
                );
                $stmt->bind_param('issss', $userId, $title, $description, $imageName, $link);
                $stmt->execute();
                $message = 'Projet ajouté.';
            } else {
                $pid = (int)$_POST['project_id'];
                // Vérifier droit
                if ($userRole==='admin' || $pid && $conn->query("SELECT user_id FROM projects WHERE id=$pid")->fetch_row()[0]==$userId) {
                    if ($imageName) {
                        $conn->query("UPDATE projects SET title='$title',description='$description',image='$imageName',link='$link' WHERE id=$pid");
                    } else {
                        $conn->query("UPDATE projects SET title='$title',description='$description',link='$link' WHERE id=$pid");
                    }
                    $message = 'Projet modifié.';
                }
            }
            break;
        case 'delete':
            $pid = (int)$_POST['project_id'];
            // Vérifier droit
            $row = $conn->query("SELECT user_id,image FROM projects WHERE id=$pid")->fetch_assoc();
            if ($row && ($userRole==='admin' || $row['user_id']==$userId)) {
                if ($row['image']) @unlink(__DIR__.'/uploads/'.$row['image']);
                $conn->query("DELETE FROM projects WHERE id=$pid");
                $message = 'Projet supprimé.';
            }
            break;
    }
}

// Lecture des projets
$projects = [];
$sql = ($userRole==='admin')
    ? "SELECT p.*, u.name FROM projects p JOIN users u ON p.user_id=u.id ORDER BY p.id DESC"
    : "SELECT * FROM projects WHERE user_id=$userId ORDER BY id DESC";
$res = $conn->query($sql);
while ($r = $res->fetch_assoc()) {
    $projects[] = $r;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Mes projets</title></head>
<body>
<h1>Projets</h1>
<?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
<p><a href="?action=add">Ajouter un projet</a> | <a href="compétence.php">Mes compétences</a></p>

<?php if ($action==='add' || $action==='edit'): 
    $edit = ($action==='edit' && isset($_GET['id']));
    $proj = ['title'=>'','description'=>'','link'=>'','image'=>''];
    if ($edit) {
        $id = (int)$_GET['id'];
        $proj = $conn->query("SELECT * FROM projects WHERE id=$id")->fetch_assoc();
    }
?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="<?= $edit?'edit':'add' ?>">
    <?php if ($edit): ?><input type="hidden" name="project_id" value="<?= $proj['id'] ?>"><?php endif; ?>
    <label>Titre : <input type="text" name="title" value="<?= htmlspecialchars($proj['title']) ?>" required></label><br>
    <label>Description :<br>
      <textarea name="description" rows="5"><?= htmlspecialchars($proj['description']) ?></textarea></label><br>
    <label>Lien : <input type="url" name="link" value="<?= htmlspecialchars($proj['link']) ?>"></label><br>
    <label>Image : <input type="file" name="image"></label>
    <?php if ($edit && $proj['image']): ?>
      <p>Image actuelle: <img src="uploads/<?= htmlspecialchars($proj['image']) ?>" width="100"></p>
    <?php endif; ?><br>
    <button type="submit"><?= $edit?'Modifier':'Ajouter' ?></button>
</form>

<?php else: ?>
<table border="1">
  <tr><th>ID</th><th>Titre</th><th>Lien</th><th>Image</th><th>Actions</th></tr>
  <?php foreach ($projects as $p): ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['title']) ?></td>
      <td><?php if ($p['link']): ?><a href="<?= htmlspecialchars($p['link']) ?>" target="_blank">Visiter</a><?php endif; ?></td>
      <td><?php if ($p['image']): ?><img src="uploads/<?= htmlspecialchars($p['image']) ?>" width="50"><?php endif; ?></td>
      <td>
        <a href="?action=edit&id=<?= $p['id'] ?>">✎</a>
        <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer?');">
          <input type="hidden" name="mode" value="delete">
          <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
          <button type="submit">🗑</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>
