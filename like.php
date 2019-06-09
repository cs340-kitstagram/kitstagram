<?php
require_once 'includes/all.php';

// like.php - adds or removes a like from a selfie and prints the new number of likes
// called via ajax by selfies.php
// parameters:
//      - selfie_id=<int> the id of the selfie
//      - like=0|1        whether to unlike or like the selfie

if (!is_logged_in()) {
  header("Location: signup.php");
  exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
  header('Status: 405'); // 405 Method Not Allowed
  exit();
}

if (!isset($_POST['selfie_id']) || !isset($_POST['like'])) {
  header("Status: 400");
  exit();
}

$db = connect_db();
$cat_id = get_logged_in_user_id();
$selfie_id = $_POST["selfie_id"];
$like = !!$_POST["like"];

if (!is_valid_id($db, "Selfies", $selfie_id)) {
  header("Status: 404");
  echo 'no such selfie';
  exit();
}

if ($like) {
  $stmt = $db->prepare("INSERT IGNORE INTO Likes (selfie_id, cat_id) VALUES (?,?)");
} else {
  $stmt = $db->prepare("DELETE FROM Likes WHERE selfie_id = ? AND cat_id = ?");
}
$stmt->bindValue(1, $selfie_id, PDO::PARAM_INT);
$stmt->bindValue(2, $cat_id, PDO::PARAM_INT);
$stmt->execute();

$stmt = $db->prepare("SELECT likes FROM Selfies WHERE id = :selfie_id");
$stmt->bindValue(1, $selfie_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row['likes'];
