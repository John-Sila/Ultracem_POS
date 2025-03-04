<?php
    // dash.php
    session_start();

    $page = $_GET['page'] ?? 'uc_sales'; // Default to 'home' if no page is specified
    if (!isset($_SESSION['permissions'])) {
        # code...
        header("Location: uc_logout.php");
    }

    // if (!in_array($page, $_SESSION['permissions']) && $page !== "uc_logout") {
    //     // If no permission, redirect to sales page
    //     header("Location: uc_logout.php");
    //     exit();
    // }
    $current_page = $_GET['page'] ?? '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="uc_styles.css?v=<?php echo time(); ?>"> <!-- Optional: Link to your CSS file -->

    <script type="text/javascript">
    </script>
</head>
<body>
    <nav class="topnav">
        <ul>
            <?php if (in_array('uc_sales', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=uc_sales" accesskey="S" class="<?= ($current_page === 'uc_sales' || !$current_page || strpos($current_page, 'sales') === 0) ? 'activeLink' : '' ?>"><span class="linkAccessKey">S</span>ales</a></li>|
            <?php endif; ?>
            <?php if (in_array('items_and_inventory', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=items_and_inventory" accesskey="t" class="<?= ($current_page === 'items_and_inventory' || strpos($current_page, 'inventory') === 0) ? 'activeLink' : '' ?>">I<span class="linkAccessKey">t</span>ems and Inventory</a></li>|
            <?php endif; ?>
            <?php if (in_array('manufacturing', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=manufacturing" accesskey="f" class="<?= ($current_page === 'manufacturing' || strpos($current_page, 'manufacturing') === 0) ? 'activeLink' : '' ?>">Manu<span class="linkAccessKey">f</span>acturing</a></li>|
            <?php endif; ?>
            <?php if (in_array('customer_management', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=customer_management" accesskey="C" class="<?= ($current_page === 'customer_management') ? 'activeLink' : '' ?>"><span class="linkAccessKey">C</span>ustomer Management</a></li>|
            <?php endif; ?>
            <?php if (in_array('user_management', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=user_management" accesskey="M" class="<?= ($current_page === 'user_management') ? 'activeLink' : '' ?>">User <span class="linkAccessKey">M</span>anagement</a></li>|
            <?php endif; ?>
            <?php if (in_array('accounts', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=accounts" accesskey="u" class="<?= ($current_page === 'accounts') ? 'activeLink' : '' ?>">Acco<span class="linkAccessKey">u</span>nts</a></li>|
            <?php endif; ?>
            <?php if (in_array('inside_information', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=inside_information" accesskey="I" class="<?= ($current_page === 'inside_information') ? 'activeLink' : '' ?>"><span class="linkAccessKey">I</span>nside Information</a></li>|
            <?php endif; ?>
            <?php if (in_array('database_management', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=database_management" accesskey="D" class="<?= ($current_page === 'database_management') ? 'activeLink' : '' ?>"><span class="linkAccessKey">D</span>atabase Management</a></li>|
            <?php endif; ?>
            <?php if (in_array('suppliers', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=suppliers" accesskey="u" class="<?= ($current_page === 'suppliers') ? 'activeLink' : '' ?>">S<span class="linkAccessKey">u</span>ppliers</a></li>|
            <?php endif; ?>
            <?php if (in_array('human_resource', $_SESSION['permissions'])): ?>
                <li><a href="dash.php?page=human_resource" accesskey="H" class="<?= ($current_page === 'human_resource' || strpos($current_page, 'human_resource') === 0) ? 'activeLink' : '' ?>"><span class="linkAccessKey">H</span>uman Resource</a></li>|
            <?php endif; ?>
            <li><a class="makeRed" href="dash.php?page=uc_logout" accesskey="L" class="<?= ($current_page === 'my_account') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v6"></path> <!-- Vertical line (switch stroke) -->
                        <path d="M5.5 8.5a7 7 0 1 0 13 0"></path> <!-- Circular shape with a gap at the top -->
                    </svg>
                <span><span class="linkAccessKey">L</span>ogout</a></span>
            </li>
        </ul>
        <p class="navCompany">Ultracem Manufacturers LTD | <span><?php echo htmlspecialchars($_SESSION['actualName'])?></span></p>
    </nav>

    <div class="content">
        <?php
        // Include the content based on the selected page
        $filePath = __DIR__ . "/$page.php"; // Absolute path to the file
        if (file_exists($filePath)) {
            include $filePath;
        } else {
            echo "<p>Page not found.</p>";
        }
        ?>
    </div>

    <footer>
        <p>OptimaByte v1.2.7</p>
        <p>&copy; <?php echo date("Y"); ?> ● OptimaByte ● All rights reserved.</p>
    </footer>
</body>
</html>
