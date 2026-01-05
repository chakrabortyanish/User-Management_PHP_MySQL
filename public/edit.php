<?php
require __DIR__ . '/../app/config/connect.php';


/* -------------------------------
   UPDATE LOGIC (POST REQUEST)
--------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id    = $_POST['id'] ?? null;
    $name  = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email']) ?? '');

    if (!$id || !$name || !$email) {
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required"
        ]);
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE users SET name = ?, email = ? WHERE id = ?"
    );
    $stmt->execute([$name, $email, $id]);

    echo json_encode([
        "status" => "success",
        "message" => "User updated successfully"
    ]);
    exit;
}

// FETCH DATA (GET REQUEST)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;
    if (!$id) die("Invalid ID");

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<p style='text-align: center'>User not found</p>";
    };
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="/user_management/assets/css/edit.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/utils/toaster.js"></script>
</head>

<body>

    <div class="form-container">
        <form class="card" id="editForm">
            <h2>Edit User</h2>

            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="input-group">
                <label>Name</label>
                <input type="text" name="name"
                    value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email"
                    value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <button type="submit" class="btn">Update User</button>

            <a href="display.php" class="back-link">← Back to User List</a>
        </form>
    </div>

    <script>
        const form = document.querySelector('#editForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('edit.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        toastError(data.message);
                    } else {
                        toastSuccess(data.message);
                        setTimeout(() => {
                            window.location.href = 'display.php';
                        }, 3000);
                    }
                })
                .catch(() => {
                    toastError('Server error');
                })
        });
    </script>

</body>

</html>