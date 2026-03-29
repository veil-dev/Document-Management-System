<?php
require_once 'includes/config.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Fetch document details
    $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) {
        $error = 'Document not found.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }

    $filePath = $doc['file_path'];

    // Delete physical file
    if (file_exists($filePath)) {
        if (!unlink($filePath)) {
            $error = 'Could not delete file from server.';
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            } else {
                die($error);
            }
        }
    }

    // Delete database record
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
    if ($stmt->execute([$id])) {
        if ($isAjax) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            header("Location: staff_dashboard.php?delete=success");
            exit;
        }
    } else {
        $error = 'Database deletion failed.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        } else {
            die($error);
        }
    }
}

// If not a valid request
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;