<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
  header("Location: signup.php");
  exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
  header('Status: 405'); // 405 Method Not Allowed
  exit();
}

if (!isset($_POST['selfie_id']) || !isset($_POST['body'])) {
  header("Status: 400");
  exit();
}

$db = connect_db();
$cat_id = get_logged_in_user_id();
$selfie_id = $_POST["selfie_id"];
$body = $_POST["body"];

if ($body === "") {
  $errors  = array("Comments cannot be empty");
  $_SESSION['flash_errors'] = $errors;
  header("Location: ".get_selfie_url($selfie_id));
  exit();
}

// TODO(ae): should probably check that the selfie exists

$stmt = $db->prepare("SELECT COALESCE(MAX(comment_number),0)+1 AS `num` FROM Comments WHERE selfie_id = :id");
$stmt->bindValue("id", $selfie_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
if (!$row) {
  header("Status: 500");
  echo "Internal server error";
  exit();
}
$comment_number = $row['num'];

$stmt = $db->prepare("INSERT INTO Comments (selfie_id, cat_id, body, comment_number) VALUES (?,?,?,?)");
$stmt->bindValue(1, $selfie_id, PDO::PARAM_INT);
$stmt->bindValue(2, $cat_id, PDO::PARAM_INT);
$stmt->bindValue(3, $body, PDO::PARAM_STR);
$stmt->bindValue(4, $comment_number, PDO::PARAM_STR);
if (!$stmt->execute()) {
  header("Status: 500");
  echo "Internal server error";
  exit();
}

$url = get_selfie_url($selfie_id) . "#comment-" . $comment_number;
header("Location: ".$url);
