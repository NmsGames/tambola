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
include_once './Class/SubCategories.php';
include_once './Class/Categories.php';
$database = new Database();
$db = $database->getConnection();
 
$items = new SubCategories($db);
$categoryID =(isset($_GET['catId']) && $_GET['catId']) ? $_GET['catId'] : '0';
$items->id = (isset($_GET['catId']) && $_GET['catId']) ? $_GET['catId'] : '0';

$result = $items->read();
 
 
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
                $sql = "SELECT * FROM category where LOWER(category_name) = '".$cat."' ";
                $result = $conn->query($sql); 
                if ($result->num_rows > 0) {
                    $err ="Duplicate category";
                }else{
                    $sql = "INSERT INTO category (category_name)
                    VALUES ('".$input_name."')";
                    
                    if ($conn->query($sql) === TRUE) {
                        $err ="Category created success";
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
        <div class="container-fluid">
            <div class="row">
                
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-title">
                        <a href="<?= pathUrl(); ?>category.php"class="text-right btn btn-primary" >Go Back Category Page</a>
                        </div>
                        <form> 
                            <div class="card-body">
                            <table class="table table-bordered table-striped" data-replace="jtable" id="example" aria-label="JS Datatable" data-locale="en" data-search="true">
                                <thead>
                                    <tr> 
                                        <th>#ID</th>
                                        <th>Category Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <?php 
                                    if($result->num_rows > 0){    
                                        $itemRecords=array();
                                        $itemRecords["items"]=array(); 
                                        while ($item = $result->fetch_assoc()) {
                                            ?> 	
                                            <tr> 
                                                <td><?= isset($item['sub_category_id'])?$item['sub_category_id']:null; ?></td>
                                                <td><b><?= isset($item['sub_category_name'])?$item['sub_category_name']:null; ?></b></td>
                                                <td><button class="btn btn-danger">DELETE</button><button class="ml-2 btn btn-success">Edit</button></td> 
                                            </tr> 
                                        <?php
                                        }  
                                    }else{     
                                        echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                    }
                                    ?>

                                </tbody>
                            </table> 
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add Sub Category</h3>
                        </div>

                        <div class="card-body" style="min-height: 300px;"> 
                                <form action="" method="post" class="shadow-lg p-4">
                                    <div class="form-group">
                                    <?php 
                                     //categories
                                    $items  = new Categories($db);
                                    $result = $items->read();
                                    ?>
                                    <label for="exampleInputEmail1">Category Name</label>
                                    <select class="form-select form-control"  name="category_id" onchange='getSubCategories(this)' aria-label="Default select example">
                                          
                                        <?php
                                        if ($result->num_rows > 0) {
                                            $itemRecords = array();
                                            $itemRecords["items"] = array();
                                            while ($item = $result->fetch_assoc()) { 
                                                if($categoryID ==$item['category_id']){ 
                                                    echo "<option  selected value='" . $item['category_id'] . "'>" . $item['category_name'] . "</option>";
                                                }
                                            }
                                        } else {
                                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                        }
                                        ?>
                                    </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Sub Category Name</label>
                                        <input type="text" name="category_name" class="form-control" required  placeholder="Enter Sub Category Name">
                                        <small id="emailHelp" class="form-text text-muted">Sub Category name should not be duplicate.</small>
                                    </div> 
                                  
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                        </div> 

                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const addButton = document.querySelector('.addButton');
        var input = document.querySelector('.input');
        const container = document.querySelector('.con_tainer');

        class item {
            constructor(itemName) {
                this.createDiv(itemName);
            }
            createDiv(itemName) {
                let input = document.createElement('input');
                input.value = itemName;
                input.disabled = true;
                input.classList.add('item_input');
                input.type = "text";

                let itemBox = document.createElement('div');
                itemBox.classList.add('item')

                let editButton = document.createElement('button');
                editButton.innerHTML = "EDIT"
                editButton.classList.add('editButton');

                let removeButton = document.createElement('button');
                removeButton.innerHTML = "REMOVE"
                removeButton.classList.add('removeButton');

                container.appendChild(itemBox);

                itemBox.appendChild(input);
                itemBox.appendChild(editButton);
                itemBox.appendChild(removeButton);

                editButton.addEventListener('click', () => this.edit(input));

                removeButton.addEventListener('click', () => this.remove(itemBox));
            }

            edit(input) {
                input.disabled = !input.disabled;
            }
            remove(item) {
                container.removeChild(item);
            }
        }
        // new item("Sport");
        function check() {
            if (input.value != "") {
                new item(input.value);
                input.value = "";
            }
        }
        addButton.addEventListener('click', check);
        window.addEventListener('keydown', (e) => {
            if (e.which == 13) {
                check();
            }
        })
    </script>

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