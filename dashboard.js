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

 // Function to update appointment status
 function updateStatus(button, status) {
    const row = button.closest('tr');
    const statusCell = row.querySelector('.status');
    statusCell.textContent = status;
    row.setAttribute('data-status', status.toLowerCase());
}

// Function to filter appointments based on status
function filterAppointments(status) {
    const rows = document.querySelectorAll('#appointment-list tr');
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = ''; // Show the row
        } else {
            row.style.display = 'none'; // Hide the row
        }
    });
}

// Search function for Appointments
function searchAppointments() {
    const query = document.getElementById("search-bar").value.toLowerCase();
    const rows = document.querySelectorAll("#appointment-list tr");
    
    rows.forEach(row => {
        const customerName = row.querySelector(".customer-name").textContent.toLowerCase();
        if (customerName.includes(query)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

//Medical Records pop up


//Medical records data
 // Retrieve form data from localStorage
 const formData = JSON.parse(localStorage.getItem('medicalRecord'));

 if (formData) {
     // Create a new row for the table
     const tableRow = document.createElement('tr');
     
     // Insert data into each table cell
     tableRow.innerHTML = `
         <td>${formData.ownerName}</td>
         <td>${formData.petName}</td>
         <td>${formData.weight}</td>
         <td>${formData.age}</td>
         <td>${formData.gender}</td>
         <td>${formData.checkupDate}</td>
         <td>${formData.time}</td>
         <td>${formData.diagnosis}</td>
         <td>${formData.treatment}</td>
     `;
     
     // Append the row to the table body
     document.getElementById('tableBody').appendChild(tableRow);
 }