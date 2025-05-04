<?php
require_once 'shared.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all events or single event
        if (isset($_GET['id'])) {
            $id = sanitizeInput($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            
            if ($event) {
                jsonResponse(true, 'Event retrieved', $event);
            } else {
                jsonResponse(false, 'Event not found');
            }
        } else {
            $result = $conn->query("SELECT * FROM events");
            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            jsonResponse(true, 'Events retrieved', $events);
        }
        break;
        
    case 'POST':
        // Create new event
        $data = json_decode(file_get_contents("php://input"), true);
        $name = sanitizeInput($data['name']);
        $location = sanitizeInput($data['location']);
        $date = sanitizeInput($data['date']);
        $attendees = sanitizeInput($data['attendees']);
        
        $stmt = $conn->prepare("INSERT INTO events (name, location, date, attendees) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $location, $date, $attendees);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Event created', ['id' => $stmt->insert_id]);
        } else {
            jsonResponse(false, 'Error creating event');
        }
        break;
        
    case 'PUT':
        // Update event
        $data = json_decode(file_get_contents("php://input"), true);
        $id = sanitizeInput($data['id']);
        $name = sanitizeInput($data['name']);
        $location = sanitizeInput($data['location']);
        $date = sanitizeInput($data['date']);
        $attendees = sanitizeInput($data['attendees']);
        
        $stmt = $conn->prepare("UPDATE events SET name = ?, location = ?, date = ?, attendees = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $location, $date, $attendees, $id);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Event updated');
        } else {
            jsonResponse(false, 'Error updating event');
        }
        break;
        
    case 'DELETE':
        // Delete event
        $data = json_decode(file_get_contents("php://input"), true);
        $id = sanitizeInput($data['id']);
        
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Event deleted');
        } else {
            jsonResponse(false, 'Error deleting event');
        }
        break;
        
    default:
        jsonResponse(false, 'Invalid request method');
        break;
}
?>