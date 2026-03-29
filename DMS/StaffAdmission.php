<?php
session_start();
require_once 'includes/config.php';

// Fetch all students
$stmt = $pdo->query("SELECT * FROM students ORDER BY name");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch document types (the five columns)
$stmt = $pdo->query("SELECT * FROM document_types ORDER BY id");
$docTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all documents (to build status matrix)
$stmt = $pdo->query("SELECT * FROM documents");
$allDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build a lookup: student_id + document_type_id => document data
$docMatrix = [];
foreach ($allDocs as $doc) {
    $key = $doc['student_id'] . '_' . $doc['document_type_id'];
    // Keep the latest document per student/type (optional)
    $docMatrix[$key] = $doc;
}

// Calculate stats for sidebar
$totalStudents = count($students);
$totalDocs = count($allDocs);
$completeCount = 0; // Define your own logic for "complete"
$incompleteCount = 0;
// Example: count students who have at least one document of each type?
// For now we'll just set dummy numbers, but you can implement your own logic.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard · Maroon text theme</title>
    <link rel="stylesheet" href="assets/staffstyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- HEADER (maroon) -->
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> Document Management System (Staff)</h1>
            <div class="user-info">
                <span><i class="fas fa-user-tie"></i> John Gabriel C. Sambajon</span>
                <span class="badge">Admissions Staff</span>
                <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- SIDEBAR (maroon details) -->
            <aside class="sidebar">
                <div class="staff-profile">
                    <div class="staff-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="staff-name">John Gabriel C. Sambajon</div>
                    <div class="staff-role">Admissions Officer</div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-chart-pie"></i> Overview</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-number"><?= $totalStudents ?></div>
                            <div class="stat-label">Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $totalDocs ?></div>
                            <div class="stat-label">Docs</div>
                        </div>
                    </div>
                    <div style="margin-top:15px;">
                        <p>Complete: <strong><?= $completeCount ?></strong></p>
                        <p>Incomplete: <strong><?= $incompleteCount ?></strong></p>
                    </div>
                </div>

                <div class="filter-section">
                    <h4><i class="fas fa-filter"></i> Filter by Status</h4>
                    <div class="filter-option">
                        <input type="checkbox" id="filter-all" checked>
                        <label for="filter-all">All Students</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="filter-complete">
                        <label for="filter-complete">Complete</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="filter-incomplete">
                        <label for="filter-incomplete">Incomplete</label>
                    </div>
                </div>
            </aside>

            <!-- MAIN PANEL -->
            <main class="panel">
                <div class="panel-header">
                    <h2><i class="fas fa-table"></i> Student Document Checklist</h2>
                    <button class="toggle-table-btn" id="toggleFullTable" type="button">Show Full Table</button>
                </div>

                <!-- SEARCH -->
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search by USN or student name...">
                    </div>
                    <button class="search-btn"><i class="fas fa-search"></i> Search</button>
                </div>

                <!-- DOCUMENT MATRIX (all students) -->
                <div style="overflow-x: auto;">
                    <table class="matrix-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <?php foreach ($docTypes as $type): ?>
                                    <th><?= htmlspecialchars($type['name']) ?></th>
                                <?php endforeach; ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="student-cell">
                                    <div class="student-name"><?= htmlspecialchars($student['name']) ?></div>
                                    <div class="student-usn"><?= htmlspecialchars($student['usn']) ?></div>
                                </td>

                                <?php foreach ($docTypes as $type): 
                                    $key = $student['id'] . '_' . $type['id'];
                                    $doc = $docMatrix[$key] ?? null;
                                    $status = $doc ? $doc['status'] : 'missing';
                                    $docId = $doc ? $doc['id'] : 0;
                                ?>
                                <!-- Inside the cell for each document type -->
                                <!-- Inside the cell -->
                                    <td>
                                        <div class="doc-status">
                                            <span class="status-badge status-<?= $status ?>">
                                                <?= ucfirst($status) ?>
                                            </span>
                                            <div class="doc-actions">
                                                <?php if ($doc && file_exists($doc['file_path'])): ?>
                                                    <a href="view.php?id=<?= $docId ?>" class="doc-view-link" target="_blank">
                                                        <i class="fas fa-eye"></i><span>View</span>
                                                    </a>
                                                    <button type="button" class="delete-btn" data-doc-id="<?= $docId ?>" data-student="<?= $student['id'] ?>" data-type="<?= $type['id'] ?>">
                                                        <i class="fas fa-trash"></i><span>Delete</span>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <!-- Upload button (always present, but hidden when a document exists) -->
                                                <button type="button" class="upload-btn <?= ($doc && file_exists($doc['file_path'])) ? 'hidden' : '' ?>" data-student="<?= $student['id'] ?>" data-type="<?= $type['id'] ?>">
                                                    <i class="fas fa-cloud-upload-alt"></i><span>Upload</span>
                                                </button>
                                                <input type="file" id="file-<?= $student['id'] ?>-<?= $type['id'] ?>" style="display: none;" accept=".pdf,.doc,.docx,.txt,.jpg,.png">
                                            </div>
                                        </div>
                                    </td>
                                <?php endforeach; ?>

                                <td>
                                    <div class="action-buttons">
                                        <!-- Reminder button removed as requested -->
                                        <a href="student_docs.php?student_id=<?= $student['id'] ?>" class="action-btn" title="View all documents">
                                            <i class="fas fa-folder-open"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mobile-table-fallback"></div>
            </main>
        </div>
    </div>

    <!-- Your existing JavaScript (unchanged) -->
    <script>
        // Add this inside the same <script> block, after the upload logic

document.addEventListener('click', function(e) {
    // ---- UPLOAD ----
    const uploadBtn = e.target.closest('.upload-btn');
    if (uploadBtn && !uploadBtn.disabled && !uploadBtn.classList.contains('hidden')) {
        const studentId = uploadBtn.dataset.student;
        const typeId = uploadBtn.dataset.type;
        const fileInput = document.getElementById(`file-${studentId}-${typeId}`);
        
        fileInput.click();
        
        fileInput.onchange = function() {
            const file = fileInput.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('student_id', studentId);
            formData.append('type_id', typeId);
            formData.append('document', file);
            
            // Show loading state (optional)
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            
            fetch('upload.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'  // Mark as AJAX
                }
            })
            .then(async response => {
                const text = await response.text(); // Get raw text first
                try {
                    return JSON.parse(text); // Attempt to parse JSON
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Server returned invalid JSON. Check upload.php for errors.');
                }
            })
            .then(data => {
                if (data.success) {
                    // Update UI
                    const docStatusDiv = uploadBtn.closest('.doc-status');
                    const statusSpan = docStatusDiv.querySelector('.status-badge');
                    statusSpan.textContent = 'Submitted';
                    statusSpan.className = 'status-badge status-submitted';
                    
                    const actionsDiv = docStatusDiv.querySelector('.doc-actions');
                    
                    // Remove any existing view/delete (should not exist, but just in case)
                    const oldView = actionsDiv.querySelector('.doc-view-link');
                    if (oldView) oldView.remove();
                    const oldDelete = actionsDiv.querySelector('.delete-btn');
                    if (oldDelete) oldDelete.remove();
                    
                    // Add new view link
                    const viewLink = document.createElement('a');
                    viewLink.href = `view.php?id=${data.doc_id}`;
                    viewLink.className = 'doc-view-link';
                    viewLink.target = '_blank';
                    viewLink.innerHTML = '<i class="fas fa-eye"></i><span>View</span>';
                    actionsDiv.insertBefore(viewLink, uploadBtn);
                    
                    // Add delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.dataset.docId = data.doc_id;
                    deleteBtn.dataset.student = studentId;
                    deleteBtn.dataset.type = typeId;
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i><span>Delete</span>';
                    actionsDiv.insertBefore(deleteBtn, uploadBtn);
                    
                    // Hide the upload button (add hidden class)
                    uploadBtn.classList.add('hidden');
                } else {
                    alert('Upload failed: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload error: ' + error.message);
            })
            .finally(() => {
                // Re-enable upload button if not hidden (in case of error, we keep it visible)
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i><span>Upload</span>';
                fileInput.value = ''; // Reset file input
            });
        };
        return;
    }
    
    // ---- DELETE ----
    const deleteBtn = e.target.closest('.delete-btn');
    if (deleteBtn) {
        e.preventDefault();
        const docId = deleteBtn.dataset.docId;
        const studentId = deleteBtn.dataset.student;
        const typeId = deleteBtn.dataset.type;
        
        if (!confirm('Are you sure you want to delete this document?')) return;
        
        // Show loading
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        
        fetch('delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'id=' + encodeURIComponent(docId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const docStatusDiv = deleteBtn.closest('.doc-status');
                const statusSpan = docStatusDiv.querySelector('.status-badge');
                statusSpan.textContent = 'Missing';
                statusSpan.className = 'status-badge status-missing';
                
                const actionsDiv = docStatusDiv.querySelector('.doc-actions');
                
                // Remove view and delete buttons
                const viewLink = actionsDiv.querySelector('.doc-view-link');
                if (viewLink) viewLink.remove();
                deleteBtn.remove(); // remove itself
                
                // Show the upload button again (remove hidden class)
                const uploadBtn = actionsDiv.querySelector('.upload-btn');
                if (uploadBtn) {
                    uploadBtn.classList.remove('hidden');
                    uploadBtn.disabled = false;
                }
            } else {
                alert('Delete failed: ' + (data.error || 'Unknown error'));
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i><span>Delete</span>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Delete error: ' + error.message);
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i><span>Delete</span>';
        });
    }
});
        function getOrCreateFallback() {
            let fallback = document.querySelector('.mobile-table-fallback');
            if (!fallback) {
                const panel = document.querySelector('.panel');
                if (!panel) return null;
                fallback = document.createElement('div');
                fallback.className = 'mobile-table-fallback';
                panel.appendChild(fallback);
            }
            return fallback;
        }

        function renderMobileTable() {
            const table = document.querySelector('.matrix-table');
            const fallback = getOrCreateFallback();
            let toggleBtn = document.querySelector('#toggleFullTable');
            const body = document.body;
            if (!body) return;

            if (!toggleBtn) {
                const panelHeader = document.querySelector('.panel-header');
                if (panelHeader) {
                    toggleBtn = document.createElement('button');
                    toggleBtn.id = 'toggleFullTable';
                    toggleBtn.type = 'button';
                    toggleBtn.className = 'toggle-table-btn';
                    toggleBtn.textContent = 'Show Full Table';
                    panelHeader.appendChild(toggleBtn);
                    toggleBtn.addEventListener('click', () => {
                        body.classList.toggle('show-cards');
                        renderMobileTable();
                    });
                }
            }

            console.log('renderMobileTable state', { hasTable: !!table, hasToggle: !!toggleBtn, hasFallback: !!fallback, bodyDisplay: getComputedStyle(body).display });

            if (!toggleBtn) {
                console.warn('toggleFullTable button not found in renderMobileTable');
            }

            if (!table) {
                console.error('matrix-table not found');
                if (fallback) {
                    fallback.style.display = 'block';
                    fallback.innerHTML = '<div style="color:#a00;font-weight:bold;">Table data is not present in this layout.</div>';
                }
                return;
            }
            if (!fallback) {
                console.warn('mobile-table-fallback not found in renderMobileTable');
                return;
            }

            const activeOnMobile = window.innerWidth <= 800;
            if (!activeOnMobile) {
                body.classList.remove('show-cards');
                toggleBtn.style.display = 'none';
                table.style.display = 'table';
                fallback.style.display = 'none';
                return;
            }

            toggleBtn.style.display = 'inline-flex';

            if (body.classList.contains('show-cards')) {
                toggleBtn.textContent = 'Show Full Table';
            } else {
                toggleBtn.textContent = 'Show Cards';
            }

            if (!fallback.innerHTML) {
                const headers = Array.from(table.querySelectorAll('thead th')).map((th) => th.textContent.trim());
                fallback.innerHTML = '';

                table.querySelectorAll('tbody tr').forEach((row) => {
                    const card = document.createElement('div');
                    card.className = 'mobile-card';

                    Array.from(row.children).forEach((cell, index) => {
                        const line = document.createElement('div');
                        line.className = 'mobile-card-row';

                        const label = document.createElement('span');
                        label.className = 'mobile-card-label';
                        label.textContent = headers[index];

                        const value = document.createElement('span');
                        value.className = 'mobile-card-value';
                        value.innerHTML = cell.innerHTML;

                        line.appendChild(label);
                        line.appendChild(value);
                        card.appendChild(line);
                    });

                    fallback.appendChild(card);
                });
            }

            if (body.classList.contains('show-cards')) {
                table.style.display = 'none';
                fallback.style.display = 'block';
            } else {
                table.style.display = 'table';
                fallback.style.display = 'none';
            }
        }

        function setupToggleButton() {
            const body = document.body;
            if (!body) return;

            let toggleBtn = document.querySelector('#toggleFullTable');
            if (!toggleBtn) {
                const panelHeader = document.querySelector('.panel-header');
                if (panelHeader) {
                    toggleBtn = document.createElement('button');
                    toggleBtn.id = 'toggleFullTable';
                    toggleBtn.type = 'button';
                    toggleBtn.className = 'toggle-table-btn';
                    toggleBtn.textContent = 'Show Full Table';
                    panelHeader.appendChild(toggleBtn);
                }
            }

            if (!toggleBtn) {
                console.warn('toggleFullTable button not found and could not be created');
                return;
            }

            toggleBtn.addEventListener('click', () => {
                body.classList.toggle('show-cards');
                renderMobileTable();
            });
        }

        window.addEventListener('load', () => {
            setupToggleButton();
            if (window.innerWidth <= 800) {
                document.body.classList.add('show-cards');
            }
            renderMobileTable();
        });

        window.addEventListener('resize', renderMobileTable);
    </script>
</body>
</html>