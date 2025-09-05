<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;
$message = [];

if(isset($_POST['add_to_cart'])){
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = 1;

   $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'");

   if(mysqli_num_rows($select_cart) > 0){
      $message[] = 'Course already in cart';
   }else{
      $insert_product = mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')");
      $message[] = 'Course added to cart successfully!';
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Courses - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; color: #e5e7eb; }
        .glass-nav { background: rgba(17, 24, 39, 0.5); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .course-card { background: #111827; border: 1px solid rgba(255, 255, 255, 0.1); transition: transform 0.3s ease; }
        .course-card:hover { transform: translateY(-8px); }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="py-4 flex justify-between items-center glass-nav rounded-full mt-4 px-6 shadow-lg">
            <a href="index.php" class="text-3xl font-bold tracking-wider">M&S <span class="text-indigo-400">Learning</span></a>
            <ul class="hidden md:flex items-center space-x-8 text-lg">
                <li><a href="index.php" class="hover:text-indigo-300">Home</a></li>
                <li><a href="course-page.php" class="text-white font-semibold">Courses</a></li>
                <li><a href="instructors.php" class="hover:text-indigo-300">Instructors</a></li>
            </ul>
            <div class="flex items-center space-x-6">
                <a href="cart.php" class="text-lg hover:text-indigo-300 relative">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $select_rows = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                    $row_count = mysqli_num_rows($select_rows);
                    if($row_count > 0){
                        echo "<span class='absolute -top-2 -right-3 bg-indigo-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center'>$row_count</span>";
                    }
                    ?>
                </a>
                <?php if (isset($_SESSION['user_name'])): ?>
                    <a href="student_panel.php" class="text-lg hover:text-indigo-300"><i class="fas fa-user-graduate mr-2"></i>My Panel</a>
                <?php else: ?>
                    <a href="login_form.php" class="text-lg hover:text-indigo-300">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>

    <main class="container mx-auto p-6 md:p-12">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-white">Explore Our Courses</h1>
            <p class="text-lg text-gray-400 mt-4 max-w-3xl mx-auto">Find the perfect course to achieve your learning goals.</p>
        </div>

        <?php
        if(!empty($message)){
            foreach($message as $msg){
                echo "<div class='mb-6 p-4 rounded-lg bg-green-500/20 text-green-300 text-center'>$msg</div>";
            }
        }
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php
            $select_products = mysqli_query($conn, "SELECT * FROM `products`");
            if(mysqli_num_rows($select_products) > 0){
                while($fetch_product = mysqli_fetch_assoc($select_products)){
            ?>
            <form action="" method="post">
                <div class="course-card rounded-lg overflow-hidden flex flex-col h-full">
                    <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="w-full h-48 object-cover">
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo $fetch_product['name']; ?></h3>
                        <p class="text-gray-400 text-sm mb-4 flex-grow"><?php echo substr($fetch_product['description'], 0, 100); ?>...</p>
                        <div class="text-2xl font-bold text-indigo-400 mb-4">$<?php echo $fetch_product['price']; ?>/-</div>
                        <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                        <button type="submit" class="mt-auto w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition" name="add_to_cart">Add to Cart</button>
                    </div>
                </div>
            </form>
            <?php
                };
            };
            ?>
        </div>
    </main>
</body>
</html>
