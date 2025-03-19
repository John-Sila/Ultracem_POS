<?php

if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=login");
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
if (!in_array("manufacturing", $_SESSION['permissions'])) {
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
    <title>Roll Processing</title>
    <script>
        function bodyLoaded() {
            document.getElementById("selectRoll").addEventListener("change", function () {
                let rollId = this.value;
                let selectBag = document.getElementById("selectBag");

                if (rollId) {
                    fetch("manufacturing/get_bags.php", {
                        method: "POST",
                        body: new URLSearchParams({ selectRoll: rollId }),
                        headers: { "Content-Type": "application/x-www-form-urlencoded" }
                    })
                    .then(response => response.text())
                    .then(data => {
                        selectBag.innerHTML = '<option value="">-- Select a Bag --</option>' + data;
                    })
                    .catch(error => console.error("Error fetching bags:", error));
                } else {
                    selectBag.innerHTML = '<option value="">-- Select a Bag --</option>';
                }
            });
        
            document.querySelector("form").addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent normal form submission

                let formData = new FormData(this);

                fetch("manufacturing/process_roll_conversion.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success"
                        }).then(() => location.reload()); // Refresh to update UI
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire("Error!", "Something went wrong.", "error");
                });
            });
        
        
        }
        function checkSalesQuotation(event) {

        }

    </script>
</head>
<body onload="bodyLoaded()">
    <div class="rollConvMainDiv">
        <h1>Bag Conversion</h1>
        <div class="hasSalesQuotationForm">
            <form action="manufacturing/process_roll_conversion.php" method="POST" onsubmit="return checkSalesQuotation(event)">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <label for="selectRoll">Select Processed Roll:</label>

                            </td>
                            <td>
                                <select name="selectRoll" id="selectRoll" required>
                                    <optgroup>
                                        <option value="">--</option>
                                    <?php
                                        require dirname(__DIR__) . "/db.php";
                
                                        try {
                                            $stmt = $pdo->query("SELECT id, color, width, features FROM rolls ORDER BY width ASC");
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['width']) . 'cm ' . htmlspecialchars($row['color'])  . ' ' . htmlspecialchars($row['features']) . '</option>';
                                            }
                                        } catch (PDOException $e) {
                                            echo '<option value="">Error fetching rolls</option>';
                                        }
                                    ?>
                                    </optgroup>
                                </select>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="selectBag">Select Bag:</label>

                            </td>
                            <td>
                                <select name="selectBag" id="selectBag" required>
                                    <option value="">-- Select a Bag --</option>
                                </select>

                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for="totalBags">Total Bags:</label>

                            </td>
                            <td>
                                <input type="number" name="totalBags" id="totalBags" required aria-required="true">
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for="rollDepleted">Roll Depleted:</label>

                            </td>
                            <td>
                                <div class="flexer">
                                    <input type="checkbox" name="rollDepleted" id="rollDepleted">
                                    <span class="makeYellow">Checking this will delete the roll from the database</span>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td aria-colspan="2" colspan="2">
                                <button type="submit">Convert</button>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</body>
</html>