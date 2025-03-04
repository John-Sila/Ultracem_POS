<?php
session_start(); // Ensure session is started

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

require dirname(__DIR__) . "/db.php";
require dirname(__DIR__) . "/libs/dompdf/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Ensure the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $customerId = $_POST['selectCustomer'] ?? '';
    $quotationIssueDate = $_POST['quotationIssueDate'] ?? date('Y-m-d');
    $quotationValidityDate = $_POST['quotationValidityDate'] ?? date('Y-m-d', strtotime('+1 month'));
    $selectedBags = $_POST['selected_bags'] ?? [];
    $bagUnits = $_POST['quoteAmount'] ?? []; // Number of units for each bag

    // Check if customer and at least one bag are selected
    if (empty($customerId) || empty($selectedBags) || empty($bagUnits)) {
        echo "Error: Customer and at least one bag must be selected with the number of units.";
        exit();
    }

    try {
        // Fetch customer name
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $customerId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$customer) {
            echo "Error: Customer not found.";
            exit();
        }
        $customerName = htmlspecialchars($customer['name']);
    } catch (PDOException $e) {
        echo "Error fetching customer: " . $e->getMessage();
        exit();
    }

    // Fetch bag details
    $bagDetails = [];
    $totalQuotationPrice = 0;

    try {
        foreach ($selectedBags as $index => $bagId) {
            $units = isset($bagUnits[$index]) ? (int)$bagUnits[$index] : 1;
            $stmt = $pdo->prepare("SELECT width, length, color, features, price FROM bags WHERE id = :id");
            $stmt->execute(['id' => $bagId]);
            $bag = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($bag) {
                $unitPrice = (float)$bag['price'];
                $totalPrice = $units * $unitPrice;
                $totalQuotationPrice += $totalPrice;

                $bagDetails[] = [
                    'size' => htmlspecialchars($bag['width'] . ' × ' . $bag['length']),
                    'color' => htmlspecialchars($bag['color']),
                    'features' => htmlspecialchars($bag['features']),
                    'unit_price' => number_format($unitPrice, 2),
                    'units' => $units,
                    'total_price' => number_format($totalPrice, 2)
                ];
            }
        }
    } catch (PDOException $e) {
        echo "Error fetching bag details: " . $e->getMessage();
        exit();
    }

    $logo_path = realpath(__DIR__ . '/../images/uc_logo1.png');
    if (!$logo_path) {
        die("Company logo does not exist.");
    }
    $type = pathinfo($logo_path, PATHINFO_EXTENSION);
    $data = file_get_contents($logo_path);
    $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);





    // get company info
    $stmt = $pdo->prepare("SELECT * FROM company_info WHERE id = 1");
    $stmt->execute();
    $company = $stmt->fetch(PDO::FETCH_ASSOC);




    // Generate the PDF using Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

    // Prepare the HTML content for the PDF
    $htmlContent = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <style>
            *{
                font-size: .95em;
                font-family: serif;
            }
            .names{
                text-transform: uppercase;
                font-size: 1.3em;
                color: ;
            }
            .identifiers {
                font-style: italic;
            }
            
            .totalAmount {
                font-style: italic;
                font-size: 1.5em;
                color: rgb(54, 124, 181);
                
            }
            
            .sqDef{
                text-align: right;
                text-transform: uppercase;
                color: rgb(226, 226, 226);
                font-weight: bold;
                font-size: 1.875em;
            }
            .topTable{
                width: 100%;
            }
            .mainTable{
                width: 100%;
                border-collapse: collapse;
                border: none;
            }
            .mainTable thead th {
                border: none;
                background-color: rgb(54, 124, 181);
                color: rgb(242, 242, 242);
                text-align: left;
                padding: 1em;
                border: .5px solid rgb(222, 231, 236);
            }
            .mainTable td {
                border: none;
                text-align: left;
            }
            .mainTable tbody tr:nth-child(even){
                background-color: rgb(242, 242, 242);
            }

        </style>
    </head>
    <body>
        <table class="topTable">
            <tbody>
                <tr>
                    <td colspan="3"><img src="' . $base64Image . '" height="100px" /></td>
                    <td><h1 class="sqDef">Sales Quotation</h1></td>
                </tr>
                <tr>
                    <td class="identifiers"><b>Billed to:</b></td>
                    <td class="identifiers"><b>From:</b></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="names"><b>' .htmlspecialchars($customer['name']) . '</b></td>
                    <td class="names"><b>' .htmlspecialchars($company['company_name']) . '</b></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><i>' .htmlspecialchars($customer['phone_number1']) . '</i></td>
                    <td><i>' . "+" .htmlspecialchars($company['default_phone_number']) . '</i></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>' .htmlspecialchars($customer['postal_address']) . '</td>
                    <td>' .htmlspecialchars($company['postal_address']) . '</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><i>' .htmlspecialchars($customer['postal_code']) . '</i></td>
                    <td><i>' .htmlspecialchars($company['postal_code']) . '</i></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>' .htmlspecialchars($customer['town']) . '</td>
                    <td>' .htmlspecialchars($company['town']) . ", " . htmlspecialchars($company['city']) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b>Issue Date:</b> <i>' . htmlspecialchars($quotationIssueDate) . '</i></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b>Valid Until:</b> <i>' . htmlspecialchars($quotationValidityDate) . '</i></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table border="1" class="mainTable" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Size (cm)</th>
                    <th>Color</th>
                    <th>Features</th>
                    <th style="text-align: center;">Unit Price (KSh)</th>
                    <th style="text-align: center;">Units</th>
                    <th style="text-align: center;">Total Price (KSh)</th>
                </tr>
            </thead>
            <tbody>';

            $sn = 1;
            foreach ($bagDetails as $bag) {
                $htmlContent .= "
                <tr>
                    <td>$sn</td>
                    <td>{$bag['size']}</td>
                    <td>{$bag['color']}</td>
                    <td>{$bag['features']}</td>
                    <td style='text-align: center;'>{$bag['unit_price']}</td>
                    <td style='text-align: center;'>{$bag['units']}</td>
                    <td style='text-align: center;'>{$bag['total_price']}</td>
                </tr>";

                $sn++;
            }

        $htmlContent .= '
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td><strong>TOTAL QUOTATION:</strong></td>
                    <td><strong class="totalAmount">KSh ' . number_format($totalQuotationPrice, 2) . '</strong></td>
                </tr>
            </tfoot>
        </table>
    </body>';

    // Load HTML into Dompdf
    $dompdf->loadHtml($htmlContent);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Output the generated PDF to the browser
    $dompdf->stream("sales_quotation.pdf", ["Attachment" => false]);
    exit();
}
?>
