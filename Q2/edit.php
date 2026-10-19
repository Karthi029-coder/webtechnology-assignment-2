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

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_number"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name === "" || $roll === "" || $course === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter valid student details.";
    } else {
        try {
            $stmt = $conn->prepare(
                "UPDATE students SET name=?, roll_number=?, email=?, course=? WHERE id=?"
            );
            $stmt->execute([$name, $roll, $email, $course, $id]);
            header("Location: view.php?id=" . $id);
            exit;
        } catch (PDOException $ex) {
            $message = "Update failed. Check whether the roll number is already used.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header"><h1>Edit Student</h1></header>
<main class="container">
    <section class="panel">
        <?php if ($message !== ""): ?><p class="error-message"><?= e($message) ?></p><?php endif; ?>
        <form method="post">
            <label for="name">Student Name</label>
            <input id="name" name="name" required maxlength="100" value="<?= e($student["name"]) ?>">

            <label for="roll_number">Roll Number</label>
            <input id="roll_number" name="roll_number" required maxlength="30" value="<?= e($student["roll_number"]) ?>">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" required maxlength="120" value="<?= e($student["email"]) ?>">

            <label for="course">Course</label>
            <input id="course" name="course" required maxlength="100" value="<?= e($student["course"]) ?>">

            <button type="submit">Update Student</button>
            <a class="btn" href="view.php?id=<?= e($id) ?>">Cancel</a>
        </form>
        <p class="hint">To add another file, return to the dashboard and use a separate upload-enabled workflow. Existing file metadata remains linked to this student.</p>
    </section>
</main>
</body>
</html>
