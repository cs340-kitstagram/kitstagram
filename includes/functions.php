<?php

// functions.php - common functionality

// Escapes a string for safely embedding in HTML
// This is just a short alias for htmlspecialchars,
// which is too long to type all the time.
function e($s) {
  return htmlspecialchars($s);
}

// Reports whether a user is currently logged in.
function is_logged_in() {
  return array_key_exists('user_id', $_SESSION);
}

// Returns the id of the currently logged in user,
// or 0 if no user is logged in.
function get_logged_in_user_id() {
  if (is_logged_in()) {
    return $_SESSION['user_id'];
  }
  return 0;
}

// Connect to the database and return a new PDO object.
// If connection is unsuccessful, prints an error and terminates the page.
function connect_db() {
  global $dsn, $dbuser, $dbpass;
  try {
    // TODO(ae): persistent?
    $db = new PDO($dsn, $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  } catch (PDOException $e) {
    echo "Error connecting to database";
    exit();
  }
  return $db;
}

// Pretty-print a timestamp like "January 5th, 2015 at 5:14am"
function pretty_date($ts) {
  return date("F jS, Y \\a\\t g:ia", $ts);
}

// Accessors

// Retrieves data about the currently-logged-in user
function get_logged_in_user($db) {
  $id = get_logged_in_user_id();
  if (!$id) {
    return FALSE;
  }
  $stmt = $db->prepare("SELECT * FROM Cats WHERE id = ?");
  $stmt->bindValue(1, $id, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch();
}

// Returns a relative URL to the profile of the cat given by the username
function get_profile_url($username) {
  return "profile.php?username=".urlencode($username);
}

// Returns a relative URL to the selfie given by the id
function get_selfie_url($id) {
  return "selfie.php?id=".urlencode($id);
}

// Returns the full URL of the current page (without query parameters)
function current_url() {
  $url = 'http';
  if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
    $url .= "s";
  }
  $url .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
  } else {
    $url .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
  }

  return $url;
}

function is_valid_id($db, $table, $id) {
  if (!is_numeric($id)) {
    return false;
  }
  $stmt = $db->prepare("SELECT EXISTS (SELECT 1 FROM $table WHERE id = :id)");
  $stmt->bindValue(":id", $id, PDO::PARAM_INT);
  $stmt->execute();
  return !!$stmt->fetch()[0];
}

// Display a "not found" error page and exit the script
function not_found() {
  header('Status: 404');
  echo "not found";
  exit(0);
}
