////////////////////SIDEBAR FUNCTION
function changeContent(sectionId) {
    document.querySelectorAll('.content-section, .appointment-container, .medical-container, .management-container').forEach(section => {
        section.style.display = 'none';
    });

    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.style.display = 'block';
    } else {
        console.error("Section not found: " + sectionId);
    }
}





///////////////////////////for notification bells
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



//////////////////////////DASHBOARD




///////////////////////////APPOINTMENT
function updateStatus(appointmentId, newStatus) {
    if (confirm(`Are you sure you want to mark this appointment as ${newStatus}?`)) {
        console.log("Sending data:", { id: appointmentId, status: newStatus });

        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: appointmentId, status: newStatus }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}, Message: ${response.statusText}`);
            }

            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid JSON response');
                }
            });
        })
        .then(data => {
            console.log("Response from server:", data);
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${appointmentId}"]`);
                if (row) {
                    const statusCell = row.querySelector('.status');
                    statusCell.textContent = newStatus;
                    row.setAttribute('data-status', newStatus);

                    if (newStatus === 'Confirmed') {
                        row.style.backgroundColor = '#e6ffe6';
                    } else if (newStatus === 'Cancelled') {
                        row.style.backgroundColor = '#ffe6e6';
                    }

                    const buttons = row.querySelectorAll('.buttons button');
                    buttons.forEach(button => button.disabled = true);
                }
                alert(`Appointment status updated to ${newStatus}.`);
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error("Full error details:", {
                error: error.message,
                stack: error.stack,
                request: {
                    url: 'update_status.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: appointmentId, status: newStatus }),
                },
            });
            alert('An error occurred while updating the status. Check the console for details.');
        });
    }
}

//appointment filter
function filterAppointments(filter) {
    console.log("Filtering appointments by:", filter);

    const rows = document.querySelectorAll('tr[data-status]');

    rows.forEach(row => {
        const status = row.getAttribute('data-status').toLowerCase();
        if (filter === 'all' || status === filter) {
            row.style.display = ''; 
        } else {
            row.style.display = 'none'; 
        }
    });
}

// Search function for Appointments
function searchAppointments() {
    const searchTerm = document.getElementById('search-appointment').value.toLowerCase();
    const appointmentList = document.getElementById('appointment-list');
    const rows = appointmentList.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const ownerName = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
        if (ownerName.includes(searchTerm)) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

////////////////////////////////////MEDICAL RECORDS
////////////////////////////////////BREED TYPE
function updateBreeds() {
    const petType = document.getElementById("petType").value;
    const breedSelect = document.getElementById("breed");
    breedSelect.innerHTML = ""; 

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "Select a breed";
    breedSelect.appendChild(defaultOption);

    const dogBreeds = [
        "Labrador Retriever", "German Shepherd", "Golden Retriever", "Bulldog",
        "Beagle", "Poodle", "Rottweiler", "Shih Tzu", "Siberian Husky",
        "Chihuahua", "Pug", "Doberman", "Dalmatian", "Border Collie", "Corgi"
    ];

    const catBreeds = [
        "Persian", "Siamese", "Maine Coon", "Ragdoll",
        "Bengal", "Sphynx", "Scottish Fold", "British Shorthair",
        "Abyssinian", "Russian Blue", "Siberian", "Norwegian Forest Cat"
    ];

    let selectedBreeds = [];

    if (petType === "Dog") {
        selectedBreeds = dogBreeds;
    } else if (petType === "Cat") {
        selectedBreeds = catBreeds;
    }

    selectedBreeds.forEach(breed => {
        const option = document.createElement("option");
        option.value = breed;
        option.textContent = breed;
        breedSelect.appendChild(option);
    });

    breedSelect.required = selectedBreeds.length > 0;
}


//////////////////Medical Records pop up
function openPopup() {
    const popupContainer = document.querySelector('.popup-container');
    if (popupContainer) {
        popupContainer.style.display = 'flex';
        setTimeout(() => popupContainer.classList.add('show'), 10);
    }
}

function closePopup() {
    const popupContainer = document.querySelector('.popup-container');
    if (popupContainer) {
        popupContainer.classList.remove('show');
        setTimeout(() => popupContainer.style.display = 'none', 300);
    }
}


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

//Search function for Medical Records
function searchMedicals() {
    const searchTerm = document.getElementById('search-medical').value.toLowerCase(); 
    const medicalList = document.getElementById('medical-list');
    const rows = medicalList.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const ownerName = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase(); 
        if (ownerName.includes(searchTerm)) {
            rows[i].style.display = ''; 
        } else {
            rows[i].style.display = 'none'; 
        }
    }
}

//timer for success meassage
setTimeout(function() {
    var successMessage = document.getElementById('successMessage');
    var errorMessage = document.getElementById('errorMessage');
    if (successMessage) successMessage.style.display = 'none';
    if (errorMessage) errorMessage.style.display = 'none';
}, 3000);


//pop up add medical records
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".view-button").forEach(button => {
        button.addEventListener("click", function () {
            let recordId = this.getAttribute("data-id");
            window.location.href = "view_record.php?id=" + recordId;
        });
    });
});




//////////////////////////USER MANAGEMENT
function searchUsers() {
    const searchTerm = document.getElementById('search-management').value.toLowerCase();

    const rows = document.querySelectorAll('#user-list tr');

    rows.forEach(row => {
        const name = row.querySelector('.tabledata:nth-child(1)').textContent.toLowerCase();
        const email = row.querySelector('.tabledata:nth-child(2)').textContent.toLowerCase();
        const role = row.querySelector('.tabledata:nth-child(3)').textContent.toLowerCase();

        if (name.includes(searchTerm) || email.includes(searchTerm) || role.includes(searchTerm)) {
            row.style.display = ''; 
        } else {
            row.style.display = 'none'; 
        }
    });
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`delete_user.php`, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}` 
        }).then(response => {
            if (response.ok) {
                location.reload(); 
            }
        });
    }
}