document.addEventListener("DOMContentLoaded", function () {
  const menu = document.getElementById("humbergericon");
  const card = document.getElementById("card1");
  menu.addEventListener("click", () => {
    card.classList.toggle("active");
  });
});
if (document.getElementById("Signin")) {
  document.getElementById("Signin").addEventListener("click", () => {
    window.location.href = "../logIn_signIn/sign_up.php";
  });
  document.getElementById("Login").addEventListener("click", () => {
    window.location.href = "../logIn_signIn/log_in.php";
  });
}
