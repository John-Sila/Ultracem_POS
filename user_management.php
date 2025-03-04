<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

$current_page = isset($_GET['page']) ?? $_GET['page']; // Default to 'sales' if not set

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// echo "<script>alert('" . addslashes($current_page) . "');</script>";

// Check if the user has permission for this page
if (!in_array("user_management", $_SESSION['permissions'])) {
    // If no permission, redirect to sales page
    header("Location: dash.php?page=uc_sales");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>

    <script>
        function confirmNewUser(event) {
            Swal.fire({
                title: 'Ultracem',
                text: 'Confirm action.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Add User',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
            return false;
        }
    </script>
</head>
<body>
    <div class="userManagementMainDiv">
        <div class="uMDiv1">
            <div class="innerDiv">
                <div class="leftDiv">
                    <h3>Add a new user</h3>
                    <form action="dash.php?page=processors/process_add_user" method="POST" onsubmit="return confirmNewUser(event)">
                        <table>
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                </tr>
                            </thead>  
                            <tbody>
                                <tr>
                                    <td>Full Name:</td>
                                    <td>
                                        <input type="text" name="new_user_full_name" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Username:</td>
                                    <td>
                                        <input type="text" name="new_user_username" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td>
                                        <input type="text" name="new_user_pwd" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rights:</td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <label for="sales_right">Sales</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="uc_sales" name="sales_right" id="sales_right" checked aria-checked="true" onclick="return false">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="items_and_inventory_right">Items & Inventory</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="items_and_inventory" name="items_and_inventory_right" id="items_and_inventory_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="manufacturing_right">Manufacturing</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="manufacturing" name="manufacturing_right" id="manufacturing_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="customer_management_right">Customer Management</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="customer_management" name="customer_management_right" id="customer_management_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="user_management_right">User Management</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="user_management" name="user_management_right" id="user_management_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="accounts_right">Accounts</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="accounts" name="accounts_right" id="accounts_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="inside_information_right">Inside Information</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="inside_information" name="inside_information_right" id="inside_information_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="db_management_right">Database Management</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="database_management" name="db_management_right" id="db_management_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="suppliers_right">Suppliers</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="suppliers" name="suppliers_right" id="suppliers_right">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="human_resource_right">Human Resource</label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="human_resource" name="human_resource_right" id="human_resource_right">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit">Create user</button>
                    </form>
                </div>
                <div class="rightDiv"></div>
            </div>

        </div>
    </div>
</body>
</html>