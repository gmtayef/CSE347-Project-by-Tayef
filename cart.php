<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

if(isset($_POST['update_update_btn'])){
   $update_value = $_POST['update_quantity'];
   $update_id = $_POST['update_quantity_id'];
   $update_quantity_query = mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_value' WHERE id = '$update_id'");
   if($update_quantity_query){
      header('location:cart.php');
   };
};

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
};

if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");
   header('location:cart.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; color: #e5e7eb; }
    </style>
</head>
<body>
    <div class="container mx-auto p-6 md:p-12">
        <h1 class="text-4xl font-bold text-center text-white mb-10">My Cart</h1>
        <div class="bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 border border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs text-gray-300 uppercase bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3">Course</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
                        $grand_total = 0;
                        if(mysqli_num_rows($select_cart) > 0){
                            while($fetch_cart = mysqli_fetch_assoc($select_cart)){
                        ?>
                        <tr class="border-b border-gray-700">
                            <td class="px-6 py-4 font-medium text-white flex items-center">
                                <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" class="h-12 w-16 rounded-md mr-4 object-cover">
                                <span><?php echo $fetch_cart['name']; ?></span>
                            </td>
                            <td class="px-6 py-4">$<?php echo number_format($fetch_cart['price']); ?>/-</td>
                            <td class="px-6 py-4">
                                <a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('remove item from cart?')" class="text-red-500 hover:text-red-400"><i class="fas fa-trash"></i> Remove</a>
                            </td>
                        </tr>
                        <?php
                            $grand_total += $fetch_cart['price'];
                            };
                        };
                        ?>
                        <tr class="bg-gray-700/50 font-bold">
                            <td class="px-6 py-4 text-white" colspan="1">Grand Total</td>
                            <td class="px-6 py-4 text-white">$<?php echo number_format($grand_total); ?>/-</td>
                            <td class="px-6 py-4">
                                <a href="cart.php?delete_all" onclick="return confirm('are you sure you want to delete all?');" class="text-red-500 hover:text-red-400"><i class="fas fa-trash"></i> Delete All</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-8 flex justify-end">
                <a href="checkout.php" class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-indigo-700 transition <?php echo ($grand_total > 1)?'':'pointer-events-none opacity-50'; ?>">Proceed to Checkout</a>
            </div>
             <div class="text-center mt-6">
                <a href="course-page.php" class="text-indigo-400 hover:text-indigo-300">or continue shopping</a>
            </div>
        </div>
    </div>
</body>
</html>
