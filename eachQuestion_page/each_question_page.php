<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../home_page/styles.css">
    <link rel="stylesheet" href="../eachQuestion_page/EQStyles.css">
    <link rel="stylesheet" href="../questions_page/Qstyles.css">
    <?php
session_start();
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "users";

if (isset($_POST['Qid']) && !empty($_POST['Qid'])) {
    $_SESSION['Qid'] = $_POST['Qid'];
}

if (isset($_SESSION['Qid']) && !empty($_SESSION['Qid'])) {
    $qid = $_SESSION['Qid'];
} else {
    die("Question ID is required");
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve answers and their average ratings
    $sql = "SELECT question.QID, question.title, question.description, answer.text, answer.AID, AVG(rate.rate) as average_rating
            FROM question
            JOIN answer ON question.QID = answer.QID
            LEFT JOIN rate ON answer.AID = rate.AID
            WHERE question.QID = :qid
            GROUP BY answer.AID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':qid', $qid, PDO::PARAM_INT);
    $stmt->execute();
    $answersResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve all details of a single question
    $sqlAll = "SELECT * FROM question WHERE question.QID = :qid";
    $stmtAll = $conn->prepare($sqlAll);
    $stmtAll->bindParam(':qid', $qid, PDO::PARAM_INT);
    $stmtAll->execute();
    $questionResults = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    $questionUser = $questionResults[0]['username'];

    // Retrieve comments on the question
    $retrieveQCsql = "SELECT `text`, `username` FROM `qcomment` WHERE QID = :qid";
    $stmtrcq = $conn->prepare($retrieveQCsql);
    $stmtrcq->bindParam(':qid', $qid, PDO::PARAM_INT);
    $stmtrcq->execute();
    $rcqResults = $stmtrcq->fetchAll(PDO::FETCH_ASSOC);

    // Handle answer submission
    if (isset($_POST['submitEQ']) && isset($_POST['user_answer']) && !empty($_POST['user_answer'])) {
        if (!isset($_SESSION['username'])) {
            echo "<script>
                    alert('You must sign up/in.');
                    window.location.href = '../logIn_signIn/sign_up.php';
                  </script>";
            exit();
        }
        $user_answer = $_POST['user_answer'];
        $username = $_SESSION['username'];
        $addAnswerSql = "INSERT INTO `answer` (`AID`, `QID`, `text`, `username`) VALUES (NULL, :qid, :useranswer, :username)";
        $stmtQ = $conn->prepare($addAnswerSql);
        $stmtQ->bindParam(':qid', $qid, PDO::PARAM_INT);
        $stmtQ->bindParam(':useranswer', $user_answer);
        $stmtQ->bindParam(':username', $username);
        $stmtQ->execute();
    }

    // Handle comment submission
    if (isset($_POST['submitEQ']) && isset($_POST['comment_to_question']) && !empty($_POST['comment_to_question'])) {
        if (!isset($_SESSION['username'])) {
            echo "<script>
                    alert('You must sign up/in.');
                    window.location.href = '../logIn_signIn/sign_up.php';
                  </script>";
            exit();
        }
        $user_comment_onQ = $_POST['comment_to_question'];
        $username = $_SESSION['username'];
        $addCommentsql = "INSERT INTO `qcomment` (`QID`, `username`, `text`) VALUES (:qid, :username, :userCommentQnQ)";
        $stmtCQ = $conn->prepare($addCommentsql);
        $stmtCQ->bindParam(':qid', $qid, PDO::PARAM_INT);
        $stmtCQ->bindParam(':userCommentQnQ', $user_comment_onQ);
        $stmtCQ->bindParam(':username', $username);
        $stmtCQ->execute();
    }

    // Handle question update
    if (isset($_POST['hiddenSub'])) {
        if (!isset($_SESSION['username'])) {
            echo "<script>
                    alert('You must sign up/in.');
                    window.location.href = '../logIn_signIn/sign_up.php';
                  </script>";
            exit();
        }
        if (strtolower($_SESSION['username']) !== strtolower($questionUser)) {
            echo "<script>
                    alert('You should edit your own questions');
                    window.location.href = '../questions_page/questions.php';
                  </script>";
            exit();
        }

        if (isset($_POST['Q_description']) && !empty($_POST['Q_description'])) {
            $description = $_POST['Q_description'];
            $sqlUpdateQ = "UPDATE question SET `description` = :description WHERE QID = :qid";
            $stmt = $conn->prepare($sqlUpdateQ);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':qid', $qid);
            $stmt->execute();
        }
        if (isset($_POST['Q_title']) && !empty($_POST['Q_title'])) {
            $title = $_POST['Q_title'];
            $sqlUpdateQ = "UPDATE question SET `title` = :title WHERE QID = :qid";
            $stmt = $conn->prepare($sqlUpdateQ);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':qid', $qid);
            $stmt->execute();
        }
    }

    // Handle question deletion
    if (isset($_POST['hiddenDeleteQ'])) {
        if (!isset($_SESSION['username'])) {
            echo "<script>
                    alert('You must sign up/in.');
                    window.location.href = '../logIn_signIn/sign_up.php';
                  </script>";
            exit();
        }
        if (strtolower($_SESSION['username']) !== strtolower($questionUser)) {
            echo "<script>
                    alert('You should delete your own questions');
                    window.location.href = '../questions_page/questions.php';
                  </script>";
            exit();
        }

        try {
            $conn->beginTransaction();

            // Delete ratings associated with the answers of the question
            $deleteRateSql = "DELETE rate FROM rate 
                              JOIN answer ON rate.AID = answer.AID 
                              WHERE answer.QID = :qid";
            $stmtRate = $conn->prepare($deleteRateSql);
            $stmtRate->bindParam(':qid', $qid, PDO::PARAM_INT);
            $stmtRate->execute();

            // Delete comments on the answers of the question
            $deleteACommentSql = "DELETE acomment FROM acomment 
                                  JOIN answer ON acomment.AID = answer.AID 
                                  WHERE answer.QID = :qid";
            $stmtAComment = $conn->prepare($deleteACommentSql);
            $stmtAComment->bindParam(':qid', $qid, PDO::PARAM_INT);
            $stmtAComment->execute();

            // Delete answers associated with the question
            $deleteAnswerSql = "DELETE FROM answer WHERE QID = :qid";
            $stmtAnswer = $conn->prepare($deleteAnswerSql);
            $stmtAnswer->bindParam(':qid', $qid, PDO::PARAM_INT);
            $stmtAnswer->execute();

            // Delete comments on the question
            $deleteQCommentSql = "DELETE FROM qcomment WHERE QID = :qid";
            $stmtQComment = $conn->prepare($deleteQCommentSql);
            $stmtQComment->bindParam(':qid', $qid, PDO::PARAM_INT);
            $stmtQComment->execute();

            // Delete the question itself
            $deleteQuestionSql = "DELETE FROM question WHERE QID = :qid";
            $stmtQuestion = $conn->prepare($deleteQuestionSql);
            $stmtQuestion->bindParam(':qid', $qid, PDO::PARAM_INT);
            $stmtQuestion->execute();

            $conn->commit();

            echo "<script>
                    alert('Question and related comments, answers, and ratings deleted successfully.');
                    window.location.href = '../questions_page/questions.php';
                  </script>";
        } catch (Exception $e) {
            $conn->rollBack();
            echo "<script>
                    alert('Failed to delete question and associated data: " . $e->getMessage() . "');
                    window.location.href = '../questions_page/questions.php';
                  </script>";
        }
    }

    // Handle answer deletion
    if (isset($_POST['delete_answer'])) {
        $answerId = $_POST['answer_iddd'];

        try {
            $conn->beginTransaction();

            // Check if the current user is the owner of the answer
            $stmt = $conn->prepare("SELECT username FROM answer WHERE AID = :aid");
            $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
            $stmt->execute();
            $answer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($answer && strtolower($answer['username']) === strtolower($_SESSION['username'])) {
                // Delete associated comments
                $stmt = $conn->prepare("DELETE FROM acomment WHERE AID = :aid");
                $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
                $stmt->execute();

                // Delete associated ratings
                $stmt = $conn->prepare("DELETE FROM rate WHERE AID = :aid");
                $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
                $stmt->execute();

                // Delete the answer itself
                $stmt = $conn->prepare("DELETE FROM answer WHERE AID = :aid");
                $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
                $stmt->execute();

                $conn->commit();
                echo "<script>window.alert('Answer with ID $answerId and all associated records have been deleted.');</script>";
            } else {
                echo "<script>window.alert('You can only delete your own answers.');</script>";
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "Error deleting record: " . $e->getMessage();
        }
    }

    // Handle answer edit
    if (isset($_POST['edit_answer'])) {
        $answerId = $_POST['answer_iddd'];
        $newAnswer = $_POST['edited_answer'] ?? null;

        if ($newAnswer !== null) {
            // Check if the current user is the owner of the answer
            $stmt = $conn->prepare("SELECT username FROM answer WHERE AID = :aid");
            $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
            $stmt->execute();
            $answer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($answer && strtolower($answer['username']) === strtolower($_SESSION['username'])) {
                try {
                    $stmt = $conn->prepare("UPDATE answer SET text = :text WHERE AID = :aid");
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
                echo "<script>window.alert('You can only edit your own answers.');</script>";
            }
        } else {
            echo "<script>window.alert('Edited answer cannot be null');</script>";
        }
    }

    // Handle rating submission
    if (isset($_POST['rating']) && isset($_POST['answer_iddd'])) {
        $rating = $_POST['rating'];
        $answerId = $_POST['answer_iddd'];

        if (!isset($_SESSION['username'])) {
            echo "<script>
                    alert('You must sign up/in.');
                    window.location.href = '../logIn_signIn/sign_up.php';
                  </script>";
            exit();
        }

        $username = $_SESSION['username'];

        try {
            // Check if the user has already rated this answer
            $stmt = $conn->prepare("SELECT * FROM rate WHERE AID = :aid AND username = :username");
            $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $existingRating = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingRating) {
                // Update existing rating
                $stmt = $conn->prepare("UPDATE rate SET rate = :rate WHERE AID = :aid AND username = :username");
                $stmt->bindParam(':rate', $rating, PDO::PARAM_INT);
                $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                // Insert new rating
                $stmt = $conn->prepare("INSERT INTO rate (AID, username, rate) VALUES (:aid, :username, :rate)");
                $stmt->bindParam(':rate', $rating, PDO::PARAM_INT);
                $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
            }

            echo "<script>window.alert('Rating submitted successfully.');</script>";
        } catch (PDOException $e) {
            echo "Error submitting rating: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


</head>
<body>
    <?php include '../components/header.php'; ?>
    <?php include "../components/menu.php"; ?>
    
    <form action="../eachQuestion_page/each_question_page.php" method="post" id="EQ_Form">
        <div id="divxx">
            <input type="hidden" name="Q_id" id="Q_id">
            <h2 id="title_of_question" style="position: absolute; left: 69px; top: 210px;">How do I undo the most recent local commits in Git?</h2>
            <input type="hidden" name="Q_title" id="Q_title">
            <p id="Qdescription" class="max litttle_bit_marginL"></p>
            <input type="hidden" name="Q_description" id="Q_description">
            <h2 style="position: absolute; left: 71px; top: 158px;">Comments</h2>
            <p id="question_comments" class="litttle_bit_marginL">Lorem ipsum dolor sit Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nulla impedit provident harum rerum eos mollitia beatae vero, delectus vitae sit eligendi, hic ex perspiciatis pariatur saepe? Natus accusamus ipsum facilis. Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab nihil accusamus saepe qui sint iste adipisci eligendi hic magni! Odio dolorum animi neque sed deserunt illum magnam molestiae nobis asperiores Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti aspernatur libero corrupti fugit? Quas voluptatem iusto, voluptates ipsam tempora pariatur. Qui amet possimus fugit repellat dolores veniam ex vel ipsum dolor, sit amet consectetur adipisicing elit. Nulla impedit provident harum rerum eos mollitia beatae vero, delectus vitae sit eligendi, hic ex perspiciatis pariatur saepe? Natus accusamus ipsum facilis. Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab nihil accusamus saepe qui sint iste adipisci eligendi hic magni! Odio dolorum animi neque sed deserunt illum magnam molestiae nobis asperiores Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti aspernatur libero corrupti fugit? Quas voluptatem iusto, voluptates ipsam tempora pariatur. Qui amet possimus fugit repellat dolores veniam ex vel quidem quidem!</p>

            <img src="../components/pics/delete.png" alt="" id="delete_question">
            <input type="hidden" id="Deleted_question_id" name="Deleted_question_id">
            <img src="../components/pics/edit.png" alt="" id="edit_question">
            <input type="submit" value="" id="hidden_S" name="Q_E_Submit">
        </div>
        
        <h2 id="comment_on_answer">Add comment to the question</h2>
        <textarea name="comment_to_question" id="comment_area" cols="30" rows="10"></textarea>

        <div id="answer_">
            <h2 id="your_answer">Your answer</h2>
            <textarea name="user_answer" id="answer_area" cols="30" rows="10"></textarea>
            <?php include "../components/stars.html";?>
        </div>
        <input type="hidden" name="user_id" id="user_id_data">
        <input type="hidden" name="user_answerr" id="user_answer_data">
        <input type="hidden" name="user_comment" id="user_comment_data">
        <input type="hidden" name="answer_id" id="answer_id_data">
        <input type="hidden" name="answer_rating" id="answer_rating_data">
        <input type="submit" value="Submit" name="hiddenSub" id="hiddenSub" style="display: none;">
        <input type="submit" value="Delete" name="hiddenDeleteQ" id="hiddenDeleteQ" style="display: none;">
    </form>

    <script>
        var answerss = <?php echo json_encode($answersResults); ?>;
        var questions = <?php echo json_encode($questionResults); ?>;
        var questionC = <?php echo json_encode($rcqResults); ?>;
    </script>
    <script src="../home_page/index.js"></script>
    <script src="../eachQuestion_page/EQ.js"></script>
</body>
</html>
<?php 
// session_start();
// $servername = "127.0.0.1"; 
// $username = "root";
// $password = "";
// $dbname = "users";

// if (isset($_POST['Qid']) && !empty($_POST['Qid'])) {
//     $_SESSION['Qid'] = $_POST['Qid'];
// }

// if (isset($_SESSION['Qid']) && !empty($_SESSION['Qid'])) {
//     $qid = $_SESSION['Qid'];
// } else {
//     die("Question ID is required");
// }

// try {
//     $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//     // Retrieve answers and their average ratings
//     $sql = "SELECT question.QID, question.title, question.description, answer.text, answer.AID, AVG(rate.rate) as average_rating
//             FROM question
//             JOIN answer ON question.QID = answer.QID
//             LEFT JOIN rate ON answer.AID = rate.AID
//             WHERE question.QID = :qid
//             GROUP BY answer.AID";
//     $stmt = $conn->prepare($sql);
//     $stmt->bindParam(':qid', $qid, PDO::PARAM_INT);
//     $stmt->execute();
//     $answersResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     // Retrieve all details of a single question
//     $sqlAll = "SELECT * FROM question WHERE question.QID = :qid";
//     $stmtAll = $conn->prepare($sqlAll);
//     $stmtAll->bindParam(':qid', $qid, PDO::PARAM_INT);
//     $stmtAll->execute();
//     $questionResults = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
//     $questionUser = $questionResults[0]['username'];

//     // Retrieve comments on the question
//     $retrieveQCsql = "SELECT `text`, `username` FROM `qcomment` WHERE QID = :qid";
//     $stmtrcq = $conn->prepare($retrieveQCsql);
//     $stmtrcq->bindParam(':qid', $qid, PDO::PARAM_INT);
//     $stmtrcq->execute();
//     $rcqResults = $stmtrcq->fetchAll(PDO::FETCH_ASSOC);

//     // Handle answer submission
//     if (isset($_POST['submitEQ']) && isset($_POST['user_answer']) && !empty($_POST['user_answer'])) {
//         if (!isset($_SESSION['username'])) {
//             echo "<script>
//                     alert('You must sign up/in.');
//                     window.location.href = '../logIn_signIn/sign_up.php';
//                   </script>";
//             exit();
//         }
//         $user_answer = $_POST['user_answer'];
//         $username = $_SESSION['username'];
//         $addAnswerSql = "INSERT INTO `answer` (`AID`, `QID`, `text`, `username`) VALUES (NULL, :qid, :useranswer, :username)";
//         $stmtQ = $conn->prepare($addAnswerSql);
//         $stmtQ->bindParam(':qid', $qid, PDO::PARAM_INT);
//         $stmtQ->bindParam(':useranswer', $user_answer);
//         $stmtQ->bindParam(':username', $username);
//         $stmtQ->execute();
//     }

//     // Handle comment submission
//     if (isset($_POST['submitEQ']) && isset($_POST['comment_to_question']) && !empty($_POST['comment_to_question'])) {
//         if (!isset($_SESSION['username'])) {
//             echo "<script>
//                     alert('You must sign up/in.');
//                     window.location.href = '../logIn_signIn/sign_up.php';
//                   </script>";
//             exit();
//         }
//         $user_comment_onQ = $_POST['comment_to_question'];
//         $username = $_SESSION['username'];
//         $addCommentsql = "INSERT INTO `qcomment` (`QID`, `username`, `text`) VALUES (:qid, :username, :userCommentQnQ)";
//         $stmtCQ = $conn->prepare($addCommentsql);
//         $stmtCQ->bindParam(':qid', $qid, PDO::PARAM_INT);
//         $stmtCQ->bindParam(':userCommentQnQ', $user_comment_onQ);
//         $stmtCQ->bindParam(':username', $username);
//         $stmtCQ->execute();
//     }

//     // Handle question update
//     if (isset($_POST['hiddenSub'])) {
//         if (!isset($_SESSION['username'])) {
//             echo "<script>
//                     alert('You must sign up/in.');
//                     window.location.href = '../logIn_signIn/sign_up.php';
//                   </script>";
//             exit();
//         }
//         if (strtolower($_SESSION['username']) !== strtolower($questionUser)) {
//             echo "<script>
//                     alert('You should edit your own questions');
//                     window.location.href = '../questions_page/questions.php';
//                   </script>";
//             exit();
//         }

//         if (isset($_POST['Q_description']) && !empty($_POST['Q_description'])) {
//             $description = $_POST['Q_description'];
//             $sqlUpdateQ = "UPDATE question SET `description` = :description WHERE QID = :qid";
//             $stmt = $conn->prepare($sqlUpdateQ);
//             $stmt->bindParam(':description', $description);
//             $stmt->bindParam(':qid', $qid);
//             $stmt->execute();
//         }
//         if (isset($_POST['Q_title']) && !empty($_POST['Q_title'])) {
//             $title = $_POST['Q_title'];
//             $sqlUpdateQ = "UPDATE question SET `title` = :title WHERE QID = :qid";
//             $stmt = $conn->prepare($sqlUpdateQ);
//             $stmt->bindParam(':title', $title);
//             $stmt->bindParam(':qid', $qid);
//             $stmt->execute();
//         }
//     }

//     // Handle question deletion
//     if (isset($_POST['hiddenDeleteQ'])) {
//         if (!isset($_SESSION['username'])) {
//             echo "<script>
//                     alert('You must sign up/in.');
//                     window.location.href = '../logIn_signIn/sign_up.php';
//                   </script>";
//             exit();
//         }
//         if (strtolower($_SESSION['username']) !== strtolower($questionUser)) {
//             echo "<script>
//                     alert('You should delete your own questions');
//                     window.location.href = '../questions_page/questions.php';
//                   </script>";
//             exit();
//         }

//         try {
//             $conn->beginTransaction();

//             // Delete ratings associated with the answers of the question
//             $deleteRateSql = "DELETE rate FROM rate 
//                               JOIN answer ON rate.AID = answer.AID 
//                               WHERE answer.QID = :qid";
//             $stmtRate = $conn->prepare($deleteRateSql);
//             $stmtRate->bindParam(':qid', $qid, PDO::PARAM_INT);
//             $stmtRate->execute();

//             // Delete comments on the answers of the question
//             $deleteACommentSql = "DELETE acomment FROM acomment 
//                                   JOIN answer ON acomment.AID = answer.AID 
//                                   WHERE answer.QID = :qid";
//             $stmtAComment = $conn->prepare($deleteACommentSql);
//             $stmtAComment->bindParam(':qid', $qid, PDO::PARAM_INT);
//             $stmtAComment->execute();

//             // Delete answers associated with the question
//             $deleteAnswerSql = "DELETE FROM answer WHERE QID = :qid";
//             $stmtAnswer = $conn->prepare($deleteAnswerSql);
//             $stmtAnswer->bindParam(':qid', $qid, PDO::PARAM_INT);
//             $stmtAnswer->execute();

//             // Delete comments on the question
//             $deleteQCommentSql = "DELETE FROM qcomment WHERE QID = :qid";
//             $stmtQComment = $conn->prepare($deleteQCommentSql);
//             $stmtQComment->bindParam(':qid', $qid, PDO::PARAM_INT);
//             $stmtQComment->execute();

//             // Delete the question itself
//             $deleteQuestionSql = "DELETE FROM question WHERE QID = :qid";
//             $stmtQuestion = $conn->prepare($deleteQuestionSql);
//             $stmtQuestion->bindParam(':qid', $qid, PDO::PARAM_INT);
//             $stmtQuestion->execute();

//             $conn->commit();

//             echo "<script>
//                     alert('Question and related comments, answers, and ratings deleted successfully.');
//                     window.location.href = '../questions_page/questions.php';
//                   </script>";
//         } catch (Exception $e) {
//             $conn->rollBack();
//             echo "<script>
//                     alert('Failed to delete question and associated data: " . $e->getMessage() . "');
//                     window.location.href = '../questions_page/questions.php';
//                   </script>";
//         }
//     }

//     // Handle answer deletion
//     if (isset($_POST['delete_answer'])) {
//         $answerId = $_POST['answer_iddd'];

//         try {
//             $conn->beginTransaction();

//             // Check if the current user is the owner of the answer
//             $stmt = $conn->prepare("SELECT username FROM answer WHERE AID = :aid");
//             $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
//             $stmt->execute();
//             $answer = $stmt->fetch(PDO::FETCH_ASSOC);

//             if ($answer && strtolower($answer['username']) === strtolower($_SESSION['username'])) {
//                 // Delete associated comments
//                 $stmt = $conn->prepare("DELETE FROM acomment WHERE AID = :aid");
//                 $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
//                 $stmt->execute();

//                 // Delete associated ratings
//                 $stmt = $conn->prepare("DELETE FROM rate WHERE AID = :aid");
//                 $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
//                 $stmt->execute();

//                 // Delete the answer itself
//                 $stmt = $conn->prepare("DELETE FROM answer WHERE AID = :aid");
//                 $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
//                 $stmt->execute();

//                 $conn->commit();
//                 echo "<script>window.alert('Answer with ID $answerId and all associated records have been deleted.');</script>";
//             } else {
//                 echo "<script>window.alert('You can only delete your own answers.');</script>";
//             }
//         } catch (PDOException $e) {
//             $conn->rollBack();
//             echo "Error deleting record: " . $e->getMessage();
//         }
//     }

//     // Handle answer edit
//     if (isset($_POST['edit_answer'])) {
//         $answerId = $_POST['answer_iddd'];
//         $newAnswer = $_POST['edited_answer'] ?? null;

//         if ($newAnswer !== null) {
//             // Check if the current user is the owner of the answer
//             $stmt = $conn->prepare("SELECT username FROM answer WHERE AID = :aid");
//             $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
//             $stmt->execute();
//             $answer = $stmt->fetch(PDO::FETCH_ASSOC);

//             if ($answer && strtolower($answer['username']) === strtolower($_SESSION['username'])) {
//                 try {
//                     $stmt = $conn->prepare("UPDATE answer SET text = :text WHERE AID = :aid");
//                     $stmt->bindParam(':text', $newAnswer, PDO::PARAM_STR);
//                     $stmt->bindParam(':aid', $answerId, PDO::PARAM_INT);
//                     if ($stmt->execute()) {
//                         echo "<script>window.alert('Answer with ID $answerId has been updated to: $newAnswer');</script>";
//                     } else {
//                         echo "Error updating record: " . $stmt->errorInfo()[2];
//                     }
//                 } catch (PDOException $e) {
//                     echo "Database error: " . $e->getMessage();
//                 }
//             } else {
//                 echo "<script>window.alert('You can only edit your own answers.');</script>";
//             }
//         } else {
//             echo "<script>window.alert('Edited answer cannot be null');</script>";
//         }
//     }
// } catch (PDOException $e) {
//     echo "Error: " . $e->getMessage();
// }
?>
