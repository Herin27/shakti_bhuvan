<?php
include 'db.php';

/* Fetch images */
$result = $conn->query("SELECT * FROM gallery ORDER BY image_type, created_at DESC");

$gallery = [];
while ($row = $result->fetch_assoc()) {
    $gallery[$row['image_type']][] = $row;
}

$categories = ['Hotel View', 'Luxury Suite', 'Deluxe Room', 'Standard Room'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Gallery</title>

    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/navbar.css">

    <style>
        body {
            margin: 0;
            font-family: 'Playfair Display', serif;
            background: #fdfbf6;
        }

        main {
            padding-top: 120px;
            max-width: 1800px;
            margin: auto;
        }

        h1, p {
            text-align: center;
        }

        /* Category Buttons */
        .category-buttons {
            text-align: center;
            margin: 30px 0;
        }

        .category-buttons button {
            margin: 6px;
            padding: 10px 22px;
            border: none;
            background: #c4a36f;
            color: #fff;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .category-buttons button.active,
        .category-buttons button:hover {
            background: #a57e3d;
        }

        /* Gallery Grid */
        .gallery-section {
            display: none;
        }

        .gallery-section.active {
            display: block;
        }

        .gallery-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0,0,0,0.6);
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 15px;
        }

        /* FULLSCREEN LIGHTBOX */
        .lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.9);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.lightbox.active { display: flex; }

.lightbox img {
    max-width: 85%;
    max-height: 85%;
    border-radius: 10px;
}

/* CONTROLS */
.lightbox-close {
    position: absolute;
    top: 25px;
    right: 30px;
    font-size: 36px;
    color: #fff;
    cursor: pointer;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    font-size: 50px;
    color: #fff;
    cursor: pointer;
    padding: 15px;
    transform: translateY(-50%);
    user-select: none;
}

.lightbox-prev { left: 30px; }
.lightbox-next { right: 30px; }

.lightbox-nav:hover,
.lightbox-close:hover { color: #c4a36f; }

    </style>
</head>

<body>

<?php include 'header.php'; ?>

<main>
    <h1>Hotel Gallery</h1>
    <p>Explore our hotel photos by categories</p>

    <!-- CATEGORY BUTTONS -->
    <div class="category-buttons">
        <?php foreach ($categories as $cat): ?>
            <button onclick="showCategory('<?php echo $cat; ?>', this)">
                <?php echo $cat; ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- GALLERY -->
    <?php foreach ($gallery as $type => $images): ?>
<div class="gallery-section" id="<?php echo $type; ?>">
    <div class="gallery-container">
    <?php foreach ($images as $index => $img): ?>
        <div class="gallery-item"
             onclick="openLightbox('<?php echo $type; ?>', <?php echo $index; ?>)">
            <img src="<?php echo $img['image_url']; ?>">
            <div class="gallery-caption"><?php echo $type; ?></div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
</main>

<?php include 'footer.php'; ?>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox">
<span class="lightbox-close" onclick="closeLightbox()">×</span>
<span class="lightbox-nav lightbox-prev" onclick="prevImage()">❮</span>
<img id="lightbox-img">
<span class="lightbox-nav lightbox-next" onclick="nextImage()">❯</span>
</div>

<script>
const galleryData = <?php echo json_encode($gallery); ?>;
let currentCategory = "";
let currentIndex = 0;

function showCategory(cat, btn) {
    document.querySelectorAll('.gallery-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.category-buttons button').forEach(b => b.classList.remove('active'));
    document.getElementById(cat).classList.add('active');
    btn.classList.add('active');
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelector(".category-buttons button")?.click();
});

function openLightbox(category, index) {
    currentCategory = category;
    currentIndex = index;
    updateImage();
    document.getElementById('lightbox').classList.add('active');
}

function updateImage() {
    document.getElementById('lightbox-img').src =
        galleryData[currentCategory][currentIndex].image_url;
}

function nextImage() {
    currentIndex = (currentIndex + 1) % galleryData[currentCategory].length;
    updateImage();
}

function prevImage() {
    currentIndex = (currentIndex - 1 + galleryData[currentCategory].length) %
                   galleryData[currentCategory].length;
    updateImage();
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
}

document.addEventListener('keydown', e => {
    if (e.key === "ArrowRight") nextImage();
    if (e.key === "ArrowLeft") prevImage();
    if (e.key === "Escape") closeLightbox();
});

document.getElementById('lightbox').addEventListener('click', e => {
    if (e.target.id === "lightbox") closeLightbox();
});
</script>

</body>
</html>