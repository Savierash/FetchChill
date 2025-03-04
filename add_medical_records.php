<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch & Chill</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">

    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <!-- Medical Records -->
        <div id="medicalRecords" class="medical-container" >
            <h2>Add Medical Record</h2>
                <form class="med-form" id="medicalForm">
                    <label for="ownerName">Owner Name:</label>
                    <input type="text" id="ownerName" required>

                    <label for="petName">Pet Name:</label>
                    <input type="text" id="petName" required>

                    <label for="weight">Weight (kg):</label>
                    <input type="number" id="weight" required>

                    <label for="age">Age:</label>
                    <input type="number" id="age" required>
                    <label for="gender">Gender:</label>

                    <select id="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>

                    <label for="checkupDate">Date of Check-Up:</label>
                    <input type="date" id="checkupDate" required>

                    <label for="time">Time:</label>
                    <input type="time" id="time" required>

                    <label for="diagnosis">Diagnosis:</label>
                    <input type="text" id="diagnosis" required>

                    <label for="treatment">Treatment:</label>
                    <input type="text" id="treatment" required>
                    
                    <button type="submit">Submit</button>
            </form>
        </div>

<script src="add_medical_records.js"></script>
    
</script>
</body>
</html>        
