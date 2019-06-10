<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
  header("Location: index.php");
  exit();
}

$db = connect_db();
$user_id = get_logged_in_user_id();

if (isset($_GET["username"])) {
  $username = $_GET["username"]; // username

  // Get user info
  $stmt = $db->prepare("SELECT c.id, c.name, c.username, c.profile FROM Cats c WHERE c.username = :username");
  $stmt->bindValue("username", $username);
  $stmt->execute();
  $cat = $stmt->fetch();

  if (!$cat) {
    not_found();
  }
} else {
  // Default to current user
  $stmt = $db->prepare("SELECT c.id, c.name, c.username, c.profile FROM Cats c WHERE c.id = :id");
  $stmt->bindValue("id", $user_id);
  $stmt->execute();
  $cat = $stmt->fetch();

  if (!$cat) {
    not_found();
  }
  $username = $cat['username'];
}

// get friends
$stmt = $db->prepare("SELECT c.id, c.username FROM Friends f JOIN Cats c ON f.friend_id = c.id WHERE f.cat_id = :id");
$stmt->bindValue("id", $cat['id']);
$stmt->execute();
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Are we friends with this cat?
$we_are_friends = 0;
foreach ($friends as $f) {
  if ($f['id'] === $user_id) {
    $we_are_friends = 1;
    break;
  }
}

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
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/kitstagram.css">
  </head>

  <body>
    <header>
      <h1>kitstagram &gt; <?php echo e($cat['name']); ?>'s Profile</h1>
      <?php include "includes/nav.php"; ?>
    </header>

    <main>

      <section class="profile-section">
        <h2>Bio</h2>
          <div>
            <p class="profile-paragraph"><?php echo e($cat['profile']); ?></p>
          </div>
      </section>

      <section class="profile-section">
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

      <section class="profile-section">
        <h2>Friends</h2>

        <?php if ($cat['id'] !== $user_id) { ?>
          <form action="friend.php" method="POST">
            <input type="hidden" name="username" value="<?= e($cat['username']) ?>">
            <input type="hidden" name="friend" value="<?= e(!$we_are_friends) ?>">
            <?php if ($we_are_friends) { ?>
              <button>Unfriend this cat</button>
            <?php } else { ?>
              <button>Friend this cat</button>
            <?php } ?>
          </form>
        <?php } ?>

        <?php foreach ($friends as $friend) { ?>
          <p><?= profile_link($friend['username']) ?></p>
        <?php } ?>
      </section>

    </main>
  </body>

</html>
