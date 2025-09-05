<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>M & S Learning - The Future of Education</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; }
        .header-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; }
        .glass-nav { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .course-card { transform-style: preserve-3d; transition: transform 0.8s; cursor: pointer; }
        .course-card.is-flipped { transform: rotateY(180deg); }
        .card-face { backface-visibility: hidden; position: absolute; width: 100%; height: 100%; }
        .card-back { transform: rotateY(180deg); }
    </style>
</head>
<body class="text-white">

    <header class="relative h-screen overflow-hidden">
        <canvas id="header-canvas" class="header-canvas"></canvas>
        <div class="relative z-10 container mx-auto px-4 h-full flex flex-col">
            <nav class="py-4 flex justify-between items-center glass-nav rounded-full mt-4 px-6">
                <a href="index.php" class="text-3xl font-bold">M & S <span class="text-indigo-400">Learning</span></a>
                <ul class="hidden md:flex items-center space-x-8 text-lg">
                    <li><a href="index.php" class="hover:text-indigo-300 transition">Home</a></li>
                    <li><a href="course-page.php" class="hover:text-indigo-300 transition">Courses</a></li>
                    <li><a href="about.php" class="hover:text-indigo-300 transition">About</a></li>
                    <li><a href="contact.php" class="hover:text-indigo-300 transition">Contact</a></li>
                </ul>
                <div class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['admin_name'])): ?>
                        <a href="admin_panel.php" class="hover:text-indigo-300 transition"><i class="fas fa-user-shield mr-1"></i> Admin Panel</a>
                        <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 py-2 px-4 rounded-full transition">Logout</a>
                    <?php elseif (isset($_SESSION['instructor_name'])): ?>
                        <a href="instructor_panel.php" class="hover:text-indigo-300 transition"><i class="fas fa-chalkboard-teacher mr-1"></i> Instructor Panel</a>
                        <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 py-2 px-4 rounded-full transition">Logout</a>
                    <?php elseif (isset($_SESSION['user_name'])): ?>
                        <a href="student_panel.php" class="hover:text-indigo-300 transition"><i class="fas fa-user-graduate mr-1"></i> My Courses</a>
                         <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 py-2 px-4 rounded-full transition">Logout</a>
                    <?php else: ?>
                        <a href="login_form.php" class="hover:text-indigo-300 transition">Login</a>
                        <a href="register_form.php" class="bg-indigo-500 hover:bg-indigo-600 py-2 px-4 rounded-full transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </nav>
            <div class="flex-grow flex flex-col justify-center items-center text-center">
                <h2 class="text-6xl md:text-8xl font-black tracking-tight leading-tight mb-4">Step Into the Future</h2>
                <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mb-8">Experience a new dimension of learning with our cutting-edge online courses.</p>
                <a href="course-page.php" class="bg-white text-black font-bold py-4 px-8 rounded-full text-lg hover:bg-gray-200 transition">Explore Now</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto p-6 md:p-12">
        <section id="why-us" class="py-20">
            <div class="text-center">
                <h2 class="text-4xl font-bold mb-4">Why M & S Learning?</h2>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-12">We provide a platform for you to learn, grow, and succeed.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="feature-card bg-gray-900 p-8 rounded-lg">
                        <i class="fas fa-chalkboard-teacher text-4xl text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Expert Instructors</h3>
                        <p class="text-gray-400">Learn from industry experts with years of practical experience.</p>
                    </div>
                    <div class="feature-card bg-gray-900 p-8 rounded-lg">
                        <i class="fas fa-laptop-code text-4xl text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Flexible Learning</h3>
                        <p class="text-gray-400">Access courses anytime, anywhere, and learn at your own pace.</p>
                    </div>
                    <div class="feature-card bg-gray-900 p-8 rounded-lg">
                        <i class="fas fa-medal text-4xl text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Career Growth</h3>
                        <p class="text-gray-400">Gain in-demand skills recognized by top companies worldwide.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="featured-courses" class="py-20">
            <h2 class="text-4xl font-bold text-center mb-12">Featured Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 3");
                if(mysqli_num_rows($select_products) > 0){
                    while ($fetch_product = mysqli_fetch_assoc($select_products)):
                ?>
                <div class="course-card h-96 relative" onclick="this.classList.toggle('is-flipped')">
                    <div class="card-face w-full h-full rounded-lg shadow-lg overflow-hidden bg-cover bg-center" style="background-image: url('uploaded_img/<?php echo $fetch_product['image']; ?>')">
                        <div class="bg-black bg-opacity-60 p-6 h-full flex flex-col justify-end">
                            <h3 class="text-2xl font-bold"><?php echo htmlspecialchars($fetch_product['name']); ?></h3>
                            <p class="text-indigo-400">Click to learn more</p>
                        </div>
                    </div>
                    <div class="card-face card-back w-full h-full rounded-lg shadow-lg bg-gray-800 p-6 flex flex-col">
                        <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($fetch_product['name']); ?></h3>
                        <p class="text-gray-300 flex-grow overflow-y-auto"><?php echo htmlspecialchars($fetch_product['description']); ?></p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-3xl font-bold text-indigo-400">$<?php echo $fetch_product['price']; ?></span>
                            <a href="checkout.php?course_id=<?php echo $fetch_product['id']; ?>" class="bg-indigo-500 text-white py-2 px-4 rounded-full hover:bg-indigo-600">Enroll Now</a>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                } else {
                    echo "<p class='col-span-3 text-center text-gray-500'>No featured courses are available at the moment. Check back soon!</p>";
                }
                ?>
            </div>
        </section>

        <section id="expert-instructors" class="py-20">
            <div class="text-center">
                <h2 class="text-4xl font-bold mb-4">Learn from Industry Experts</h2>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-12">Our instructors are leaders in their fields, dedicated to your success.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                    <?php
                    $select_instructors = mysqli_query($conn, "SELECT * FROM `instructors` LIMIT 4");
                    if(mysqli_num_rows($select_instructors) > 0){
                        while ($instructor = mysqli_fetch_assoc($select_instructors)):
                    ?>
                    <div class="text-center">
                        <img class="w-40 h-40 rounded-full mx-auto mb-4 object-cover border-4 border-gray-700" src="instructor_img/<?php echo $instructor['image']; ?>" alt="<?php echo htmlspecialchars($instructor['name']); ?>">
                        <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($instructor['name']); ?></h3>
                        <p class="text-indigo-400"><?php echo htmlspecialchars($instructor['expertise']); ?></p>
                    </div>
                    <?php 
                        endwhile;
                    }
                    ?>
                </div>
                <a href="instructors.php" class="mt-12 inline-block bg-indigo-500 text-white font-bold py-3 px-8 rounded-full hover:bg-indigo-600 transition">Meet All Instructors</a>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-gray-400">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">M & S Learning</h3>
                    <p>The future of education is here. Join us and unlock your potential.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="hover:text-white">About Us</a></li>
                        <li><a href="contact.php" class="hover:text-white">Contact</a></li>
                        <li><a href="course-page.php" class="hover:text-white">All Courses</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Stay Updated</h4>
                    <p class="mb-4">Get the latest news and course updates from us.</p>
                    <form action="">
                        <div class="flex">
                            <input type="email" placeholder="Your email" class="w-full px-4 py-2 bg-gray-800 text-white border border-gray-700 rounded-l-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button class="bg-indigo-500 hover:bg-indigo-600 px-4 rounded-r-full"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Connect With Us</h4>
                    <div class="flex space-x-4 text-2xl">
                        <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center">
                <p>&copy; <?php echo date("Y"); ?> M & S Learning. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('header-canvas'), alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);

        const starsGeometry = new THREE.BufferGeometry();
        const starsCount = 5000;
        const posArray = new Float32Array(starsCount * 3);
        for (let i = 0; i < starsCount * 3; i++) {
            posArray[i] = (Math.random() - 0.5) * 10;
        }
        starsGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
        const starsMaterial = new THREE.PointsMaterial({ size: 0.008, color: 0xffffff });
        const starField = new THREE.Points(starsGeometry, starsMaterial);
        scene.add(starField);

        camera.position.z = 5;

        let mouseX = 0;
        let mouseY = 0;
        document.addEventListener('mousemove', (event) => {
            mouseX = event.clientX;
            mouseY = event.clientY;
        });

        function animate() {
            requestAnimationFrame(animate);
            
            starField.rotation.x += 0.00005;
            starField.rotation.y += 0.0001;
            
            camera.position.x += ( (mouseX / window.innerWidth) * 2 - 1 - camera.position.x) * 0.02;
            camera.position.y += ( - (mouseY / window.innerHeight) * 2 + 1 - camera.position.y) * 0.02;

            camera.lookAt(scene.position);
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            renderer.setSize(window.innerWidth, window.innerHeight);
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
        });
    </script>
</body>
</html>