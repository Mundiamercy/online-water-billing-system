<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: _login.php");
    exit();
}

// Fetch all registered users (without email column)
$stmtUsers = $pdo->query("SELECT id, full_name, username, phone FROM users ORDER BY full_name ASC");
$users = $stmtUsers->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body, html {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .logout {
            text-align: right;
            margin-bottom: 15px;
        }
        a.logout-link, a.dashboard-link {
            color: #fff;
            background-color: #007BFF;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px;
        }
        #toggleFormBtn {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 10px;
        }
        #addUserForm {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
            width: 350px;
            display: none;
            margin-bottom: 30px;
        }
        #addUserForm input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #addUserForm button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        #userSearch {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-width: 800px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 900px;
            background: white;
            box-shadow: 0 0 10px #ccc;
            margin-bottom: 50px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            word-break: break-word;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a.action-link {
            margin: 0 5px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
        a.edit-link {
            color: #007BFF;
        }
        a.delete-link {
            color: #dc3545;
        }
        h2 {
            text-align: center;
            margin-top: 0;
        }
    </style>
</head>
<body>

<div class="logout">
    Welcome, <?= htmlspecialchars($_SESSION['admin_username']); ?> |
    <a href="admin_logout.php" class="logout-link">Logout</a>
    <a href="admin_dashboard.php" class="dashboard-link">Water Usage Records</a>
</div>

<h2>User Management</h2>

<button id="toggleFormBtn">+ Add New User</button>

<form method="POST" action="admin_register_user.php" id="addUserForm">
    <input type="text" name="full_name" placeholder="Full Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <!-- Removed email input -->
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="phone" placeholder="Phone Number (optional)">
    <button type="submit">Add User</button>
</form>

<input type="text" id="userSearch" placeholder="Search users by name, username, or phone...">

<table id="usersTable">
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td>
                    <a href="admin_edit_user.php?id=<?= $user['id'] ?>" class="action-link edit-link">Edit</a>
                    <a href="admin_delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="action-link delete-link">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No registered users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    // Toggle add user form visibility
    const toggleFormBtn = document.getElementById('toggleFormBtn');
    const addUserForm = document.getElementById('addUserForm');

    toggleFormBtn.addEventListener('click', () => {
        if (addUserForm.style.display === 'block') {
            addUserForm.style.display = 'none';
            toggleFormBtn.textContent = '+ Add New User';
        } else {
            addUserForm.style.display = 'block';
            toggleFormBtn.textContent = '− Hide Form';
        }
    });

    // Search filter for users
    document.getElementById('userSearch').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');

        rows.forEach(row => {
            const fullName = row.cells[0].textContent.toLowerCase();
            const username = row.cells[1].textContent.toLowerCase();
            const phone = row.cells[2].textContent.toLowerCase();

            if (fullName.includes(filter) || username.includes(filter) || phone.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
