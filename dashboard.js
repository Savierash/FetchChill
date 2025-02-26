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
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function(event) {
    var dropdown = document.getElementById("notifDropdown");
    var bell = document.querySelector(".notification-bell");
    
    if (dropdown && bell && !dropdown.contains(event.target) && !bell.contains(event.target)) {
        dropdown.style.display = "none";
    }
});

document.getElementById('unreadBtn').addEventListener('click', function() {
    document.getElementById('unreadBtn').classList.add('active');
    document.getElementById('allBtn').classList.remove('active');
    document.querySelectorAll('.notification-item').forEach(item => {
        if (!item.classList.contains('unread')) {
            item.style.display = 'none';
        } else {
            item.style.display = 'block';
        }
    });
});

document.getElementById('allBtn').addEventListener('click', function() {
    document.getElementById('allBtn').classList.add('active');
    document.getElementById('unreadBtn').classList.remove('active');
    document.querySelectorAll('.notification-item').forEach(item => {
        item.style.display = 'block';
    });
});

function markAsRead(element) {
    element.classList.remove('unread');
    element.classList.add('read');
}

document.getElementById('allBtn').addEventListener('click', function() {
    document.getElementById('allBtn').classList.add('active');
    document.getElementById('unreadBtn').classList.remove('active');
    document.querySelectorAll('.notification-item').forEach(item => {
        item.style.display = 'block';
    });
});

function markAsRead(element) {
    element.classList.remove('unread');
    element.classList.add('read');
}


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


// Medical Records
document.getElementById("medicalForm").addEventListener("submit", function(event) {
    event.preventDefault();

    // Get values from the form
    let owner = document.getElementById("owner").value;
    let pet = document.getElementById("pet").value;
    let breed = document.getElementById("breed").value;
    let weight = document.getElementById("weight").value;
    let age = document.getElementById("age").value;
    let gender = document.getElementById("gender").value;
    let date = document.getElementById("date").value;
    let diagnosis = document.getElementById("diagnosis").value;
    let treatment = document.getElementById("treatment").value;

    // Insert new record into the table
    let table = document.getElementById("recordsTable");
    let row = table.insertRow();

    // Insert cells into the new row
    row.insertCell(0).innerText = owner;
    row.insertCell(1).innerText = pet;
    row.insertCell(2).innerText = breed;
    row.insertCell(3).innerText = weight;
    row.insertCell(4).innerText = age;
    row.insertCell(5).innerText = gender;
    row.insertCell(6).innerText = date;
    row.insertCell(7).innerText = diagnosis;
    row.insertCell(8).innerText = treatment;

    // Reset the form
    document.getElementById("medicalForm").reset();

    // Send the data to PHP using Fetch API (POST request)
    const petData = {
        ownername: owner,
        petname: pet,
        breed: breed,
        weight: weight,
        age: age,
        gender: gender,
        visitdate: date,
        diagnosis: diagnosis,
        treatment: treatment
    };

    fetch('petrecords.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(petData),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Show success message
    })
    .catch((error) => {
        console.error('Error:', error);
    });
});



//USER MANAGEMENT
// Assuming users array is initialized somewhere in the code
let users = [];  // If you haven't defined this, initialize it

// Function to render the user list
function renderUserList() {
    const userListDiv = document.getElementById('userList');
    userListDiv.innerHTML = ''; // Clear existing list

    if (users.length === 0) {
        userListDiv.innerHTML = 'No users available.'; // Optional message if no users exist
    }

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
    
    if (newUserName && !users.includes(newUserName)) { // Check for empty or duplicate username
        users.push(newUserName); // Add the new user to the list
        renderUserList(); // Re-render the user list
        document.getElementById('newUserName').value = ''; // Clear input field
    } else {
        alert('Please enter a valid, unique username.'); // Optional alert for invalid input
    }
};

// Function to delete a user
function deleteUser(index) {
    if (index >= 0 && index < users.length) {
        users.splice(index, 1); // Remove the user from the array
        renderUserList(); // Re-render the user list
    }
}

// Function to edit a user
function editUser(index) {
    const newUserName = prompt('Edit username:', users[index]);
    if (newUserName && newUserName !== users[index]) { // Check if the new name is different
        users[index] = newUserName; // Update the user name
        renderUserList(); // Re-render the user list
    }
}

// Initial render of user list
renderUserList();
