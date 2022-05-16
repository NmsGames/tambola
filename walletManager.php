<?php
require_once('./header.php');
 
include_once './config.php';
include_once './Class/Users.php';
$database = new Database();
$db = $database->getConnection();
$user = new Users($db);

//Create User
if (!empty($_POST)) {
    
    if (isset($_POST['submit'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") { 

            $amount   = isset($_POST['amount'])?$_POST['amount']:null;
            $utype    = isset($_POST['utype'])?$_POST['utype']:null;
            $user_id  = isset($_POST['user_id'])?$_POST['user_id']:null;
           

            if(!empty($amount) && !empty($utype) && !empty($user_id)){

                $data['amount']   =  $amount; 
                $data['user_id']  =  $user_id; 
                $data['utype']    =  $utype; 
                $result = $user->updateCoin($data);

                if($result){
                    $flash['success_message']= 'success';
                }else{
                    $flash['error_message']= 'some error occur.';
                }


            }else{
                $flash['error_message']= 'Enter the amount correctly !!';
            }
        }
    }
} 
$result = $user->getUsers();
?>
<!-- ADD Flash Message -->
<?php if(isset($flash))  include_once './flash.php'; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    

    <!-- Main content -->
    <section class="content"> 
            <div class="row"> 
                <div class="col-md-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Wallet Manager</h3>
                        </div>
                        <form> 
                            <div class="card-body">
                                <table data-replace="jtable" id="example" aria-label="JS Datatable" data-locale="en" data-search="true" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="col-1">#UserID</th>
                                            <th>User Name</th>
                                            <th>Email ID</th>
                                            <th>Coins</th>
                                            <th>Credit/Debit</th>
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
                                                    <span class="badge bg-primary p-1"><?= isset($item['coins']) ? $item['coins'] : '0'; ?></span> 
                                                    </td>
                                                    </td>
                                                    <td>
                                                        <button type="button"  class="btn btn-success credit_wellet"  data-id = <?php echo $item['user_id']?> > <i class="fas fa-plus"></i> Credit</button>
                                                        <button type="button" class="ml-2 btn btn-danger debit_wellet" data-id = <?php echo $item['user_id']?> ><i class="fas fa-minus"></i> Debit</button>
                                                    </td>
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



    <div id="modal-debit" class="modal fade show" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Wallet</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h5> Debit Amount </h5>
                    <form enctype="multipart/form-data" method="post" accept-charset="utf-8">
                        <div class="form-group">
                            <div class="input text">
                                <input type="text" name="amount" class="form-control" placeholder="Enter amount" oninput="this.value = this.value.replace(/[^0-9.-]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" maxlength="10" id="amount">
                            </div>  
                           <input type="hidden"  name="utype" class="form-control" value="debit">          
                           <input type="hidden"  name="user_id" class="form-control" placeholder="Amount" id="udid" value="">
                        </div>
                        <div class="form-group balance-btn">
                            <button type="submit" name="submit" class="btn btn-primary submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-credit" class="modal fade show" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Wallet</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h5> Credit Amount </h5>
                    <form enctype="multipart/form-data" method="post" accept-charset="utf-8">
                        <div class="form-group">
                            <div class="input text">
                                <input type="text" name="amount" class="form-control" placeholder="Enter amount" oninput="this.value = this.value.replace(/[^0-9.-]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" maxlength="10" id="amount">
                            </div>  
                           <input type="hidden"  name="utype" class="form-control" value="credit">          
                           <input type="hidden"  name="user_id" class="form-control" placeholder="Amount" id="ucid" value="">
                        </div>
                        <div class="form-group balance-btn">
                            <button type="submit" name="submit" class="btn btn-primary submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>






    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>


<?php
require_once('./footer.php');
?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".credit_wellet").click(function () {
            var user_id = $(this).attr('data-id');
            $('#ucid').val(user_id);
            $('#modal-credit').modal('show');
        });
        $(".debit_wellet").click(function () {
            var user_id = $(this).attr('data-id');
            $('#udid').val(user_id);
            $('#modal-debit').modal('show');
        });
    });    
    
</script>



