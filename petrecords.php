<?php
//for inserting new pet records
include 'pet_connection.php';
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);

if($_SERVER ["REQUEST_METHOD"] == "POST"){
    $ownername = $input ['ownername'];
    $petname = $input ['petname'];
    $breed = $input ['breed'];
    $weight = $input['weight'];
    $age = $input ['age'];
    $gender = $input ['gender'];
    $diagnosis = $input ['diagnosis'];
    $treatment =$input ['treatment'];
    $visitdate = $input ['visitdate'];


    $sql ="INSERT INTO petrecords (ownername, petname, breed, weight, age, gender, diagnosis, treatment, visitdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sssiiisss", $ownername, $petname, $breed, $weight, $age, $gender, $diagnosis, $treatment, $visitdate);

    $result = $stmt->execute();
    if($result){
        echo json_encode(["message" => "Pet record added succesfully!"]);
    } else{
        echo json_encode(["message" => "Error"]);
    }

    
    $stmt->close();
    
}
    // for upadting pet records
    $request_uri = explode("/", $_SERVER['REQUEST_URI']);
    $id = end($request_uri);
    if ($_SERVER["REQUEST_METHOD"] == "PUT" && is_numeric($id)) {
        $ownername = $input['ownername'];
        $petname = $input['petname'];
        $breed = $input['breed'];
        $weight = $input['weight'];
        $age = $input['age'];
        $gender = $input['gender'];
        $diagnosis = $input['diagnosis'];
        $treatment = $input['treatment'];
        $visitdate = $input['visitdate'];

        // SQL query to update data
        $sql = "UPDATE petrecords SET ownername=?, petname=?, breed=?, weight=?, age=?, gender=?, diagnosis=?, treatment=?, visitdate=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiisssi", $ownername, $petname, $breed, $weight, $age, $gender, $diagnosis, $treatment, $visitdate, $id);

        // Execute and check
        $result = $stmt->execute();
        if ($result) {
            echo json_encode(["message" => "Record updated successfully!", "result" => $input]);
        } else {
            echo json_encode(["message" => "Error"]);
        }

        $stmt->close();
        
} 
    // for deleting pet records
    if ($_SERVER["REQUEST_METHOD"] == "DELETE" && is_numeric($id)) {

        $sql = "DELETE FROM petrecords WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        // Execute and check
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(["message" => "Record deleted successfully!"]);
        } else {
            echo json_encode(["message" => "Record does not exist"]);
        }

        $stmt->close();
        
}
    //for searching owner name for pet records
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ownername'])) {
        $ownername = $_GET['ownername'];
        $sql = "SELECT * FROM petrecords WHERE ownername LIKE ?";
        $stmt = $conn->prepare($sql);
        $search_term = "%" . $ownername . "%";
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $records = array();
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            echo json_encode($records);
        } else {
            echo json_encode(["message" => "No records found."]);
        }

        $stmt->close();
}
    // used to view records or fetch all records
    $sql = "SELECT * FROM petrecords";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        $records = array();
        while ($row = $result->fetch_assoc()){
            $records[] = $row;
        }
        echo json_encode($records);
    } else{
        echo json_encode(["message" => "No records Found."]);
    }
$conn->close();
?>