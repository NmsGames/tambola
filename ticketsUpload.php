<?php
require_once('./header.php'); 
require_once('./dbconnection.php');
// Processing form data when form is submitted

$siteUrl = pathUrl();
 
include_once './config.php';
include_once './Class/Categories.php';
include_once './Class/Tickets.php';

$database  = new Database();
$db        = $database->getConnection();
 

 
 

//CREate Tickets
if (!empty($_POST)) {
    
    if (isset($_POST['submit'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {   
          
            $totalfiles = count($_FILES['file']['name']); 
            $category_id = isset($_POST['category_id'])?$_POST['category_id']:null;
            $sub_category_id = isset($_POST['sub_category_id'])?$_POST['sub_category_id']:null;
            if(!empty($category_id)){
                if(!empty($sub_category_id)){
                     
                    $statusMsg = '';
                    // Looping over all files 
                    for($i=0;$i<$totalfiles;$i++){
                        $ticket         = new Tickets($db);  
                        $file           = $_FILES['file']['name'][$i];
                        $file_info      = pathinfo($file);  
                        $ticket_name    = $file_info['filename'];
                        $ticket->ticketname= $ticket_name;
                        $result_data    = $ticket->read();
                        $response_data  = $result_data->fetch_assoc();
                        
                      //path to upload in server
                        $targetDir      = "uploads/";
                        $fileName       = basename($file);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType       = pathinfo($targetFilePath,PATHINFO_EXTENSION);
                        
                        //Check Image type 
                        $allowTypes = array('jpg','JPG','JPEG','PNG','png','jpeg');
                        if(in_array($fileType, $allowTypes)){
                            // Upload file to server 
                            if(move_uploaded_file($_FILES["file"]["tmp_name"][$i], $targetFilePath)){
                                if($result_data->num_rows>0){ 
                                    $ticket->ticket_id  = $response_data['ticket_id'];
                                    $ticket->category_id= $category_id;
                                    $ticket->ticketname = $ticket_name;
                                    $ticket->sub_category_id= $sub_category_id;
                                    $ticket->ticket_file_url = $targetFilePath;	  
                                    $ticket->update() ;
                                    $statusMsg = "Success";
                                    continue;
                                }else{
                                    $ticket->category_id= $category_id;
                                    $ticket->ticketname = $ticket_name;
                                    $ticket->sub_category_id= $sub_category_id;
                                    $ticket->ticket_file_url = $targetFilePath;  
                                    $ticket->create() ;
                                    $statusMsg = "Success";
                                    continue;
                                }
                            }else{
                                continue;

                                $statusMsg = "Sorry, there was an error uploading your file.";
                            }
                        }else{
                            continue;
                            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
                        }
                        exit;
                    
                
                    } 
                }else{
                    $error_message= 'Sub category does not empty';
                }
            }else{
                $error_message= 'Category does not empty';
            }   
        }
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Tickets</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content"> 
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create Tickets</h3>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div style="display:none;">
                                <input type="hidden" name="_method" value="POST" />
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="first-name">
                                        <span class="required">Select Category</span>
                                    </label>
                                    <?php 
                                     //categories
                                    $items  = new Categories($db);
                                    $result = $items->read();
                                    ?>
                                    <select class="form-select form-control"  name="category_id" onchange='getSubCategories(this)' aria-label="Default select example">
                                        <option selected>Select Category</option>

                                        <?php
                                        if ($result->num_rows > 0) {
                                            $itemRecords = array();
                                            $itemRecords["items"] = array();
                                            while ($item = $result->fetch_assoc()) {
                                                echo "<option  value='" . $item['category_id'] . "'>" . $item['category_name'] . "</option>";
                                            }
                                        } else {
                                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                        }
                                        ?>
                                    </select>
                                </div> 
                                <div class="form-group">
                                    <label for="first-name">
                                        <span class="required">Select Sub Category</span>
                                    </label>

                                    <select class="form-select form-control" name="sub_category_id" id="sub_category" aria-label="Default select example">
                                        <option selected>Select Sub Category</option>

                                        <?php
                                        if ($result->num_rows > 0) {
                                            $itemRecords = array();
                                            $itemRecords["items"] = array();
                                            while ($item = $result->fetch_assoc()) {
                                                echo "<option value='" . $item['category_id'] . "'>" . $item['category_name'] . "</option>";
                                            }
                                        } else {
                                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                        }
                                        ?>
                                    </select>
                                </div> 
                                <div class="form-group">
                                    <label for="profile-picture">Select Image</label>
                                    <div class="input file"><input multiple type="file" name="file[]" onchange="loadGroupFile(event)" accept="image/*" class="" id="image"></div>
                                </div> 
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Tickets List</h3>
                        </div>
                         
                            <div class="card-body">
                            <table data-replace="jtable" id="example" aria-label="JS Datatable" data-locale="en" data-search="true" class="table table-bordered table-striped">
                                <thead>
                                    <tr> 
                                        <th>#TICKET ID</th>
                                        <th>Tickets Name</th>
                                        <th>Category Name</th>
                                        <th>Sub Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <?php 
                                    //Tickets
                                    $tck    = new Tickets($db);
                                    $data = $tck->readTickets();
                                  
                                    if($data->num_rows > 0){    
                                         
                                        while ($item = $data->fetch_assoc()) {
                                            $id = $item['ticket_id'];
                                            $ticket_name = $item['ticket_name'];
                                            ?> 	
                                            <tr> 
                                                <td><?= isset($item['ticket_id'])?$item['ticket_id']:null; ?></td>
                                                <td><?php
                                                echo "<a href = '$siteUrl/ticketList.php?ticketId=$id'> $ticket_name </a>";
                                                ?></td>
                                                <td><?= isset($item['category_name'])?$item['category_name']:null; ?></td>
                                                <td><?= isset($item['sub_category_name'])?$item['sub_category_name']:null; ?></td>
                                                <td><a href="<?= $siteUrl ?>Api/backend-script.php?ticketId=<?= $id ?>&del=TKT" class="removeBt btn btn-danger" onclick="return confirm('Are you sure you want to delete this item')">Delete</a> </td> 
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
                       
                    </div>
                </div>
            </div>
       
    </section>
    <script>
        function getSubCategories(val){
            // alert('adfdfdfd')
            $.ajax({    
            type: "GET",
            url: "Api/backend-script.php",             
            data: { type:'sub',Id:val.value},                
            dataType: "html",                  
            success: function(data){
                data = JSON.parse(data);         
                $("#sub_category").html(data); 
            }
        })
    }
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