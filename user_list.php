<?php

@include 'config.php';

// Check if the user is logged in as admin
session_start();
if (!isset($_SESSION['admin_name'])) {
    header('Location: login_form.php');
    exit;
}

// Delete user if delete button is clicked
if (isset($_POST['delete'])) {
    $id = $_POST['user_id'];
    
    // Delete associated records in the 'pembayaran' table
    $delete_pemayaran_query = "DELETE FROM pembayaran WHERE user_id = $id";
    mysqli_query($conn, $delete_pemayaran_query);
    
    // Now, delete the user
    $delete_query = "DELETE FROM user_form WHERE id = $id";
    mysqli_query($conn, $delete_query);
    
    // Redirect to refresh the page after deletion
    header('Location: user_list.php');
    exit;
}

// Fetch list of users with user_type 'nasabah'
$query = "SELECT * FROM user_form WHERE user_type = 'nasabah'";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User List - Admin</title>

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">
   <div class="content">
      <h3>User List - Admin</h3>
      <table>
         <thead>
            <tr>
               <th>ID</th>
               <th>Name</th>
               <th>Email</th>
               <th>Gender</th>
               <th>Address</th>
               <th>DoB</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
               <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo $row['name']; ?></td>
                  <td><?php echo $row['email']; ?></td>
                  <td><?php echo $row['gender']; ?></td>
                  <td><?php echo $row['address']; ?></td>
                  <td><?php echo $row['DoB']; ?></td>
                  <td>
                     <form action="" method="post">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
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
