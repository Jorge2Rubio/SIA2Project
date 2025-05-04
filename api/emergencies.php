<?php
require_once 'shared.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all emergencies or single emergency
        if (isset($_GET['id'])) {
            $id = sanitizeInput($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM emergencies WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $emergency = $result->fetch_assoc();
            
            if ($emergency) {
                jsonResponse(true, 'Emergency retrieved', $emergency);
            } else {
                jsonResponse(false, 'Emergency not found');
            }
        } else {
            $result = $conn->query("SELECT * FROM emergencies");
            $emergencies = [];
            while ($row = $result->fetch_assoc()) {
                $emergencies[] = $row;
            }
            jsonResponse(true, 'Emergencies retrieved', $emergencies);
        }
        break;
        
    case 'POST':
        // Create new emergency
        $data = json_decode(file_get_contents("php://input"), true);
        $location = sanitizeInput($data['location']);
        $description = sanitizeInput($data['description']);
        $event_id = isset($data['event_id']) ? sanitizeInput($data['event_id']) : null;
        $media = isset($data['media']) ? sanitizeInput($data['media']) : null;
        $status = 'pending'; // default status
        
        $stmt = $conn->prepare("INSERT INTO emergencies (location, description, event_id, media, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $location, $description, $event_id, $media, $status);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Emergency reported', ['id' => $stmt->insert_id]);
        } else {
            jsonResponse(false, 'Error reporting emergency');
        }
        break;
        
    case 'PUT':
        // Update emergency (admin only)
        $data = json_decode(file_get_contents("php://input"), true);
        $id = sanitizeInput($data['id']);
        $location = sanitizeInput($data['location']);
        $description = sanitizeInput($data['description']);
        $status = sanitizeInput($data['status']);
        
        $stmt = $conn->prepare("UPDATE emergencies SET location = ?, description = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssi", $location, $description, $status, $id);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Emergency updated');
        } else {
            jsonResponse(false, 'Error updating emergency');
        }
        break;
        
    case 'DELETE':
        // Delete emergency (admin only)
        $data = json_decode(file_get_contents("php://input"), true);
        $id = sanitizeInput($data['id']);
        
        $stmt = $conn->prepare("DELETE FROM emergencies WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Emergency deleted');
        } else {
            jsonResponse(false, 'Error deleting emergency');
        }
        break;
        
    default:
        jsonResponse(false, 'Invalid request method');
        break;
}
?>