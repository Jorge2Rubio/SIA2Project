<?php

// Fetch emergencies
$emergencies = json_decode(file_get_contents('http://localhost/event-emergency-system/api/emergencies.php'), true)['data'];

// Fetch events
$events = json_decode(file_get_contents('http://localhost/event-emergency-system/api/events.php'), true)['data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Emergency System Admin Dashboard</h1>
        <a href="../index.php" class="btn">Public View</a>
        
        <h2>Active Emergencies</h2>
        <table>
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Event</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emergencies as $emergency): ?>
                <tr>
                    <td><?= htmlspecialchars($emergency['location']) ?></td>
                    <td><?= htmlspecialchars($emergency['description']) ?></td>
                    <td><?= htmlspecialchars($emergency['status']) ?></td>
                    <td>
                        <?php if ($emergency['event_id']): ?>
                            <?php 
                                $event = array_filter($events, function($e) use ($emergency) {
                                    return $e['id'] == $emergency['event_id'];
                                });
                                $event = reset($event);
                                echo htmlspecialchars($event['name'] ?? 'Unknown');
                            ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $emergency['id'] ?>" class="btn">Edit</a>
                        <a href="delete.php?id=<?= $emergency['id'] ?>" class="btn danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Active Events</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Attendees</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['name']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['date']) ?></td>
                    <td><?= htmlspecialchars($event['attendees']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>