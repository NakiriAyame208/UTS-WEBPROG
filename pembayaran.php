<?php
session_start();

@include 'config.php';

// Check if user type is not set or not 'nasabah', redirect to login page
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'nasabah'){
   header('location:login_form.php');
   exit;
}

// User is logged in as 'nasabah', proceed to fetch user ID
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Get form data
    $kategori = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];

    // Handle file upload
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];
    $file_error = $_FILES['file']['error'];

    // Check if file is uploaded successfully
    if($file_error === 0) {
        // Generate unique file name to prevent overwriting
        $file_destination = 'uploads/' . uniqid('', true) . '_' . $file_name;
        // Move the uploaded file to the specified destination
        if(move_uploaded_file($file_tmp, $file_destination)) {
            // Prepare and execute the SQL query to insert data into the database
            $query = "INSERT INTO pembayaran (user_id, kategori_simpanan, tanggal_transfer, jumlah_transfer, file_upload_bukti_transfer) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "issss", $user_id, $kategori, $tanggal, $jumlah, $file_destination);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            // Redirect to user page after successful submission
            header('Location: user_page.php');
            exit;
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "Error uploading file: " . $file_error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pembayaran Form</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>Form Pembayaran</h3>
      <form action="" method="POST" enctype="multipart/form-data">
         <div>
            <label for="kategori">Kategori Simpanan:</label>
            <select name="kategori" id="kategori" required>
               <option value="Wajib">Wajib</option>
               <option value="Sukarela">Sukarela</option>
            </select>
         </div>
         <div>
            <label for="tanggal">Tanggal Transfer:</label>
            <input type="date" name="tanggal" id="tanggal" required>
         </div>
         <div>
            <label for="jumlah">Jumlah Transfer:</label>
            <input type="number" name="jumlah" id="jumlah" step="0.01" min="0.01" required>
         </div>
         <div>
            <label for="file">File Upload Bukti Transfer:</label>
            <input type="file" name="file" id="file" accept="image/*" required>
         </div>
         <button type="submit" name="submit">Submit</button>
      </form>
   </div>
</div>

</body>
</html>
