<?php
// Fetch events from API
$api_url = 'http://localhost/event-emergency-system/api/events.php';
$events = json_decode(file_get_contents($api_url), true)['data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Organizer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Event Organizer</h1>
        <a href="create.php" class="btn">Create New Event</a>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Attendees</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['name']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['date']) ?></td>
                    <td><?= htmlspecialchars($event['attendees']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $event['id'] ?>" class="btn">Edit</a>
                        <a href="delete.php?id=<?= $event['id'] ?>" class="btn danger">Delete</a>
                        <a href="emergency.php?event_id=<?= $event['id'] ?>" class="btn emergency">Report Emergency</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>