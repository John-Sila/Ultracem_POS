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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bags Entry</title>
    <script>

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

        function selectBagRow(event) {
            var checkbox = event.target;
            const thisNumInput = checkbox.closest("tr").querySelectorAll(".amountToUpdate")[0];
            const thisResetBtn = checkbox.closest("tr").querySelectorAll(".resetBtn")[0];
            const thisUpdateButton = checkbox.closest("tr").querySelectorAll(".updateBtn")[0];
            const thisDeleteBtn = checkbox.closest("tr").querySelectorAll(".deleteBtn")[0];

            thisNumInput.disabled = !checkbox.checked;
            thisResetBtn.disabled = !checkbox.checked;
            thisUpdateButton.disabled = !checkbox.checked;
            thisDeleteBtn.disabled = !checkbox.checked;
        }
        
        function deleteBag(event) {
            let row = event.target.closest("tr");
            let bagIdInput = row.querySelectorAll(".hideThis")[0];
            let bagAmount = row.querySelectorAll(".amountToUpdate")[0]?.value;
            
            if (!bagIdInput) {
                alert("Bag ID input not found.");
                return;
            }

            let bagId = bagIdInput.value;
            Swal.fire({
                title: 'Confirm',
                text: 'Delete this bag?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) { // Ensures only confirmed deletion runs
                    fetch("dash.php?page=inventory/process_delete_bag", {  // Adjusted path
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "bag_id=" + encodeURIComponent(bagId),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network response was not ok.");
                        }
                        return response.text();
                    })
                    .then(data => {
                        Swal.fire("Deleted!", "Bag deleted successfully.", "success");
                        // row.remove();
                        location.reload();
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error", "Something went wrong.", "error");
                    });
                }
            });

            return false;
        }

        function resetBag(event) {
            let row = event.target.closest("tr");
            let bagIdInput = row.querySelectorAll(".hideThis")[0];
            
            if (!bagIdInput) {
                alert("Bag ID input not found.");
                return;
            }

            let bagId = bagIdInput.value;
            Swal.fire({
                title: 'Confirm',
                text: 'Reset this bag?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Reset',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) { // Ensures only confirmed deletion runs
                    fetch("dash.php?page=inventory/process_reset_bag", {  // Adjusted path
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "bag_id=" + encodeURIComponent(bagId),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network response was not ok.");
                        }
                        return response.text();
                    })
                    .then(data => {
                        Swal.fire("Updated!", "Bag reset successfully.", "success").then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error", "Something went wrong.", "error");
                    });
                }
            });

            return false;
        }
        
        function updateBag(event) {
                let row = event.target.closest("tr");
                let bagId = row.querySelectorAll(".hideThis")[0].value;
                let newAmountInput = row.querySelectorAll(".amountToUpdate")[0];
                let newAmount = newAmountInput.value.trim();

                // Validate input
                if (newAmount === "" || isNaN(newAmount) || newAmount <= 0) {
                    Swal.fire({
                        title: 'Action blocked!',
                        text: 'Enter a valid value',
                        icon: 'error',
                        confirmButtonText: 'Return'
                    });
                    return;  // Stop execution immediately
                }

                Swal.fire({
                    title: 'Confirm',
                    text: 'Update bag?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("dash.php?page=inventory/process_update_bag", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "bag_id=" + encodeURIComponent(bagId) + "&received_amount=" + encodeURIComponent(newAmount),
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Network response was not ok.");
                            }
                            return response.text();
                        })
                        .then(data => {
                            Swal.fire("Updated!", "Bag updated successfully.", "success").then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire("Error", "Something went wrong.", "error");
                        });
                    }
                });

                return false;
            }
    
    </script>
</head>
<body>
    <div class="bagsEntryMainDiv">
        <h3>Bags Entry</h3>
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <!-- <th>Bag ID</th> -->
                    <th>Size</th>
                    <th>Color</th>
                    <th>Features</th>
                    <th>Current Amount</th>
                    <th>Last Updated [dd-MM-yyyy]</th>
                    <th>Enter Quantity</th>
                    <th colspan="3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bags as $bag): ?>
                    <tr>
                        <form action="" method="POST">
                            <input type="hidden" class="hideThis" name="thisBags[id]" value="<?php echo $bag['id']; ?>" readonly>
                            <td>
                                <input type="checkbox" name="chooseBag" class="chooseBag" onclick="selectBagRow(event)">
                            </td>
                            <td><?php echo $bag['width']; ?> × <?php echo $bag['length']; ?></td>
                            <td><?php echo $bag['color']; ?></td>
                            <td><?php echo $bag['features']; ?></td>
                            <td><?php echo $bag['amount']; ?></td>
                            <td>
                                <?php echo !empty($bag['last_updated']) 
                                    ? (new DateTime($bag['last_updated']))->format('d-m-Y H:i:s') 
                                    : 'N/A'; ?>
                            </td>

                            <td>
                                <input type="number" name="amountToUpdate" min="1" class="amountToUpdate" disabled>
                            </td>
        
                            <td>
                                <button type="button" onclick="resetBag(event)" class="resetBtn" disabled>Reset</button>
                                <button type="button" onclick="updateBag(event)" class="updateBtn" disabled>Update</button>
                                <button type="button" onclick="deleteBag(event)" class="deleteBtn" disabled>Delete</button>
                            </td>
                        </form>
                    </tr>
                    
                    
                <?php endforeach; ?>
                <tr>
                </tr>
            </tbody>
        </table>

    </div>
</body>
</html>