<?php
require "db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit("Invalid student ID.");
}

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    http_response_code(404);
    exit("Student not found.");
}

$stmt = $conn->prepare(
    "SELECT * FROM student_files WHERE student_id = ? ORDER BY id DESC"
);
$stmt->execute([$id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <h1>Student Details</h1>
    <nav><a href="index.php">Back to Dashboard</a></nav>
</header>
<main class="container">
    <section class="panel">
        <h2><?= e($student["name"]) ?></h2>
        <p><strong>Roll Number:</strong> <?= e($student["roll_number"]) ?></p>
        <p><strong>Email:</strong> <?= e($student["email"]) ?></p>
        <p><strong>Course:</strong> <?= e($student["course"]) ?></p>
        <p><strong>Registered:</strong> <?= e($student["created_at"]) ?></p>

        <h2>Uploaded Files</h2>
        <?php if (!$files): ?>
            <p>No files uploaded for this student.</p>
        <?php else: ?>
            <ul class="file-list">
            <?php foreach ($files as $file): ?>
                <li>
                    <p><strong>Type:</strong> <?= e($file["file_type"]) ?></p>
                    <p><strong>Upload date:</strong> <?= e($file["upload_date"]) ?></p>
                    <?php if (in_array($file["file_type"], ["image/jpeg", "image/png"], true)): ?>
                        <img class="preview" src="<?= e($file["file_path"]) ?>"
                             alt="Student uploaded image">
                    <?php endif; ?>
                    <a class="btn" href="<?= e($file["file_path"]) ?>"
                       target="_blank" rel="noopener">Open uploaded file</a>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a class="btn" href="edit.php?id=<?= e($id) ?>">Edit Student</a>
        <a class="btn" href="index.php">Back to Student List</a>
    </section>
</main>
</body>
</html>
