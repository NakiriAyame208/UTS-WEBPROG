<?php
session_start();

@include 'config.php';

// Check if user type is not set or not 'nasabah', redirect to login page
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'nasabah'){
   header('location:login_form.php');
   exit;
}

// User is logged in as 'nasabah', proceed to fetch data
$user_id = $_SESSION['user_id'];

// Calculate total simpanan
$total_simpanan = getTotalSimpanan($conn, $user_id);

// Function to get total simpanan for the user
function getTotalSimpanan($conn, $user_id) {
    // Query to fetch sum of jumlah_transfer for approved transactions only
    $query = "SELECT SUM(jumlah_transfer) AS total_simpanan FROM pembayaran WHERE user_id = ? AND verification_status = 'approved'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total_simpanan);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $total_simpanan;
}

// Fetch other data as usual
$total_simpanan = getTotalSimpanan($conn, $user_id);
$simpanan_kategori = getSimpananByCategory($conn, $user_id);
$proof_of_payment = getProofOfPayment($conn, $user_id);

// Function to get simpanan by category for the user
function getSimpananByCategory($conn, $user_id) {
    $query = "SELECT kategori_simpanan, SUM(jumlah_transfer) AS total_kategori FROM pembayaran WHERE user_id = ? AND verification_status = 'approved' GROUP BY kategori_simpanan";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $kategori_simpanan, $total_kategori);
    $simpanan_kategori = array();
    while (mysqli_stmt_fetch($stmt)) {
        $simpanan_kategori[$kategori_simpanan] = $total_kategori;
    }
    mysqli_stmt_close($stmt);
    return $simpanan_kategori;
}

// Function to get proof of payment for the user
function getProofOfPayment($conn, $user_id) {
   $query = "SELECT file_upload_bukti_transfer FROM pembayaran WHERE user_id = ? AND kategori_simpanan = 'pokok' AND verification_status = 'approved'";
   $stmt = mysqli_prepare($conn, $query);
   mysqli_stmt_bind_param($stmt, "i", $user_id);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_bind_result($stmt, $proof_of_payment);
   mysqli_stmt_fetch($stmt);
   mysqli_stmt_close($stmt);
   return $proof_of_payment;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Page</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>Hi, <span>Nasabah</span></h3>
      <h1>Welcome <span><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User"; ?></span></h1>
      <p>This is a user page</p>
      <a href="profile.php" class="btn">Profile</a>
      <div>
         <h4>Total Simpanan</h4>
         <p><?php echo isset($total_simpanan) ? $total_simpanan : "No data available"; ?></p>
      </div>
      <div>
         <h4>Simpanan Berdasarkan Kategori</h4>
         <ul>
            <?php 
            if (!empty($simpanan_kategori)) {
                foreach ($simpanan_kategori as $kategori => $total) {
                    echo "<li>$kategori: $total</li>";
                }
            } else {
                echo "<li>No data available</li>";
            }
            ?>
         </ul>
      </div>
      <?php if (!empty($proof_of_payment)) { ?>
         <div>
            <h4>Bukti Pembayaran Simpanan Pokok</h4>
            <img src="<?php echo $proof_of_payment; ?>" alt="Bukti Pembayaran">
         </div>
      <?php } ?>
      <a href="pembayaran.php" class="btn">Pembayaran</a>
      <a href="history.php" class="btn">History</a>
      <a href="logout.php" class="btn">Logout</a>
   </div>
</div>

</body>
</html>
