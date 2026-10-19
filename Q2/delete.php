<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit("Invalid student ID.");
}

$uploadDir = realpath(__DIR__ . "/uploads");
try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT file_path FROM student_files WHERE student_id = ?");
    $stmt->execute([$id]);
    $files = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $conn->commit();

    if ($uploadDir !== false) {
        foreach ($files as $path) {
            $fullPath = realpath(__DIR__ . "/" . $path);
            if ($fullPath !== false &&
                strpos($fullPath, $uploadDir . DIRECTORY_SEPARATOR) === 0 &&
                is_file($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    header("Location: index.php");
    exit;
} catch (Throwable $ex) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    exit("Unable to delete the student record.");
}
?>
