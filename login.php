<?php
require_once 'includes/all.php';

if (is_logged_in()) {
  header("Location: index.php");
  exit();
}

$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $db = connect_db();

  $stmt = $db->prepare("SELECT id, password_hash FROM Cats WHERE username=:username");
  $stmt->bindValue("username", $username);
  $stmt->execute();

  $row = $stmt->fetch();
  if (!$row) {
    $errors['username'] = 'no account exists with that username';
  } else {
    if (password_verify($password, $row["password_hash"])) {
      $_SESSION["user_id"] = $row["id"];
    } else {
      $errors['password'] = 'wrong password';
    }
  }

  if (!$errors) {
    header("Location: index.php");
    exit();
  }
}
?>
<!doctype htmL>
<html>
  <head>
    <title>Log in | Kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
  </head>

  <body>
    <div class="container">

      <form class="form-signin" action="" method="POST">
        <h2 class="form-signin-heading">Please sign in</h2>

        <div class="<?php if (isset($errors['username'])) echo 'has-error'; ?>">
          <label for="inputUsername" class="sr-only">Username</label>
          <input type="text" id="inputUsername" name="username" class="form-control first" placeholder="Username" required autofocus value="<?php if (isset($username)) echo htmlspecialchars($username); ?>">
          <?php if (isset($errors['username'])) { ?>
            <p class="help-block"><?= htmlspecialchars($errors['username']) ?></p>
          <?php } ?>
        </div>

        <div class="<?php if (isset($errors['password'])) echo 'has-error'; ?>">
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" id="inputPassword" name="password" class="form-control last" placeholder="Password" required>
          <?php if (isset($errors['password'])) { ?>
            <p class="help-block"><?= htmlspecialchars($errors['password']) ?></p>
          <?php } ?>
        </div>

        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <br>
        <a href="signup.php">Create new account</a>
      </form>

    </div>
  </body>
</html>
