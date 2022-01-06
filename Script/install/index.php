<?php

error_reporting(0);
ini_set("memory_limit", "-1");
set_time_limit(0);
$ServerErrors = array();
$config = '../resources/config.php';

if (!empty($_POST['host']) && !empty($_POST['user']) && !empty($_POST['db']) && !empty($_POST['site_url']) && !empty($_POST['purshase_code'])) {

    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass_db'];
    $db = $_POST['db'];
    $error = false;
    $Connect = mysqli_connect($host, $user, $pass, $db);

    if (mysqli_connect_errno()) {
        echo "<div style='padding: 10px;background-color:#ffffff;'>Failed to connect to MySQL: " . mysqli_connect_error() . "</div>";
        $error = true;
    }
    if (!filter_var($_POST['site_url'], FILTER_VALIDATE_URL)) {
        echo "<div style='padding: 10px;background-color:#ffffff;'>Invalid site url</div>";
        $error = true;
    }

    if ($error == false) {
        $file_config = '<?php
$host = "' . $host . '";
$user = "' . $user . '";
$pass = "' . $pass . '";
$name = "' . $db . '";
$site_url = "' . $_POST['site_url'] . '"; // e.g (http://example.com)
$purshase_code = "' . $_POST['purshase_code'] . '";
?>';
        $file_put_contents = file_put_contents($config, $file_config);
        $success = false;
        if ($file_put_contents) {
            $filename = '../twidley.sql';
            $templine = '';
            $lines = file($filename);
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '')
                    continue;
                $templine .= $line;
                $query = false;
                if (substr(trim($line), -1, 1) == ';') {
                    $query = mysqli_query($Connect, $templine);
                    $templine = '';
                }
            }

            if ($query) {
                $site_url = $_POST['site_url'];
                $Connect = mysqli_connect($host, $user, $pass, $db);

                mysqli_query($Connect, "UPDATE `so_config` SET `value` = '{$_POST['site_name']}' WHERE `name` = 'site_name'");
                if (mysqli_connect_errno()) {
                    echo "<div style='padding: 10px;background-color:#ffffff;'>" . mysqli_connect_error() . "</div>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Twidley - Install</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=460">
        <link rel="icon" href="../themes/default/images/favicon.png" />
        <script type="text/javascript" src="../themes/default/javascript/jquery-3.2.1.js"></script>
        <script type="text/javascript" src="../themes/default/javascript/jquery.ui.js"></script>
        <script type="text/javascript" src="../themes/default/javascript/jquery.form.js"></script>
        <link rel="stylesheet" href="../themes/default/stylesheet/jquery.ui.css"/>
        <link rel="stylesheet" href="../themes/default/stylesheet/twemoji-awesome.css"/>
        <link rel="stylesheet" href="../themes/default/stylesheet/font-awesome-4.7.0/css/font-awesome.css"/>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
            body{margin-top:20px;}
            .fa-fw {width: 2em;}
            #asfasfa {
                text-align: center;
                font-weight: bold;
                font-size: 20px;
                color: #673AB7;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="nav nav-pills nav-stacked admin-menu">
                        <li <?php if (!isset($_GET['page'])) { ?>class="active"<?php } ?>>
                            <a href="#">
                                Welcome
                            </a>
                        </li>
                        <li <?php if ($_GET['page'] == 'config') { ?>class="active"<?php } ?>>
                            <a href="#">
                                Config
                            </a>
                        </li>
                        <li <?php if ($_GET['page'] == 'finished') { ?>class="active"<?php } ?>>
                            <a href="#">
                                Finished
                            </a>
                        </li>
                    </ul>
                </div>
                <?php
                if (!isset($_GET['page'])) {
                    ?>
                    <div class="col-md-9 well admin-content" id="home">
                        <p><strong>Sowler 1.0</strong></p>
                        <p><br>
                            Welcome to Installation page of Sowler , follow the steps  :)<br>
                        </p>
                        <a href="../install/?page=config" class="btn btn-primary" >Start Install</a>
                    </div>
                <?php } ?>
                <?php
                if (isset($_GET['page']) && $_GET['page'] == 'config') {
                    ?>
                    <div class="col-md-9 well admin-content" id="config">
                        <form class="form-horizontal" action="?page=finished" method="post">
                            <fieldset>
                                <!-- Text input-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">MYSQL Host</label>  
                                    <div class="col-md-4">
                                        <input id="textinput" name="host" type="text" placeholder="" class="form-control input-md">
                                        <span class="help-block">set your host (ex: localhost)</span>  
                                    </div>
                                </div>

                                <!-- Text input-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">MYSQL User</label>  
                                    <div class="col-md-4">
                                        <input id="textinput" name="user" type="text" placeholder="" class="form-control input-md">
                                        <span class="help-block">set your user (ex: root)</span>  
                                    </div>
                                </div>

                                <!-- Text input-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">MYSQL Database</label>  
                                    <div class="col-md-4">
                                        <input id="textinput" name="db" type="text" placeholder="" class="form-control input-md">
                                        <span class="help-block">set your database name</span>  
                                    </div>
                                </div>

                                <!-- Password input-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="passwordinput">MYSQL Password</label>
                                    <div class="col-md-4">
                                        <input id="passwordinput" name="pass_db" type="text" placeholder="" class="form-control input-md">
                                        <span class="help-block">set your password database</span>
                                    </div>
                                </div>

                                <!-- Text input-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">Site URL</label>  
                                    <div class="col-md-4">
                                        <input id="textinput" name="site_url" type="text" placeholder="" class="form-control input-md">
                                        <span class="help-block">set url of your website<br> (ex: http://localhost)</span>  
                                    </div>
                                </div>

                                <!-- Text input-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">Site Name</label>  
                                    <div class="col-md-4">
                                        <input id="textinput" name="site_name" type="text" placeholder="" class="form-control input-md">
                                        <span class="help-block">set name of your website<br> (ex: Twidley)</span>  
                                    </div>
                                </div>

                                <!-- Text input-->


                                <div class="form-group" style="text-align: center;">
                                    <p dir="auto" id="asfasfa">*The first account will be admin account*</p>
                                </div>

                                <!-- Button -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="singlebutton">Set Config</label>
                                    <div class="col-md-4">
                                        <button type="submit" onclick="So_SubmitInstall();" class="btn btn-primary">
                                            Install Config
                                        </button>
                                    </div>
                                </div>

                            </fieldset>
                        </form>

                    </div>
                <?php } ?>
                <?php
                if (isset($_GET['page']) && $_GET['page'] == 'finished') {
                    ?>
                    <div class="col-md-9 well admin-content" id="finished">
                        <p><strong>Twidley 1.0</strong></p>
                        <p>
                            Congratulations!<br>
                            Delete folder <strong>install/</strong>
                        </p>
                        <a href="../" class="btn btn-primary" >Let's Go!</a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <script>
            function So_SubmitInstall() {
                $('button').attr('disabled', true);
                $('button').text('Installing..');
                $('form').submit();
            }
        </script>
    </body>
</html>