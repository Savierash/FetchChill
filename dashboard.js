//SIDEBAR FUNCTION
function changeContent(contentId) {
    // Hide all sections first
    const sections = document.querySelectorAll('.content-section, .appointment-container, .medical-container, .management-container');
    sections.forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected section
    const contentToShow = document.getElementById(contentId);
    if (contentToShow) {
        contentToShow.style.display = 'block';
    }
}




//for notification bells
function toggleDropdown() {
    var dropdown = document.getElementById("notifDropdown");
    dropdown.classList.toggle("active");
}

// Close dropdown when clicking outside
document.addEventListener("click", function(event) {
    var dropdown = document.getElementById("notifDropdown");
    var bell = document.querySelector(".notification-bell");

    if (!dropdown.contains(event.target) && !bell.contains(event.target)) {
        dropdown.classList.remove("active");
    }
});


//DASHBOARD
// Function para kunin ang data mula sa API
async function fetchDashboardData() {
    try {
        const response = await fetch('https://example.com/api/dashboard'); // Palitan ito ng totoong API endpoint
        const data = await response.json();

        // I-update ang UI gamit ang nakuha na data
        document.getElementById('services-count').textContent = data.services;
        document.getElementById('confirmed-count').textContent = data.confirmed;
        document.getElementById('pending-count').textContent = data.pending;
        document.getElementById('cancelled-count').textContent = data.cancelled;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
}

// Tawagin ang function pag-load ng page
document.addEventListener("DOMContentLoaded", fetchDashboardData);

// I-refresh ang data kada 5 segundo (5000ms) para real-time update
setInterval(fetchDashboardData, 5000);


//APPOINTMENT
function updateStatus(button, newStatus) {
    let row = button.closest("tr");
    let statusCell = row.querySelector(".status");
    statusCell.textContent = newStatus;

    if (newStatus === 'Confirmed') {
        statusCell.style.color = 'green';
    } else if (newStatus === 'Pending') {
        statusCell.style.color = 'orange';
    } else if (newStatus === 'Cancelled') {
        statusCell.style.color = 'red';
    }
}

//MEDICAL RECORDS
document.getElementById("medicalForm").addEventListener("submit", function(event) {
    event.preventDefault();
    
    let breed = document.getElementById("breed").value;
    let weight = document.getElementById("weight").value;
    let date = document.getElementById("date").value;
    let diagnosis = document.getElementById("diagnosis").value;
    let treatment = document.getElementById("treatment").value;
    
    let table = document.getElementById("recordsTable");
    let row = table.insertRow();
    row.insertCell(0).innerText = breed;
    row.insertCell(1).innerText = weight;
    row.insertCell(2).innerText = date;
    row.insertCell(3).innerText = diagnosis;
    row.insertCell(4).innerText = treatment;
    
    document.getElementById("medicalForm").reset();
});



//USER MANAGEMENT
// Initial user data
let users = ['Suniga', 'Naaantok na'];

// Function to render the user list
function renderUserList() {
    const userListDiv = document.getElementById('userList');
    userListDiv.innerHTML = ''; // Clear existing list

    users.forEach((user, index) => {
        const userItem = document.createElement('div');
        userItem.classList.add('user-item');

        const username = document.createElement('span');
        username.classList.add('username');
        username.textContent = user;

        const editBtn = document.createElement('button');
        editBtn.classList.add('edit-btn');
        editBtn.textContent = 'Edit';
        editBtn.onclick = () => editUser(index);

        const deleteBtn = document.createElement('button');
        deleteBtn.classList.add('delete-btn');
        deleteBtn.textContent = 'Delete';
        deleteBtn.onclick = () => deleteUser(index);

        userItem.appendChild(username);
        userItem.appendChild(editBtn);
        userItem.appendChild(deleteBtn);
        userListDiv.appendChild(userItem);
    });
}

// Function to add a new user
document.getElementById('addUserBtn').onclick = function() {
    const newUserName = document.getElementById('newUserName').value.trim();
    if (newUserName) {
        users.push(newUserName); // Add the new user to the list
        renderUserList(); // Re-render the user list
        document.getElementById('newUserName').value = ''; // Clear input field
    }
};

// Function to delete a user
function deleteUser(index) {
    users.splice(index, 1); // Remove the user from the array
    renderUserList(); // Re-render the user list
}

// Function to edit a user (can be expanded to handle editing)
function editUser(index) {
    const newUserName = prompt('Edit username:', users[index]);
    if (newUserName) {
        users[index] = newUserName; // Update the user name
        renderUserList(); // Re-render the user list
    }
}

// Initial render of user list
renderUserList();