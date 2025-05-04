<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'location' => $_POST['location'],
        'description' => $_POST['description'],
        'media' => $_POST['media'] ?? null
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost/event-emergency-system/api/emergencies.php', false, $context);
    $response = json_decode($result, true);
    
    if ($response['success']) {
        header("Location: index.php?success=1");
        exit;
    } else {
        $error = "Error reporting emergency: " . $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Emergency Reporting System</h1>
        <a href="admin/dashboard.php" class="btn">Admin Dashboard</a>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Emergency reported successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="location">Emergency Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="media">Media (optional):</label>
                <input type="text" id="media" name="media" placeholder="URL to image or video">
            </div>
            
            <button type="submit" class="btn emergency">Report Emergency</button>
        </form>
    </div>
</body>
</html>