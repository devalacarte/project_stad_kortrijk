<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/button.css">
    <link rel="stylesheet" type="text/css" href="../css/fonts.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="../Stem/css/Main.css">
</head>

<body class="col-xs-12">
<div class="white-space" id="wrapper">
    <?php include_once '../includes/secure_session.php'; ?>
    <div id="content" class="center-horizontal">
        <form class="form-horizontal" method="post" action="" autocomplete="off">
        <!-- USER -->
            <h1 id="boer"><img class="col-xs-10 col-sm-5 col-md-3 center-horizontal" style="max-height: 236px; max-width: 448px" src="images/boer.png"/></h1>

            <div class="form-group" id="user">
                <div class="col-xs-10 col-sm-5 col-md-3 center-horizontal">
                    <input autocomplete="off" name="inputUser" type="text" class="form-control" id="inputUser" placeholder="Gebruikersnaam">
                </div>
            </div>

            <!-- PASS -->
            <div class="form-group" id="password">
                <div class="col-xs-10 col-sm-5 col-md-3 center-horizontal">
                    <input autocomplete="off" name="inputPassword" type="password" class="form-control" id="inputPassword" placeholder="PIN">
                </div>
            </div>

            <!-- CONFIRM -->
            <div class="form-group" id="confirm">
                <div id="requirements" class="col-xs-10 col-sm-5 col-md-3 center-horizontal"><span>Kies een vier-cijferig wachtwoord</span></div>
                <div class="col-xs-10 col-sm-5 col-md-3 center-horizontal">
                    <input autocomplete="off" name="inputConfirm" type="password" class="form-control" id="inputConfirm" placeholder="Bevestig PIN">
                </div>
            </div>

            <div class="form-group" id="btnsubmit ">
                <div class="center-horizontal text-center">
                    <button name="submit" type="text" class="btn btn-default btnVotes" id="submit">Login</button>
                </div>
            </div>

        </form>
        <!-- SUBMIT -->

        <!--
    <h1><img class="col-xs-12" style="margin: 0 auto; float: none; max-height: 200px; max-width: 532px" src="images/stempagina/logo.png"/></h1>
-->

    </div>

    <h1 id="cocklogin"><img class="col-xs-12" style="margin: 0 auto; float: none; max-height: 200px; max-width: 532px" src="../images/logo.png"/></h1>


    <!--
    <div id="footer">
        <img src="images/stempagina/footer.png">
        <span>Copyright &#169; 2014 - Kortrijk Conge</span>
    </div>
    -->
</div>




<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/sha512.js"></script>

<script type="text/javascript">
    
        function randomIntFromInterval(min, max) {
            return Math.floor(Math.random() * (max - min + 1) + min);
        }

    function formhash(form, password) {
        var p = document.createElement("input");

        // nieuw element voor ons hashed paswoord op te slaan en door te sturen naar php
        form.appendChild(p);
        p.name = "p";
        p.type = "hidden";
        p.value = hex_sha512(password.value);

        // plaintext wachtwoord niet zenden
        password.value = "";
        //form.submit();
    }
</script>

<script type="text/javascript">
    var nieuwPass = 0; //boolean om te checken of er een nieuw wachtwoord moet worden ingevoerd 1 = nieuw wachtwoord invullen
    //$('form').attr('action', 'someNewUrl.php');


    $(document).ready(function () {
        $("#submit").attr("disabled", "disabled");
        $("input:text").val("");
        $("input:password").val("");

        $('#inputUser').focusout(function () {
            var input = $(this).val();

            //ajaxgebruiken voor php script aan te spreken om data op te halen
            var ajaxurl = '../includes/database.php', data = {'postUser': input};
            /*Wat te doen met het resultaat = response
             0 = passwoord bestaat niet, user wel -> paswoord invoeren en user inloggen
             1 = paswoord bestaat -> user inloggen
             2 = user bestaat niet -> foutmelding
             */

            $.post(ajaxurl, data, function (response) {
                if (response == 0) {
                    $('#confirm').show();
                    nieuwPass = response;
                    $('#submit').enable();
                    //nog geen passwoord voor gebruiker
                } else if (response == 1) {
                    $('#confirm').hide();
                    nieuwPass = response;
                    //password bestaat voor gebruiker
                } else if (response == 2) {
                    $('#confirm').hide();
                    nieuwPass = response;
                    //gebruiker bestaat niet
                }
            });
        });


        $('#inputPassword').keyup(function () {
            var input = $(this).val();
            var regTest = new RegExp(/^\d{4}$/);
            var confirmPassword = $("#inputConfirm").val();
            if (nieuwPass == 1) {
                if (regTest.test(input) == true) {
                    $("#submit").removeAttr("disabled");
                }
                else {
                    $("#submit").attr("disabled", "disabled");
                }
            }else if(nieuwPass == 0) {
                if (regTest.test(input) == true){
                    $("#inputPassword").addClass("icon");
                }else {
                    $("#inputPassword").removeClass("icon");
                }

                if(confirmPassword == null || confirmPassword == "")
                {
                    return;
                }

                if (regTest.test(input) == true  && confirmPassword == input) {
                    $("#submit").removeAttr("disabled");
                    $("#inputConfirm").addClass("icon");
                }
                else {
                    $("#submit").attr("disabled", "disabled");
                    $("#inputPassword").removeClass("icon");
                    $("#inputConfirm").removeClass("icon");
                }
            }else if(nieuwPass == 2) {
                $("#submit").attr("disabled", "disabled");
                $("#inputPassword").removeClass("icon");
                $("#inputConfirm").removeClass("icon");
            }

        });


        $('#inputConfirm').keyup(function () {
            var input = $(this).val();
            var password = $("#inputPassword").val();
            var regTest = new RegExp(/^\d{4}$/);
            if (regTest.test(input) == true && password == input)
            {
                $("#submit").removeAttr("disabled");
                $("#inputConfirm").addClass("icon");
            }
            else {
                $("#submit").attr("disabled", "disabled");
                $("#inputConfirm").removeClass("icon");
            }
        });


        $('#submit').click(function () {

            if (nieuwPass == 0) { //nieuw passwoord in db steken + inloggen
                $('form').attr('action', '../includes/registerHandler.php');
                formhash(this.form, this.form.inputPassword);

            } else if (nieuwPass == 1) { //inloggen
                $('form').attr('action', '../includes/process_login.php');
                formhash(this.form, this.form.inputPassword);
            }
        });
    });

</script>
</body>


</html>