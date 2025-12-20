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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | Shakti Bhuvan</title>

    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #c4a36f;
            --dark-gold: #a57e3d;
            --bg-cream: #fdfbf6;
            --text-dark: #2c2c2c;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-cream);
            color: var(--text-dark);
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #666;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        main {
            padding-top: 140px;
            max-width: 1400px;
            margin: auto;
            margin-bottom: 110px;
            padding-left: 20px;
            padding-right: 20px;
            min-height: 80vh;
        }

        /* --- Category Filter Styling --- */
        .category-wrapper {
            position: sticky;
            top: 80px;
            z-index: 10;
            background: rgba(253, 251, 246, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            margin-bottom: 40px;
        }

        .category-buttons {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .category-buttons button {
            padding: 12px 28px;
            border: 1px solid #ddd;
            background: transparent;
            color: #444;
            font-size: 14px;
            font-weight: 500;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-buttons button.active {
            background: var(--primary-gold);
            color: #fff;
            border-color: var(--primary-gold);
            box-shadow: 0 4px 15px rgba(196, 163, 111, 0.3);
        }

        .category-buttons button:hover:not(.active) {
            border-color: var(--primary-gold);
            color: var(--primary-gold);
        }

        /* --- Gallery Grid Layout --- */
        .gallery-section {
            display: none;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .gallery-section.active {
            display: block;
        }

        .gallery-container {
            columns: 3 300px; /* Masonry effect */
            column-gap: 20px;
        }

        .gallery-item {
            break-inside: avoid;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            cursor: zoom-in;
            background: #eee;
        }

        .gallery-item img {
            width: 100%;
            display: block;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
            height: 250px;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        /* Hover Overlay */
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .overlay-text {
            color: #fff;
            font-size: 1.2rem;
            font-family: 'Playfair Display', serif;
        }

        /* --- Lightbox --- */
        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.95);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }

        .lightbox.active { display: flex; }

        .lightbox img {
            max-width: 90%;
            max-height: 80vh;
            object-fit: contain;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            border: 3px solid rgba(255,255,255,0.1);
        }

        .lightbox-close {
            position: absolute;
            top: 30px;
            right: 40px;
            font-size: 40px;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            font-size: 40px;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            padding: 20px;
            transform: translateY(-50%);
            transition: 0.3s;
        }

        .lightbox-nav:hover { color: var(--primary-gold); }
        .lightbox-prev { left: 20px; }
        .lightbox-next { right: 20px; }

        @media (max-width: 768px) {
            .gallery-container { columns: 2; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>

<body>

<?php include 'header.php'; ?>

<main>
    <div style="text-align: center;">
        <p class="subtitle">Experience Luxury</p>
        <h1>Our Gallery</h1>
    </div>

    <div class="category-wrapper">
        <div class="category-buttons">
            <?php foreach ($categories as $index => $cat): ?>
                <button class="<?php echo $index === 0 ? 'active' : ''; ?>" onclick="showCategory('<?php echo str_replace(' ', '-', $cat); ?>', this)">
                    <?php echo $cat; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach ($gallery as $type => $images): 
        $safe_id = str_replace(' ', '-', $type);
    ?>
    <div class="gallery-section" id="<?php echo $safe_id; ?>">
        <div class="gallery-container">
            <?php foreach ($images as $index => $img): ?>
                <div class="gallery-item" onclick="openLightbox('<?php echo $type; ?>', <?php echo $index; ?>)">
                    <img src="<?php echo $img['image_url']; ?>" alt="<?php echo $type; ?>" loading="lazy">
                    <div class="gallery-overlay">
                        <div class="overlay-text"><i class="fa fa-search-plus"></i> View Image</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</main>

<?php include 'footer.php'; ?>

<div class="lightbox" id="lightbox">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <span class="lightbox-nav lightbox-prev" onclick="prevImage()"><i class="fa fa-chevron-left"></i></span>
    <img id="lightbox-img">
    <span class="lightbox-nav lightbox-next" onclick="nextImage()"><i class="fa fa-chevron-right"></i></span>
</div>

<script>
const galleryData = <?php echo json_encode($gallery); ?>;
let currentCategory = "";
let currentIndex = 0;

function showCategory(id, btn) {
    document.querySelectorAll('.gallery-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.category-buttons button').forEach(b => b.classList.remove('active'));
    
    const target = document.getElementById(id);
    if(target) {
        target.classList.add('active');
        btn.classList.add('active');
    }
}

// Set default view on load
document.addEventListener("DOMContentLoaded", () => {
    const firstBtn = document.querySelector(".category-buttons button");
    if(firstBtn) firstBtn.click();
});

function openLightbox(category, index) {
    currentCategory = category;
    currentIndex = index;
    updateImage();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden'; // Stop scrolling
}

function updateImage() {
    const img = document.getElementById('lightbox-img');
    img.style.opacity = '0';
    setTimeout(() => {
        img.src = galleryData[currentCategory][currentIndex].image_url;
        img.style.opacity = '1';
    }, 150);
}

function nextImage() {
    currentIndex = (currentIndex + 1) % galleryData[currentCategory].length;
    updateImage();
}

function prevImage() {
    currentIndex = (currentIndex - 1 + galleryData[currentCategory].length) % galleryData[currentCategory].length;
    updateImage();
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Keyboard navigation
document.addEventListener('keydown', e => {
    if (!document.getElementById('lightbox').classList.contains('active')) return;
    if (e.key === "ArrowRight") nextImage();
    if (e.key === "ArrowLeft") prevImage();
    if (e.key === "Escape") closeLightbox();
});
</script>

</body>
</html>