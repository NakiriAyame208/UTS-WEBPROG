<?php
session_start();

// Check if user type is not set or not 'nasabah', redirect to login page
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'nasabah') {
    header('location:login_form.php');
    exit;
}

// Include database connection
@include 'config.php';

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Fetch transaction history from the database
$query = "SELECT tanggal_transfer, jumlah_transfer, kategori_simpanan, verification_status FROM pembayaran WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $tanggal_transfer, $jumlah_transfer, $kategori_simpanan, $verification_status);
$transactions = array();
while (mysqli_stmt_fetch($stmt)) {
    // Add the verification status to the transaction details
    $transactions[] = array(
        'tanggal_transfer' => $tanggal_transfer,
        'jumlah_transfer' => $jumlah_transfer,
        'kategori_simpanan' => $kategori_simpanan,
        'verification_status' => $verification_status
    );
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Transaction History</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>Transaction History - Nasabah</h3>
      <table>
         <thead>
            <tr>
               <th>Tanggal Transfer</th>
               <th>Jumlah Transfer</th>
               <th>Kategori Simpanan</th>
               <th>Verification Status</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($transactions as $transaction): ?>
               <tr>
                  <td><?php echo $transaction['tanggal_transfer']; ?></td>
                  <td><?php echo $transaction['jumlah_transfer']; ?></td>
                  <td><?php echo $transaction['kategori_simpanan']; ?></td>
                  <td><?php echo $transaction['verification_status']; ?></td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
      <a href="user_page.php" class="btn">Back to User Page</a>
   </div>
</div>

</body>
</html>
