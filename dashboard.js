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

async function fetchDashboardData() {
    try {
        const response = await fetch('https://example.com/api/dashboard'); 
        const data = await response.json();

        document.getElementById('services-count').textContent = data.services;
        document.getElementById('confirmed-count').textContent = data.confirmed;
        document.getElementById('pending-count').textContent = data.pending;
        document.getElementById('cancelled-count').textContent = data.cancelled;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
}


document.addEventListener("DOMContentLoaded", fetchDashboardData);
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
            row.style.display = ''; 
        } else {
            row.style.display = 'none'; 
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
function openPopup() {
    document.querySelector('.popup-container').style.display = 'flex';
}

function closePopup() {
    document.querySelector('.popup-container').style.display = 'none';
}

document.querySelector('.open-popup-button').addEventListener('click', openPopup);
document.querySelector('.close-btn').addEventListener('click', closePopup);


 /////////////////////////// Submit button medical records
document.getElementById('medicalForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    let formData = new FormData(document.getElementById('medicalForm'));

    fetch('dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  
    .then(data => {
        console.log(data);  
        
        document.getElementById('popupForm').style.display = 'none';
        document.getElementById('medicalRecords').style.display = 'block';
        
        location.reload(); 
    })
    .catch(error => {
        console.error('Error:', error);
    });
});


 //////////////////// Database pet records
 document.getElementById("medicalForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("dashboard.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadRecords(); 
        this.reset(); 
    })
    .catch(error => console.error("Error:", error));
});


function loadRecords() {
    fetch("dashboard.php")
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = ""; 

        if (data.message) {
            tableBody.innerHTML = "<tr><td colspan='10'>No records found.</td></tr>";
        } else {
            data.forEach(record => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${record.ownername}</td>
                        <td>${record.petname}</td>
                        <td>${record.breed}</td>
                        <td>${record.weight}</td>
                        <td>${record.age}</td>
                        <td>${record.gender}</td>
                        <td>${record.visitdate}</td>
                        <td>${record.time}</td>
                        <td>${record.diagnosis}</td>
                        <td>${record.treatment}</td>
                    </tr>
                `;
            });
        }
    })
    .catch(error => console.error("Error:", error));
}

loadRecords();


//timer for success meassage
window.onload = function() {
    setTimeout(function() {
        
        var successMessage = document.getElementById("successMessage");
        if (successMessage) {
            successMessage.style.display = "none";
        }
    }, 10000); 
}