<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// Check if the user has permission for this page
if (!in_array("items_and_inventory", $_SESSION['permissions'])) {
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
        <title>Roll Registry</title>
        <script>

            function bodyLoaded() {
                const inputs = document.querySelectorAll('input');
                for (let i = 0; i < inputs.length; i++) {
                    const element = inputs[i];
                    element.value = "";  
                }
            }

            function rollIsPanelClicked(event) {
                var checkbox = event.target;
                var rollColor2 = checkbox.closest('tr').querySelector("#rollColor2");
                var panel_span = document.getElementById("panel_span");
                if (checkbox.checked) {
                    rollColor2.disabled = false;
                    panel_span.innerHTML = "Panel";
                } else {
                    rollColor2.disabled = true;
                    rollColor2.selectedIndex = 0;
                    panel_span.innerHTML = "Plain";
                }
            }
            
            function checkForm(event) {
                var panel_check = document.getElementById("rollIsPanel");
                var rollColor2 = document.getElementById("rollColor2");
                if (panel_check && panel_check.checked && rollColor2.options[rollColor2.selectedIndex].value.trim() === "" ) {
                    Swal.fire({
                        title: 'Action incomplete',
                        text: 'A panel roll must have 2 colors selected.',
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonText: 'Go back'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            return false;
                        }
                    });
                    return false;
                }
                
                Swal.fire({
                    title: 'Confirm',
                    text: 'Add roll?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Register',
                    cancelButtonText: 'Go back',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // return true;
                        event.target.submit();
                    }
                });
                return false;
            }
        </script>
    </head>

    <body onload="bodyLoaded()">
        <div class="rollsEntryMainDiv">
            <h3>Rolls Entry</h3>
            <form action="dash.php?page=inventory/process_roll_entry" method="POST" onsubmit="return checkForm(event)">
                <table>
                    <thead>
                        <tr>
                            <th>Panel</th>
                            <th>Width (cm)</th>
                            <th>Length (m)</th>
                            <th>Weight (kg)</th>
                            <th>Color</th>
                            <th>Features</th>
                            <th>Price (KSh, VAT Inc.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" onclick="rollIsPanelClicked(event)" name="rollIsPanel" id="rollIsPanel" value="rollIsPanel" />
                            </td>
                            <td>
                                <input type="number" name="roll_width" id="roll_width" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="roll_length" id="roll_length" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="roll_weight" id="roll_weight" min="0.1" step="any" required>
                            </td>
                            <td class="rollColors">
                                <select name="rollColor1" id="rollColor1" required>
                                    <optgroup>
                                        <option value="">--</option>
                                        <option value="Green">Green</option>
                                        <option value="Multicolor">Multicolor</option>
                                        <option value="White">White</option>
                                        <option value="Yellow">Yellow</option>
                                    </optgroup>
                                </select>
                                <select name="rollColor2" id="rollColor2" disabled aria-disabled="true">
                                    <optgroup>
                                        <option value="">--</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Red">Red</option>
                                        <option value="Green">Green</option>
                                    </optgroup>
                                </select>
                                <span id="panel_span">Plain</span>

                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="gussette_feature" id="gussette_feature" value="Gussetted">
                                            </td>
                                            <td><label for="gussette_feature">Gussetted</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="lamination_feature" id="lamination_feature" value="Laminated">
                                            </td>
                                            <td><label for="lamination_feature">Laminated</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="printing_feature" id="printing_feature" value="Printed">
                                            </td>
                                            <td><label for="printing_feature">Printed</label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <input type="number" name="roll_price" id="roll_price" min="1" required>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit">Add roll</button>
            </form>
        </div>
    </body>
</html>