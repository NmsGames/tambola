<?php
require_once('./header.php');
require_once('./dbconnection.php');
// Processing form data when form is submitted
$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$baseurl = "http://" . $host . $path . "/";

$err = "";
include_once './config.php';
include_once './Class/Categories.php';

$database = new Database();
$db = $database->getConnection();

$items = new Categories($db);

$items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

$result = $items->read();
$actual_link = 'http://' . $_SERVER['HTTP_HOST'];

if (!empty($_POST)) {
    if (isset($_POST['submit'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $input_name = trim($_POST["category_name"]);
            if (empty($input_name)) {
                $err = "Please enter a category name.";
            } elseif (!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
                $err = "Please enter a valid name.";
            } else {
                $err = '';
                $cat = strtolower($input_name);
                $sql = "SELECT * FROM category where LOWER(category_name) = '" . $cat . "' ";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $err = "Duplicate category";
                } else {
                    $sql = "INSERT INTO category (category_name)
                    VALUES ('" . $input_name . "')";

                    if ($conn->query($sql) === TRUE) {
                        $err = "Category created success";
                    } else {
                        $err = "Error: " . $sql . "<br>" . $conn->error;
                    }
                }
            }

            // $conn->close();
        }
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    

    <!-- Main content -->
    <section class="content"> 
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add Category</h3>
                        </div>

                        <div class="card-body" style="min-height: 300px;"> 
                                <form action="" method="post" class="shadow-lg p-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Category Name</label>
                                        <input type="text" name="category_name" class="form-control" required  placeholder="Enter Category Name">
                                        <small id="emailHelp" class="form-text text-muted">Category name should not be duplicate.</small>
                                    </div> 
                                  
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                        </div> 

                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Category List</h3>
                        </div>
                        <form>
                            <div style="display:none;">
                                <input type="hidden" name="_method" value="POST" />
                            </div>
                            <div class="card-body">
                                <table data-replace="jtable" id="example" aria-label="JS Datatable" data-locale="en" data-search="true" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Category Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            $itemRecords = array();
                                            $itemRecords["items"] = array();
                                            while ($item = $result->fetch_assoc()) {
                                                $id = $item['category_id'];
                                                $cat = $item['category_name'];
                                        ?>
                                                <tr>
                                                    <td><?= isset($item['category_id']) ? $item['category_id'] : null; ?></td>
                                                    <td><?php
                                                        echo "<a href = '$actual_link/tambola/subcategory.php?catId=$id'> $cat </a>";
                                                        ?>
                                                    </td>
                                                    <td><button class="btn btn-danger">DELETE</button><button class="ml-2 btn btn-success">Edit</button></td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                        }
                                        ?>

                                    </tbody>
                                </table> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        
    </section>
    

    <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>

<?php


require_once('./footer.php');
?>