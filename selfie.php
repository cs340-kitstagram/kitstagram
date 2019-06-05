<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
  header("Location: index.php");
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
  header("Status: 404");
  echo "not found";
  exit(0);
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
  </head>

  <body>
    <div class="container">
      <h1><?php echo e($cat['name']); ?>'s Selfie | Kitstagram</h1>

      <img src="./uploads/<?php echo e($selfie['filename']); ?>">

      <p><?php echo e($selfie['caption']); ?></p>
      <p>Uploaded by <?php echo e($cat['name']); ?></p>
      <p>Uploaded on <?php echo e(pretty_date($selfie["date_uploaded"])); ?></p>
      <p><?php echo e($selfie['likes']); ?> likes </p>

    </div>
  </body>
</html>
