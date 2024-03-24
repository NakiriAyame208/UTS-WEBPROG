<?php
session_start();

@include 'config.php';

if(!isset($_SESSION['admin_name'])) {
    header('location: login_form.php');
    exit;
}

$query = "SELECT pembayaran.id AS transaction_id, user_form.id AS user_id, user_form.name AS user_name, pembayaran.tanggal_transfer, pembayaran.jumlah_transfer, pembayaran.kategori_simpanan, pembayaran.verification_status FROM pembayaran INNER JOIN user_form ON pembayaran.user_id = user_form.id";
$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>History - Admin</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>Transaction History - Admin</h3>
      <table>
         <thead>
            <tr>
               <th>ID User</th>
               <th>Nama</th>
               <th>Tanggal Transfer</th>
               <th>Jumlah Transfer</th>
               <th>Kategori Simpanan</th>
               <th>Status</th>
            </tr>
         </thead>
         <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
               <tr>
                  <td><?php echo $row['user_id']; ?></td>
                  <td><?php echo $row['user_name']; ?></td>
                  <td><?php echo $row['tanggal_transfer']; ?></td>
                  <td><?php echo $row['jumlah_transfer']; ?></td>
                  <td><?php echo $row['kategori_simpanan']; ?></td>
                  <td><?php echo $row['verification_status']; ?></td>
               </tr>
            <?php } ?>
         </tbody>
      </table>
      <a href="admin_page.php" class="btn">Back to Admin Page</a>
   </div>
</div>

</body>
</html>
