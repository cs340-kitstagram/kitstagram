<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
  header("Location: signup.php");
  exit();
}

$errors = array();

$db = connect_db();

$id = $_GET["id"];

// Get Selfie information
$stmt = $db->prepare("SELECT id, likes, caption, filename, UNIX_TIMESTAMP(date_uploaded) as date_uploaded
  FROM Selfies WHERE id = :selfie_id");
$stmt->bindValue("selfie_id", $id);
$stmt->execute();

$selfie = $stmt->fetch();
if (!$selfie) {
  not_found();
}

// Get uploader information

$stmt = $db->prepare("SELECT c.name, c.username
  FROM Cats c JOIN Selfies s ON s.cat_id = c.id
  WHERE s.id = :selfie_id");
$stmt->bindValue("selfie_id", $id);
$stmt->execute();
$cat = $stmt->fetch();

// Get comments
$stmt = $db->prepare("SELECT c.username, m.comment_number, m.date_posted, m.body
  FROM Comments m
  LEFT JOIN Cats c ON c.id = m.cat_id
  WHERE m.selfie_id = :selfie_id
  ORDER BY m.comment_number;");
$stmt->bindValue("selfie_id", $id);
$stmt->execute();

$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

function e($s) { return htmlspecialchars($s); }

?>
<!doctype htmL>
<html>
  <head>
    <title><?php echo e($cat['name']); ?>'s Selfie | Kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="styles/normalize.css">
  </head>

  <body>
    <header>
      <h1><?php echo e($cat['name']); ?>'s Selfie | Kitstagram</h1>
    </header>

    <main>
      <img src="./uploads/<?php echo e($selfie['filename']); ?>">

      <p><?php echo e($selfie['caption']); ?></p>
      <p>Uploaded by <?php echo e($cat['name']); ?></p>
      <p>Uploaded on <?php echo e(pretty_date($selfie["date_uploaded"])); ?></p>
      <p><?php echo e($selfie['likes']); ?> likes </p>
    </main>

    <main>
      <h2>Comments</h2>

      <?php foreach ($comments as $c) { ?>
        <article class="comment" id="comment-<?= e($c['comment_number']) ?>">
          <div class="comment-header">
            <span class="comment-number">
              <a href="#comment-<?= e($c['comment_number']) ?>"><?=
                '#'.e($c['comment_number'])
              ?></a>
            </span>
            <span class="username">
              <a href="<?= e(get_profile_url(e($c['username']))) ?>"><?= e($c['username']) ?></a>
            </span>
          <p><?= e($c['body']) ?></p>
        </article>
      <?php } ?>
    </main>
  </body>
</html>
