<?php
include('db.php');

// Fetch products
$result = $con->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Mart POS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background: #333; color: #fff; padding: 15px; text-align: center; }
        .search-bar { margin: 20px; text-align: center; }
        .search-bar input { width: 300px; padding: 8px; }
        .search-bar button { margin-left: 10px; padding: 8px; }
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px;
            padding: 10px;
            width: 180px;
            text-align: center;
            transition: 0.3s;
        }
        .product:hover { box-shadow: 0 0 10px rgba(0,0,0,0.2); }
        .product img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .product-name { font-weight: bold; margin-top: 10px; }
        .product-price { color: green; margin-top: 5px; }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <h1>Welcome to Nyvar Mart</h1>
    </header>

    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search by name or barcode...">
        <button onclick="startScanner()">📷 Scan Barcode</button>
        <!-- <input type="file" id="uploadInput" accept="image/*"> -->
    </div>


    <!-- Product Grid -->
    <div class="product-grid" id="productGrid">
        <?php while($p = $result->fetch_assoc()): ?>
            <div class="product" data-barcode="<?= $p['barcode'] ?>">
                <img src="<?= $p['image'] ?>" alt="<?= $p['name'] ?>">
                <div class="product-name"><?= $p['name'] ?></div>
                <div class="product-price">$<?= number_format($p['price'], 2) ?></div>
                <div class="product-barcode">Barcode: <?= $p['barcode'] ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- JavaScript for Search + Barcode -->
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', filterProducts);

        function filterProducts() {
            let filter = searchInput.value.toLowerCase();
            let products = document.querySelectorAll('.product');
            products.forEach(function(product) {
                let name = product.querySelector('.product-name').textContent.toLowerCase();
                let barcode = product.getAttribute('data-barcode').toLowerCase();
                if (name.includes(filter) || barcode.includes(filter)) {
                    product.style.display = "";
                } else {
                    product.style.display = "none";
                }
            });
        }

        // Barcode scanner with camera
        let codeReader = new ZXing.BrowserMultiFormatReader();
        async function startScanner() {
            document.getElementById('scanner').style.display = "block";
            try {
                const videoInputDevices = await codeReader.listVideoInputDevices();
                const firstDeviceId = videoInputDevices[0].deviceId;
                codeReader.decodeFromVideoDevice(firstDeviceId, 'scannerVideo', (result, err) => {
                    if (result) {
                        searchInput.value = result.text;
                        filterProducts();
                        stopScanner();
                    }
                });
            } catch (err) {
                alert("Camera access error: " + err);
            }
        }
        function stopScanner() {
            codeReader.reset();
            document.getElementById('scanner').style.display = "none";
        }
    </script>

</body>
</html>
