
<!DOCTYPE html>
<html lang="en">
<head>
<?php
 session_start(); // Start the session to use session variables
if(isset($_POST['Ssubmit']))
 if (isset($_POST["Susername"]) && isset($_POST["Spassword"]) && $_POST["Susername"]!=null && $_POST["Spassword"]!=null) {

    // echo"<script>window.alert('fill info correctly!!');</script>";

    $email = $_POST["Susername"];
    $password = $_POST["Spassword"];
     $host = "localhost"; // Hostname
     $dbUsername = "root"; // Username for the database
     $dbPassword = ""; // Password (leave empty for root)
     $database = "users"; // Database name

     try {
         $conn = new PDO("mysql:host=$host;dbname=$database", $dbUsername, $dbPassword);
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //  echo "Connected successfully";

         // Using prepared statements to prevent SQL Injection
         $stmt = $conn->prepare("INSERT INTO user (username, password) VALUES (:email, :password)");
         $stmt->bindParam(':email', $email);
         $stmt->bindParam(':password', $password);

         if ($stmt->execute()) {
             $_SESSION['username'] = $email;
             header("Location: ../questions_page/questions.php"); // Redirect to the questions page
             exit();
         }
     } catch(PDOException $e) {
         if ($e->getCode() == 23000) { // Handle duplicate entry which means user already exists
            echo"<script>window.alert('User already exists!!');</script>";
         } else {
             echo "Connection failed: " . $e->getMessage();
         }
     }
 }
else{
    echo"<script>window.alert('fill info correctly!!');</script>";
}
 ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../home_page/styles.css">
    <link rel="stylesheet" href="../logIn_signIn/SLstyles.css">
</head>
<body>
<?php 
     session_start();
     if(isset($_SESSION['errorAsk'])&&$_SESSION['errorAsk']="t")
     echo "<script> window.alert('You need to login before asking question') </script>";

     include "../components/logIn_signIn.php";

 
    ?>
 
    
   <?php 
   if(isset($_POST["Susername"])&&isset($_POST["Spassword"])){
$email = $_POST["Susername"];
$password = $_POST["Spassword"];}
?>
<!-- name of username field Susername -->
<!-- name of password field Spassword -->
<script src="../home_page/index.js"></script>
    <script src="../logIn_signIn/SL.js"></script>
</body>
</html>
