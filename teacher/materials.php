<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../student/materials.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get teacher stats
$stats_stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_materials,
        SUM(download_count) as total_downloads,
        GROUP_CONCAT(DISTINCT subject) as subjects_covered
    FROM materials 
    WHERE teacher_id = ?
");
$stats_stmt->bind_param("i", $_SESSION['user_id']);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

// Handle material deletion
if (isset($_GET['delete'])) {
    $material_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM materials WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $material_id, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: materials.php?deleted=1");
    exit();
}

// Get all materials for this teacher
$stmt = $conn->prepare("SELECT * FROM materials WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$materials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Materials - Catch-Up Corner</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .material-card {
            position: relative;
        }

        .material-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .teacher-stats {
            display: flex;
            justify-content: space-around;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
        }

        .stat-item {
            flex: 1;
            padding: 0 10px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #166088;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }

        .subjects-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }

        .subject-badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
    </style> -->
</head>

<body>

    <div class="dashboard-nav" id="dashboard-nav">
        <div class="nav-title">
            <img src="../assets/icon/book.svg" alt="book icon" height="32" width="32" class="">
            <span>Catch-Up corner</span>
        </div>

        <div class="nav-cont">
            <nav class="nav-links">
                <a href="materials.php">Home</a>
                <a href="about.php">About</a>
                <a href="contact.php">contact</a>
            </nav>
            <div class="nav-profile">
                <img src="../assets/icon/profile.svg" alt="icon" width="16" height="16">
                <span><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                <span>Teacher</span>
            </div>
            <a href="../logout.php" class="btn-logout"><object type="image/svg+xml" data="../assets/icon/logout.svg" class="svg-icon" width="16" height="16"></object>
                Logout</a>
        </div>
    </div>

       <div class="dashboard-header">
        <div class="dashboard-header-cont">
            <h1>Teacher Dashboard - <?php echo htmlspecialchars($_SESSION['fullname']); ?></h1>
            <p>Manage and upload educational materials for your students</p>

            <!-- <div class="teacher-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['total_materials']; ?></div>
                    <div class="stat-label">Materials Uploaded</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['total_downloads']; ?></div>
                    <div class="stat-label">Total Downloads</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo count(explode(',', $stats['subjects_covered'])); ?></div>
                    <div class="stat-label">Subjects Covered</div>
                    <div class="subjects-list">
                        <?php
                        $subjects = array_unique(explode(',', $stats['subjects_covered']));
                        foreach ($subjects as $subject):
                        ?>
                            <span class="subject-badge"><?php echo ucfirst($subject); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div> -->

        </div>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-content-head">

            <div class="dashboard-content-intro">
                <h1>Upload New Material</h1>
                <p>Share resources with your students who missed class</p>
                <button id="addMaterialBtn" class="btn">+ Add New Material</button>
            </div>
        </div>

   

    <div class="material-part">
        <h2>Your Materials (<?php echo count($materials); ?>)</h2>
        <p>Manage, edit, or delete your uploaded materials</p>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Material deleted successfully</div>
        <?php endif; ?>

        <?php foreach ($materials as $material): ?>
            <div class="material-card-teacher">

                <div class="material-detail">
                    <h3><?php echo htmlspecialchars($material['title']); ?></h3>
                    <p><strong><?php echo htmlspecialchars(ucfirst($material['subject'])); ?></strong></p>
                    <p><?php echo htmlspecialchars($material['description']); ?></p>
                    <p>Uploaded: <?php echo date('M j, Y', strtotime($material['created_at'])); ?> |
                        Downloads: <?php echo $material['download_count']; ?></p>
                    <p>File Type: <?php echo strtoupper($material['file_type']); ?></p>
                </div>
                <div class="material-actions">
                    <a href="edit.php?id=<?php echo $material['id']; ?>" class="btn">Edit</a>
                    <a href="materials.php?delete=<?php echo $material['id']; ?>" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this material?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Add Material Modal -->
    <div id="addMaterialModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Upload New Material</h2>
            <p>Fill in the details for your new educational material.</p>

            <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" placeholder="Enter material title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" placeholder="Describe the content and purpose" required></textarea>
                </div>

                <div class="form-group">
                    <label for="type">Type *</label>
                    <select id="type" name="type" required>
                        <option value="">Select type</option>
                        <option value="notes">Notes</option>
                        <option value="quiz">Quiz</option>
                        <option value="assignment">Assignment</option>
                        <option value="video">Video</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select subject</option>
                        <option value="biology">Biology</option>
                        <option value="mathematics">Mathematics</option>
                        <option value="physics">Physics</option>
                        <option value="chemistry">Chemistry</option>
                        <option value="history">History</option>
                        <option value="english">English</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="file">File Upload *</label>
                    <input type="file" id="file" name="file" required>
                    <p class="help-text">Supported formats: PDF, DOC, PPT, MP4, MOV, AVI (Max 20MB)</p>
                </div>

                <button type="submit" class="btn">Upload Material</button>
                <button type="button" class="btn btn-outline" id="cancelUpload">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Modal handling
        const modal = document.getElementById("addMaterialModal");
        const btn = document.getElementById("addMaterialBtn");
        const span = document.getElementsByClassName("close")[0];
        const cancelBtn = document.getElementById("cancelUpload");

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        cancelBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>