<?php
// In a real app, you would have proper authentication here

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

// Fetch the emergency to edit
$emergency_url = 'http://localhost/event-emergency-system/api/emergencies.php?id=' . $id;
$emergency = json_decode(file_get_contents($emergency_url), true)['data'];

if (!$emergency) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'location' => $_POST['location'],
        'description' => $_POST['description'],
        'status' => $_POST['status']
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'PUT',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost/event-emergency-system/api/emergencies.php', false, $context);
    $response = json_decode($result, true);
    
    if ($response['success']) {
        header("Location: dashboard.php?success=1");
        exit;
    } else {
        $error = "Error updating emergency: " . $response['message'];
    }
}

// Fetch events for reference
$events_url = 'http://localhost/event-emergency-system/api/events.php';
$events = json_decode(file_get_contents($events_url), true)['data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Emergency</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Emergency</h1>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($emergency['location']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($emergency['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="pending" <?= $emergency['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $emergency['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $emergency['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Associated Event:</label>
                <?php if ($emergency['event_id']): ?>
                    <?php 
                        $event = array_filter($events, function($e) use ($emergency) {
                            return $e['id'] == $emergency['event_id'];
                        });
                        $event = reset($event);
                        echo htmlspecialchars($event['name'] ?? 'Unknown') . ' (ID: ' . $emergency['event_id'] . ')';
                    ?>
                <?php else: ?>
                    <p>No associated event</p>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Update Emergency</button>
        </form>
    </div>
</body>
</html>