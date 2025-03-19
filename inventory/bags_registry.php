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
        <title>Bag Registry</title>
        <script>

            function bodyLoaded() {
                const inputs = document.querySelectorAll('input');
                for (let i = 0; i < inputs.length; i++) {
                    const element = inputs[i];
                    element.value = "";  
                }

                uncheckCheckBoxes();
            }

            function bagIsPanelClicked(event) {
                var checkbox = event.target;
                var bagColor2 = checkbox.closest('tr').querySelector("#bagColor2");
                var panel_span = document.getElementById("panel_span");
                if (checkbox.checked) {
                    bagColor2.disabled = false;
                    panel_span.innerHTML = "Panel";
                } else {
                    bagColor2.disabled = true;
                    bagColor2.selectedIndex = 0;
                    panel_span.innerHTML = "Plain";
                }
            }
            
            function checkForm(event) {
                event.preventDefault(); // Stop form from submitting immediately

                var panel_check = document.getElementById("bagIsPanel");
                var bagColor2 = document.getElementById("bagColor2");

                if (panel_check && panel_check.checked && bagColor2.value.trim() === "") {
                    Swal.fire({
                        title: 'Action incomplete',
                        text: 'A panel bag must have 2 colors selected.',
                        icon: 'error',
                        confirmButtonText: 'Go back'
                    });
                    return; // Stop here
                }

                Swal.fire({
                    title: 'Confirm',
                    text: 'Register Bag?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Register',
                    cancelButtonText: 'Go back',
                }).then((result) => {
                    if (result.isConfirmed) {
                        event.target.submit(); // Submit the form properly
                    }
                });
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
        <div class="bagsRegistryMainDiv">
            <h3>Bags Registry</h3>
            <form action="dash.php?page=inventory/process_bags_registry" method="POST" onsubmit="return checkForm(event)">
                <table>
                    <thead>
                        <tr>
                            <th>Panel</th>
                            <th>Width (cm)</th>
                            <th>Length (cm)</th>
                            <th>Color</th>
                            <th>Features</th>
                            <th>Price (KSh, VAT Inc.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" onclick="bagIsPanelClicked(event)" name="bagIsPanel" id="bagIsPanel" value="bagIsPanel" />
                            </td>
                            <td>
                                <input type="number" name="bag_width" id="bag_width" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="bag_length" id="bag_length" min="1" required>
                            </td>
                            <td class="bagColors">
                                <select name="bagColor1" id="bagColor1" required>
                                    <optgroup>
                                        <option value="">--</option>
                                        <option value="Green">Green</option>
                                        <option value="Multicolor">Multicolor</option>
                                        <option value="White">White</option>
                                        <option value="Yellow">Yellow</option>
                                    </optgroup>
                                </select>
                                <select name="bagColor2" id="bagColor2" disabled aria-disabled="true">
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
                                                <input type="checkbox" name="handles_feature" id="handles_feature" value="Handles">
                                            </td>
                                            <td><label for="handles_feature">Handles</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="hemming_feature" id="hemming_feature" value="Hemmed">
                                            </td>
                                            <td><label for="hemming_feature">Hemmed</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="lamination_feature" id="lamination_feature" value="Laminated">
                                            </td>
                                            <td><label for="lamination_feature">Laminated</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="lining_feature" id="lining_feature" value="Lined">
                                            </td>
                                            <td><label for="lining_feature">Lined</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="printing_feature" id="printing_feature" value="Printed">
                                            </td>
                                            <td><label for="printing_feature">Printed</label></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="punch_hole_feature" id="punch_hole_feature" value="Punch Hole">
                                            </td>
                                            <td><label for="punch_hole_feature">Punched</label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <input type="number" name="bag_price" id="bag_price" min="1" required>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit">Register bag</button>
            </form>
        </div>
    </body>
</html>