<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../home_page/styles.css" />
    <link rel="stylesheet" href="../eachQuestion_page/EQStyles.css" />
    <link rel="stylesheet" href="../questions_page/Qstyles.css"/>
</head>
<?php 
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "users";

try {
    // Create a PDO instance as a database connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}

$userQuestions = [];
$userAnswers = [];

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];

    if (isset($_POST['MyQ'])) {
        try {
            $query = "SELECT * FROM question WHERE username = :username";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $user, PDO::PARAM_STR);
            $stmt->execute();
            $userQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $allQ = json_encode($userQuestions);
            echo "<script>var isAnswer = false;</script>";
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }

    if (isset($_POST['MyA'])) {
        try {
            $query = "SELECT 
                a.AID, 
                a.text AS answer_text, 
                AVG(r.rate) AS average_rating, 
                GROUP_CONCAT(DISTINCT ac.text ORDER BY ac.AID SEPARATOR '; ') AS comments
            FROM 
                answer a
            LEFT JOIN 
                rate r ON a.AID = r.AID
            LEFT JOIN 
                acomment ac ON a.AID = ac.AID
            WHERE 
                a.username = :username
            GROUP BY 
                a.AID, a.text
            ORDER BY 
                a.AID";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $user, PDO::PARAM_STR);
            $stmt->execute();
            $userAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $allQ = json_encode($userAnswers);
            echo "<script>var isAnswer = true;</script>";
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}

if (!isset($_POST['MyA']) && !isset($_POST['MyQ'])) {
    try {
        $sql = "SELECT 
            q.QID, 
            q.title, 
            q.description,
            COUNT(a.AID) AS TotalAnswers
        FROM 
            question q 
        LEFT JOIN 
            answer a ON q.QID = a.QID 
        GROUP BY 
            q.QID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            $allQ = json_encode($results);
        } else {
            echo "No data found.";
        }
        echo "<script>var isAnswer = false;</script>";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}

if (isset($_POST['delete_answer'])) {
    $answerId = $_POST['answer_iddd'];

    try {
        $pdo->beginTransaction();

        // Delete associated comments
        $stmt = $pdo->prepare("DELETE FROM acomment WHERE AID = :aid");
        $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete associated ratings
        $stmt = $pdo->prepare("DELETE FROM rate WHERE AID = :aid");
        $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the answer itself
        $stmt = $pdo->prepare("DELETE FROM answer WHERE AID = :aid");
        $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
        $stmt->execute();

        $pdo->commit();
        echo "<script>window.alert('Answer with ID $answerId and all associated records have been deleted.');</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error deleting record: " . $e->getMessage();
    }
}

if (isset($_POST['edit_answer'])) {
    $answerId = $_POST['answer_iddd'];
    $newAnswer = $_POST['edited_answer'] ?? null;

    if ($newAnswer !== null) {
        try {
            $stmt = $pdo->prepare("UPDATE answer SET text = :text WHERE AID = :aid");
            $stmt->bindParam(':text', $newAnswer, PDO::PARAM_STR);
            $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo "<script>window.alert('Answer with ID $answerId has been updated to: $newAnswer');</script>";
            } else {
                echo "Error updating record: " . $stmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "<script>window.alert('Edited answer cannot be null');</script>";
    }
}

if (isset($_POST['Searched_Value'])) {
    // Handle search logic here
}
?>


<body>
    <!-- here is the header -->
    <?php 
    include "../components/header.php";
    include "../components/static_menu.html";
    ?>

    <h1 id="allQ">All Questions</h1>
    <p id="numberOfQ">24,129,421 questions</p>
    <form action="questions.php" method="post">
        <input type="submit" value="Ask Question" id="askButton" name="askQ"/>
    </form>
    <form method="post" action="../questions_page/questions.php" id="my_answers_form">

    </form>
    

    <?php
    if (isset($_POST['askQ'])) {
        if (!isset($_SESSION['username'])) {
            $_SESSION['errorAsk'] = "t";
            header("Location:../logIn_signIn/sign_up.php");
            exit();
        } else {
            header("Location:../ask_question/indexx.php");
            exit();
        }
    }
    ?>

    <?php include "../components/programmingtips.html"; ?>
   
    
    <?php
    if (isset($_POST['MyQ'])) {
    }
    
    if (isset($_POST['MyA'])) {
        echo "<script>var answer_cards = " . json_encode($userAnswers) . ";</script>";
    }
    ?>

    <form action="../eachQuestion_page/each_question_page.php" method="post" id="questionsF">
        <input type="submit" style="display: none;" id="questionsF_S" value="submit">
    </form>
    <script>
var question_cardsss=<?php echo $allQ?>;
var answer_cards=<?php  echo json_encode($userAnswers);?>;
       
    </script>
    <script src="../home_page/index.js"></script>
    <script src="./jj.js"></script>
</body>
</html>