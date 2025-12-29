<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password - Shakti Bhuvan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="text-center mb-4">Reset Password</h4>
                        <form action="reset_logic.php" method="POST">
                            <div class="mb-3">
                                <label>Registered Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter your mobile"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <button type="submit" name="reset_btn" class="btn btn-primary w-100">Update
                                Password</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="admin.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>