<?php
session_start();

// Check if this is a direct access (should come from success page)
if (!isset($_SESSION['order_submitted'])) {
    header("Location: success.php");
    exit();
}

// Get cart data from localStorage via POST (passed from JavaScript)
$cart = json_decode($_POST['cart'] ?? '[]', true);
$totalAmount = $_POST['total'] ?? 0;
$orderId = $_POST['order_id'] ?? 'N/A';
$orderDate = date('F j, Y');
$invoiceNumber = 'INV-' . time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $invoiceNumber ?></title>
    <style>
        /* Invoice Styles */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .invoice-title {
            font-size: 28px;
            color: #333;
            margin: 0;
        }
        .invoice-meta {
            text-align: right;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .bill-to {
            margin-bottom: 15px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th {
            background: #f5f5f5;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #ddd;
        }
        .invoice-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .invoice-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 30px;
            border-top: 2px solid #eee;
            padding-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            width: 150px;
            font-weight: bold;
        }
        .total-value {
            width: 150px;
            text-align: right;
        }
        .grand-total {
            font-size: 18px;
            color: #2ecc71;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        @media print {
            body {
                padding: 0;
                background: none;
            }
            .no-print {
                display: none;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">Invoice</h1>
                <p>Order ID: <?= htmlspecialchars($orderId) ?></p>
            </div>
            <div class="invoice-meta">
                <p><strong>Invoice #:</strong> <?= $invoiceNumber ?></p>
                <p><strong>Date:</strong> <?= $orderDate ?></p>
            </div>
        </div>

        <div class="company-info">
            <h3>Elevate Your Space</h3>
            <p>123 Furniture Street</p>
            <p>Bangalore, Karnataka 560001</p>
            <p>Phone: (+91) 12349-56780</p>
            <p>Email: support@elevateyourspace.com</p>
        </div>

        <div class="bill-to">
            <h3>Bill To:</h3>
            <p><?= htmlspecialchars($_SESSION['user_name'] ?? 'Customer') ?></p>
            <p><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
            <p><?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?></p>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="60">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td class="text-right">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">₹<?= number_format($totalAmount, 2) ?></div>
            </div>
            <div class="total-row">
                <div class="total-label">Shipping:</div>
                <div class="total-value">₹<?= number_format(50, 2) ?></div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">Grand Total:</div>
                <div class="total-value">₹<?= number_format($totalAmount + 50, 2) ?></div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Terms: Payment due within 15 days</p>
            <button class="no-print" onclick="window.print()">Print Invoice</button>
        </div>
    </div>

    <script>
        // When page loads, send cart data to server if not already available
        document.addEventListener("DOMContentLoaded", function() {
            // Check if we have PHP data already
            const hasCartData = <?= !empty($cart) ? 'true' : 'false' ?>;

            if (!hasCartData) {
                // Get cart from localStorage
                const cart = JSON.parse(localStorage.getItem("cart") || []);
                const total = parseFloat(localStorage.getItem("order_total") || 0);
                const orderId = localStorage.getItem("order_id") || 'N/A';

                // Send to server to generate invoice
                fetch('invoice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        cart: cart,
                        total: total,
                        order_id: orderId
                    })
                })
                .then(response => response.text())
                .then(html => {
                    document.open();
                    document.write(html);
                    document.close();
                });
            }
        });
    </script>
</body>
</html>