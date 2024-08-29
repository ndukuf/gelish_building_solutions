<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name'] ?> | Home</title>
        <?php require_once 'head.php'; ?>
    </head>
    <body style="margin-bottom:155px;">
        <?php require_once 'navbar.php'; ?>
        <section>
            <!-- <div id="carouselCaptions" class="carousel slide" data-ride="carousel"> -->
            <div id="carouselCaptions" class="carousel slide carousel-fade" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselCaptions" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselCaptions" data-slide-to="1"></li>
                    <li data-target="#carouselCaptions" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="Logo/Gelish Building Solutions logo.png" class="d-block w-100" alt="Gelish Building Solutions">
                        <div class="carousel-caption d-none d-md-block">
                            <p class="font-weight-bold text-secondary">Building beautiful homes one dream at a time.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="Logo/Gelish Building Solutions logo.png" class="d-block w-100" alt="Gelish Building Solutions">
                        <div class="carousel-caption d-none d-md-block">
                            <p class="font-weight-bold text-secondary">Making your vision become a reality.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="Logo/Gelish Building Solutions logo.png" class="d-block w-100" alt="Gelish Building Solutions">
                        <div class="carousel-caption d-none d-md-block">
                            <p class="font-weight-bold text-secondary">It's your dream home. Let's build it right.</p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselCaptions" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselCaptions" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>
        <section class="bg-secondary p-4">
            <div class="container">
                <p class="text-center font-weight-bold">Products</p>
                <div class="container">
                    <div class="row">
                        <?php
                            $product_status = 6;

                            $selectProductsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price`,`product_count` FROM `products` WHERE `product_status`=? ORDER BY RAND()';
                            
                            // Prepare the SQL statement
                            $selectProductsStmt = $dbConnection->prepare($selectProductsSql);

                            // Bind parameters to the prepared statement
                            $selectProductsStmt->bind_param('s',$product_status);

                            // Execute the prepared statement
                            $selectProductsStmt->execute();

                            // Retrieve the result set
                            $selectProductsResult = $selectProductsStmt->get_result();

                            // Close the $selectProductsStmt prepared statement to free up resources
                            $selectProductsStmt->close();

                        ?>
                        <!-- Fetch data as an associative array -->
                        <?php while ($selectProductsAssoc= $selectProductsResult->fetch_assoc()) { ?>
                            <a href="details?item=<?php echo $selectProductsAssoc['product_code']; ?>" class="text-decoration-none text-dark col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 mt-1">
                                <div class="card" style="width:250px;">
                                    <?php
                                        // Define the SQL query to select the product image name from the products_images table
                                        // where the product code matches the given product code, limiting the results to 1
                                        $selectProductImageSql = 'SELECT `product_image_name` FROM `products_images` WHERE `product_code`=? LIMIT 1';

                                        // Prepare the SQL statement for execution
                                        $selectProductImageStmt = $dbConnection->prepare($selectProductImageSql);

                                        // Bind the product code parameter to the prepared statement
                                        // $selectProductsAssoc['product_code'] contains the product code value to be used in the query
                                        $selectProductImageStmt->bind_param('s', $selectProductsAssoc['product_code']);

                                        // Execute the prepared statement to perform the query on the database
                                        $selectProductImageStmt->execute();

                                        // Retrieve the result set from the executed query
                                        $selectProductImageResult = $selectProductImageStmt->get_result();

                                        // Close the $selectProductImageStmt prepared statement to free up resources
                                        $selectProductImageStmt->close();

                                        // Fetch the first row from the result set as an associative array
                                        // This will contain the product image name for the given product code
                                        $selectProductImageAssoc= $selectProductImageResult->fetch_assoc();

                                        // Define the SQL query to select the product code from the wishlist table
                                        // where the product code matches the given product code, limiting the results to 1
                                        $checkProductWishlistAvailabilitySql = 'SELECT `product_code` FROM `wishlist` WHERE `user_id`=? AND `product_code`=? LIMIT 1';

                                        // Prepare the SQL statement for execution
                                        $checkProductWishlistAvailabilityStmt = $dbConnection->prepare($checkProductWishlistAvailabilitySql);

                                        // Bind the product code parameter to the prepared statement
                                        // $selectProductsAssoc['product_code'] contains the product code value to be used in the query
                                        $checkProductWishlistAvailabilityStmt->bind_param('ss', $_SESSION['user_id'],$selectProductsAssoc['product_code']);

                                        // Execute the prepared statement to perform the query on the database
                                        $checkProductWishlistAvailabilityStmt->execute();

                                        // Retrieve the result set from the executed query
                                        $checkProductWishlistAvailabilityResult = $checkProductWishlistAvailabilityStmt->get_result();

                                        // Close the $checkProductWishlistAvailabilityStmt prepared statement to free up resources
                                        $checkProductWishlistAvailabilityStmt->close();

                                        // Check if the product image name is not empty
                                        if (!empty($selectProductImageAssoc['product_image_name'])) {
                                            // Display the product image using the product image name
                                            // and the productsImages directory
                                            // Set the height and width of the image to 200px and 100px respectively
                                            // Use the object-fit:contain style to maintain the aspect ratio of the image
                                            // Set the alt attribute to the admin/productsImages/product_image_name
                                            // for accessibility and SEO purposes
                                        ?>
                                            <img src="productsImages/<?php echo $selectProductImageAssoc['product_image_name']; ?>" height="200px" width="100px" style="object-fit:contain;"class="card-img-top" alt="admin/productsImages/<?php echo $selectProductImageAssoc['product_image_name']; ?>">
                                            <?php
                                        } else {
                                            // Display the company logo if the product image name is empty
                                            // Set the height and width of the image to 200px and 100px respectively
                                            // Use the object-fit:contain style to maintain the aspect ratio of the image
                                            // Set the alt attribute to the company name for accessibility and SEO purposes
                                        ?>
                                            <img src="<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" height="200px" width="100px" style="object-fit:contain;"class="bg-dark card-img-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>">
                                            <?php
                                        }
                                    ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $selectProductsAssoc['product_name']; ?></h5>
                                        <p class="card-text font-weight-bold"><?php echo $selectProductsAssoc['product_selling_price']; ?></p>
                                    </div>
                                    <div class="card-footer">
                                        <div>
                                            <?php if ($checkProductWishlistAvailabilityResult->num_rows !== 1) { ?>
                                                <button type="button" class="float-left btn btn-transparent"><i class="far fa-heart fa-lg"></i></button>
                                            <?php } else { ?>
                                                <button type="button" class="float-left btn btn-transparent"><i class="fas fa-heart fa-lg" style="color: #fd1d29;"></i></button>
                                            <?php } ?>
                                            <button type="button" class="float-right btn btn-transparent border border-dark">Add to cart <i class="fas fa-cart-plus fa-flip-horizontal"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>