
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
    <title>Student Dashboard · Maroon Theme</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/studentstyles.css">
</head>
<body>
    <div class="dashboard">
        <!-- Header (maroon) -->
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> Document Management System</h1>
            <div class="user-info">
                <span><i class="fas fa-user-graduate"></i> Juan Dela Cruz</span>
                <span class="badge">USN: 2024-12345</span>
                <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sidebar (maroon accents) -->
            <aside class="sidebar">
                <div class="student-profile">
                    <div class="student-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="student-name">Juan Dela Cruz</div>
                    <div class="student-usn">USN: 2024-12345</div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-envelope"></i> Contact</h3>
                    <p>juan.delacruz@ama.edu.ph</p>
                    <small>Verified email</small>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-calendar-alt"></i> Documents Summary</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-number">4</div>
                            <div class="stat-label">Submitted</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">3</div>
                            <div class="stat-label">Verified</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">1</div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-shield-alt"></i> Security</h3>
                    <p>Last login: <strong>March 4, 2026</strong></p>
                    <p>2FA: <span style="color: #27ae60;">Enabled</span></p>
                </div>
            </aside>

            <!-- Main Panel -->
            <main class="panel">
                <div class="panel-header">
                    <h2><i class="fas fa-folder-open"></i> My Documents</h2>
                    <button class="btn-primary"><i class="fas fa-upload"></i> Upload New Document</button>
                </div>

                <!-- Table with scroll wrapper -->
                <div class="table-wrapper">
                    <table class="documents-table">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th>Type</th>
                                <th>Date Uploaded</th>
                                <th>Status</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="document-name">
                                        <i class="fas fa-file-pdf"></i> Grade 12 Report Card
                                    </div>
                                </td>
                                <td>PDF</td>
                                <td>2026-02-28</td>
                                <td><span class="status status-verified">Verified</span></td>
                                <td><button class="action-btn"><i class="fas fa-download"></i> PDF</button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="document-name">
                                        <i class="fas fa-file-image"></i> PSA Birth Certificate
                                    </div>
                                </td>
                                <td>JPG</td>
                                <td>2026-02-25</td>
                                <td><span class="status status-verified">Verified</span></td>
                                <td><button class="action-btn"><i class="fas fa-download"></i> JPG</button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="document-name">
                                        <i class="fas fa-file-pdf"></i> Good Moral Certificate
                                    </div>
                                </td>
                                <td>PDF</td>
                                <td>2026-03-01</td>
                                <td><span class="status status-submitted">Submitted</span></td>
                                <td><button class="action-btn"><i class="fas fa-download"></i> PDF</button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="document-name">
                                        <i class="fas fa-file-pdf"></i> Diploma (JHS)
                                    </div>
                                </td>
                                <td>PDF</td>
                                <td>2026-02-20</td>
                                <td><span class="status status-verified">Verified</span></td>
                                <td><button class="action-btn"><i class="fas fa-download"></i> PDF</button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="document-name">
                                        <i class="fas fa-file-image"></i> 2x2 ID Picture
                                    </div>
                                </td>
                                <td>PNG</td>
                                <td>2026-02-15</td>
                                <td><span class="status status-rejected">Rejected</span></td>
                                <td><button class="action-btn"><i class="fas fa-redo-alt"></i> Re-upload</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="scroll-hint">← Swipe to see all columns →</div>
            </main>
        </div>
    </div>
</body>
</html>