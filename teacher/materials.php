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
</head>

<body>
 <?php include './nav.php'; ?>

    <main>

        <div class="dashboard-header-teacher">
            <div class="dashboard-header-cont">
                <h1>Teacher Dashboard - <?php echo htmlspecialchars($_SESSION['fullname']); ?></h1>
                <p>Manage and upload educational materials for your students</p>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="dashboard-content-head">
                <div class="dashboard-content-intro">
                    <h1>Upload New Material</h1>
                    <p>Share resources with your students who missed class</p>
                    <button id="addMaterialBtn" class="btn-add-material">+ Add New Material</button>
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
                            <a href="#" class="operation edit-btn" data-id="<?php echo $material['id']; ?>">
                                <img src="../assets/icon/edit.svg" alt="edit" width="24" height="24">
                            </a>
                            <a href="materials.php?delete=<?php echo $material['id']; ?>" class="operation btn-danger"
                                onclick="return confirm('Are you sure you want to delete this material?')">
                                <img src="../assets/icon/delete.svg" alt="delete" width="24" height="24">
                            </a>
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
                        <div class="form-group-modal">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" placeholder="Enter material title" required>
                        </div>

                        <div class="form-group-modal">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" placeholder="Describe the content and purpose" required></textarea>
                        </div>

                        <div class="form-group-modal">
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

                        <div class="form-group-modal">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select subject</option>
                                <option value="Web design">Web design</option>
                                <option value="mathematics">Mathematics</option>
                                <option value="physics">Physics</option>
                                <option value="chemistry">Chemistry</option>
                                <option value="history">History</option>
                                <option value="english">English</option>
                            </select>
                        </div>

                        <div class="form-group-modal">
                            <label for="file">File Upload *</label>
                            <input type="file" id="file" name="file" required>
                        </div>

                        <div class="form-group-modal">
                            <p class="help-text">Supported formats: PDF, DOC, PPT, MP4, MOV, AVI (Max 20MB)</p>
                        </div>

                        <button type="submit" class="form-btn">Upload Material</button>
                        <button type="button" class="btn-cancel btn-outline" id="cancelUpload">Cancel</button>
                    </form>
                </div>
            </div>

            <!-- Edit Material Modal -->
            <div id="editMaterialModal" class="modal">
                <div class="modal-content">
                    <span class="close-edit">&times;</span>
                    <h2>Edit Your Material</h2>
                    <p>Update the details for your educational material.</p>
                    <form id="editForm" method="POST" action="./edit.php">
                        <input type="hidden" id="edit-id" name="id" value="">
                        <div class="form-group-modal">
                            <label for="edit-title">Title *</label>
                            <input type="text" id="edit-title" name="title" required>
                        </div>
                        <div class="form-group-modal">
                            <label for="edit-description">Description *</label>
                            <textarea id="edit-description" name="description" required></textarea>
                        </div>
                        <div class="form-group-modal">
                            <label for="edit-type">Type *</label>
                            <select id="edit-type" name="type" required>
                                <option value="notes">Notes</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                                <option value="video">Video</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group-modal">
                            <label for="edit-subject">Subject *</label>
                            <select id="edit-subject" name="subject" required>
                                <option value="Web design">Web design</option>
                                <option value="mathematics">Mathematics</option>
                                <option value="physics">Physics</option>
                                <option value="chemistry">Chemistry</option>
                                <option value="history">History</option>
                                <option value="english">English</option>
                            </select>
                        </div>
                        <div class="form-group-modal">
                            <label>Current File:</label>
                            <p id="current-file-info"></p>
                        </div>
                        <button type="submit" class="form-btn">Update Material</button>
                        <button type="button" class="btn-cancel btn-outline" id="cancelEdit">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- <footer -->
    <footer class="footer">
        <p>© Catch-Up-Corner 2025</p>
    </footer>

    <script>
        // Add Material Modal handling
        const addModal = document.getElementById("addMaterialModal");
        const addBtn = document.getElementById("addMaterialBtn");
        const addClose = document.querySelector("#addMaterialModal .close");
        const cancelBtn = document.getElementById("cancelUpload");

        addBtn.onclick = function() {
            addModal.style.display = "block";
        }

        addClose.onclick = function() {
            addModal.style.display = "none";
        }

        cancelBtn.onclick = function() {
            addModal.style.display = "none";
        }

        // Edit Material Modal handling
        const editModal = document.getElementById("editMaterialModal");
        const editClose = document.querySelector("#editMaterialModal .close-edit");
        const cancelEdit = document.getElementById("cancelEdit");
        const editBtns = document.querySelectorAll(".edit-btn");

        // Fetch material data and populate edit form
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const materialId = this.getAttribute('data-id');

                // Fetch material data via AJAX
                fetch(`get_material.php?id=${materialId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('edit-id').value = data.id;
                            document.getElementById('edit-title').value = data.title;
                            document.getElementById('edit-description').value = data.description;
                            document.getElementById('edit-type').value = data.type;
                            document.getElementById('edit-subject').value = data.subject;
                            document.getElementById('current-file-info').textContent =
                                `${data.file_path.split('/').pop()} (${(data.file_size/1024/1024).toFixed(2)} MB)`;

                            editModal.style.display = "block";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });

        editClose.onclick = function() {
            editModal.style.display = "none";
        }

        cancelEdit.onclick = function() {
            editModal.style.display = "none";
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
        }
    </script>
    <script src="../assets/js/navscroll.js"></script>
</body>

</html>