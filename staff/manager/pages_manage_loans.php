<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Loans</title>
    <!-- Include any necessary CSS files here -->
</head>
<body>
    <!-- Include header/navigation bar -->
    <?php include("dist/_partials/nav.php"); ?>

    <div class="container">
        <h1>Manage Loans</h1>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Loan ID</th>
                    <th>Client Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch loans data from the database
                $fetch_loans_query = "SELECT * FROM loans";
                $result = $mysqli->query($fetch_loans_query);
                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['loan_id']; ?></td>
                            <td><?php echo $row['client_name']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending') { ?>
                                    <a href="approve_loan.php?loan_id=<?php echo $row['loan_id']; ?>">Approve</a>
                                <?php } ?>
                                <a href="delete_loan.php?loan_id=<?php echo $row['loan_id']; ?>" onclick="return confirm('Are you sure you want to delete this loan?')">Delete</a>
                            </td>
                        </tr>
                        <?php
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No loans found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Include any necessary JavaScript files here -->
</body>
</html>
