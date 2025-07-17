<?php
include 'url_restrictrion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness+ Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for status icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
      .status-expired {
        color: #dc3545 !important; /* Bootstrap danger/red */
        font-weight: bold;
      }
      .status-active {
        color: #198754 !important; /* Bootstrap success/green */
        font-weight: bold;
      }
      .status-pending {
        color: #fd7e14 !important; /* Bootstrap orange */
        font-weight: bold;
      }
    </style>
</head>
<body>
  
<!-- nav bar start -->
<?php include 'navbar.php'; ?>
<!-- nav bar end -->

<div id="content">
  <div id="content-header">
    <h1 class="text-center">Member's Current Status <i class="fas fa-eye"></i></h1>
  </div>
  <div class="container-fluid">
    <?php

    // Count active and expired members
    $activeCount = 0;
    $expiredCount = 0;
    $today = date('Y-m-d');
    $countQry = "SELECT dor, plan FROM members";
    $countResult = mysqli_query($connection, $countQry);
    while ($row = mysqli_fetch_assoc($countResult)) {
        $planMonths = (int)$row['plan'];
        $registrationDate = $row['dor'];
        $expDate = '';
        if (!empty($registrationDate) && $planMonths > 0) {
            $expDateObj = new DateTime($registrationDate);
            $expDateObj->modify("+$planMonths months");
            $expDate = $expDateObj->format('Y-m-d');
            if ($expDate >= $today) {
                $activeCount++;
            } else {
                $expiredCount++;
            }
        } else {
            $expiredCount++;
        }
    }
    ?>

    <!-- Display total active and expired members below the status title -->
    <div class="mb-4">
        <span class="badge bg-success">Active Members: <?php echo $activeCount; ?></span>
        <span class="badge bg-danger">Expired Members: <?php echo $expiredCount; ?></span>
    </div>
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class='widget-box'>
          <div class='widget-title'>
            <span class='icon'> <i class='fas fa-th'></i> </span>
            <h5>Status table</h5>
          </div>
          <div class='widget-content nopadding'>
      
      <!-- Add Member button and Search bar start -->
      <div class="p-3 d-flex justify-content-between align-items-center">
        <a href="action/addMember.php" class="btn btn-success">+ Add Member</a>
        <input type="text" id="memberSearch" class="form-control w-50" placeholder="Search members...">
      </div>
      <!-- Add Member button and Search bar end -->

      <?php
      // Fetch members from the database
      $qry = "SELECT * FROM members";
      $result = mysqli_query($connection, $qry);

      // Prepare arrays for sorting
      $activeRows = [];
      $pendingRows = [];
      $expiredRows = [];
      $today = date('Y-m-d');

      while ($row = mysqli_fetch_assoc($result)) {
        $planMonths = (int)$row['plan'];
        $registrationDate = $row['dor'];

        // Compute expiration date
        $expDate = '';
        if (!empty($registrationDate) && $planMonths > 0) {
            $expDateObj = new DateTime($registrationDate);
            $expDateObj->modify("+$planMonths months");
            $expDate = $expDateObj->format('Y-m-d');
        }

        $statusClass = '';
        $statusText = '';
        $statusIcon = '';

        if (empty($registrationDate) || $planMonths <= 0) {
            $statusClass = 'status-pending';
            $statusIcon = '<i class="fas fa-circle"></i>';
            $statusText = 'Pending Reg';
            $pendingRows[] = [
                'row' => $row,
                'registrationDate' => $registrationDate,
                'expDate' => $expDate,
                'statusClass' => $statusClass,
                'statusIcon' => $statusIcon,
                'statusText' => $statusText
            ];
        } elseif ($expDate >= $today) {
            $statusClass = 'status-active';
            $statusIcon = '<i class="fas fa-circle"></i>';
            $statusText = 'Active';
            $activeRows[] = [
                'row' => $row,
                'registrationDate' => $registrationDate,
                'expDate' => $expDate,
                'statusClass' => $statusClass,
                'statusIcon' => $statusIcon,
                'statusText' => $statusText
            ];
        } else {
            $statusClass = 'status-expired';
            $statusIcon = '<i class="fas fa-circle"></i>';
            $statusText = 'Expired';
            $expiredRows[] = [
                'row' => $row,
                'registrationDate' => $registrationDate,
                'expDate' => $expDate,
                'statusClass' => $statusClass,
                'statusIcon' => $statusIcon,
                'statusText' => $statusText
            ];
        }
      }

      echo "<table class='table table-bordered table-hover data-table'>
        <thead>
          <tr>
            <th>User ID</th>
            <th>Fullname</th>
            <th>Contact Number</th>
            <th>Registration Date</th>
            <th>Expiration Date</th>
            <th>Plan</th>
            <th>Membership Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>";

      // Show Active first, then Pending, then Expired
      foreach ([$activeRows, $pendingRows, $expiredRows] as $group) {
        foreach ($group as $item) {
            $row = $item['row'];
            $editUrl = "action/editMember.php?id=" . $row['user_id'];
            $deleteUrl = "action/deleteMember.php?id=" . $row['user_id'];
            $paymentUrl = "action/paymentMember.php?user_id=" . $row['user_id'];
            echo "<tr>
                <td><div class='text-center'>{$row['user_id']}</div></td>
                <td><div class='text-center'>{$row['fullname']}</div></td>
                <td><div class='text-center'>{$row['contact']}</div></td>
                <td><div class='text-center'>{$item['registrationDate']}</div></td>
                <td><div class='text-center'>{$item['expDate']}</div></td>
                <td><div class='text-center'>{$row['plan']} Month/s</div></td>
                <td><div class='text-center {$item['statusClass']}'>{$item['statusIcon']} {$item['statusText']}</div></td>
                <td class='text-center'>
                    <a class='btn btn-primary btn-sm' href='{$paymentUrl}'><i class='fas fa-money-bill-wave'></i> Payment</a>
                    <a class='btn btn-warning btn-sm' href='{$editUrl}'><i class='fas fa-edit'></i> Edit</a>
                    <a class='btn btn-danger btn-sm' href='{$deleteUrl}' onclick=\"return confirm('Are you sure you want to delete this member?');\"><i class='fas fa-trash'></i> Delete</a>
                </td>
              </tr>";
        }
      }
      echo "</tbody></table>";
      ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script src="js/searchMember.js"></script>
</body>
</html>