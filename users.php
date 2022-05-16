<?php
require_once('./header.php');
 
include_once './config.php';
include_once './Class/Users.php';
$database = new Database();
$db = $database->getConnection();
$user = new Users($db); 
$result = $user->getUsers();
 
 
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
                                            <th>#UserID</th>
                                            <th>User Name</th>
                                            <th>Email ID</th>
                                            <th>Number of Tickets</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        
                                        if ($result->num_rows > 0) { 
                                            while ($item = $result->fetch_assoc()) {
                                                $id = $item['user_id'];
                                                
                                        ?>
                                                <tr>
                                                    <td><?= isset($item['user_id']) ? $item['user_id'] : null; ?></td>
                                                    <td>
                                                    <?= isset($item['username']) && !empty($item['username'])  ? $item['username'] : 'Guest001'.$id; ?>
                                                        <?php
                                                       // echo "<a href = '$actual_link/tambola/subcategory.php?catId=$id'> $cat </a>";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?= isset($item['email']) ? $item['email'] : 'GuestUser001'.$id; ?> 
                                                    </td>
                                                    <td>
                                                    <span class="badge bg-primary p-1"><?= isset($item['total_ticktes']) ? $item['total_ticktes'] : '0'; ?></span> 
                                                    </td>
                                                    </td>
                                                    <td><button disabled class="btn btn-danger">DELETE</button><button class="ml-2 btn btn-success" disabled>Edit</button></td>
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