<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already started
}
if (isset($_SESSION['username'])) {
    echo '<header>
    <img src="../components/pics/menu.png" alt="" id="humbergericon" />
    <img src="../components/pics/stack-overflow-3.png" alt="ll" id="SOFLogo" />
    <span id="words">
        <a href="">about</a>
        <a href="">Products</a>
        <a href="">for Teams</a>
    </span>
    <form action="../questions_page/questions.php" method="post" id="search_form">
        <input type="text" name="Searched_Value" id="Search" placeholder="Search" width="400px" />
        </form>
        <form action="../questions_page/questions.php" method="post" id="search_AQ">
        <input type="submit" value="Sign out" id="Signout" name="SignOut" />
        <input type="submit" value="My questions" id="" class="purple_button" name="MyQ"/>
        <input type="submit" value="My answer" id="my_temp_answers" class="white_button" name="MyA" />
        </form>
   
</header>';
if(isset($_POST['SignOut'])){
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Start the session if it's not already started
    }
    session_destroy();
    header("Location: 	../questions_page/questions.php"); // Redirect to the welcome page
exit();
}
} else { 
   
    echo '<header>
        <img src="../components/pics/menu.png" alt="" id="humbergericon" />
        <img src="../components/pics/stack-overflow-3.png" alt="ll" id="SOFLogo" />
        <span id="words">
            <a href="">about</a>
            <a href="">Products</a>
            <a href="">for Teams</a>
        </span>
        <form action="../questions_page/questions.php" method="get" id="search_form">
            <input type="text" name="Searched_Value" id="Search" placeholder="Search" width="400px" />
        </form>
        <input type="button" value="Log in" id="Login" />
        <input type="button" value="Sign up" id="Signin" />
    </header>';
}
?>