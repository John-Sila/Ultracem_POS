<?php

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

// Check if user has permission to access sales
if ($_SESSION["permissions"] && !in_array("uc_sales", $_SESSION["permissions"])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

    require "db.php";

    // $stmt = $pdo->query("SELECT id, width, length, color, features, total_quantity, unit_price FROM bags");
    $stmt = $pdo->query("SELECT * FROM bags ORDER BY width");
    $bags = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Quotation Entry</title>
    <script>
        function checkSalesQuotation(event) {
            const customer = event.target.querySelector("#selectCustomer").options[event.target.querySelector("#selectCustomer").selectedIndex].value;
            if (isNaN(customer) || customer < 1 || customer === "") {
                Swal.fire({
                    title: 'Action blocked!',
                    text: 'Select a Valid customer.',
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
            
            const anyChecked = [...event.target.querySelectorAll(".selectThisBag")].some(checkbox => checkbox.checked);
            const anyActualEntries = [...event.target.querySelectorAll(".quoteAmount")].some(input => input.value > 0);
            if (!anyChecked || !anyActualEntries) {
                Swal.fire({
                    title: 'Action blocked!',
                    text: 'Incomplete entries.',
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
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('quotationIssueDate').value = today;

            const ISOLessToday = new Date();
            ISOLessToday.setMonth(ISOLessToday.getMonth() + 1);
            const oneMonthFromNow = ISOLessToday.toISOString().split('T')[0];
            document.getElementById('quotationValidityDate').value = oneMonthFromNow;

            uncheckCheckBoxes();
        }

        function uncheckCheckBoxes() {
            const checkboxlist = document.querySelectorAll('.selectThisBag');
            for (let i = 0; i < checkboxlist.length; i++) {
                const element = checkboxlist[i];
                element.checked = false;
            }
        }
        
        function checkBoxClicked(event) {
            event.target.closest("tr").querySelectorAll(".quoteAmount")[0].disabled = !event.target.checked;
        }

        function bagAmountChanged(event) {
            if (isNaN(event.target.value) || event.target.value < 1) {
                event.target.closest("tr").querySelectorAll(".selectThisBag")[0].checked = false;
            }
        }
    </script>
</head>
<body onload="bodyLoaded()">
    <div class="sQEntryMainDiv">
        <h1>Sales Quotation</h1>
        <div class="hasSalesQuotationForm">
            <form action="sales/process_sales_quotation_entry.php" method="POST" onsubmit="return checkSalesQuotation(event)">
                <div class="formGroup">
                    <label for="selectCustomer">Select Customer:</label>
                    <select name="selectCustomer" id="selectCustomer" required>
                        <optgroup>
                            <option value="">--</option>
                        <?php
                            require dirname(__DIR__) . "/db.php";

                            try {
                                $stmt = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo '<option value="">Error fetching customers</option>';
                            }
                        ?>
                        </optgroup>
                    </select>
                </div>
                <div class="formGroup">
                    <label for="quotationIssueDate">Quotation Issue Date:</label>
                    <input type="date" name="quotationIssueDate" id="quotationIssueDate">
                    <span class="makeYellow">default = today</span>
                </div>
                <div class="formGroup">
                    <label for="quotationValidityDate">Quotation Valid Till:</label>
                    <input type="date" name="quotationValidityDate" id="quotationValidityDate">
                    <span class="makeYellow">default = 1 month after issuance</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Select Bag</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Features</th>
                            <th>Unit Price</th>
                            <th>Quotation Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bags as $bag): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="selectThisBag" name="selected_bags[]" value="<?= $bag['id']; ?>" onclick="checkBoxClicked(event)">
                            </td>
                            <td><input type="text" class="appearSpan" value="<?= htmlspecialchars($bag['width'] . ' × ' . $bag['length']); ?>" name="qBagSize" readonly></td>
                            <td><input type="text" class="appearSpan" value="<?= htmlspecialchars($bag['color']); ?>" name="qBagColor" readonly></td>
                            <td><input type="text" class="appearSpan" value="<?= htmlspecialchars($bag['features']); ?>" name="qBagFeatures" readonly></td>
                            <td><input type="text" class="appearSpan" value="<?= number_format($bag['price'], 2); ?>" name="qBagPrice" readonly></td>
                            <td><input type="number" name="quoteAmount[]" class="quoteAmount" min="1" id="quoteAmount[<?= htmlspecialchars($bag['id']); ?>]" oninput="bagAmountChanged(event)" disabled></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit">Generate Quotation</button>
            </form>
        </div>
    </div>
</body>
</html>