
    <?php 
    include "../components/header.php";
    ?>
    <?php include "../components/menu.php"; ?>
    <form id="login_signin_form" method="POST" action="log_in.php">
    <h1 id="signup_login">
        log in
    </h1>
        <input type="text" placeholder="enter your username:"  id="username_field" name="username">
        <input type="password" placeholder="enter your password:" id="password_field" name="passwords">
        <input type="submit" value="submit" id="submit_btn" onclick="submitForm()" name="submit_btn">
        <input type="checkbox" name="isloginorsignup" id="checkboxSL" style="display: none;" >
    </form>

