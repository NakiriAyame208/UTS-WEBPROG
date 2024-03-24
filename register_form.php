<?php

@include 'config.php';

session_start();

if(isset($_POST['submit']))
{
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = ($_POST['password']);
   $cpass = ($_POST['cpassword']);
   $gender = mysqli_real_escape_string($conn, $_POST['Gender']); 
   $address = mysqli_real_escape_string($conn, $_POST['Address']); 
   $DoB = mysqli_real_escape_string($conn, $_POST['DoB']);
   $captcha = $_POST['captcha'];
   $captcharandom = $_POST['captcha-rand'];

   // Verifikasi captcha
   if ($captcha != $captcharandom) {
      $error[] = 'Invalid captcha value';
   }

   // Mengunggah bukti pembayaran
   $proof_of_payment = '';
   if ($_FILES['proof_of_payment']['name']) {
      $target_dir = "uploads/";
      $target_file = $target_dir . basename($_FILES["proof_of_payment"]["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
      // Check if image file is a actual image or fake image
      $check = getimagesize($_FILES["proof_of_payment"]["tmp_name"]);
      if ($check !== false) {
         // File is an image
         $uploadOk = 1;
      } else {
         $error[] = "File is not an image.";
         $uploadOk = 0;
      }
      // Check if file already exists
      if (file_exists($target_file)) {
         $error[] = "Sorry, file already exists.";
         $uploadOk = 0;
      }
      // Check file size
      if ($_FILES["proof_of_payment"]["size"] > 100000000) {
         $error[] = "Sorry, your file is too large.";
         $uploadOk = 0;
      }
      // Allow certain file formats
      if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
         $error[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
         $uploadOk = 0;
      }
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
         $error[] = "Sorry, your file was not uploaded.";
      // if everything is ok, try to upload file
      } else {
         if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
            $proof_of_payment = $target_file;
         } else {
            $error[] = "Sorry, there was an error uploading your file.";
         }
      }
   }

   // Jika tidak ada error, lanjutkan proses registrasi
   if (empty($error)) {
      // Lakukan pemeriksaan lain seperti kecocokan password, dll.
      // Selanjutnya, masukkan data ke database dan lanjutkan ke halaman login atau halaman lain sesuai kebutuhan.

      // Misalnya:
      $user_type = 'nasabah'; // Set user_type sebagai nasabah

      // Query untuk menyimpan data ke database
      $insert = "INSERT INTO user_form(name, email, password, user_type, gender, address, DoB, proof_of_payment) VALUES('$name','$email','$pass','$user_type','$gender','$address','$DoB','$proof_of_payment')";
      mysqli_query($conn, $insert);

      // Redirect ke halaman login atau halaman lain
      header('location:login_form.php');
      exit;
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
   <title>register form</title>

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <?php
      if(isset($error))
      {
         foreach($error as $error)
         {
            echo '<span class="error-msg">'.$error.'</span>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="enter your name">
      <input type="email" name="email" required placeholder="enter your email">
      <input type="text" name="Address" required placeholder="Enter your address">
      <input type="text" name="Gender" required placeholder="What is your gender?">
      <input type="date" name="DoB" required placeholder="What is your Birth date?">
      <input type="password" name="password" required placeholder="enter your password">
      <input type="password" name="cpassword" required placeholder="confirm your password">
      <input type="file" name="proof_of_payment" required>
      <input type="text" name="captcha" id="captcha"placeholder="Enter Captcha"required data-parsley-trigger="keyup" class="form-protocol"/>
      <input type="hidden" name="captcha-rand" value="<?php echo $rand; ?>">
      <label for="captcha-code">Captcha code</label>
      <div class="captcha"><?php echo $rand; ?></div>
      <input type="submit" name="submit" value="register now" class="form-btn">
      <p>already have an account? <a href="login_form.php">login now</a></p>
   </form>

</div>

</body>
</html>
