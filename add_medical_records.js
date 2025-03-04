//Medical Records data
 // Handle form submission
document.getElementById('medicalForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Get form data
    const formData = {
        ownerName: document.getElementById('ownerName').value,
        petName: document.getElementById('petName').value,
        weight: document.getElementById('weight').value,
        age: document.getElementById('age').value,
        gender: document.getElementById('gender').value,
        checkupDate: document.getElementById('checkupDate').value,
        time: document.getElementById('time').value,
        diagnosis: document.getElementById('diagnosis').value,
        treatment: document.getElementById('treatment').value
    };

    // Store form data in localStorage
    localStorage.setItem('medicalRecord', JSON.stringify(formData));

    // Redirect back to the previous page
    window.history.back();  // This will go back to the previous page
});
