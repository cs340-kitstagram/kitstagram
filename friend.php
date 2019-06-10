<?php
require_once 'includes/all.php';

// friend.php - friends or unfriends a cat
// parameters:
//      - username=<str>  the username of the cat to friend
//      - friend=0|1      whether to unfriend or friend the cat

if (!is_logged_in()) {
  header("Location: signup.php");
  exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
  header('Status: 405'); // 405 Method Not Allowed
  exit();
}

if (!isset($_POST['username']) || !isset($_POST['friend'])) {
  header("Status: 400");
  exit();
}

$db = connect_db();
$my_id = get_logged_in_user_id();
$username = $_POST["username"];
$friending = !!$_POST["friend"];

// Get the cat's id
$stmt = $db->prepare("SELECT c.id, c.username FROM Cats c WHERE c.username = :username");
$stmt->bindValue("username", $username);
$stmt->execute();
$friend = $stmt->fetch();

if (!$friend) {
  header("Status: 404");
  echo 'no such cat';
  exit();
}

if ($friending) {
  $stmt = $db->prepare("INSERT IGNORE INTO Friends (cat_id, friend_id) VALUES (?,?), (?,?)");
} else {
  $stmt = $db->prepare("DELETE FROM Friends WHERE (cat_id = ? AND friend_id = ?) OR (cat_id = ? AND friend_id = ?)");
}
$stmt->bindValue(1, $my_id, PDO::PARAM_INT);
$stmt->bindValue(2, $friend['id'], PDO::PARAM_INT);
$stmt->bindValue(3, $friend['id'], PDO::PARAM_INT);
$stmt->bindValue(4, $my_id, PDO::PARAM_INT);
$stmt->execute();

// Return to the profile page
header("Location: ".get_profile_url($friend['username']));
echo $row['likes'];
