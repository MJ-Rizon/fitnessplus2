
<!-- Top Navbar for Desktop -->
<nav class="top-nav">
    <a href="dashboard.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo ' active'; ?>">
        <span class="bi bi-house"></span> Home
    </a>
    <a href="schedule.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='schedule.php') echo ' active'; ?>">
        <span class="bi bi-calendar-event"></span> Schedule
    </a>
    <a href="program.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='program.php') echo ' active'; ?>">
        <span class="bi bi-activity"></span> Workout Programs
    </a>
    <a href="chatbot.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='chatbot.php') echo ' active'; ?>">
        <span class="bi bi-robot"></span> AI Chatbot
    </a>
    <a href="logout.php" class="nav-link">
        <span class="bi bi-box-arrow-right"></span> Logout
    </a>
</nav>

<!-- Bottom Navbar for Mobile -->
<nav class="bottom-nav d-flex d-sm-none justify-content-around">
    <a href="dashboard.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo ' active'; ?>">
        <span class="bi bi-house"></span><br>Home
    </a>
    <a href="schedule.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='schedule.php') echo ' active'; ?>">
        <span class="bi bi-calendar-event"></span><br>Schedule
    </a>
    <a href="program.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='program.php') echo ' active'; ?>">
        <span class="bi bi-activity"></span><br>Workout
    </a>
    <a href="chatbot.php" class="nav-link<?php if(basename($_SERVER['PHP_SELF'])=='chatbot.php') echo ' active'; ?>">
        <span class="bi bi-robot"></span><br>AI Chatbot
    </a>
    <a href="logout.php" class="nav-link">
        <span class="bi bi-box-arrow-right"></span><br>Logout
    </a>
</nav>