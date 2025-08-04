   <header>
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
                    <span class="id_teach">Teacher</span>
                </div>
                <a href="../logout.php" class="btn-logout"><object type="image/svg+xml" data="../assets/icon/logout.svg" class="svg-icon" width="16" height="16"></object>
                    Logout</a>
            </div>
        </div>
    </header>