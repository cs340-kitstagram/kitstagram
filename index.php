<?php
require_once 'includes/all.php';

if (is_logged_in()) {
  header("Location: dashboard.php");
} else {
  header("Location: signup.php");
}
