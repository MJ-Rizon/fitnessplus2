<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Login/admin_login.php");
    exit;
}
include '../config.php';


$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // GET method: show the data of the selected coach
  if (!isset($_GET["id"])) {
    header("location: ../coach.php");
    exit;
  }

  $id = intval($_GET["id"]);

  // read the row of the selected coach from database table
  $sql = "SELECT * FROM coach WHERE id=$id";
  $result = mysqli_query($connection, $sql);
  $row = mysqli_fetch_assoc($result);

  if (!$row) {
    header("location: ../coach.php");
    exit;
  }

  $name = $row["name"];
  $birth_date = $row["birth_date"];
  $specialization = $row["specialization"];
  $contact_number = $row["contact_number"];
  $email = $row["email"];
}
else {
  // POST method: update the data of the selected coach
  $id = $_POST["id"];
  $name = $_POST["name"];
  $birth_date = $_POST["DoB"];
  $specialization = $_POST["specialization"];
  $contact_number = $_POST["contactNumber"];
  $email = $_POST["email"];
  $password = $_POST["password"];

  do {
    if (empty($name) || empty($birth_date) || empty($specialization) || empty($contact_number) || empty($email)) {
      $errorMessage = "All the fields are required";
      break;
    }

    // update the coach in database
    if (!empty($password)) {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $sql = "UPDATE coach SET name='$name', birth_date='$birth_date', specialization='$specialization', contact_number='$contact_number', email='$email', password='$hashedPassword' WHERE id=$id";
    } else {
      $sql = "UPDATE coach SET name='$name', birth_date='$birth_date', specialization='$specialization', contact_number='$contact_number', email='$email' WHERE id=$id";
    }
    $result = mysqli_query($connection, $sql);

    if (!$result) {
      $errorMessage = "Invalid query: " . mysqli_error($connection);
      break;
    }

    $successMessage = "Coach updated correctly";
    header("location: ../coach.php");
    exit;

  } while (true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1>Edit Coach</h1>

        <?php
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
            ";
        } 
        ?>

        <form method="post">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="inputCName" class="form-label">Coach Name</label>
                    <input type="text" name="name" class="form-control" id="inputCName" placeholder="Name" value="<?php echo $name; ?>">
                </div>
                <div class="col-md-6">
                    <label for="inputDob" class="form-label">Date of Birth</label>
                    <input type="date" name="DoB" class="form-control" id="inputDob" placeholder="Date" value="<?php echo $birth_date; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="inputSpecialization" class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" id="inputSpecialization" placeholder="Specialization" value="<?php echo $specialization; ?>">
                </div>
                <div class="col-md-6">
                    <label for="inputNumber" class="form-label">Contact Number</label>
                    <input type="text" name="contactNumber" class="form-control" id="inputNumber" placeholder="Contact Number" value="<?php echo $contact_number; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="inputEmail" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email" value="<?php echo $email; ?>">
                </div>
            </div>
            <?php
            if (!empty($successMessage)) {
                echo "
                    <div class ='row mb-3'>
                        <div class='offset-sm-3 col-sm-6'>
                            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <strong>$successMessage</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        </div>
                    </div>
                    ";
            }
            ?>

            <!-- buttons submit and cancel start -->
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary" value="value">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="../coach.php">cancel</a>
                </div>
            </div>
            <!-- buttons submit and cancel end -->
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>