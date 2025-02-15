//sign up and sign in form
const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
});

//PHP FETCH CODE
document.getElementById('registerForm').onsubmit = function(event) {
    event.preventDefault();
    fetch('signup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(this)).toString()
    })
    .then(response => response.json())
    .then(data => alert(data.message));
};

document.getElementById('loginForm').onsubmit = function(event) {
    event.preventDefault();
    fetch('signin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(this)).toString()
    })
    .then(response => response.json())
    .then(data => alert(data.message));
};

