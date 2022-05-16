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
include_once './Class/Tickets.php';
$database = new Database();
$db = $database->getConnection();

$items = new Categories($db);

$actual_link = 'http://' . $_SERVER['HTTP_HOST'];

// if (!empty($_POST)) {
//     if (isset($_POST['submit'])) {
//         if ($_SERVER["REQUEST_METHOD"] == "POST") {
//             $input_name = trim($_POST["category_name"]);
//             if (empty($input_name)) {
//                 $err = "Please enter a category name.";
//             } elseif (!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
//                 $err = "Please enter a valid name.";
//             } else {
//                 $err = '';
//                 $cat = strtolower($input_name);
//                 $sql = "SELECT * FROM category where LOWER(category_name) = '" . $cat . "' ";
//                 $result = $conn->query($sql);
//                 if ($result->num_rows > 0) {
//                     $err = "Duplicate category";
//                 } else {
//                     $sql = "INSERT INTO category (category_name)
//                     VALUES ('" . $input_name . "')";

//                     if ($conn->query($sql) === TRUE) {
//                         $err = "Category created success";
//                     } else {
//                         $err = "Error: " . $sql . "<br>" . $conn->error;
//                     }
//                 }
//             }

//             // $conn->close();
//         }
//     }
// }
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    

    <!-- Main content -->
    <section class="content"> 
            <div class="row"> 
                <div class="col-md-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Users</h3>
                        </div>
                        <form> 
                            <div class="card-body">
                                <table data-replace="jtable" id="example" aria-label="JS Datatable" data-locale="en" data-search="true" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#Sr Number</th>
                                            <th>User Name</th>
                                            <th>Email ID</th>
                                            <th>Event ID</th>
                                            <th>Event Date</th>
                                            <th>Event Time</th>
                                            <th>Number Of Tickets</th>
                                            <th>Cost Of Tickets</th>
                                            <th>Purchased Date</th> 
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ticket = new Tickets($db); 
                                        $result = $ticket->purchaseTicketHistory();
                                        if ($result->num_rows > 0) { 
                                            $serial_numver = 1;
                                            while ($item = $result->fetch_assoc()) {
                                                $id = $item['user_id'];   
                                        ?>
                                                <tr>
                                                    <td><?= $serial_numver ?></td> 
                                                    <td>
                                                        <?= isset($item['username'])&& !empty($item['username']) ? $item['username'] : 'Guest0'.$id; ?> 
                                                    </td>  
                                                    <td>
                                                        <?= isset($item['email']) ? $item['email'] : ''; ?> 
                                                    </td>  
                                                    <td>
                                                        <?= isset($item['event_id']) ? $item['event_id'] : 'TEST'; ?> 
                                                    </td>  
                                                    <td>
                                                       <?= isset($item['event_date']) ? $item['event_date'] : ''; ?>  
                                                    </td>  
                                                    <td>
                                                       <?= isset($item['event_time']) ? $item['event_time'] : ''; ?>  
                                                    </td>  
                                                    <td>
                                                       <?= isset($item['tickets']) ? $item['tickets'] : 0; ?>  
                                                    </td>  
                                                    <td>
                                                       <?= isset($item['ticket_cost']) ? $item['ticket_cost'] : 0; ?>  
                                                    </td>  
                                                    <td>
                                                       <?= isset($item['created_at']) ? $item['created_at'] : ''; ?>  
                                                    </td>  
                                                    <td>
                                                     <?= isset($item['is_expired']) && $item['is_expired']==1 ? '<span class="badge bg-danger p-1">Expired</span>' : '<span class="badge bg-primary p-1">Active</span>'; ?>  
                                                    </td>
                                                     
                                                </tr>
                                        <?php
                                        $serial_numver ++;
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