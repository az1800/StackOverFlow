<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../home_page/styles.css">
    <link rel="stylesheet" href="../ask_question/AQStyles.css">

    <title>Document</title>
</head>
<body>
    <!-- header and menu -->
    <?php
session_start();

if (isset($_SESSION['username'])) {
    if (isset($_POST['Question_title'], $_POST['Question_description'])) {
        if (isset($_POST['Question_submit'])) {
            if (!empty($_POST['Question_title']) && !empty($_POST['Question_description'])) {
                $title = $_POST['Question_title'];
                $description = $_POST['Question_description'];
                $user = $_SESSION['username'];

                $host = "localhost"; // Hostname
                $dbname = "users"; // Database name
                $username = "root"; // Database username
                $password = ""; // Database password (leave empty for root user)

                try {
                    // Create a new PDO connection
                    $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Prepare and execute the SQL statement
                    $stmt = $connection->prepare("INSERT INTO `question` (`title`, `description`, `username`) VALUES (:title, :description, :username)");
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':username', $user);
                    $stmt->execute();

                    // Redirect to the questions page after successful insertion
                    header("Location: ../questions_page/questions.php");
                    exit();
                } catch (PDOException $e) {
                    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                }
            } else {
                echo "<script> window.alert('Please fill in all required fields.'); </script>";
            }
        }
    }
} else {
    echo "<script> window.alert('You are not logged in.'); </script>";
    header("Location: ../logIn_signIn/log_in.php");
    exit();
}

include "../components/header.php";
include "../components/menu.php";
?>
    <h2 id="ask_qiestion" class="">Ask Question</h2>
    <form action="../ask_question/indexx.php" method="post">
    <div id="title" class="purple_card"> 
        <label id="titlee" class="leftM f-21">Title:</label> <br>
        <input type="text" name="Question_title" id="title_bar">
    </div>
    <div id="description_" class="purple_card">
        <label  class="leftM f-21">enter the description:</label><br>
        <div class="center"><textarea name="Question_description" id="quetion_description_ask" cols="30" rows="10"></textarea></div>
    </div>
    <input type="submit" value="submit" id="submotB" name="Question_submit">
    </form>
    <section></section>
    <script src="../home_page/index.js"></script>
</body>
</html>
<?php 

    ?>