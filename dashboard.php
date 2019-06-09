<?php
require_once 'includes/all.php';
if (!is_logged_in()) {
  header("Location: index.php");
  exit();
}
$db = connect_db();
$user_id = get_logged_in_user_id();
$username = $_GET["username"]; // username
// Get user info
$stmt = $db->prepare("SELECT c.id, c.name, c.username, c.profile FROM Cats c WHERE c.username = :username");
$stmt->bindValue("username", $username);
$stmt->execute();
$cat = $stmt->fetch();
if (!$cat) {
  not_found();
}
// get friends
$stmt = $db->prepare("SELECT c.username FROM Friends f JOIN Cats c ON f.friend_id = c.id WHERE f.cat_id = :id");
$stmt->bindValue("id", $cat['id']);
$stmt->execute();
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get Selfies
$stmt = $db->prepare("SELECT s.id, s.likes, s.caption, s.filename, UNIX_TIMESTAMP(s.date_uploaded) as date_uploaded, u.username
  FROM Cats c LEFT JOIN Friends f ON f.cat_id = c.id LEFT JOIN Cats u ON u.id = f.friend_id LEFT JOIN Selfies s ON 
  s.cat_id = f.friend_id WHERE c.username = :username ORDER BY s.date_uploaded;");
$stmt->bindValue("username", $username);
$stmt->execute();
$selfies = $stmt->fetchAll(PDO::FETCH_ASSOC);
  #SELECT f.friend_id FROM Friends f WHERE f.cat_id = :profile_id;
    #INSERT INTO Friends (cat_id, friend_id) VALUES (:current_id, :profile_id);
function profile_link($username) {
  return '<a href="'. e(get_profile_url(e($username))) .'">'. e($username) .'</a>';
}
?>
<!doctype htmL>
<html>
  <head>
    <title><?php echo e($cat['name']); ?>'s Dashboard | Kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="styles/kitstagram.css">
  </head>

  <body>
    <header>
      <h1><?php echo e($cat['name']); ?>'s Dashboard | Kitstagram</h1>
    </header>

    <main>

      <section class="dashboard-section">
        <h2>Selfies</h2>

        <?php foreach ($selfies as $selfie) { ?>
          <div class="selfie-small">
            <a href="<?= get_selfie_url($selfie['id']) ?>">
              <figure>
                <img src="./uploads/<?= e($selfie['filename']); ?>">
              </figure>
            </a>

            <p><?php echo e($selfie['caption']); ?></p>
            <p><?php echo profile_link($selfie['username']); ?></p>
            <p><?php echo e($selfie['likes']); ?> likes </p>
          </div>
        <?php } ?>
      </section>
    </main>
  </body>
</html>
