<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
  header("Location: signup.php");
  exit();
}

$db = connect_db();
$my = get_logged_in_user($db);

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

$stmt = $db->prepare("SELECT COALESCE(max(m.comment_number)+1, 1) as `num` FROM Comments m
  WHERE m.selfie_id = :selfie_id");
$stmt->bindValue("selfie_id", $id);
$stmt->execute();
$row = $stmt->fetch();
$next_comment_number = $row['num'];


function profile_link($username) {
  return '<a href="'. e(get_profile_url(e($username))) .'">'. e($username) .'</a>';
}

?>
<!doctype html>
<html>
  <head>
    <title><?php echo e($cat['name']); ?>'s Selfie | Kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/kitstagram.css">
  </head>

  <body>
    <header>
      <h1>kitstagram &gt; <?php echo e($cat['name']); ?>'s Selfie</h1>
    </header>

    <?php include 'includes/flash.php' ?>

    <main>
      <figure class="selfie">
        <img src="./uploads/<?php echo e($selfie['filename']); ?>">
      </figure>

      <div class="selfie-info">
        <p><?php echo e($selfie['caption']); ?></p>
        <p>Uploaded
          by <?= profile_link($cat['name']) ?>
          on <?php echo e(pretty_date($selfie["date_uploaded"])); ?></p>
        <p>&#x2764; <?php echo e($selfie['likes']); ?> likes </p>
      </div>
    </main>

    <div class="comments">
      <h2>Comments</h2>

      <?php foreach ($comments as $c) { ?>
        <article class="comment" id="comment-<?= e($c['comment_number']) ?>">
          <div class="comment-header">
            <span class="comment-number">
              <a href="#comment-<?= e($c['comment_number']) ?>"><?=
                '#'.e($c['comment_number'])
              ?></a>
            </span>
            <span class="comment-author">
              <?= profile_link($c['username']) ?>
            </span>
          <p><?= e($c['body']) ?></p>
        </article>
      <?php } ?>

      <form action="comment.php" method="POST">
        <article class="comment your-comment">
          <div class="comment-header">
            <span class="comment-number">
              <?= '#'.e($next_comment_number) ?>
            </span>
            <span class="comment-author">
              <?= profile_link($my['username']) ?>
            </span>
          </div>
          <textarea name="body" rows="3" cols="50"></textarea>
          <input type="hidden" name="selfie_id" value="<?= e($selfie['id']) ?>">
          <div>
            <button>Add Comment</button>
          </div>
        </article>
      </form>
    </div>
  </body>
</html>
