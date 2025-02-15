// Sign up and Sign in form toggle
const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
});


// Sign up form submission
document.getElementById('registerForm').onsubmit = function(event) {
  event.preventDefault(); // Prevent form from submitting normally
  fetch('signup.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(new FormData(this)).toString() // Send form data
  })
  .then(response => response.json())
  .then(data => {
      if (data.status === "success") {
          alert(data.message); // Display success message
          window.location.href = 'signin.php'; // Redirect to signin page after signup
      } else {
          alert(data.message); // Display the error message as a popup
      }
  })
  .catch(error => {
      console.error("Error:", error);
  });
};

// Login form submission
document.getElementById('loginForm').onsubmit = function(event) {
  event.preventDefault();  // Prevent default form submission
  fetch('signin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(new FormData(this)).toString()
  })
  .then(response => response.json())
  .then(data => {
      if (data.status === 'success') {
          alert("Login successful! Redirecting to home...");
          window.location.href = 'home.html'; // Redirect to home page after login
      } else {
          alert(data.message); // Display the error message
      }
  })
  .catch(error => {
      console.error("Error:", error);
  });
};
