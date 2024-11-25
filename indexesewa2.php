<?php
session_start();
if(isset($_POST['submit'])) {
    $con = mysqli_connect("localhost", "root", "", "ecomm");

    if(isset($_SESSION['email'])) {
        $user_email = $_SESSION['email'];
        $result = mysqli_query($con, "SELECT id, email FROM users WHERE email = '$user_email'");
        if($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['id'];
            $umail = $row['email'];
        } else {
            $umail = '';
            $user_id = 0;
        }
    }

    $epay_url = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
    $tax_amount = 10;
    $total_amount = $total + $tax_amount;

    $transaction_uuid = uniqid("ab-", true);
    $transaction_uuid = preg_replace("/[^a-zA-Z0-9]/", "", $transaction_uuid);

    $product_code = "EPAYTEST";
    $product_service_charge = 0;
    $product_delivery_charge = 0;
    $success_url = "http://localhost/E-CommerceWebsite/successEsewa.php";
    $failure_url = "https://google.com";
    $signed_field_names = "total_amount,transaction_uuid,product_code";

    $secret_key = "8gBm/:&EnhH.1/q";
    $message = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";

    $signature = hash_hmac('sha256', $message, $secret_key, true);
    $signature = base64_encode($signature);

    $insert = "INSERT INTO Order_Placement (user_id, pay_id, sales_date) VALUES ('$user_id', '$transaction_uuid', NOW())";
    $query = mysqli_query($con, $insert);

    // Update product quantities in the cart
    foreach($stmt as $row) {
        $name = $row['name'];
        $quantity = $row['quantity'];
        $sql_reduce = "UPDATE products SET Quantity = Quantity - $quantity WHERE id = '{$row['product_id']}'";
        $result_reduce = mysqli_query($con, $sql_reduce);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay through Esewa</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form action="<?php echo $epay_url; ?>" method="POST">
                    <input type="hidden" id="amount" name="amount" value="<?php echo $total; ?>" required>
                    <input type="hidden" id="tax_amount" name="tax_amount" value="<?php echo $tax_amount; ?>" required>
                    <input type="hidden" id="total_amount" name="total_amount" value="<?php echo $total_amount; ?>" required>
                    <input type="hidden" id="transaction_uuid" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>" required>
                    <input type="hidden" id="product_code" name="product_code" value="<?php echo $product_code; ?>" required>
                    <input type="hidden" id="product_service_charge" name="product_service_charge" value="<?php echo $product_service_charge; ?>" required>
                    <input type="hidden" id="product_delivery_charge" name="product_delivery_charge" value="<?php echo $product_delivery_charge; ?>" required>
                    <input type="hidden" id="success_url" name="success_url" value="<?php echo $success_url; ?>" required>
                    <input type="hidden" id="failure_url" name="failure_url" value="<?php echo $failure_url; ?>" required>
                    <input type="hidden" id="signed_field_names" name="signed_field_names" value="<?php echo $signed_field_names; ?>" required>
                    <input type="hidden" id="signature" name="signature" value="<?php echo $signature; ?>" required>
                    <input type="submit" name="submit" value="Pay Via Esewa" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
    <!-- jQuery library -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
