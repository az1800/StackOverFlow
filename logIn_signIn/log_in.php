<!DOCTYPE html>
<html lang="en">
<head>
<?php 
     session_start();

     if (isset($_POST["Lusername"]) && isset($_POST["Lpassword"])) {
         $email = $_POST["Lusername"];
         $password = $_POST["Lpassword"];

         if (!empty($email) && !empty($password)) {
             $host = "localhost"; // Hostname
             $dbUsername = "root"; // Database Username
             $dbPassword = ""; // Database Password (empty for root)
             $database = "users"; // Database Name

             try {
                 $conn = new PDO("mysql:host=$host;dbname=$database", $dbUsername, $dbPassword);
                 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                 // Prepared statement to prevent SQL injection
                 $stmt = $conn->prepare("SELECT password FROM user WHERE username = :username");
                 $stmt->bindParam(':username', $email);
                 $stmt->execute();

                 if ($stmt->rowCount() > 0) {
                     $user = $stmt->fetch(PDO::FETCH_ASSOC);

                     if ($user && $user['password'] === $password) {
                         $_SESSION['username'] = $email; // Ensure session variable matches with the signup page
                         header("Location: ../questions_page/questions.php"); // Redirect to the questions page
                         exit();
                     } else {
                         $login_error = "Incorrect password.";
                     }
                 } else {
                     $login_error = "No user exists with that username.";
                 }
             } catch (PDOException $e) {
                 $login_error = "Connection failed: " . $e->getMessage();
             }
         } else {
             $login_error = "Please fill in all information!";
         }
     }
     ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../home_page/styles.css">
    <link rel="stylesheet" href="../logIn_signIn/SLstyles.css">
</head>
<body>
<?php if(isset($login_error)) echo "<script>window.alert('$login_error');</script>"; ?>
     <?php include "../components/logIn_signIn.php"; ?>
     <script src="../home_page/index.js"></script>
     <script src="../logIn_signIn/SL.js"></script>
</body>
</html>
