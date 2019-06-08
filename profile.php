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
$stmt = $db->prepare("SELECT s.id, s.likes, s.caption, s.filename, UNIX_TIMESTAMP(s.date_uploaded) as date_uploaded
  FROM Selfies s JOIN Cats c ON c.id = s.cat_id WHERE c.username = :username;
");
$stmt->bindValue("username", $username);
$stmt->execute();
$selfies = $stmt->fetchAll(PDO::FETCH_ASSOC);

  #SELECT f.friend_id FROM Friends f WHERE f.cat_id = :profile_id;
    #INSERT INTO Friends (cat_id, friend_id) VALUES (:current_id, :profile_id);


?>
<!doctype htmL>
<html>
  <head>
    <title><?php echo e($cat['name']); ?>'s Profile | Kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="styles/kitstagram.css">
  </head>

  <body>
    <header>
      <h1><?php echo e($cat['name']); ?>'s Profile | Kitstagram</h1>
    </header>

    <main>

      <section>
        <h2>My Selfies</h2>

        <?php foreach ($selfies as $selfie) { ?>
          <div class="selfie-small">
            <a href="<?= get_selfie_url($selfie['id']) ?>">
              <figure>
                <img src="./uploads/<?= e($selfie['filename']); ?>">
              </figure>
            </a>

            <p><?php echo e($selfie['caption']); ?></p>
            <p><?php echo e($selfie['likes']); ?> likes </p>
          </div>
        <?php } ?>
      </section>

      <section class="profile profile-friends">
        <h2>Friends</h2>

        <?php foreach ($friends as $friend) { ?>
          <p><?php echo e($friend['username']); ?></p>
        <?php } ?>
      </section>

      <section>
        <h2>Bio</h2>
        <div style="border-radius: 5px;">
          <p><?php echo e($cat['profile']); ?></p>
        </div>
      </section>

    </main>
  </body>

</html>
