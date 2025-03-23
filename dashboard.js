// SIDEBAR FUNCTION
function changeContent(sectionId) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');
    
    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.style.display = 'block';
        
        // Map sectionId to menu item text
        const sectionMap = {
            'dashboard': 'Dashboard',
            'appointments': 'Appointments',
            'medicalRecords': 'Medical Records',
            'userManagement': 'User Management'
        };

        // Update sidebar highlighting
        document.querySelectorAll('.menu li').forEach(li => {
            li.classList.remove('active');
            // Check if the menu item's text matches the mapped section name
            if (li.textContent.trim() === sectionMap[sectionId]) {
                li.classList.add('active');
            }
        });
    } else {
        console.error(`Section not found: ${sectionId}`);
    }
}

// NOTIFICATION FUNCTIONS
function toggleDropdown() {
    const dropdown = document.getElementById("notifDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

function handleNotificationFilter() {
    const unreadBtn = document.getElementById('unreadBtn');
    const allBtn = document.getElementById('allBtn');
    
    unreadBtn.addEventListener('click', () => {
        unreadBtn.classList.add('active');
        allBtn.classList.remove('active');
        document.querySelectorAll('.notification-item').forEach(item => {
            item.style.display = item.classList.contains('unread') ? 'block' : 'none';
        });
    });

    allBtn.addEventListener('click', () => {
        allBtn.classList.add('active');
        unreadBtn.classList.remove('active');
        document.querySelectorAll('.notification-item').forEach(item => {
            item.style.display = 'block';
        });
    });
}

function markAsRead(element) {
    element.classList.remove('unread');
    element.classList.add('read');
    updateNotificationCount();
}

function updateNotificationCount() {
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const badge = document.getElementById('notifCount');
    badge.textContent = unreadCount;
    badge.style.display = unreadCount > 0 ? 'inline' : 'none';
}

// APPOINTMENT FUNCTIONS
// Update Status Function (Handles multiple IDs in one request)
async function updateStatus(appointmentIds, newStatus) {
    if (!Array.isArray(appointmentIds)) {
        appointmentIds = [appointmentIds]; // Convert single ID to array for consistency
    }

    if (appointmentIds.length === 0) {
        alert('No appointments selected.');
        return;
    }

    if (!confirm(`Are you sure you want to mark ${appointmentIds.length} appointment(s) as ${newStatus}?`)) return;

    try {
        const response = await fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: appointmentIds, status: newStatus })
        });

        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();
        
        if (data.success) {
            appointmentIds.forEach(id => {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    const statusCell = row.querySelector('.status');
                    statusCell.textContent = newStatus;
                    row.setAttribute('data-status', newStatus.toLowerCase());
                    row.style.backgroundColor = newStatus === 'Confirmed' ? '#e6ffe6' : 
                                             newStatus === 'Cancelled' ? '#ffe6e6' : '';
                    row.querySelectorAll('.buttons button').forEach(btn => btn.disabled = true);
                }
            });
            alert(`Updated ${appointmentIds.length} appointment(s) to ${newStatus}.`);
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        alert('An error occurred while updating the status.');
    }
}

// Handle Bulk Actions
function handleBulkActions() {
    const selectAll = document.getElementById('select-all');
    const confirmAll = document.getElementById('confirm-all');
    const cancelAll = document.getElementById('cancel-all');

    // Toggle all checkboxes when "Select All" is clicked
    selectAll.addEventListener('change', () => {
        document.querySelectorAll('input[name="select_appointment"]').forEach(cb => {
            cb.checked = selectAll.checked;
        });
    });

    // Confirm All: Update all checked appointments to Confirmed
    confirmAll.addEventListener('click', () => {
        const checkedBoxes = document.querySelectorAll('input[name="select_appointment"]:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        if (ids.length > 0) {
            updateStatus(ids, 'Confirmed');
        } else {
            alert('Please select at least one appointment to confirm.');
        }
    });

    // Cancel All: Update all checked appointments to Cancelled
    cancelAll.addEventListener('click', () => {
        const checkedBoxes = document.querySelectorAll('input[name="select_appointment"]:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        if (ids.length > 0) {
            updateStatus(ids, 'Cancelled');
        } else {
            alert('Please select at least one appointment to cancel.');
        }
    });
}

function filterAppointments(filter) {
    const rows = document.querySelectorAll('#appointment-list tr');
    rows.forEach(row => {
        const status = row.getAttribute('data-status').toLowerCase();
        if (filter === 'all') {
            row.style.display = ''; // Show all rows
        } else {
            row.style.display = (status === filter) ? '' : 'none'; // Show only matching status
        }
    });

    // Optional: Highlight the active filter button
    document.querySelectorAll('.appointment-filter button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().replace(/\s+/g, '') === filter) {
            btn.classList.add('active');
        }
    });
}

function searchAppointments() {
    const searchTerm = document.getElementById('search-appointment').value.toLowerCase();
    const rows = document.querySelectorAll('#appointment-list tr');
    
    rows.forEach(row => {
        const customer = row.cells[1].textContent.toLowerCase();
        row.style.display = customer.includes(searchTerm) ? '' : 'none';
    });
}

function handleBulkActions() {
    const selectAll = document.getElementById('select-all');
    const confirmAll = document.getElementById('confirm-all');
    const cancelAll = document.getElementById('cancel-all');

    // Keep the "Select All" checkbox functionality for manual selection
    selectAll.addEventListener('change', () => {
        document.querySelectorAll('input[name="select_appointment"]').forEach(cb => {
            cb.checked = selectAll.checked;
        });
    });

    // Confirm All: Select all and update status to Confirmed
    confirmAll.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('input[name="select_appointment"]');
        checkboxes.forEach(cb => {
            cb.checked = true; // Automatically check all boxes
            updateStatus(cb.value, 'Confirmed'); // Update status for each
        });
        selectAll.checked = true; // Ensure the "Select All" checkbox reflects the action
    });

    // Cancel All: Select all and update status to Cancelled
    cancelAll.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('input[name="select_appointment"]');
        checkboxes.forEach(cb => {
            cb.checked = true; // Automatically check all boxes
            updateStatus(cb.value, 'Cancelled'); // Update status for each
        });
        selectAll.checked = true; // Ensure the "Select All" checkbox reflects the action
    });
}

// MEDICAL RECORDS FUNCTIONS
function updateBreeds() {
    const petType = document.getElementById("petType").value;
    const breedSelect = document.getElementById("breed");
    breedSelect.innerHTML = '<option value="">Select a breed</option>';

    const breeds = {
        Dog: ["Labrador Retriever", "German Shepherd", "Golden Retriever", "Bulldog", "Beagle", 
              "Poodle", "Rottweiler", "Shih Tzu", "Siberian Husky", "Chihuahua", "Pug", 
              "Doberman", "Dalmatian", "Border Collie", "Corgi"],
        Cat: ["Persian", "Siamese", "Maine Coon", "Ragdoll", "Bengal", "Sphynx", 
              "Scottish Fold", "British Shorthair", "Abyssinian", "Russian Blue", 
              "Siberian", "Norwegian Forest Cat"]
    };

    (breeds[petType] || []).forEach(breed => {
        const option = document.createElement("option");
        option.value = breed;
        option.textContent = breed;
        breedSelect.appendChild(option);
    });
}

function openPopup() {
    const popup = document.getElementById('popupForm');
    popup.style.display = 'flex';
    setTimeout(() => popup.classList.add('show'), 10);
}

function closePopup() {
    const popup = document.getElementById('popupForm');
    popup.classList.remove('show');
    setTimeout(() => popup.style.display = 'none', 300);
}

function handleMedicalForm() {
    document.getElementById('medicalForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            const response = await fetch('dashboard.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) throw new Error('Form submission failed');
            
            closePopup();
            location.reload();
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('Error saving medical record');
        }
    });
}

function searchMedicals() {
    const searchTerm = document.getElementById('search-medical').value.toLowerCase();
    const rows = document.querySelectorAll('#medical-list tr');
    
    rows.forEach(row => {
        const ownerName = row.cells[0].textContent.toLowerCase();
        row.style.display = ownerName.includes(searchTerm) ? '' : 'none';
    });
}

// USER MANAGEMENT FUNCTIONS
function searchUsers() {
    const searchTerm = document.getElementById('search-management').value.toLowerCase();
    const rows = document.querySelectorAll('#user-list tr');
    
    rows.forEach(row => {
        const text = Array.from(row.cells).slice(0, 3)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) return;

    try {
        const response = await fetch('delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${userId}`
        });
        
        if (!response.ok) throw new Error('Delete failed');
        location.reload();
    } catch (error) {
        console.error('Error deleting user:', error);
        alert('Error deleting user');
    }
}

// INITIALIZATION
document.addEventListener('DOMContentLoaded', () => {
    // Set initial active state for Dashboard
    const initialSection = 'dashboard';
    document.querySelectorAll('.menu li').forEach(li => {
        if (li.textContent.trim() === 'Dashboard') { // Match exact text
            li.classList.add('active');
        }
    });

    // Notification handlers
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('notifDropdown');
        const bell = document.querySelector('.notification-bell');
        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
    
    handleNotificationFilter();
    updateNotificationCount();
    
    // Medical records
    handleMedicalForm();
    
    // Appointments
    handleBulkActions();
    
    // Hide messages after 3 seconds
    setTimeout(() => {
        document.querySelectorAll('.message').forEach(msg => msg.style.display = 'none');
    }, 3000);
});