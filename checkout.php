<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

$message = [];
$message_type = 'error';

if(isset($_POST['order_btn'])){
   $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
   $amount = (float)$_POST['amount'];
   
   $account_number = '';
   $transaction_id = '';

   if($payment_method == 'bkash'){
       $account_number = mysqli_real_escape_string($conn, $_POST['bkash_number']);
       $transaction_id = mysqli_real_escape_string($conn, $_POST['bkash_trx_id']);
   } elseif($payment_method == 'card'){
       $account_number = mysqli_real_escape_string($conn, $_POST['card_number']);
       $transaction_id = mysqli_real_escape_string($conn, $_POST['card_trx_id']);
   }

   if(empty($payment_method) || empty($account_number) || empty($transaction_id) || empty($amount)){
       $message[] = 'Please fill out all payment details.';
   } else {
       $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
       if(mysqli_num_rows($cart_query) > 0){
         
          $order_id = rand(1000, 9999); 

          
          $insert_payment = mysqli_query($conn, "INSERT INTO `payments`(order_id, user_id, payment_method, account_number, transaction_id, amount) VALUES('$order_id', '$user_id', '$payment_method', '$account_number', '$transaction_id', '$amount')");

          if($insert_payment){
              while($product_item = mysqli_fetch_assoc($cart_query)){
                 $product_id_query = mysqli_query($conn, "SELECT id FROM `products` WHERE name = '{$product_item['name']}'");
                 $product_id = mysqli_fetch_assoc($product_id_query)['id'];
                 $price = $product_item['price'];
                 
                 $insert_order = mysqli_query($conn, "INSERT INTO `orders`(user_id, product_id, price) VALUES('$user_id', '$product_id', '$price')");
              }
              mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");
              $message[] = 'Purchase successful! You can now access your courses in the student panel.';
              $message_type = 'success';
              header('refresh:3;url=student_panel.php');
          } else {
              $message[] = 'Could not process payment. Please try again.';
          }
       }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; color: #e5e7eb; }
    </style>
</head>
<body>
    <div class="container mx-auto p-6 md:p-12">
        <h1 class="text-4xl font-bold text-center text-white mb-10">Complete Your Purchase</h1>
        
        <?php
        if(!empty($message)){
            $bg_color = ($message_type === 'success') ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300';
            foreach($message as $msg){
                echo "<div class='mb-6 p-4 rounded-lg $bg_color text-center'>$msg";
                if($message_type === 'success'){
                    echo "<p class='text-sm'>Redirecting you now...</p>";
                }
                echo "</div>";
            }
        }
        ?>

        <div class="max-w-3xl mx-auto bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 border border-gray-700">
            <h2 class="text-2xl font-bold text-white mb-6">Order Summary</h2>
            <div class="space-y-4">
                <?php
                $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
                $total = 0;
                if(mysqli_num_rows($select_cart) > 0){
                    while($fetch_cart = mysqli_fetch_assoc($select_cart)){
                    $total_price = $fetch_cart['price'] * $fetch_cart['quantity'];
                    $total += $total_price;
                ?>
                <div class="flex justify-between items-center text-gray-300">
                    <span><?php echo $fetch_cart['name']; ?></span>
                    <span>$<?php echo number_format($fetch_cart['price']); ?>/-</span>
                </div>
                <?php
                    }
                }
                ?>
                <div class="border-t border-gray-600 pt-4 flex justify-between items-center font-bold text-white text-lg">
                    <span>Grand Total</span>
                    <span>$<?php echo number_format($total); ?>/-</span>
                </div>
            </div>

            <form action="" method="post" class="mt-8">
                <h3 class="text-xl font-bold text-white mb-4">Select Payment Method</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="radio" id="bkash" name="payment_method" value="bkash" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" onclick="showPaymentForm('bkash')">
                        <label for="bkash" class="ml-3 block text-sm font-medium text-gray-300">bKash</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="card" name="payment_method" value="card" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" onclick="showPaymentForm('card')">
                        <label for="card" class="ml-3 block text-sm font-medium text-gray-300">Card Payment</label>
                    </div>
                </div>

                <div id="bkash_form" class="hidden mt-6 space-y-4">
                    <input type="text" name="bkash_number" placeholder="bKash Number" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                    <input type="text" name="bkash_trx_id" placeholder="Transaction ID" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                </div>

                <div id="card_form" class="hidden mt-6 space-y-4">
                    <input type="text" name="card_number" placeholder="Card Number" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                    <input type="text" name="card_trx_id" placeholder="Transaction ID" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                </div>
                
                <input type="hidden" name="amount" value="<?php echo $total; ?>">

                <button type="submit" name="order_btn" class="w-full mt-8 bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Confirm Purchase</button>
            </form>
        </div>
    </div>

    <script>
        function showPaymentForm(method) {
            document.getElementById('bkash_form').classList.add('hidden');
            document.getElementById('card_form').classList.add('hidden');
            if (method) {
                document.getElementById(method + '_form').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
