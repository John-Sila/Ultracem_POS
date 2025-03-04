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

require dirname(__DIR__) . "/db.php";

// Fetch all bags
$stmt = $pdo->query("SELECT * FROM bags ORDER BY width ASC");
$bags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// fetch rolls
$stmt = $pdo->query("SELECT * FROM rolls ORDER BY width ASC");
$rolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// fetch liners
$stmt = $pdo->query("SELECT * FROM liners ORDER BY width ASC");
$liners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// fetch handles
$stmt = $pdo->query("SELECT * FROM handles ORDER BY color ASC");
$handles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items</title>
    <script>
        function toggleDateInput(id) {
            let dateText = document.getElementById("dateText_" + id);
            let dateInput = document.getElementById("dateInput_" + id);

            if (dateInput.style.display === "none") {
                dateInput.style.display = "inline-block";
                dateText.style.display = "none";
            } else {
                dateInput.style.display = "none";
                dateText.style.display = "inline";
            }
        }
        function toggleDateInput2(id) {
            let dateText = document.getElementById("dateText2_" + id);
            let dateInput = document.getElementById("dateInput2_" + id);

            if (dateInput.style.display === "none") {
                dateInput.style.display = "inline-block";
                dateText.style.display = "none";
            } else {
                dateInput.style.display = "none";
                dateText.style.display = "inline";
            }
        }
        function toggleDateInput3(id) {
            let dateText = document.getElementById("dateText3_" + id);
            let dateInput = document.getElementById("dateInput3_" + id);
    
            if (dateInput.style.display === "none") {
                dateInput.style.display = "inline-block";
                dateText.style.display = "none";
            } else {
                dateInput.style.display = "none";
                dateText.style.display = "inline";
            }

        }
        function toggleDateInput4(id) {
            let dateText = document.getElementById("dateText4_" + id);
            let dateInput = document.getElementById("dateInput4_" + id);

            if (dateInput.style.display === "none") {
                dateInput.style.display = "inline-block";
                dateText.style.display = "none";
            } else {
                dateInput.style.display = "none";
                dateText.style.display = "inline";
            }
        }
    </script>
</head>
<body>
    <div class="assetsMainDiv">
        <h3>Fixed & Current Assets</h3>
        <div class="assetsDiv1">
            <h4>Bags</h4>
            <table>
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Size (cm)</th>
                        <th>Color</th>
                        <th>Features</th>
                        <th>Current Amount</th>
                        <th>Last Updated [dd-MM-yyyy]</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $index = 1;
                        foreach ($bags as $bag): ?>
                            <tr>
                                <td><?php echo $index ?></td>
                                <td><?php echo $bag['width']; ?> × <?php echo $bag['length']; ?></td>
                                <td><?php echo $bag['color']; ?></td>
                                <td><?php echo !empty($bag['features']) ? $bag['features'] : '--'; ?></td>
                                <td><?php echo $bag['amount']; ?></td>
                               

                                <td>
                                    <?php if (!empty($bag['last_updated'])): 
                                        $formattedDate = (new DateTime($bag['last_updated']))->format('d-m-Y H:i:s'); 
                                        $inputDate = (new DateTime($bag['last_updated']))->format('Y-m-d'); // Format for input[type="date"]
                                    ?>
                                        <span id="dateText3_<?php echo $bag['id']; ?>"><?php echo $formattedDate; ?></span>
                                        <input type="date" id="dateInput3_<?php echo $bag['id']; ?>" value="<?php echo $inputDate; ?>" style="display: none;">
                                        <button onclick="toggleDateInput3('<?php echo $bag['id']; ?>')">
                                            📆
                                        </button>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $index++;?>
                        <?php endforeach;
                    ?>
                </tbody>
            </table>

        </div>


        <div class="assetsDiv2">
            <h4>Rolls</h4>
            <table>
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Width (cm)</th>
                        <th>Length* (m)</th>
                        <th>Weight* (kg)</th>
                        <th>Color</th>
                        <th>Features</th>
                        <th>Average Cost per KG</th>
                        <th>Average Weight (g) per Meter</th>
                        <th>Date Added [dd-MM-yyyy]</th>
                        <th>Last Modified [dd-MM-yyyy]</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $index = 1;
                        foreach ($rolls as $roll): ?>
                            <tr>
                                <td><?php echo $index ?></td>
                                <td><?php echo $roll['width']; ?></td>
                                <td><?php echo $roll['length']; ?></td>
                                <td><?php echo number_format($roll['weight'], 2); ?></td>
                                <td><?php echo $roll['color']; ?></td>
                                <td><?php echo !empty($roll['features']) ? $roll['features'] : '--'; ?></td>
                                <td><?php echo "Ksh " . number_format($roll['cost_per_kg'], 2); ?></td>
                                <td><?php echo number_format($roll['unit_meter_weight'], 2); ?></td>
                                <td>
                                    <?php if (!empty($roll['date_added'])): 
                                        $formattedDate = (new DateTime($roll['date_added']))->format('d-m-Y H:i:s'); 
                                        $inputDate = (new DateTime($roll['date_added']))->format('Y-m-d'); // Format for input[type="date"]
                                    ?>
                                        <span id="dateText_<?php echo $roll['id']; ?>"><?php echo $formattedDate; ?></span>
                                        <input type="date" id="dateInput_<?php echo $roll['id']; ?>" value="<?php echo $inputDate; ?>" style="display: none;">
                                        <button onclick="toggleDateInput('<?php echo $roll['id']; ?>')">
                                            📆
                                        </button>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($roll['last_modified'])): 
                                        $formattedDate = (new DateTime($roll['last_modified']))->format('d-m-Y H:i:s'); 
                                        $inputDate = (new DateTime($roll['last_modified']))->format('Y-m-d'); // Format for input[type="date"]
                                    ?>
                                        <span id="dateText2_<?php echo $roll['id']; ?>"><?php echo $formattedDate; ?></span>
                                        <input type="date" id="dateInput2_<?php echo $roll['id']; ?>" value="<?php echo $inputDate; ?>" style="display: none;">
                                        <button onclick="toggleDateInput2('<?php echo $roll['id']; ?>')">
                                            📆
                                        </button>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $index++;?>
                        <?php endforeach;
                    ?>
                </tbody>
            </table>

        </div>

        <div class="assetsDiv3">
            <h4>Liners</h4>
            <table>
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Width (cm)</th>
                        <th>Length (cm)</th>
                        <th>Amount</th>
                        <th>Last Modified [dd-MM-yyyy]</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $index = 1;
                        foreach ($liners as $liner): ?>
                            <tr>
                                <td><?php echo $index ?></td>
                                <td><?php echo $liner['width']; ?></td>
                                <td><?php echo $liner['length']; ?></td>
                                <td>
                                    <?php if (!empty($liner['last_modified'])): 
                                        $formattedDate = (new DateTime($liner['last_modified']))->format('d-m-Y H:i:s'); 
                                        $inputDate = (new DateTime($liner['last_modified']))->format('Y-m-d'); // Format for input[type="date"]
                                    ?>
                                        <span id="dateText4_<?php echo $liner['id']; ?>"><?php echo $formattedDate; ?></span>
                                        <input type="date" id="dateInput4_<?php echo $liner['id']; ?>" value="<?php echo $inputDate; ?>" style="display: none;">
                                        <button onclick="toggleDateInput4('<?php echo $liner['id']; ?>')">
                                            📆
                                        </button>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $index++;?>
                        <?php endforeach;
                    ?>
                </tbody>
            </table>

        </div>
        

    </div>
    
</body>
</html>