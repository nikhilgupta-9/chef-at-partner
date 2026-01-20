<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db-conn.php"; // Ensure this includes your database connection
include "functions.php";

if (!isset($_GET['edit_product_details'])) {
    die("Product ID is missing from the URL.");
}

$product_id = intval($_GET['edit_product_details']);

// Fetch product details using mysqli_query()
$query = "SELECT * FROM products WHERE pro_id = $product_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    die("Product not found.");
}

// Fetch categories
$category_query = "SELECT * FROM `categories`";
$categories = $conn->query($category_query);


$sql = "SELECT * FROM `categories` ORDER BY id DESC";
$check = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Sales</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">

    <?php include "links.php"; ?>
</head>

<body class="crm_body_bg">

    <?php
    include "header.php";
    ?>
    <section class="main_content dashboard_part large_header_bg">

        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner ">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="col-lg-12">
                            <div class="white_card card_height_100 mb_30">
                                <div class="white_card_header">
                                    <div class="box_header m-0">
                                        <div class="main-title">
                                            <h2 class="m-0">Update Product Details</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="white_card_body">

                                    <!-- <div class="col-md-12">
                                        <a href="multiple_img.php?id=<?= $product['pro_id'] ?>">
                                            <button type="" class="btn btn-danger">
                                                Add More Prodcut Images
                                            </button>
                                        </a>
                                    </div> -->
                                    <br />
                                    <br />
                                    <div class="card-body">
                                        <form action="update-product.php" method="POST" enctype="multipart/form-data">
                                            <!-- Hidden Input for Product ID -->
                                            <input type="hidden" name="pro_id" value="<?= $product['pro_id'] ?>" />

                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Product Name</label>
                                                    <input type="text" class="form-control" name="pro_name"
                                                        id="inputEmail4" value="<?= $product['pro_name'] ?>"
                                                        placeholder="Product Name" required />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Parent Category
                                                        Name</label>
                                                    <select class="form-control" name="pro_cate" required
                                                        onchange="get_subcategory(this.value)">
                                                        <option value="">--select--</option>
                                                        <?php foreach ($check as $val) { ?>
                                                            <option value="<?= $val['cate_id'] ?>">
                                                                <?= ucwords($val['categories']) ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Sub Category</label>
                                                    <select class="form-control" name="pro_sub_cate" id="subcate_id"
                                                        required>
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>


                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Stock</label>
                                                    <input type="text" class="form-control" name="stock"
                                                        id="inputEmail4" value="<?= $product['stock'] ?>"
                                                        placeholder="Stock" required />
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Product Image(s)</label>
                                                    <input type="file" class="form-control" name="pro_img[]"
                                                        id="pro_img" multiple />

                                                    <?php
                                                    // Assume $product['pro_img'] contains image filenames separated by commas
                                                    $images = explode(',', $product['pro_img']);
                                                    ?>

                                                    <div>
                                                        <small>Current Images:</small>
                                                        <?php foreach ($images as $img): ?>
                                                            <div style="display:inline-block; margin-right:10px;">
                                                                <img src="assets/img/uploads/<?= trim($img) ?>"
                                                                    style="height: 250px; width: 250px;"
                                                                    alt="Product Image">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>


                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Is it New
                                                        Arrival</label>
                                                    <select id="inputState" name="new_arrival" class="form-control"
                                                        required>
                                                        <option value="0" <?= $product['new_arrival'] == 0 ? 'selected' : '' ?>>No</option>
                                                        <option value="1" <?= $product['new_arrival'] == 1 ? 'selected' : '' ?>>Yes</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Is it Trending</label>
                                                    <select id="inputState" name="trending" class="form-control"
                                                        required>
                                                        <option value="0" <?= $product['trending'] == 0 ? 'selected' : '' ?>>No</option>
                                                        <option value="1" <?= $product['trending'] == 1 ? 'selected' : '' ?>>Yes</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="inputEmail4">Product Short
                                                        Description</label>
                                                    <textarea class="form-control" name="short_desc"
                                                        required><?= $product['short_desc'] ?></textarea>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="inputEmail4">Product Long
                                                        Description</label>
                                                    <textarea class="form-control" name="pro_desc"
                                                        required><?= $product['description'] ?></textarea>
                                                </div>


                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">MRP</label>
                                                    <input type="text" class="form-control" name="mrp" id="inputEmail4"
                                                        value="<?= $product['mrp'] ?>" placeholder="MRP" required />
                                                </div>


                                                <!-- <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Discount In %</label>
                                                    <input type="text" class="form-control"
                                                        name="whole_sale_selling_price" id="inputEmail4"
                                                        value="<?= $product['whole_sale_selling_price'] ?>"
                                                        placeholder="Discount Percent" required />
                                                </div> -->


                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Selling Price</label>
                                                    <input type="text" class="form-control" name="selling_price"
                                                        id="inputEmail4" value="<?= $product['selling_price'] ?>"
                                                        placeholder="Selling Price" required />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Qty</label>
                                                    <input type="text" class="form-control" name="qty" id="inputEmail4"
                                                        value="<?= $product['qty'] ?>" placeholder="MRP" required />
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Meta Title</label>
                                                    <input type="text" class="form-control" name="meta_title"
                                                        id="inputEmail4" value="<?= $product['meta_title'] ?>"
                                                        placeholder="Meta Title" required />
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Meta Keyword</label>
                                                    <input type="text" class="form-control" name="meta_key"
                                                        id="inputEmail4" value="<?= $product['meta_key'] ?>"
                                                        placeholder="Meta Keyword" required />
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="inputEmail4">Meta Description</label>
                                                    <input type="text" class="form-control" name="meta_desc"
                                                        id="inputEmail4" value="<?= $product['meta_desc'] ?>"
                                                        placeholder="Meta Description" required />
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label" for="inputState">Status</label>
                                                    <select id="inputState" name="status" class="form-control" required>
                                                        <option value="1" <?= $product['status'] == 1 ? 'selected' : '' ?>>
                                                            Active</option>
                                                        <option value="0" <?= $product['status'] == 0 ? 'selected' : '' ?>>
                                                            Deactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <button type="submit" name="update-product" class="btn btn-primary">Update
                                                Product</button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>

        <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

        <script>
            CKEDITOR.replace('pro_desc')
            CKEDITOR.replace('short_desc')
        </script>

        <!-- ajax function for selecting category then automatically show sub category  -->
        <script type="text/javascript">
            function get_subcategory(cate_id) {
                var cate_id = cate_id;
                $.ajax({
                    url: 'functions.php',
                    method: 'post',
                    data: { cate_id: cate_id },
                    error: function () {
                        alert("something went wrong");
                    },
                    success: function (data) {
                        $("#subcate_id").html(data);
                        // alert(data);
                    }
                })
            }
        </script>

        <?php
        // Fetch the current product data
// $product_id = $_GET['id'];
// $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
// $stmt->execute([$product_id]);
// $product = $stmt->fetch(PDO::FETCH_ASSOC);
        

        ?>