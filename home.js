//sign up requird fields
//if (sessionStorage.getItem("isLoggedIn") !== "true") {
   // window.location.href = "index.html"; // Babalik sa login page kung hindi naka-login
//}

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



