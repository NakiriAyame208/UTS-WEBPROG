<?php
session_start();

@include 'config.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_name'])) {
    header('Location: login_form.php');
    exit;
}

// Fetch pending transactions
$query = "SELECT id, user_id, tanggal_transfer, jumlah_transfer, kategori_simpanan FROM pembayaran WHERE verification_status = 'pending'";
$result = mysqli_query($conn, $query);

// Handle form submission (accept/reject)
if (isset($_POST['accept']) || isset($_POST['reject'])) {
    $transaction_id = $_POST['transaction_id'];
    $status = isset($_POST['accept']) ? 'approved' : 'rejected';
    $update_query = "UPDATE pembayaran SET verification_status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $status, $transaction_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: verification.php'); // Redirect to refresh the page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h3>Verification - Admin</h3>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Tanggal Transfer</th>
                        <th>Jumlah Transfer</th>
                        <th>Kategori Simpanan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo $row['tanggal_transfer']; ?></td>
                            <td><?php echo $row['jumlah_transfer']; ?></td>
                            <td><?php echo $row['kategori_simpanan']; ?></td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="accept">Accept</button>
                                    <button type="submit" name="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="admin_page.php" class="btn">Back to Admin Page</a>
        </div>
    </div>
</body>
</html>
