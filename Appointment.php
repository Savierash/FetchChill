<?php
require_once 'PetDatabase.php';

class Appointment {
    private $conn;

    public function __construct() {
        $this->conn = PetDatabase::getInstance();
    }

    public function GetAppointment($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE id = ?");
            if ($stmt === false) {
                throw new mysqli_sql_exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param('i', $id);
            if ($stmt->execute() === false) {
                throw new mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $appointment = $result->fetch_assoc() ?? [];
            $stmt->close();
            return $appointment;
        } catch (mysqli_sql_exception $e) {
            error_log("Error in GetAppointment: " . $e->getMessage());
            return [];
        }
    }

    public function GetAllAppointments() {
        try {
            $query = "SELECT * FROM appointments";
            $result = $this->conn->query($query);
            if ($result === false) {
                throw new mysqli_sql_exception("Query failed: " . $this->conn->error);
            }
            return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (mysqli_sql_exception $e) {
            error_log("Error in GetAllAppointments: " . $e->getMessage());
            return [];
        }
    }

    public function CreateAppointments($user_id, $service_type, $appointment_date, $appointment_time) {
        try {
            $query = "INSERT INTO appointments (user_id, service_type, appointment_date, appointment_time) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new mysqli_sql_exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("isss", $user_id, $service_type, $appointment_date, $appointment_time);
            if ($stmt->execute() === false) {
                throw new mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $e) {
            error_log("Error in CreateAppointments: " . $e->getMessage());
            return false;
        }
    }

    public function UpdateStatus($status, $id) {
        try {
            $stmt = $this->conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            if ($stmt === false) {
                throw new mysqli_sql_exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param('si', $status, $id);
            if ($stmt->execute() === false) {
                throw new mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $e) {
            error_log("Error in UpdateStatus: " . $e->getMessage());
            return false;
        }
    }

    public function UpdateStatuses($status, $ids) {
        try {
            if (empty($ids)) return false;
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $query = "UPDATE appointments SET status = ? WHERE id IN ($placeholders)";
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new mysqli_sql_exception("Prepare failed: " . $this->conn->error);
            }
            $types = 's' . str_repeat('i', count($ids));
            $stmt->bind_param($types, $status, ...$ids);
            if ($stmt->execute() === false) {
                throw new mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $e) {
            error_log("Error in UpdateStatuses: " . $e->getMessage());
            return false;
        }
    }
}
?>