<?php
require_once('./header.php');
include_once './Class/Users.php';
include_once './config.php';
$database = new Database();
$db = $database->getConnection();
$user = new Users($db); 



//Create User
if (!empty($_POST)) {
    
    if (isset($_POST['submit'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {  

            $username = isset($_POST['user_name'])?$_POST['user_name']:null;
            $email    = isset($_POST['email'])?$_POST['email']:null;
            $password = isset($_POST['password'])?$_POST['password']:null;
            $cpassword = isset($_POST['confirm_password'])?$_POST['confirm_password']:null;

            if(!empty($username) && !empty($email) && !empty($password) && !empty($cpassword)){

                if($cpassword == $password){

                    $checkEmail = $user->selectUser($email);

                    if($checkEmail->num_rows > 0){
                       $flash['error_message']= 'Email already exist.';
                    }else{
                        $data      =   (object) array();
                        $data->username  =  $username; 
                        $data->email  =  $email; 
                        $data->password  =  $password; 
                        $result = $user->createUser($data); 
                        $flash['success_message'] = "User created successfully.";
                    }
                }else{
                    $flash['error_message']= 'Confirm password not match.';
                }

            }else{
                $flash['error_message']= 'Please fill all detail.';
            }
        }
    }
}


 
 
?>
<!-- ADD Flash Message -->
<?php if(isset($flash))  include_once './flash.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    

    <!-- Main content -->
    <section class="content"> 
            <div class="row"> 
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add User</h3>
                        </div>
                        <form enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div style="display:none;">
                                <input type="hidden" name="_method" value="POST">
                            </div>       
                           <div class="row">
                                <div class="col-md-12">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="first-name">
                                                      User Name <span class="required"></span>
                                                    </label>
                                                    <input type="text" name="user_name" maxlength="30" class="form-control" placeholder="User Name" id="user-name">
                                                </div>
                                            </div>
                                           
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="email">
                                                        Email <span class="required">*</span>
                                                    </label>
                                                    <input type="email" name="email" class="form-control" placeholder="E-Mail Address" maxlength="255" id="email">
                                                </div>
                                            </div>                         
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Password <span class="required">*</span></label>
                                                    <input type="password" name="password" class="form-control" placeholder="Password" id="password">
                                                </div>
                                            </div>

                                        
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="confirm-password">Confirm Password <span class="required">*</span></label>
                                                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" id="confirm-password">
                                                </div>
                                            </div>
                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
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