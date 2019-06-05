<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
	header("Location: login.php");
	exit(0);
}

$db = connect_db();
$user_id = get_logged_in_user_id();


?>

<!doctype html>
<html lang="en">
<head>
<title>Profile | Kitstagram</title>
<link rel="icon" type="image/png" href="images/favicon.png">
</head>

<header>
  <title>$Username's Profile</title>
</header>

<body>
<main>

	<div style="border-radius: 5px">
		<header>My Selfies</header>
	</div>

	<div style="border-radius: 5px">
		<header>Bio</header>
	</div>



</main>
</body>
</html>