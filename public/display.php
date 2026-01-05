<?php
require __DIR__ . '/../app/config/connect.php';


$sql = "SELECT id, name, email FROM users";
$stmt = $pdo->query($sql);

$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="/user_management/assets/css/display.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/utils/toaster.js"></script>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>User Management</h2>
            <a href="user.php" class="btn-add"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffffff">
                    <path d="M440-120v-320H120v-80h320v-320h80v320h320v80H520v320h-80Z" />
                </svg> Add User</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td class="password">••••••••</td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn edit">Edit</a>
                                    <button class="btn delete" onclick="openModal(<?= $user['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No data found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Delete</h3>

            <form id="deleteForm">
                <input type="hidden" name="id" id="deleteUserId">

                <label>Email</label>
                <input type="email" name="email" placeholder="Enter email" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>

                <div class="modal-actions">
                    <button type="button" onclick="confirmDeleteSweet()" class="btn delete">Delete</button>
                    <button type="button" class="btn cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal(id) {
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function confirmDeleteSweet() {
            Swal.fire({
                title: "Are you sure?",
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e74c3c",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector("#deleteForm").requestSubmit();
                }
            });
        }
        // delete module
        const form = document.querySelector('#deleteForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('delete.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        toastError(data.message);
                    } 
                    else if(data.status === 'success'){
                        toastSuccess(data.message);
                        closeModal();
                        setTimeout(() => location.reload(), 3000);
                    }
                    else {
                        toastWarning(data.message);
                    }
                })
                .catch(() => {
                    toastError('Server error_1');
                })
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- <script>
        const form = document.getElementById("deleteForm");

        fetch("delete.php", {
                method: "POST",
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                console.log('DATA: ',data);
                if (data.status === "error") {
                    toastr.error(data.message);
                } else {
                    toastr.success("Account deleted");
                }
            });
    </script> -->
</body>

</html>