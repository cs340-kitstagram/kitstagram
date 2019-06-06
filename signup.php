<?php
require_once 'includes/all.php';

if (is_logged_in()) {
  header("Location: index.php");
  exit();
}

// Define variables and initialize with empty values
$username = $name = $password = "";

$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $name = $_POST['name'];
  $password = $_POST['password'];

  $db = connect_db();

  // insert into database
  $db = "INSERT INTO Cats (id, username, password_hash, name, profile) VALUES (DEFAULT, ?, ?, ?, NULL)";

  $stmt = $db->prepare("SELECT username, password_hash FROM Cats"); // Don't think this is right
  
  $stmt->bindValue("username", $username);
  $stmt->bindValue("name", $name);
  $stmt->bindValue("password_hash", $password);
  $stmt->execute();





  // errors to implement:
  // account already exists
  // passwords dont match

  if (!$errors) {
    header("Location: index.php");
    exit();
  }
}
?>

<!doctype htmL>
<html>
  <head>
    <title>Sign Up | Kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="styles/normalize.css">
  </head>

  <body>
    <div class="container">

      <form class="form-signin" action="" method="POST">
        <h2 class="form-signin-heading">Please Create a New Account</h2>

        <div >
          <label for="inputUsername" class="sr-only">Enter the Username You Want to Use</label>
          <input type="text" id="inputUsername" name="username" class="form-control first" 
                placeholder="Username" required autofocus value="<?php echo $username; ?>">
        </div>

        <div >
          <label for="inputName" class="sr-only">Enter your Name</label>
          <input type="text" id="inputName" name="name" class="form-control first" 
                placeholder="Name" required autofocus value="<?php echo $name; ?>">
        </div>

        <div class="<?php if (isset($errors['password'])) echo 'has-error'; ?>">
          <label for="inputPassword" class="sr-only">Enter a New Password</label>
          <input type="password" id="inputPassword" name="password" class="form-control last" placeholder="Password" required  value="<?php echo $password; ?>">
        </div>
        
        <button class="btn btn-lg btn-primary btn-block" type="submit">Create Account</button>
      </form>
      <br>
      <a href="login.php">Already have an Account? Log in Here</a>
    </div>
  </body>
</html>