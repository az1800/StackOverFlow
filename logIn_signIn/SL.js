document.addEventListener("DOMContentLoaded", setupFormAttributes);

function setupFormAttributes() {
  const header = document.getElementById("signup_login");
  const checkbox = document.getElementById("checkboxSL");
  const form = document.getElementById("login_signin_form");
  const path = window.location.pathname;
  const page = path.split("/").pop();

  if (page === "log_in.php") {
    header.innerHTML = "Log in";
    checkbox.checked = true;
    form.setAttribute("method", "POST");
    form.setAttribute("action", "../logIn_signIn/log_in.php");
    document.getElementById("username_field").setAttribute("name", "Lusername");
    document.getElementById("password_field").setAttribute("name", "Lpassword");
    document.getElementById("submit_btn").setAttribute("name", "Lsubmit");
  } else if (page === "sign_up.php") {
    header.innerHTML = "Sign Up";
    checkbox.checked = false;
    form.setAttribute("method", "POST");
    form.setAttribute("action", "../logIn_signIn/sign_up.php");
    document.getElementById("username_field").setAttribute("name", "Susername");
    document.getElementById("password_field").setAttribute("name", "Spassword");
    document.getElementById("submit_btn").setAttribute("name", "Ssubmit");
  } else {
    console.log("Unknown page:", page);
  }
}

function submitForm() {
  const form = document.getElementById("login_signin_form");
  form.submit();
}
