<?php
require_once 'includes/all.php';

if (!is_logged_in()) {
	header("Location: login.php");
	exit(0);
}

$db = connect_db();
$user_id = get_logged_in_user_id();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$tmp = $_FILES["selfie"]["tmp_name"];
	$size = $_FILES["selfie"]["size"];

	$errors = array();
	$maxsize = 1048756;
	if ($size > $maxsize) {
		$errors[] = "Upload failed: file size $size exceeds maximum size of $maxsize";
	} else {
		$info = @getimagesize($tmp);
		if ($info === false) {
			$errors[] = "Upload failed: not an image";
		} else {
			$type = $info[2];
			if (!($type === IMAGETYPE_JPEG)) {
				$errors[] = "Upload failed: only JPEG images are allowed";
			}
		}
	}

	if (count($errors)) {
		$_SESSION['flash_errors'] = $errors;
		header("Location: upload.php");
		exit(0);
	}

	$filehash = hash_file("sha256", $tmp);
	$newfilename = "$filehash" . '.jpg';
	$destination = './uploads/' . $filehash . '.jpg';

	if (!file_exists($destination)) {
		move_uploaded_file($tmp, $destination);
		chmod($destination, 0444);
	}

	$caption = $_POST["caption"];
	$stmt = $db->prepare("INSERT INTO Selfies (cat_id, caption, filename) VALUES (?,?,?)");
	$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
	$stmt->bindValue(2, $caption, PDO::PARAM_STR);
	$stmt->bindValue(3, $newfilename, PDO::PARAM_STR);
	$stmt->execute();

	$pic_id = $db->lastInsertId();

	$_SESSION['flash_success'] = array('Upload Successful');

	// TODO(ae): redirect to new file
	header("Location: index.php");
}

?>
<!doctype html>
<html>
  <head>
    <title>Upload a selfie | kitstagram</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/kitstagram.css">
  </head>

  <body>
    <header>
      <h1>kitstagram &gt; Upload a selfie</h1>
    </header>

    <?php include 'includes/flash.php' ?>

    <main>
      <form action="" method=POST enctype="multipart/form-data">

        <div>
          <input type="file" name="selfie">
          <p class="help-block">Supported File Types: JPEG, JPG, less than 1 MB </p>

          <p>Caption (optional):<br>
            <textarea name="caption" rows="3" cols="50"></textarea>
          </p>
        </div>

        <button>Upload</button>

      </form>
    </main>
  </body>
</html>
