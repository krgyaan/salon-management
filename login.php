<?php
require 'includes/transactions.php';
session_start();

if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $role = htmlspecialchars($_POST['role']);


    // Validate user role
    if ($role !== 'admin' && $role !== 'branch') {
        die('Invalid role');
    }

    $user = loginUser($username, $password, $role);

    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['branch_id'] = $user['branch_id'];

        header("Location: index.php");
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bright Link Hair Fixing</title>

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <link rel="stylesheet" href="assets/css/backend-plugin.min.css">
    <link rel="stylesheet" href="assets/css/backend.css?v=1.0.0">
    <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
</head>

<body class="bg-dark-light">
    <div id="loading">
        <div id="loading-center"></div>
    </div>

    <div class="wrapper">
        <section class="login-content">
            <div class="container">
                <div class="row align-items-center justify-content-center height-self-center">
                    <div class="col-lg-8">
                        <div class="card auth-card">
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center auth-content">
                                    <div class="col-lg-7 align-self-center">
                                        <div class="p-3">
                                            <h2 class="mb-2">Log In</h2>
                                            <p>Login to stay connected.</p>
                                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="p-0 m-0">Username</label>
                                                            <input class=" form-control" type="email" name="username">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="p-0 m-0">Password</label>
                                                            <input class=" form-control" type="password" name="password">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="p-0 m-0">Log in As:</label>
                                                            <select id="role" class="form-control " name="role" required>
                                                                <option value="">Select Role</option>
                                                                <option value="admin">Admin</option>
                                                                <option value="branch">Branch</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <button type="submit" name="login" class="btn btn-primary">Log In</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 content-right">
                                        <img src="assets/images/login/login.jpg" class="img-fluid image-right" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="assets/js/backend-bundle.min.js"></script>
    <script src="assets/js/table-treeview.js"></script>
    <script src="assets/js/customizer.js"></script>
    <script async src="assets/js/chart-custom.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>
