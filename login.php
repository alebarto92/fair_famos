<!DOCTYPE html>
<html>
<head>
<?php include("config.php");?>
    <title>LOGIN :: <?php echo $projecttitle;?> </title>

    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!--Bootsrap 4 CDN-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!--Fontawesome CDN-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <!--Custom styles-->
    <link rel="stylesheet" type="text/css" href="css/styles.css">

    <link rel="icon" href="favicon.ico" type="image/png" />

</head>
<body>
<div class="container">
    <div class="d-flex justify-content-center h-100">
        <div class="card">
            <div class="card-header">
                <h3>Sign In</h3>
                <div id="responsologin"></div>
                <div class="d-flex justify-content-end social_icon">
                    <span><img style="width:240px;" src="logolabima.png"/></span>
                </div>
            </div>
            <div class="card-body">
                <form class="form-signin" id="loginform" enctype="application/x-www-form-urlencoded" name="loginform" action="javascript:loggati();">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="username" name="matricolalogin" id="matricolalogin">

                    </div>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" class="form-control" placeholder="password" name="passwordlogin" id="passwordlogin">
                    </div>
                    <!--<div class="row align-items-center remember">
                        <input type="checkbox">Remember Me
                    </div>-->
                    <div class="form-group">
                        <input type="submit" value="Login" class="btn float-right login_btn">
                    </div>
                </form>
            </div>
            <!--<div class="card-footer">
                <div class="d-flex justify-content-center links">
                    Don't have an account?<a href="#">Sign Up</a>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="#">Forgot your password?</a>
                </div>
            </div>-->
        </div>
    </div>
</div>
<script type="text/javascript">
    function loggati(){
        $.post("ajax_login.php", $("#loginform").serialize(), function(msg){$("#responsologin").html(msg);} );
    }
</script>
</body>
</html>
