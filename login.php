<!DOCTYPE html>
<html lang="en">
<?php
function pathUrl($dir = __DIR__){ 
    $root = "";
    $dir = str_replace('\\', '/', realpath($dir)); 
    //HTTPS or HTTP
    $root .= !empty($_SERVER['HTTPS']) ? 'https' : 'http'; 
    //HOST
    $root .= '://' . $_SERVER['HTTP_HOST']; 
    //ALIAS
    if(!empty($_SERVER['CONTEXT_PREFIX'])) {
        $root .= $_SERVER['CONTEXT_PREFIX'];
        $root .= substr($dir, strlen($_SERVER[ 'CONTEXT_DOCUMENT_ROOT' ]));
    } else {
        $root .= substr($dir, strlen($_SERVER[ 'DOCUMENT_ROOT' ]));
    } 
    $root .= '/'; 
    return $root;
  } ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./dist//css//style.css">
</head>

<body>
     
    <div class="wrapper fadeInDown">
        <div id="formContent">
            <div class="fadeIn first">
                <img src="https://opulasoft.com/Tambola/img/logo.png" width="150" height="181" id="icon" alt="User Icon" />
            </div>

            <!-- Login Form -->
            <span id="errmessage"></span>
            <form method="post" id="cpa-form">
                
                <input type="text" id="Username" class="fadeIn second" value="test@admin" name="Username" placeholder="Username">
                <br>
                <span id="err"></span>
                <input type="text" id="password" class="fadeIn third"value="admin" name="login" placeholder="password">
                <br>
                <span id="passerr"></span>
                <br>
                <input type="submit" name="login" class="fadeIn fourth" value="Log In">

            </form>

            <input type="hidden" id="url" value="<?= pathUrl(); ?>">
            <!-- Remind Passowrd -->
            <div id="formFooter">
                <a class="underlineHover" href="#">Forgot Password?</a>
            </div>

        </div>
    </div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $("#cpa-form").submit(function(e) {
        e.preventDefault();
        const Username = $('#Username').val();
        const password = $('#password').val();
        const url = $('#url').val();
        if (!Username) {
            $('#errmessage').html('Username is required')
        } else {
            if (!password) {
                $('#errmessage').html('Please enter your password')
            } else {
                $.ajax({
                type: "post",
                url: "Api/backend-script.php",             
                data: { typ:'login',username:'rajendra@test.com',password:12345},                
                dataType: "html",                  
                success: function(data){
                    // window.location.replace("http://localhost/tambola/category.php");
                    data = JSON.parse(data);  
                    if(data.status===200){
                        $('#errmessage').html(data.message)
                        setInterval(function () {
                            $('#errmessage').html('')
                            window.location.replace(url);
                        }, 2000);
                        
                    }else{
                        $('#errmessage').html(data.message)
                        setInterval(function () {
                            $('#errmessage').html('')
                        }, 50000);
                    }
                    console.log(data)       
                    // $("#sub_category").html(data); 
                }
             })
            }
        }


       
    });
    //     function loginForm(e){
    //         e.preventDefault()

    // }
</script>

</html>