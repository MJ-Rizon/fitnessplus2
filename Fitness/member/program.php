<?php
session_start();
include '../db/connection.php';

// Require member login
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: Login/member_login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Programs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>

<!-- nav bar start -->
<?php include 'navbar.php'; ?>
<!-- nav bar end -->

<div class="container mt-5">
    <h2 class="mb-4">Workout Program by Muscle Group</h2>
    <div class="accordion" id="workoutAccordion">
        <?php
        // Now each exercise is an array with name, sets, and reps
        $programs = [
            'Chest' => [
                ['name' => 'Barbell Bench Press', 'sets' => 4, 'reps' => '8-12'],
                ['name' => 'Incline Dumbbell Press', 'sets' => 3, 'reps' => '10-12'],
                ['name' => 'Chest Fly Machine', 'sets' => 3, 'reps' => '12-15'],
                ['name' => 'Push-Ups', 'sets' => 3, 'reps' => '15-20'],
            ],
            'Back' => [
                ['name' => 'Pull-Ups', 'sets' => 4, 'reps' => '8-12'],
                ['name' => 'Lat Pulldown', 'sets' => 3, 'reps' => '10-12'],
                ['name' => 'Seated Row', 'sets' => 3, 'reps' => '10-12'],
                ['name' => 'Deadlift', 'sets' => 3, 'reps' => '6-8'],
            ],
            'Legs' => [
                ['name' => 'Squats', 'sets' => 4, 'reps' => '8-12'],
                ['name' => 'Leg Press', 'sets' => 3, 'reps' => '10-12'],
                ['name' => 'Lunges', 'sets' => 3, 'reps' => '12-15'],
                ['name' => 'Leg Extension', 'sets' => 3, 'reps' => '12-15'],
            ],
            'Shoulders' => [
                ['name' => 'Overhead Press', 'sets' => 4, 'reps' => '8-12'],
                ['name' => 'Lateral Raise', 'sets' => 3, 'reps' => '12-15'],
                ['name' => 'Front Raise', 'sets' => 3, 'reps' => '12-15'],
                ['name' => 'Reverse Pec Deck', 'sets' => 3, 'reps' => '12-15'],
            ],
            'Arms' => [
                ['name' => 'Bicep Curl', 'sets' => 3, 'reps' => '10-15'],
                ['name' => 'Tricep Pushdown', 'sets' => 3, 'reps' => '10-15'],
                ['name' => 'Hammer Curl', 'sets' => 3, 'reps' => '10-15'],
                ['name' => 'Tricep Dips', 'sets' => 3, 'reps' => '10-15'],
            ],
            'Abs' => [
                ['name' => 'Crunches', 'sets' => 3, 'reps' => '15-20'],
                ['name' => 'Plank', 'sets' => 3, 'reps' => '30-60 sec'],
                ['name' => 'Leg Raises', 'sets' => 3, 'reps' => '12-15'],
                ['name' => 'Russian Twist', 'sets' => 3, 'reps' => '20 (10/side)'],
            ]
        ];
        $i = 0;
        foreach ($programs as $muscle => $exercises): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $i ?>">
                    <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $i ?>">
                        <?= htmlspecialchars($muscle) ?>
                    </button>
                </h2>
                <div id="collapse<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $i ?>" data-bs-parent="#workoutAccordion">
                    <div class="accordion-body">
                        <ul>
                            <?php foreach ($exercises as $exercise): ?>
                                <li>
                                    <strong><?= htmlspecialchars($exercise['name']) ?></strong>
                                    — <?= htmlspecialchars($exercise['sets']) ?> sets × <?= htmlspecialchars($exercise['reps']) ?> reps
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>