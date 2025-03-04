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
// Check if the user has permission for this page
if (!in_array("inside_information", $_SESSION['permissions'])) {
    // If no permission, redirect to sales page
    header("Location: dash.php?page=uc_sales");
    exit();
}


require dirname(__DIR__) . "\ultracem_pos\db.php"; // Database connection

// Fetch existing company info (id = 1)
$stmt = $pdo->prepare("SELECT * FROM company_info WHERE id = 1");
$stmt->execute();
$company = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript">
        document.querySelector("#insideInfoForm").addEventListener("submit", function (event) {
            event.preventDefault();
            let checkboxes = document.querySelectorAll("input[type='checkbox']");
            checkboxes.forEach(function (checkbox) {
                if (!checkbox.checked) {
                    let fieldName = checkbox.name.replace("fields[", "").replace("]", "");
                    let field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.disabled = true; // Prevents unchecked fields from being submitted
                    }
                }
            });

        });

        function checkNewInsideInfo(event) {
            const anyChecked = [...event.target.querySelectorAll("input[type='checkbox']")].some(checkbox => checkbox.checked);
            if (!anyChecked) {
                Swal.fire({
                    title: 'Action blocked!',
                    text: 'No checked detail.',
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonText: 'Return'
                    // cancelButtonText: 'Go back'
                }).then((result) => {
                    if (result.isConfirmed) {
                        return false;
                    }
                });
                return false;
            }
        }

        function bodyLoaded() {
            uncheckCheckBoxes();
        }

        function uncheckCheckBoxes() {
            const checkboxlist = document.querySelectorAll('input[type=checkbox]');
            for (let i = 0; i < checkboxlist.length; i++) {
                const element = checkboxlist[i];
                element.checked = false;
            }
        }

    </script>
</head>
<body onload="bodyLoaded()">
    <div class="insideInformationMainDiv">
        <div class="insideInfoDiv1">
            <h3>Company Detailer</h3>
            <div class="innerDiv">
            <div class="leftDiv">
                <form action="dash.php?page=processors/process_inside_information" method="POST" id="insideInfoForm" onsubmit="return checkNewInsideInfo(event)">
                    <table>
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Current</th>
                                <th>New</th>
                                <th>Confirm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="company_name">Company Name:</label></td>
                                <td><span><b><?= $company['company_name'] ?? 'N/A' ?></b></span></td>
                                <td><input type="text" class="extend" name="company_name" id="company_name"></td>
                                <td>
                                    <input type="checkbox" name="fields[company_name]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="company_phone1">Company Phone 1:</label></td>
                                <td><span><b><?= $company['default_phone_number'] ?? 'N/A' ?></b></span></td>
                                <td>
                                    <input type="number" placeholder="254714253647" name="company_phone1" id="company_phone1">
                                </td>
                                <td>
                                    <input type="checkbox" name="fields[company_phone1]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="company_phone2">Company Phone 2:</label></td>
                                <td><span><b><?= $company['secondary_phone_number'] ?? 'N/A' ?></b></span></td>
                                <td>
                                    <input type="number" name="company_phone2" id="company_phone2">
                                </td>
                                <td>
                                    <input type="checkbox" name="fields[company_phone2]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="company_postal_address">Postal Address:</label></td>
                                <td><span><b><?= $company['postal_address'] ?? 'N/A' ?></b></span></td>
                                <td><input type="text" class="extend" name="company_postal_address" id="company_postal_address"></td>
                                <td>
                                    <input type="checkbox" name="fields[company_postal_address]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="company_postal_code">Postal Code:</label></td>
                                <td><span><b><?= $company['postal_code'] ?? 'N/A' ?></b></span></td>
                                <td><input type="number" class="extend" name="company_postal_code" id="company_postal_code"></td>
                                <td>
                                    <input type="checkbox" name="fields[company_postal_code]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="company_city">City:</label></td>
                                <td><span><b><?= $company['city'] ?? 'N/A' ?></b></span></td>
                                <td><input type="text" class="extend" name="company_city" id="company_city"></td>
                                <td>
                                    <input type="checkbox" name="fields[company_city]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="company_town">Town:</label></td>
                                <td><span><b><?= $company['town'] ?? 'N/A' ?></b></span></td>
                                <td><input type="text" class="extend" name="company_town" id="company_town"></td>
                                <td>
                                    <input type="checkbox" name="fields[company_town]" value="1">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><span>--</span></td>
                            </tr>
                            <tr>
                                <td><label for="company_kra">KRA:</label></td>
                                <td><span><b><?= $company['kra'] ?? 'N/A' ?></b></span></td>
                                <td><input type="text" class="extend" name="company_kra" id="company_kra"></td>
                                <td>
                                    <input type="checkbox" name="fields[company_kra]" value="1">
                                </td>
                            </tr>
                        </tbody>
                    </table>
    
                    <button type="submit">Update</button>
                </form>
            </div>
            </div>
        </div>
    </div>
</body>
</html>