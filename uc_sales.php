<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultracem Sales</title>
</head>
<body>
    <div class="salesMainDiv">
        <div class="salesDiv1">
            <h3>Transactions</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <a href="dash.php?page=sales/sales_quotation_entry">Sales Quotation Entry</a>
                    <a href="">Sales Order Entry</a>
                </div>
                <div class="rightDiv">
                    <a href="">Third Parties</a>
                    <a href="">Brief as is</a>
                </div>
            </div>
        </div>

        <div class="salesDiv2">
            <h3>Inquiries & Reports</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <a href="">Sale Quotation Inquiry</a>
                    <a href="">Sale Order Inquiry</a>
                    <a href="">Customer Transaction Inquiry</a>
                </div>
                <div class="rightDiv">
                    <a href="">Customer and Sales Reports</a>
                </div>
            </div>
        </div>


        <div class="salesDiv3">
            <h3>Maintenance</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <!-- <a href="">Add and Manage Customers</a> -->
                    <a href="">Customer Branches</a>
                    <a href="">Recurrent Invoices</a>
                    <a href="">Bulk Import Customers</a>
                </div>
                <div class="rightDiv">
                    <a href="">Sales Types</a>
                    <a href="">Sales Areas</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>