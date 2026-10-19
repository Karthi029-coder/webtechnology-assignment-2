<?php
require "db.php";

$message = "";
$uploadDir = __DIR__ . "/uploads/";

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    exit("Unable to create uploads directory.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_number"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name === "" || $roll === "" || $course === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter valid student details.";
    } else {
        $conn->beginTransaction();
        $savedPath = null;

        try {
            $stmt = $conn->prepare(
                "INSERT INTO students (name, roll_number, email, course)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $roll, $email, $course]);
            $studentId = (int)$conn->lastInsertId();

            $file = $_FILES["document"] ?? null;
            if ($file && $file["error"] !== UPLOAD_ERR_NO_FILE) {
                if ($file["error"] !== UPLOAD_ERR_OK ||
                    $file["size"] > 2 * 1024 * 1024) {
                    throw new RuntimeException("Upload failed. Maximum file size is 2 MB.");
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file["tmp_name"]);
                $types = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "application/pdf" => "pdf"
                ];

                if (!isset($types[$mime])) {
                    throw new RuntimeException("Only JPG, PNG and PDF files are allowed.");
                }

                $filename = bin2hex(random_bytes(16)) . "." . $types[$mime];
                $savedPath = $uploadDir . $filename;
                $relativePath = "uploads/" . $filename;

                if (!move_uploaded_file($file["tmp_name"], $savedPath)) {
                    throw new RuntimeException("Unable to save uploaded file.");
                }

                $stmt = $conn->prepare(
                    "INSERT INTO student_files (student_id, file_path, file_type)
                     VALUES (?, ?, ?)"
                );
                $stmt->execute([$studentId, $relativePath, $mime]);
            }

            $conn->commit();
            $message = "Student registered successfully!";
        } catch (PDOException $ex) {
            if ($conn->inTransaction()) $conn->rollBack();
            if ($savedPath && is_file($savedPath)) unlink($savedPath);
            $message = "Could not save student. Check for duplicate roll numbers.";
        } catch (RuntimeException $ex) {
            if ($conn->inTransaction()) $conn->rollBack();
            if ($savedPath && is_file($savedPath)) unlink($savedPath);
            $message = $ex->getMessage();
        }
    }
}

$students = $conn->query(
    "SELECT * FROM students ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>College Student Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <h1>College Student Admin Panel</h1>
    <p>Student records and profile document management</p>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="#add-student">Add Student</a>
        <a href="#student-records">Student Records</a>
    </nav>
</header>

<main class="container">
    <section class="panel" id="add-student">
        <h2>Add Student</h2>
        <?php if ($message !== ""): ?>
            <p class="message"><?= e($message) ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="name">Student Name</label>
            <input id="name" name="name" required maxlength="100">

            <label for="roll_number">Roll Number</label>
            <input id="roll_number" name="roll_number" required maxlength="30">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" required maxlength="120">

            <label for="course">Course</label>
            <input id="course" name="course" required maxlength="100"
                   placeholder="e.g. Computer Science">

            <label for="document">Profile Image / ID Proof / Certificate (optional)</label>
            <input id="document" type="file" name="document"
                   accept=".jpg,.jpeg,.png,.pdf">
            <small>Allowed: JPG, PNG, PDF. Maximum size: 2 MB.</small>

            <button type="submit">Add Student</button>
        </form>
    </section>

    <section class="panel" id="student-records">
        <h2>Student Records</h2>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Roll Number</th>
                    <th>Email</th><th>Course</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$students): ?>
                <tr><td colspan="6">No student records yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= e($s["id"]) ?></td>
                    <td><?= e($s["name"]) ?></td>
                    <td><?= e($s["roll_number"]) ?></td>
                    <td><?= e($s["email"]) ?></td>
                    <td><?= e($s["course"]) ?></td>
                    <td class="actions">
                        <a class="btn" href="view.php?id=<?= e($s["id"]) ?>">View</a>
                        <a class="btn btn-edit" href="edit.php?id=<?= e($s["id"]) ?>">Edit</a>
                        <form method="post" action="delete.php"
                              onsubmit="return confirm('Delete this student?');">
                            <input type="hidden" name="id" value="<?= e($s["id"]) ?>">
                            <button class="btn btn-delete" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
<footer>College Student Admin Panel · PHP &amp; MySQL</footer>
</body>
</html>
