<?php
session_start();
include "db-conn.php";

$sql = "SELECT * FROM `categories` ORDER BY id DESC";
$check = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add New Product | Sales Dashboard</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">

    <?php include "links.php"; ?>
    <style>
        .form-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            padding: 25px;
            border-left: 4px solid #4e73df;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 1.3rem;
        }

        .form-control,
        .form-select {
            border-radius: 4px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }

        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .btn-submit {
            background: #4e73df;
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #3a5bbf;
            transform: translateY(-2px);
        }

        .preview-image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .preview-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
        }

        .required-field::after {
            content: " *";
            color: #f44336;
        }
    </style>
</head>

<body class="crm_body_bg">

    <?php include "header.php"; ?>
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
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0">Add New Product</h2>
                                        <p class="m-0 text-muted">Fill in the details below to add a new product to your
                                            inventory</p>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div class="card-body">
                                    <form id="productForm" action="functions.php" method="post"
                                        enctype="multipart/form-data">

                                        <!-- Basic Information Section -->
                                        <div class="form-section">
                                            <div class="section-title">
                                                <i class="fas fa-info-circle"></i> Basic Information
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="proName">Product
                                                        Name</label>
                                                    <input type="text" class="form-control" name="pro_name" id="proName"
                                                        placeholder="Enter product name" />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="brandName">Brand
                                                        Name</label>
                                                    <input type="text" class="form-control" name="brand_name"
                                                        id="brandName" placeholder="Enter brand name" />
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label" for="inputEmail4">Short
                                                    Description</label>
                                                <textarea class="form-control" name="short_desc" required></textarea>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label" for="inputEmail4">Product
                                                    Description</label>
                                                <textarea class="form-control" name="pro_desc" required></textarea>
                                            </div>

                                        </div>

                                        <!-- Category & Inventory Section -->
                                        <div class="form-section">
                                            <div class="section-title">
                                                <i class="fas fa-tags"></i> Category & Inventory
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="proCate">Main
                                                        Category</label>
                                                    <select class="form-select" name="pro_cate" id="proCate" required
                                                        onchange="get_subcategory(this.value)">
                                                        <option value="" disabled selected>Select a category</option>
                                                        <?php foreach ($check as $val) { ?>
                                                            <option value="<?= $val['cate_id'] ?>">
                                                                <?= ucwords($val['categories']) ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="subcate_id">Sub Category</label>
                                                    <select class="form-select" name="pro_sub_cate" id="subcate_id">
                                                        <option value="" selected>Select subcategory (optional)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="stock">Stock
                                                        Quantity</label>
                                                    <input type="number" class="form-control" name="stock" id="stock"
                                                        placeholder="Available quantity" min="0" />
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="status">Status</label>
                                                    <select class="form-select" name="status" id="status" required>
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pricing Section -->
                                        <div class="form-section">
                                            <div class="section-title">
                                                <i class="fas fa-tag"></i> Pricing Information
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="mrp">Manufacturer's
                                                        Price (MRP)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" name="mrp" id="mrp"
                                                            placeholder="0.00" step="0.01" min="0" />
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="sellingPrice">Selling
                                                        Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" name="selling_price"
                                                            id="sellingPrice" placeholder="0.00" step="0.01" min="0" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="newArrival">Show in Inquery Form</label>
                                                    <select class="form-select" name="new_arrival" id="newArrival">
                                                        <option value="0" selected>No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="trending">Mark as Special
                                                        Offer</label>
                                                    <select class="form-select" name="trending" id="trending">
                                                        <option value="0" selected>No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Media Section -->
                                        <div class="form-section">
                                            <div class="section-title">
                                                <i class="fas fa-images"></i> Product Images
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label required-field" for="pro_img">Upload
                                                        Product Images</label>
                                                    <input type="file" class="form-control" name="pro_img[]"
                                                        id="pro_img" multiple />
                                                        
                                                    <small class="text-muted">You can select multiple images (JPEG, PNG,
                                                        max 5MB each)</small>
                                                    <div class="preview-image-container" id="imagePreview"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SEO Section -->
                                        <div class="form-section">
                                            <div class="section-title">
                                                <i class="fas fa-search"></i> SEO Settings
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label required-field" for="metaTitle">Meta
                                                        Title</label>
                                                    <input type="text" class="form-control" name="meta_title"
                                                        id="metaTitle"
                                                        placeholder="Title for search engines (50-60 characters)"
                                                        maxlength="60" />
                                                    <small class="text-muted" id="titleCounter">0/60 characters</small>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="metaKey">Meta
                                                        Keywords</label>
                                                    <input type="text" class="form-control" name="meta_key" id="metaKey"
                                                        placeholder="Comma separated keywords" />
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required-field" for="metaDesc">Meta
                                                        Description</label>
                                                    <input type="text" class="form-control" name="meta_desc"
                                                        id="metaDesc"
                                                        placeholder="Brief description for search results" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-2"></i>Reset Form
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-submit" name="add-product">
                                                <i class="fas fa-plus-circle me-2"></i>Add Product
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>

        <!-- <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script> -->
        <script>

            // Image preview functionality
            document.getElementById('pro_img').addEventListener('change', function (event) {
                const previewContainer = document.getElementById('imagePreview');
                previewContainer.innerHTML = '';

                if (this.files) {
                    Array.from(this.files).forEach(file => {
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('preview-image');
                            previewContainer.appendChild(img);
                        }

                        reader.readAsDataURL(file);
                    });
                }
            });

            // Character counter for meta title
            document.getElementById('metaTitle').addEventListener('input', function () {
                const counter = document.getElementById('titleCounter');
                counter.textContent = `${this.value.length}/60 characters`;

                if (this.value.length > 60) {
                    counter.style.color = 'red';
                } else {
                    counter.style.color = '#6c757d';
                }
            });

            // AJAX function for subcategory selection
            function get_subcategory(cate_id) {
                $.ajax({
                    url: 'functions.php',
                    method: 'post',
                    data: { cate_id: cate_id },
                    error: function () {
                        alert("Error loading subcategories");
                    },
                    success: function (data) {
                        $("#subcate_id").html(data);
                    }
                });
            }

            // Form validation
            document.getElementById('productForm').addEventListener('submit', function (event) {
                const mrp = parseFloat(document.getElementById('mrp').value);
                const sellingPrice = parseFloat(document.getElementById('sellingPrice').value);

                if (sellingPrice > mrp) {
                    alert('Selling price cannot be higher than MRP');
                    event.preventDefault();
                }
            });
        </script>
    </section>
</body>

</html>
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

        <script>
            CKEDITOR.replace('pro_desc')
            CKEDITOR.replace('short_desc')
        </script>