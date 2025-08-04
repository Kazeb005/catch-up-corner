<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../teacher/materials.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Initialize filters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? sanitizeInput($_GET['type']) : '';
$subject_filter = isset($_GET['subject']) ? sanitizeInput($_GET['subject']) : '';

// Build query with filters
$query = "SELECT m.*, u.fullname AS teacher_name 
          FROM materials m
          JOIN users u ON m.teacher_id = u.id
          WHERE 1=1";

$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (m.title LIKE ? OR m.description LIKE ? OR u.fullname LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

if (!empty($type_filter)) {
    $query .= " AND m.type = ?";
    $params[] = $type_filter;
    $types .= 's';
}

if (!empty($subject_filter)) {
    $query .= " AND m.subject = ?";
    $params[] = $subject_filter;
    $types .= 's';
}

$query .= " ORDER BY m.created_at DESC";


$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$materials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


$types_stmt = $conn->query("SELECT DISTINCT type FROM materials");
$subjects_stmt = $conn->query("SELECT DISTINCT subject FROM materials");
$types_list = $types_stmt->fetch_all(MYSQLI_ASSOC);
$subjects_list = $subjects_stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Materials - Catch-Up Corner</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <span>student</span>
            </div>
            <a href="../logout.php" class="btn-logout"><object type="image/svg+xml" data="../assets/icon/logout.svg" class="svg-icon" width="16" height="16"></object>
                Logout</a>
        </div>
    </div>

    <div class="dashboard-header">
        <div class="dashboard-header-cont">
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
            <p>Catch up on missed materials and stay on track with your studies</p>

        </div>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-content-head">

            <div class="dashboard-content-intro">
                <h3>Find Your Materials</h3>
                <p>Search and filter through available study materials</p>
            </div>

            <div class="filter-section">
                <form method="GET" action="materials.php">

                    <div class="filter-groups">
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Search materials..."
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <div class="filter-group">
                            <select name="type">
                                <option value="">All Types</option>
                                <?php foreach ($types_list as $type): ?>
                                    <option value="<?php echo $type['type']; ?>"
                                        <?php echo $type_filter === $type['type'] ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($type['type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <select name="subject">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects_list as $subject): ?>
                                    <option value="<?php echo $subject['subject']; ?>"
                                        <?php echo $subject_filter === $subject['subject'] ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($subject['subject']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                    <button type="submit" class="btn-apply-filters">Apply Filters</button>
                    <?php if (!empty($search) || !empty($type_filter) || !empty($subject_filter)): ?>
                        <a href="materials.php" class="btn btn-outline">Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>

        </div>

        <div class="material-part">


            <h2>Available Materials (<?php echo count($materials); ?>)</h2>

            <?php if (empty($materials)): ?>
                <div class="alert alert-info">No materials found matching your criteria</div>
            <?php else: ?>
                <div class="material-container">
                    <?php foreach ($materials as $material): ?>
                        <div class="material-card">
                            <a href="download.php?id=<?php echo $material['id']; ?>" class="btn download-btn">Download</a>

                            <h3><?php echo htmlspecialchars($material['title']); ?></h3>
                            <p><strong><?php echo ucfirst($material['type']); ?> - <?php echo ucfirst($material['subject']); ?></strong></p>
                            <p><?php echo htmlspecialchars($material['description']); ?></p>

                            <div class="material-meta">
                                <p>Uploaded: <?php echo date('M j, Y', strtotime($material['created_at'])); ?>
                                    by <?php echo htmlspecialchars($material['teacher_name']); ?></p>
                                <p>Downloads: <?php echo $material['download_count']; ?> |
                                    File Type: <?php echo strtoupper($material['file_type']); ?> |
                                    Size: <?php echo round($material['file_size'] / 1024 / 1024, 2); ?> MB</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
</body>
<script src="../assets/js/"></script>

</html>