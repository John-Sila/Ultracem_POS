<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

// $current_page = isset($_GET['page']) ?? $_GET['page']; // Default to 'sales' if not set

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// Check if the user has permission for this page
if (!in_array("customer_management", $_SESSION['permissions'])) {
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
    <title>Customer Management</title>
    <script>
        function checkNewCustomer(event) {

            const code1 = event.target.querySelector("#customer_contact_code1").value;
            const code2 = event.target.querySelector("#customer_contact_code2").value;
            const c1 = event.target.querySelector("#customer_phone1").value;
            const c2 = event.target.querySelector("#customer_phone2").value;

            if (c1.length !== 9) {
                Swal.fire({
                    title: 'Action blocked',
                    text: 'Contact should have 9 digits.',
                    icon: 'error',
                    confirmButtonText: 'Edit',
                }).then((result) => {
                    if (result.isConfirmed) {
                        return false;
                    }
                });
                return false;
            }

            event.target.querySelector("#fullNumber1").value = code1 + c1;
            
            if (c2.length !== 9) {
                event.target.querySelector("#fullNumber2").value = "Nil";
            } else {
                event.target.querySelector("#fullNumber2").value = code1 + c2;

            }

            Swal.fire({
                title: 'Ultracem',
                text: 'Confirm action.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Add Customer',
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
    <div class="customerManagementMainDiv">
        <div class="cMDiv1">
            <div class="innerDiv">
                <div class="leftDiv">
                    <h3>Create a New Customer</h3>
                    <form action="dash.php?page=processors/process_add_customer" method="POST" onsubmit="return checkNewCustomer(event)">
                        <input type="text" class="hideTheseInputs" name="fullNumber1" id="fullNumber1">
                        <input type="text" class="hideTheseInputs" name="fullNumber2" id="fullNumber2">
                        <table>
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><label for="customer_name">Customer Name:</label></td>
                                    <td><input type="text" class="extend" name="customer_name" id="customer_name" required></td>
                                </tr>
                                <tr>
                                    <td><label for="customer_phone1">Phone Number 1:</label></td>
                                    <td>
                                        <select name="customer_contact_code1" id="customer_contact_code1">
                                            <optgroup>
                                                <option value="+254" selected>+254</option>
                                                <option value="+255">+255</option>
                                                <option value="+256">+256</option>
                                            </optgroup>
                                        </select>
                                        <input type="number" placeholder="714253647" name="customer_phone1" id="customer_phone1" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="customer_phone2">Phone Number 2:</label></td>
                                    <td>
                                        <select name="customer_contact_code2" id="customer_contact_code2">
                                            <optgroup>
                                                <option value="+254" selected>+254</option>
                                                <option value="+255">+255</option>
                                                <option value="+256">+256</option>
                                            </optgroup>
                                        </select>
                                        <input type="number" name="customer_phone2" id="customer_phone2">
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="customer_postal_address">Postal Address:</label></td>
                                    <td><input type="text" class="extend" name="customer_postal_address" id="customer_postal_address" required></td>
                                </tr>
                                <tr>
                                    <td><label for="customer_postal_code">Postal Code:</label></td>
                                    <td><input type="number" class="extend" name="customer_postal_code" id="customer_postal_code" required></td>
                                </tr>
                                <tr>
                                    <td><label for="customer_city">City:</label></td>
                                    <td><input type="text" class="extend" name="customer_city" id="customer_city" required></td>
                                </tr>
                                <tr>
                                    <td><label for="customer_town">Town:</label></td>
                                    <td><input type="text" class="extend" name="customer_town" id="customer_town" required></td>
                                </tr>
                            </tbody>
                        </table>
        
                        <button type="submit">Add</button>
                    </form>
                </div>

                <!-- <div class="rightDiv">
                    <p>This</p>
                </div> -->

            </div>
        </div>
        
    </div>
</body>
</html>