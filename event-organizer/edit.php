<?php
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Fetch the event to edit
$event_url = 'http://localhost/event-emergency-system/api/events.php?id=' . $id;
$event = json_decode(file_get_contents($event_url), true)['data'];

if (!$event) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'name' => $_POST['name'],
        'location' => $_POST['location'],
        'date' => $_POST['date'],
        'attendees' => $_POST['attendees']
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'PUT',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost/event-emergency-system/api/events.php', false, $context);
    $response = json_decode($result, true);
    
    if ($response['success']) {
        header("Location: index.php?success=1");
        exit;
    } else {
        $error = "Error updating event: " . $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        <a href="index.php" class="btn">Back to Events</a>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Event Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="datetime-local" id="date" name="date" value="<?= htmlspecialchars(str_replace(' ', 'T', substr($event['date'], 0, 16))); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="attendees">Number of Attendees:</label>
                <input type="number" id="attendees" name="attendees" value="<?= htmlspecialchars($event['attendees']) ?>" required>
            </div>
            
            <button type="submit" class="btn">Update Event</button>
        </form>
    </div>
</body>
</html>