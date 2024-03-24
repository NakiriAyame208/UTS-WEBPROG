<?php
@include 'config.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$query = "SELECT name, email, password, address, DoB, gender, profile_picture FROM user_form WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name, $email, $password, $address, $DoB, $gender, $profile_picture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
        // Handle password change form submission
        $newPassword = $_POST['newPassword'];
        $confirmNewPassword = $_POST['confirmNewPassword'];

        // Validate new password and confirm password
        if ($newPassword !== $confirmNewPassword) {
            $passwordError = "Passwords do not match.";
        } else {
            // Update password in the database
            $updateQuery = "UPDATE user_form SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $newPassword, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Redirect back to profile.php after password change
            header('Location: profile.php');
            exit();
        }
    } elseif (isset($_FILES['profilePicture'])) {
        // Handle profile picture upload
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profilePicture"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["profilePicture"]["size"] > 5000000) { // 5MB limit
            $error = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $error = "Sorry, your file was not uploaded.";
        } else {
            // Move the uploaded file
            if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) {
                // Update profile picture path in the database
                $updateQuery = "UPDATE user_form SET profile_picture = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($stmt, "si", $target_file, $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                // Redirect back to profile.php after profile picture change
                header('Location: profile.php');
                exit();
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Profile</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>User Profile</h3>
      <div class="profile-picture">
         <?php if(!empty($profile_picture)): ?>
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
         <?php else: ?>
            <img src="default_profile_picture.jpg" alt="Default Profile Picture">
         <?php endif; ?>
      </div>
      <form action="" method="post" enctype="multipart/form-data">
         <label for="profilePicture">Upload New Profile Picture:</label>
         <input type="file" id="profilePicture" name="profilePicture" required>
         <input type="submit" value="Confirm Profile Picture Change">
         <?php if (isset($error)) echo "<span class='error'>$error</span>"; ?>
      </form>
      <p><strong>Name:</strong> <?php echo $name; ?></p>
      <p><strong>Email:</strong> <?php echo $email; ?></p>
      <p><strong>Password:</strong> <?php echo $password; ?></p>
      <p><strong>Address:</strong> <?php echo $address; ?></p>
      <p><strong>Date of Birth:</strong> <?php echo $DoB; ?></p>
      <p><strong>Gender:</strong> <?php echo $gender; ?></p>
      
      <!-- Change password form (initially hidden) -->
      <button id="changePasswordBtn">Change Password</button>
      <form id="changePasswordForm" action="" method="post" style="display: none;">
         <label for="newPassword">New Password:</label>
         <input type="password" id="newPassword" name="newPassword" required><br>
         <label for="confirmNewPassword">Confirm New Password:</label>
         <input type="password" id="confirmNewPassword" name="confirmNewPassword" required><br>
         <input type="submit" value="Change Password">
         <?php if (isset($passwordError)) echo "<span class='error'>$passwordError</span>"; ?>
      </form>
      
      <!-- Button to go back to user_page.php -->
      <form action="user_page.php" method="post">
         <button type="submit">Go Back to User Page</button>
   </div>
</div>

<script>
// Show/hide the change password form when the button is clicked
document.getElementById('changePasswordBtn').addEventListener('click', function() {
    var form = document.getElementById('changePasswordForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
});
</script>

</body>
</html>
