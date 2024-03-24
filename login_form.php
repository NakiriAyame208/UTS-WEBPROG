<?php
session_start();

@include 'config.php';

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Not hashed
    $captcha = $_POST['captcha'];
    $captcharandom = $_POST['captcha-rand'];

    if ($captcha != $captcharandom) {
        $error[] = 'Invalid captcha value';
    } else {
        $select = "SELECT id, name, email, password, user_type FROM user_form WHERE email = ?";
        $stmt = mysqli_prepare($conn, $select);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $id, $name, $email, $stored_password, $user_type);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
                if($password === $stored_password) { // Compare plain text password with stored password
                    if($user_type == 'admin') {
                        $_SESSION['admin_name'] = $name; // Set admin_name in session
                        header('Location: admin_page.php');
                        exit();
                    } elseif($user_type == 'nasabah') {
                        $_SESSION['user_id'] = $id; // Set user_id in session
                        $_SESSION['user_name'] = $name; // Set user_name in session
                        $_SESSION['user_type'] = $user_type; // Set user_type in session
                        header('Location: user_page.php');
                        exit();
                    }
                } else {
                    $error[] = 'Incorrect password!';
                }
            } else {
                $error[] = 'Email not found!';
            }
        }
    }
}

$rand = rand(9999, 1000);
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="form-container">
   <form action="" method="post">
      <h3>Login Now</h3>
      <?php
      if(isset($error)) {
         foreach($error as $error) {
            echo '<span class="error-msg">'.$error.'</span>';
         }
      }
      ?>
      <input type="email" name="email" required placeholder="Enter your email">
      <input type="password" name="password" required placeholder="Enter your password">
      <input type="text" name="captcha" id="captcha" placeholder="Enter Captcha" required data-parsley-trigger="keyup" class="form-protocol">
      <input type="hidden" name="captcha-rand" value="<?php echo $rand; ?>">
      <label for="captcha-code">Captcha code: <?php echo $rand; ?></label>
      <input type="submit" name="submit" value="Login" class="form-btn">
      <p>Don't have an account? <a href="register_form.php">Register Now</a></p>
   </form>
</div>

</body>
</html>
