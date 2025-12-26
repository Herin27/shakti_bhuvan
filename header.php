<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/navbar.css">
</head>
<style>
.contact-info a {
    color: inherit;
    text-decoration: none;
}

.contact-info a:hover {
    text-decoration: none;
}
</style>

<body>
    <header class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <a href="index.php"><img src="assets/images/logo.png" alt="Shakti Bhuvan Logo"></a>
            </div>
            <div class="logo-text">
                <h1>Shakti Bhuvan</h1>
                <span>Premium Stays</span>
            </div>
        </div>

        <div class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>

        <nav class="nav-links" id="nav-menu">
            <a href="index.php">Home</a>
            <a href="rooms.php">Rooms</a>
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact</a>
            <a href="admin.php">Admin</a>

            <div class="mobile-contact-info">
                <a href="rooms.php" class="book-btn">Book Now</a>
            </div>
        </nav>

        <div class="contact-info desktop-only">
            <span><i class="fas fa-phone"></i> <a href="tel:+919265900219">+91 92659 00219</a></span>
            <span><i class="fas fa-envelope"></i> <a
                    href="mailto:shaktibhuvanambaji@gmail.com">shaktibhuvanambaji@gmail.com</a></span>
            <a href="rooms.php" class="book-btn">Book Now</a>
        </div>
    </header>

    <script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');

    mobileMenu.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
    </script>
</body>

</html>