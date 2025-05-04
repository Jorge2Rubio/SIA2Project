<?php
// In a real app, you would have proper authentication here

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = ['id' => $id];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'DELETE',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost/event-emergency-system/api/emergencies.php', false, $context);
    $response = json_decode($result, true);
    
    if ($response['success']) {
        header("Location: dashboard.php?deleted=1");
        exit;
    } else {
        $error = "Error deleting emergency: " . $response['message'];
        header("Location: dashboard.php?error=" . urlencode($error));
        exit;
    }
}

// Fetch the emergency for confirmation
$emergency_url = 'http://localhost/event-emergency-system/api/emergencies.php?id=' . $id;
$emergency = json_decode(file_get_contents($emergency_url), true)['data'];

if (!$emergency) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Emergency</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Delete Emergency</h1>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
        
        <div class="alert warning">
            <p>Are you sure you want to delete this emergency?</p>
            <p><strong>Location:</strong> <?= htmlspecialchars($emergency['location']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($emergency['description']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($emergency['status']) ?></p>
        </div>
        
        <form method="POST">
            <button type="submit" class="btn danger">Confirm Delete</button>
            <a href="dashboard.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>