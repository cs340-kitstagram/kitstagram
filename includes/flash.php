<?php

if (isset($_SESSION["flash_success"])) {
  foreach($_SESSION["flash_success"] as $msg) {
    echo '<div class="flash flash-success">'.e($msg).'</div>';
  }
  unset($_SESSION["flash_success"]);
}
if (isset($_SESSION["flash_errors"])) {
  foreach($_SESSION["flash_errors"] as $msg) {
    echo '<div class="flash flash-warning">'.e($msg).'</div>';
  }
  unset($_SESSION["flash_errors"]);
}
