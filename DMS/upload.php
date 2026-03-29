<?php
require_once 'includes/config.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $studentId = $_POST['student_id'] ?? 0;
    $typeId = $_POST['type_id'] ?? 0;

    if (!$studentId || !$typeId) {
        $error = 'Missing student or document type.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }

    // Validate file type
    $allowed = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png'];
    $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $error = 'Invalid file type. Allowed: ' . implode(', ', $allowed);
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }

    // Check file size (optional, e.g., max 5MB)
    if ($_FILES['document']['size'] > 5 * 1024 * 1024) {
        $error = 'File too large (max 5MB).';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }

    // Generate unique filename and move
    $newFileName = uniqid() . '.' . $ext;
    $uploadPath = 'uploads/' . $newFileName;
    if (!move_uploaded_file($_FILES['document']['tmp_name'], $uploadPath)) {
        $error = 'Failed to save file. Check folder permissions.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }

    // Insert into database (if a document already exists for this student/type, you might want to delete the old one first)
    // Here we simply insert a new record – you may want to handle overwrites differently.
    $stmt = $pdo->prepare("INSERT INTO documents (student_id, document_type_id, file_path, file_name, status) VALUES (?, ?, ?, ?, 'submitted')");
    $fileName = $_FILES['document']['name'];
    if ($stmt->execute([$studentId, $typeId, $uploadPath, $fileName])) {
        $docId = $pdo->lastInsertId();
        if ($isAjax) {
            echo json_encode(['success' => true, 'doc_id' => $docId, 'file_path' => $uploadPath]);
            exit;
        } else {
            header("Location: staff_dashboard.php?upload=success");
            exit;
        }
    } else {
        // Database insert failed – delete the uploaded file to avoid orphaned files
        unlink($uploadPath);
        $error = 'Database insert failed.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }
}

// If not a POST request, show an error
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;