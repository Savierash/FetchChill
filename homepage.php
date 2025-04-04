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
    <title>Fetch & Chill</title>
  </head>  
  
  <<style>
  @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");
@import url('https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Roboto+Flex:opsz,wght@8..144,100..1000&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&family=Mochiy+Pop+One&family=Roboto+Flex:opsz,wght@8..144,100..1000&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');


* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body,
  input {
    font-family: "Poppins", sans-serif;
  }

/***************************NAVBAR********************************/
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #C5DAEF;
    padding: 0 50px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
    z-index: 1000;
    animation: fadeInDown 0.5s ease-out;
}

.logo {
    max-width: 50px;
    height: auto;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 20px;
}

.nav-links li {
    display: inline;
}

.nav-links a {
    text-decoration: none;
    color: black;
    font-size: 16px;
    font-weight: 600;
    padding: 10px 15px;
    transition: 0.3s;
    border-radius: 25px;
}

.nav-links a:hover {
    background-color: #E4813A;
}

.logout-btn {
    background-color: #5995FD;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
}

.logout-btn:hover {
    background-color: #E4813A;
    transform: scale(1.05);
}

.logout-btn:active {
    background-color: #E4813A;
    transform: scale(0.95);
}

/**************************Responsive Navbar*******************************/
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        padding: 15px;
    }

    .nav-links {
        flex-direction: column;
        align-items: center;
        margin-top: 10px;
        animation: slideDown 0.5s ease-out;
    }
}

/*****************************HOME SECTION*************************/
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.home {
    background-color: #C5DAEF;
    padding: 80px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.home-container {
    display: flex;
    align-items: center;
    max-width: 1200px;
    gap: 40px;
    text-align: left;
    animation: fadeInUp 1s ease-out;
}

.home-content {
    max-width: 500px;
}

.username {
    color: #5995FD; 
    font-weight: bold;
}

.home-content h1 {
    font-size: 70px;
    margin-bottom: 15px;
    font-family: "Mochiy Pop One";
    color: #000000;
}

.home-content p {
    font-size: 1.2rem;
    margin-bottom: 20px;
    color: #000000;
    font-family: "Mochiy Pop One";
}

.home-btn {
    background-color: white;
    color: #04befe;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
}

.btn:hover {
    background-color: #f9f9f9;
}

.home-image img {
    width: 100%;
    max-width: 1000px;
    border-radius: 15px;
}

/*********************Button got to dashboard**************************/
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.btn_dashboard {
    display: inline-block;
    padding: 10px 20px;
    background-color: #5995FD !important; 
    color: white !important;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.btn_dashboard:hover {
    background-color: #E4813A !important;
    animation: pulse 1s infinite;
}

.btn_dashboard:active {
    background-color: #D07F2A !important;
    transform: scale(0.95);
}

@media (max-width: 768px) {
    .home-container {
        flex-direction: column;
        text-align: center;
    }

    .home-image img {
         max-width: 100%;
    }
}

/********************ABOUT SECTION******************/
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.about-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 70px;
    background-color: #C5DAEF;
    animation: fadeIn 1s ease-out;
}

.about-content h2 {
    font-size: 40px;
    margin-bottom: 30px;
    color: #000000;
    margin-left: 100px;
}

.about-content p {
    font-size: 20px;
    font-family: "lexend Deca";
    color: #534E46;
    margin-left: 100px;
}

.about-image {
    width: 35%;
    display: flex;
    justify-content: center;
    margin-right: 100px;
}

.about-image img {
    width: 100%;
    height: auto;
    border-radius: 53px;
    animation: fadeIn 1.5s ease-out;
}

/*******************Bottom Containers******************/
@keyframes scaleUp {
    from {
        transform: scale(1);
    }
    to {
        transform: scale(1.05);
    }
}

.about-bottom-containers {
    display: flex;
    justify-content: center; 
    align-items: center; 
    padding: 20px;
    background-color: #C5DAEF;
}

.bottom-container {
    background-color: #ffffff;
    padding: 15px;
    border-radius: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    text-align: center;
    transition: transform 0.3s ease-in-out;
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
    align-items: center;
    width: 400px; 
    margin: 50px; 
}

.bottom-container img {
    width: 100%;
    height: auto;
    border-radius: 10px;
    margin-bottom: 15px;
}

.bottom-container p {
    margin-top: 10px;
    color: #333;
}

.bottom-container:hover {
    animation: scaleUp 0.3s ease-out forwards;
}

/***************Service section******************/
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-20px);
    }
    60% {
        transform: translateY(-10px);
    }
}

.services {
    background-color: #E4EFE4; 
    padding: 100px;
}

.services-container {
    text-align: center;
    max-width: 1200px;
    margin: 0 auto;
}

.services h2 {
    font-size: 36px;
    margin-bottom: 30px;
    color: #333;
}

.services-list {
    display: flex;
    justify-content: space-around;
    gap: 20px;
}

.service-item {
    background-color: #7AC8BB;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    width: 24%;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    text-align: center;
    position: relative;
    margin-bottom: 20px;
    transition: transform 0.3s ease-in-out;
}

.service-item:hover {
    animation: bounce 0.5s;
}

.service-item img {
    width: 100%;
    height: auto;
    border-radius: 10px;
    margin-bottom: 15px;
}

.service-item h3 {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

.service-item p {
    font-size: 16px;
    color: #666;
    line-height: 1.5;
}

.fullscreen-info {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    text-align: center;
    z-index: 100;
    padding: 20px;
    animation: fadeIn 0.5s ease-out;
}

.fullscreen-info.show {
    display: flex; 
}

.info-container {
    background: #7AC8BB;
    color: black;
    padding: 20px;
    border-radius: 10px;
    max-width: 500px;
    text-align: center;
}

.info-container button {
    margin-top: 10px;
    padding: 10px 20px;
    border: none;
    background: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
}

.info-container button:hover {
    background: #0056b3;
}

.service-highlight {
    background-color: #C5DAEF; 
    padding: 30px 0;
    text-align: center;
}

.service-highlight-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    flex-wrap: wrap; 
    padding: 20px;
}

.service-highlight-text {
    flex: 1;
    text-align: left;
    padding: 20px;
}

.service-highlight-text h2 {
    background-image: url("img/pic10.jpg"); 
    background-size: cover;
    background-position: right center;
    background-repeat: no-repeat;
    display: inline-block;
    padding: 95px;
    font-size: 26px;
    color: #33363F; 
    font-weight: bold;
    font-family: "Mochiy Pop One";
    border-radius: 10px;
}

.service-highlight-text p {
    font-size: 20px;
    color: #333;
    font-family: "lexend Deca";
    font-weight: normal;
    color: #534E46B2;
}

.service-highlight-image {
    flex: 1;
    text-align: center;
}

.service-highlight-image img {
    width: 100%;
    max-width: 500px; 
    border-radius: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .service-highlight-container {
        flex-direction: column;
        text-align: center;
    }

    .service-highlight-text {
        text-align: center;
        padding: 10px;
    }

    .service-highlight-image img {
        max-width: 80%;
    }
}

/* Responsiveness */
@media (max-width: 768px) {
    .services-list {
        flex-direction: column;
        align-items: center;
    }

    .service-item {
        width: 80%; 
        margin-bottom: 30px;
    }
}
  </style>

  <body>

    <!------------NAVBAR-------------->
    <nav class="navbar">
        <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo"></a>
            <ul class="nav-links">
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </ul>
    </nav>


    <!-----------HOME SECTION------------->
    <section class="home" id="home">
        <div class="home-container">
            <div class="home-content">
                <h1>Hi, <span class="username"> <?php echo $_SESSION['username']; ?>!</h1></span>
                <h1>Welcome to Fetch&Chill</h1>
                <p>Where every fetch ends with cuddle</p>
                <!------------Go to Dashboard Button-------------->
                <a href="dashboard.php" class="btn_dashboard">Go to Dashboard</a>
            </div>
            <div class="home-image">
                <img src="img/pic4.jpg" alt="Happy Dog & Cat">
            </div>
        </div>
    </section>


    




<script>
    
//SERVICE LIST TOGGLE BUTTON
// Data for services (replace with actual details for each service)
const servicesInfo = [
   { title: 'Medical Care', description: 'We provide comprehensive medical care for pets.' },
   { title: 'Grooming', description: 'Our grooming services keep your pet looking great.' },
   { title: 'Vaccine', description: 'Ensure your pet’s health with our vaccination services.' },
   { title: 'Training', description: 'We offer training sessions for better pet behavior.' }
];

// Get the fullscreen info element
const fullscreenInfo = document.querySelector('.fullscreen-info');
const serviceTitle = document.getElementById('service-title');
const serviceDescription = document.getElementById('service-description');

// Function to show fullscreen info
function showInfo(index) {
   const service = servicesInfo[index];
   serviceTitle.textContent = service.title;
   serviceDescription.textContent = service.description;
   fullscreenInfo.classList.add('show');
}

// Function to close the fullscreen info
function closeFullscreenInfo() {
   fullscreenInfo.classList.remove('show');
}
</script>
  </body>
</html>