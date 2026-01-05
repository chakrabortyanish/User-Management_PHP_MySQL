<?php

require __DIR__ . '/../app/config/connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header("Content-Type: application/json");

    $name = $_POST["name"] ?? '';
    $email = strtolower($_POST["email"]) ?? '';
    $password = $_POST["password"] ?? '';

    if (!$name || !$email || !$password) {
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required"
        ]);
        exit;
    }

    // check if email already exists
    $validEmail = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $validEmail->execute([$email]);
    $user = $validEmail->fetch();
    if ($user) {
        echo json_encode([
            "status" => "warning",
            "message" => "Email already exists"
        ]);
        exit;
    }

    // check password length
    if(strlen($password) < 6){
        echo json_encode([
            "status" => "warning",
            "message" => "Password must be at least 6 characters long"
        ]);
        exit;
    }

    // HASH PASSWORD BEFORE STORING
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $email, $hashedPassword]);

        echo json_encode([
            "status" => "success",
            "message" => "User created successfully"
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to create user"
        ]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="/user_management/assets/css/user.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/utils/toaster.js"></script>
</head>

<body>

    <div class="form-container">
        <form id="addUserForm" class="card">
            <h2>Add User</h2>

            <div class="input-group">
                <label>Name</label>
                <input type="text" name="name" placeholder="Enter name" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" name="submit" class="btn">Create User</button>
        </form>
    </div>

    <script>
        const form = document.getElementById("addUserForm");

        form.addEventListener("submit", function(e) {
            e.preventDefault(); // 🚫 stop page reload

            fetch("user.php", {
                    method: "POST",
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "error") {
                        toastError(data.message);
                    } else if (data.status === "warning") {
                        toastWarning(data.message);
                    } else {
                        toastSuccess(data.message);
                        form.reset();

                        // optional redirect after success
                        setTimeout(() => {
                            window.location.href = "display.php";
                        }, 2000);
                    }
                })
                .catch(() => {
                    toastError("Server error");
                });
        });
    </script>

    <!-- <script src="./utils/toaster.js"></script> -->
</body>

</html>