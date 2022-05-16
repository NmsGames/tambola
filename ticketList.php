<!DOCTYPE html>
<html lang="en">
<?php
$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$baseurl = "http://" . $host . $path . "/"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Details</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <!-- <link rel="stylesheet" href="./dist//css//style.css"> -->
</head>
<style>
    body {
        box-sizing: border-box;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #BA68C8
    }

    .profile-button {
        background: rgb(99, 39, 120);
        box-shadow: none;
        border: none
    }

    .profile-button:hover {
        background: #682773
    }

    .profile-button:focus {
        background: #682773;
        box-shadow: none
    }

    .profile-button:active {
        background: #682773;
        box-shadow: none
    }

    .back:hover {
        color: #682773;
        cursor: pointer
    }

    .labels {
        font-size: 11px
    }

    .add-experience:hover {
        background: #BA68C8;
        color: #fff;
        cursor: pointer;
        border: solid 1px #BA68C8
    }

    .ticket {
        width: 75px;
        font-size: 19px;
        font-weight: bold;
        height: 46px;
        text-align: center;
    }

    .removeBt {
        width: 40px;
        height: 43px;
        padding: 15px;
    }

    div.ex3 {
        /* background-color: lightblue; */
        height: 70vh;
        width: 100%;
        overflow-y: auto;
    }
</style>

<body>
    <?php
    include_once './config.php';
    include_once './Class/Tickets.php';
    include_once './Class/TicketCodes.php';
    include_once './Class/Types.php';
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
    }
    $siteUrl = pathUrl();
   
    $database = new Database();
    $db = $database->getConnection();

    $items = new Tickets($db);
    $tickID = (isset($_GET['ticketId']) && $_GET['ticketId']) ? $_GET['ticketId'] : '0';
    $items->ticketId =$tickID;
    $result = $items->read();
    $data = $result->fetch_assoc();
 
    if (isset($_POST['update_ticket'])) {  
        $totalfiles     = count($_POST['t_number']);
        $category_id    = isset($_POST['category_id']) ? $_POST['category_id'] : null;
        $type_id        = isset($_POST['type_id']) ? $_POST['type_id'] : null;
        $ticket_id      = isset($_POST['ticket_id']) ? $_POST['ticket_id'] : null;
        $sub_category_id = isset($_POST['sub_category_id']) ? $_POST['sub_category_id'] : null;
        $code = isset($_POST['t_number']) ? $_POST['t_number'] : null;

        if (!empty($category_id)) {
            if (!empty($sub_category_id)) {
                if (!empty($type_id)) {
                    if (!empty($ticket_id)) {
                        // Looping over all files 
                        for ($i = 0; $i < $totalfiles; $i++) {
                            $ticket = new TicketCodes($db);
                            $number = $code[$i];
                            if (isset($number) && !empty($number)) {
                                
                                $itemDetails = array(
                                    "ticket_id"     => $ticket_id,
                                    "code"          => $number,
                                    "category_id"   => $category_id,
                                    "type_id"       => $type_id,
                                    "sub_category_id" => $sub_category_id
                                );
                                $result = $ticket->selecCodes($itemDetails);
                                if($result->num_rows>0){
                                    $rsdata = $result->fetch_assoc();
                                    $updateItem = array(
                                        "ticket_id"     => $ticket_id,
                                        "code"          => $number,
                                        "category_id"   => $category_id,
                                        "type_id"       => $type_id,
                                        "sub_category_id" => $sub_category_id,
                                        "ticket_number_id" => $rsdata['ticket_number_id']
                                    );
                                    // $ticket->updateCodes($updateItem);
                                    continue;
                                }else{
                                    $ticket->createCodes($itemDetails);
                                    continue;
                                }  
                                
                            } else {
                                continue;
                            }
                        }
                    } else {
                        $error_message = 'Sub category does not empty';
                    }
                } else {
                    $error_message = 'Sub category does not empty';
                }
            } else {
                $error_message = 'Sub category does not empty';
            }
        } else {
            $error_message = 'Category does not empty';
        }
       
    }



    ?>
    <div class="container-fluid rounded bg-white mt-5 mb-5">
        <div class="jumbotron">
            <div class="row">
                <div class="col-md-7 border-right">
                    <img class="rounded-circle mt-5" style="width:700px;min-height: 80vh;" src="<?= $data['ticket_file_url'];       ?>">
                </div>
                <div class="col-md-5 border-right">
                    <?php
                    //Selet tickets category types
                    $type   = new Types($db);
                    $type->cat_id = isset($data['category_id']) ? $data['category_id'] : 0;
                    $type->sub_id  = isset($data['sub_category_id']) ? $data['sub_category_id'] : 0;
                    $result1 = $type->read();
                   
                     

                    ?>
                    <div class="card">
                        <div class="card-header">
                          <a href="<?= $siteUrl ?>/ticketsUpload.php"class="text-left btn btn-primary" >Go Back Tickets Page</a>

                            <h4 class="text-right">Tickets Number Settings</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= isset($data['category_name']) ? $data['category_name'] : null ?> & <?php echo isset($data['sub_category_name']) ? $data['sub_category_name'] : null ?></h5>
                            <div class="ex3">

                                <div class="row">
                                    <?php
                                    if ($result1->num_rows > 0) {  
                                        while ($item = $result1->fetch_assoc()) { 
                                    ?>
                                            <div class="col-md-6">
                                                <form class="shadow-lg" method="post">
                                                    <input type="hidden" name="category_id" value="<?= isset($data['category_id']) ? $data['category_id'] : 0 ?>">
                                                    <input type="hidden" name="sub_category_id" value="<?= isset($data['sub_category_id']) ? $data['sub_category_id'] : 0 ?>">
                                                    
                                                    <input type="hidden" name="ticket_id" value="<?= (isset($_GET['ticketId']) && $_GET['ticketId']) ? $_GET['ticketId'] : '0'; ?>">
                                                    <h4 class="card-title"><?php echo $item['name'] ?></h4>
                                                    <button type="submit" name="update_ticket" class="mr-2 btn btn-success">Update</button>
                                                    <span class="addRow mr-2 btn btn-info">+</span>
                                                    <input type="hidden" value="<?php echo isset($item['type_id']) ? $item['type_id'] : 0; ?>">
                                                    <input type="hidden" name="type_id" value="<?= isset($item['type_id']) ? $item['type_id'] : 0; ?>">
                                                    <br>
                                                    <div class="card shadow-lg">
                                                        <?php 
                                                        $ID = isset($item['type_id'])? $item['type_id']:0;
                                                        $sql = "SELECT * FROM `ticket_numbers` WHERE `type_id` = '".$ID."' AND  ticket_id='".$tickID."'";
                                                        $result3 = $db->query($sql); 
                                                        // print_r($result3);
                                                        // exit;
                                                        ?>
                                                        <br>
                                                        <div id="cat<?php echo isset($item['type_id']) ? $item['type_id'] : 0; ?>">
                                                        <?php 
                                                        if($result3->num_rows>0){
                                                            while ($item = $result3->fetch_assoc()){?>
                                                                <div class="input-group mb-3">
                                                                    <input type="number" name="t_number[]" class="ticket" placeholder="#T-001" value="<?=$item['code'] ?>" autocomplete="off"> 
                                                                    <?php $ticketID = $item['ticket_number_id'] ?>
                                                                <a href="<?= $siteUrl ?>Api/backend-script.php?codeId=<?= $ticketID ?>&del=tt" class="removeBt btn btn-danger" onclick="return confirm('Are you sure you want to delete this item')">X</a href="">
                                                            </div>
                                                            <?php }
                                                        }
                                                        ?>
                                                       
                                                    </div>
                                                    </div>
                                                </form>
                                            </div>
                                    <?php
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                    } ?>


                                </div>

                            </div>
                        </div>
                    </div>





                </div>
            </div>
        </div>

    </div>
    </div>
    </div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
    // add row
    $(".addRow").click(function() {
        console.log('adfd');
        var html = '';
        html += '<div class="input-group mb-3">';
        html += '<input type="number" name="t_number[]" class="ticket" placeholder="#T-001" autocomplete="off"> <span class="removeBt btn btn-danger">X</span>';

        html += '</div>';
        console.log($(this).next().val())
        const classname = $(this).next().val()
        $(`#cat${classname}`).append(html);
    });

    // remove row
    $(document).on('click', '#removeRow', function() {
        $(this).closest('#inputFormRow').remove();
    });
</script>

</html>