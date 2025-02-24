const sign_in_btn = document.querySelector("#sign-in-btn");
const forgot_password_btn = document.querySelector("#forgot-password-btn");
const container = document.querySelector(".container");

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
  container.classList.remove("forgot-password-mode");
});

forgot_password_btn.addEventListener("click", () => {
  container.classList.add("forgot-password-mode");
  container.classList.remove("sign-up-mode");
});




// Sign up form submission
document.getElementById('registerForm').onsubmit = function(event) {
    event.preventDefault(); // Prevent form from submitting normally
    
    // Send form data using fetch
    fetch('signup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(this)).toString() // Send form data
    })
    .catch(error => {
        console.error("Error:", error);
    });
  };
  
// Login form submission
document.getElementById('loginForm').onsubmit = function(event) {
    event.preventDefault();  // Prevent default form submission
  
    // Send form data using fetch
    fetch('signin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(this)).toString() // Send form data
    })
    .catch(error => {
        console.error("Error:", error);  // Log any errors
    });
  };
  
  