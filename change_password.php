<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
@include 'config.php';

echo "Change Password Page"; // Debugging message

if(isset($_POST['submit'])) {
    echo "Form submitted"; // Debugging message
    
    $current_password = md5($_POST['current_password']);
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);
    
    $user_id = $_SESSION['user_id'];
    
    // Fetch the current password from the database
    $query = "SELECT password FROM user_form WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $db_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    // Verify if the current password matches the password in the database
    if ($db_password == $current_password) {
        // Verify if the new password and confirm password match
        if ($new_password == $confirm_password) {
            // Update the password in the database
            $update_query = "UPDATE user_form SET password = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "si", $new_password, $user_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
            
            echo "Password changed successfully."; // Debugging message
            // Redirect back to user_page.php after successful password change
            header('Location: user_page.php');
            exit();
        } else {
            echo "New password and confirm password do not match."; // Debugging message
        }
    } else {
        echo "Current password is incorrect."; // Debugging message
    }
}
?>
