<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="home.css" />
    <title>Fetch & Chill</title>
  </head>   
  <body>

<!--NAVBAR-->
<nav class="navbar">
    <a href="#"><img src="img/logo.jpg.jpg" alt="Logo" class="logo"></a>
    <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#about-us">About Us</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#contact">Contact</a></li>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </ul>
</nav>


<!-- HOME SECTION -->
<section class="home" id="home">
    <div class="home-container">
        <div class="home-content">
            <h1>Hi, <span class="username"><?php echo $_SESSION['username']; ?>!</h1></span>
            <h1>Welcome to Fetch&Chill</h1>
            <p>Where every fetch ends with cuddle</p>
        </div>
        <div class="home-image">
            <img src="img/pic4.jpg" alt="Happy Dog & Cat">
        </div>
    </div>
</section>

<!-- ABOUT US SECTION -->
<section class="about-us" id="about-us">
    <div class="about-container">
        <div class="about-content">
            <h2>About us</h2>
            <p>We know life get’s  busy, and keeping up with your pet’s <br/> 
            care can be a challenge--that’s where Fetch&Chill comes in!<br/> 
            Our app helps fur parent’s schedule grooming, <br/> 
            vaccinations, trainings, and health check-ups with ease. No <br/>
             more missed vet visits or late meals--our feeding time <br/> 
             reminders, appointment manager, and medical records <br/> 
             tracker keep everything in one place. Because a happy,<br/> 
             healthy pet means more time for belly rubs, tail wags, and cuddles.
            </p>
        </div>
        <div class="about-image">
            <img src="img/pic13.jpg" alt="Happy Pet with Owner">
        </div>
    </div>

    <!-- Bottom 3 Containers -->
    <div class="about-bottom-containers">
        <div class="bottom-container">
            <img src="img/pic14.jpg" alt="Pet Care">
            <p>Pamper your pet with love and care. Our services will have your furry friend looking and feeling their best.</p>
        </div>
        <div class="bottom-container">
            <img src="img/pic11.jpg" alt="Grooming">
            <p>The health and well-being of cats and dogs is our priority. We ensure
            that every pet recieves the attention they deserve.</p>
        </div>
        <div class="bottom-container">
            <img src="img/pic12.jpg" alt="Vet Care">
            <p>Pets are more than just animals they’re family. They fill our homes with love, loyalty, and happiness.</p>
        </div>
    </div>
</section>


<!-- SERVICES SECTION -->
<section class="service-highlight" id="services">
    <div class="service-highlight-container">
        <div class="service-highlight-text">
            <h2>Find Your Good Services <br> For Your Pet</h2>
        </div>
        <div class="service-highlight-image">
            <img src="img/pic9.jpg" alt="Happy Pet with Owner">
        </div>
    </div>
</section>

<section class="services">
    <div class="services-container">
        <h2>Services we offer</h2>
        <div class="services-list">
            <div class="service-item" onclick="showInfo(0)">
                <img src="img/pic5.jpg" alt="Medical Care">
                <h3>Medical Care</h3>
            </div>
            <div class="service-item" onclick="showInfo(1)">
                <img src="img/pic6.jpg" alt="Grooming">
                <h3>Grooming</h3>
            </div>
            <div class="service-item" onclick="showInfo(2)">
                <img src="img/pic7.jpg" alt="Vaccine">
                <h3>Vaccine</h3>
            </div>
            <div class="service-item" onclick="showInfo(3)">
                <img src="img/pic8.jpg" alt="Training">
                <h3>Training</h3>
            </div>
        </div>
    </div>
</section>

<!-- Fullscreen Info Pop-up -->
<div class="fullscreen-info">
    <div class="info-container">
        <h2 id="service-title">Service Title</h2>
        <p id="service-description">Details about the service.</p>
        <button onclick="closeFullscreenInfo()">Close</button>
    </div>
</div>


<!-- PET PROFILE SECTION -->
<section class="pet-profile">
    <div class="pet-profile-title">
        <h2>PET INFO</h2>
    </div>

    <div class="profile-container">
        <!-- Pet Profile Container -->
        <div class="profile-left-container">
            <h2>Pet Profile</h2>
            <div class="profile-left">
                <img id="pet-image" src="img/pet-placeholder.jpg" alt="Pet Image" class="pet-image">
                
                <div class="upload-container">
                    <button type="button" onclick="uploadImage()">Upload Image</button>
                </div>
            </div>
        </div>
        
        <!-- Pet Details Container -->
        <div class="profile-right-container">
            <h2>Pet Details</h2>
            <div class="profile-right">
                <form id="pet-form">
                    <label for="pet-name">Name:</label>
                    <input type="text" id="pet-name" name="pet-name" required>
                    
                    <div class="grid-container">
                        <div>
                            <label for="pet-age">Age:</label>
                            <input type="number" id="pet-age" name="pet-age" required>
                        </div>
                        <div>
                            <label for="pet-dob">Date of Birth:</label>
                            <input type="date" id="pet-dob" name="pet-dob" required>
                        </div>
                        <div>
                            <label for="pet-gender">Gender:</label>
                            <select id="pet-gender" name="pet-gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="pet-breed">Breed:</label>
                            <input type="text" id="pet-breed" name="pet-breed" required>
                        </div>
                    </div>
                    
                    <label for="owner-name">Owner's Name:</label>
                    <input type="text" id="owner-name" name="owner-name" required>
                    
                    <label for="owner-address">Owner's Address:</label>
                    <textarea id="owner-address" name="owner-address" required></textarea>
                    
                    <div class="form-buttons">
                        <button type="button" onclick="goBack()">Back</button>
                        <button type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


<!-- CONTACT SECTION -->
<section class="contact" id="contact">
    <div class="contact-container">
        <!-- Left Side: Contact Form -->
        <div class="contact-form">
            <h2>Contact Us</h2>
            <form action="send_message.php" method="POST">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Your Message:</label>
                <textarea id="message" name="message" required></textarea>

                <div class="form-buttons">
                    <button type="submit">Send Message</button>
                </div>
            </form>
        </div>
        
        <!-- Right Side: Contact Details -->
        <div class="contact-details">
            <h2>Get in Touch</h2>
            <p>If you have any questions or inquiries, feel free to contact us.</p>
            <p>Email: info@fetchandchill.com</p>
            <p>Phone: (123) 456-7890</p>
            <p>Address: 123 Fetch St., Chill City</p>
        </div>
    </div>
</section>




<script src="home.js"></script>
  </body>
</html>