<?php
include('db.php');
// Handle Add Product
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $barcode = $_POST['barcode'];
    $image = $_POST['image']; // store image path or handle upload
// Handle image upload 
    $targetDir = "uploads/"; 
    $targetFile = $targetDir . basename($_FILES["image"]["name"]); 
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile); 
    $sql = "INSERT INTO products (name, price, image, barcode) VALUES ('$name','$price','$targetFile','$barcode')"; 
    $con->query($sql);
}

// Handle Edit Product
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $barcode = $_POST['barcode'];
    $image = $_POST['image'];

    $sql = "UPDATE products SET name='$name', price='$price', image='$image', barcode='$barcode' WHERE id=$id";
    $con->query($sql);
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM products WHERE id=$id");
}

// Fetch Products
$result = $con->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background: #f4f4f4; }
        .scanner { margin: 10px; text-align: center; }
    </style>
</head>
<body>
    <h2>Manage Products</h2>

    <form method="post" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>

    <!-- Barcode Input with Scan Options -->
    <input type="text" id="barcodeInput" name="barcode" placeholder="Barcode" required>
    <button type="button" onclick="startScanner()">📷 Scan Barcode</button>
    <!-- <input type="file" id="uploadBarcode" accept="image/*"> -->

    <!-- Image Input with Camera or Upload -->
    <input type="file" name="image" accept="image/*" capture="environment" required>
    <small>You can upload an image or take a photo directly</small>

    <button type="submit" name="add">Add Product</button>
</form>


    <!-- Scanner Video
    <div id="scanner" style="display:none; text-align:center;">
        <video id="scannerVideo" width="300" height="200"></video>
        <button onclick="stopScanner()">Stop</button>
    </div> -->

    <hr>

    <!-- Product Table -->
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Barcode</th><th>Image</th><th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td>$<?= number_format($row['price'],2) ?></td>
            <td><?= $row['barcode'] ?></td>
            <td><img src="<?= $row['image'] ?>" width="50"></td>
            <td>
                <!-- Edit Form -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="text" name="name" value="<?= $row['name'] ?>">
                    <input type="number" step="0.01" name="price" value="<?= $row['price'] ?>">
                    <input type="text" name="barcode" value="<?= $row['barcode'] ?>">
                    <input type="text" name="image" value="<?= $row['image'] ?>">
                    <button type="submit" name="edit">Save</button>
                </form>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Barcode Scanner Script -->
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        let codeReader = new ZXing.BrowserMultiFormatReader();

        async function startScanner() {
            document.getElementById('scanner').style.display = "block";
            try {
                const devices = await codeReader.listVideoInputDevices();
                const firstDeviceId = devices[0].deviceId;
                codeReader.decodeFromVideoDevice(firstDeviceId, 'scannerVideo', (result, err) => {
                    if (result) {
                        document.getElementById('barcodeInput').value = result.text;
                        stopScanner();
                    }
                });
            } catch (err) {
                alert("Camera error: " + err);
            }
        }

        function stopScanner() {
            codeReader.reset();
            document.getElementById('scanner').style.display = "none";
        }

        // // Upload image to scan barcode
        // document.getElementById('uploadInput').addEventListener('change', async function() {
        //     const file = this.files[0];
        //     if (!file) return;
        //     const reader = new FileReader();
        //     reader.onload = async function() {
        //         try {
        //             const result = await codeReader.decodeFromImageUrl(reader.result);
        //             document.getElementById('barcodeInput').value = result.text;
        //         } catch (err) {
        //             alert("Could not detect barcode in image.");
        //         }
        //     };
        //     reader.readAsDataURL(file);
        // });
    </script>
</body>
</html>
