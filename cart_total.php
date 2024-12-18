<?php
	include 'includes/session.php';

	if(isset($_SESSION['user'])){
		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT * FROM cart LEFT JOIN products on products.id=cart.product_id WHERE user_id=:user_id");
		$stmt->execute(['user_id'=>$user['id']]);

		$total = 0;
		$usd_to_npr_rate = 123.45; // Replace with your actual conversion rate

		foreach($stmt as $row){
			$subtotal = $row['price'] * $row['quantity'];
			$total += $subtotal;
		}

		$pdo->close();

		$total_npr = $total * $usd_to_npr_rate; // Convert total to NPR

		echo json_encode($total_npr);
	}
?>
