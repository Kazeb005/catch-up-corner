
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

// Prepare and execute query
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$materials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get unique types and subjects for filters
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
    <style>
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filter-group {
            display: inline-block;
            margin-right: 15px;
        }
        .material-card {
            position: relative;
            padding: 20px;
            margin-bottom: 15px;
        }
        .download-btn {
            position: absolute;
            top: 15px;
            right: 15px;
        }
    </style>
</head>
<body>
    <div class="dashboard-nav">
        <div>
            <span>Catch-Up corner</span>
        </div>

        <div>
            <nav>
                <a href="#">Home</a>
                <a href="#">About</a>
                <a href="#">contact</a>
            </nav>
            <div>
                <i></i>
                <span><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                <span>student</span>
            </div>
            <button><a href="../logout.php" class="btn">Logout</a></button>
        </div>
    </div>

    <div class="dashboard-header">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
        <p>Catch up on missed materials and stay on track with your studies</p>
        
    </div>

    <div class="dashboard-content">
        <h2>Find Your Materials</h2>
        <p>Search and filter through available study materials</p>
        
        <div class="filter-section">
            <form method="GET" action="materials.php">
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
                
                <button type="submit" class="btn">Apply Filters</button>
                <?php if (!empty($search) || !empty($type_filter) || !empty($subject_filter)): ?>
                    <a href="materials.php" class="btn btn-outline">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <h2>Available Materials (<?php echo count($materials); ?>)</h2>
        
        <?php if (empty($materials)): ?>
            <div class="alert alert-info">No materials found matching your criteria</div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</body>
</html>