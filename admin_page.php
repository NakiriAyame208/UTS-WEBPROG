<?php

session_start();

@include 'config.php';

if(!isset($_SESSION['admin_name'])) 
{
    header('location: login_form.php');
    exit;
}

// Total Simpanan
$total_simpanan = getTotalSimpanan($conn);

// Simpanan Berdasarkan Kategori
$simpanan_kategori = getSimpananByCategory($conn);

function getTotalSimpanan($conn) {
    $query = "SELECT SUM(jumlah_transfer) AS total_simpanan FROM pembayaran WHERE verification_status = 'approved'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_simpanan'];
}

function getSimpananByCategory($conn) {
    $query = "SELECT kategori_simpanan, SUM(jumlah_transfer) AS total_kategori FROM pembayaran WHERE verification_status = 'approved' GROUP BY kategori_simpanan";
    $result = mysqli_query($conn, $query);
    $simpanan_kategori = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $simpanan_kategori[$row['kategori_simpanan']] = $row['total_kategori'];
    }
    return $simpanan_kategori;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>Hi, <span>Admin</span></h3>
      <h1>Welcome <span><?php echo $_SESSION['admin_name']; ?></span></h1>
      <p>This is an admin page</p>
      <div>
         <h4>Total Simpanan</h4>
         <p><?php echo $total_simpanan; ?></p>
      </div>
      <div>
         <h4>Simpanan Berdasarkan Kategori</h4>
         <ul>
            <?php foreach ($simpanan_kategori as $kategori => $total) { ?>
               <li><?php echo $kategori . ": " . $total; ?></li>
            <?php } ?>
            <a href="historyadmin.php" class="btn">History Admin</a>
            <a href="verification.php" class="btn">Verify All</a>
            <a href="user_list.php" class="btn">User List</a> 
            <a href="logout.php" class="btn">Logout</a>
         </ul>
      </div>
   </div>
</div>

</body>
</html>
