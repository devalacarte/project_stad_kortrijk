<!DOCTYPE html>
<?php
/*
 * Simple timer example
 *
 */

// include error reporting
include_once '../includes/header.php';

// include functions
include_once '../includes/functions.php';
// create new instance of functions-class
$db = new DBFunctions;

// get parameters
$parameters = $db->getParameters();
?>
<html>

<head>
    <meta charset="utf-8">
    <title>Stemsite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/Main.css">
    <link rel ="stylesheet" type="text/css" href="../css/button.css">
    <link rel="stylesheet" type="text/css" href="../css/fonts.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/countdown.css">
</head>

<?php include '../includes/db_connect.php'; ?>

<body> <!--  volledige pagina: container-fluid -->
<div class="white-space" id="wrapper">
    <h1><img class="col-xs-12" style="margin: 0 auto; float: none; max-height: 200px; max-width: 532px" src="../images/logo.png"/></h1>
    <div class="contentFadeIn"></div>
    <div id="content" class="contentContainer">
        <div class="clear-float"></div>






        <div id="wettentopbottom" class="col-xs-12">

            <div id="countdown-small" class="visible-xs text-center">
                <span id="tmr-h1" class="mr-h1 countdown-small-number">0</span>
                <span id="tmr-h2" class="tmr-h2 countdown-small-number">0</span>
                <span class="countdown-small-separator">:</span>
                <span id="tmr-m1" class="tmr-m1 countdown-small-number">0</span>
                <span id="tmr-m2" class="tmr-m2 countdown-small-number">0</span>
                <span class="countdown-small-separator">:</span>
                <span id="tmr-s1" class="tmr-s1 countdown-small-number">0</span>
                <span id="tmr-s2" class="tmr-s2 countdown-small-number">0</span>
            </div>

            <div id="countdown-large" class="visible-md visible-lg text-center">
                <span id="tmr-h1" class="tmr-h1 countdown-large-number">0</span>
                <span id="tmr-h2" class="tmr-h2 countdown-large-number">0</span>
                <span class="countdown-large-separator">:</span>
                <span id="tmr-m1" class="tmr-m1 countdown-large-number">0</span>
                <span id="tmr-m2" class="tmr-m2 countdown-large-number">0</span>
                <span class="countdown-large-separator">:</span>
                <span id="tmr-s1" class="tmr-s1 countdown-large-number">0</span>
                <span id="tmr-s2" class="tmr-s2 countdown-large-number">0</span>
            </div>

            <div id="countdown-medium" class="visible-sm text-center">
                <span id="tmr-h1" class="tmr-h1 countdown-medium-number">0</span>
                <span id="tmr-h2" class="tmr-h2 countdown-medium-number">0</span>
                <span class="countdown-medium-separator">:</span>
                <span id="tmr-m1" class="tmr-m1 countdown-medium-number">0</span>
                <span id="tmr-m2" class="tmr-m2 countdown-medium-number">0</span>
                <span class="countdown-medium-separator">:</span>
                <span id="tmr-s1" class="tmr-s1 countdown-medium-number">0</span>
                <span id="tmr-s2" class="tmr-s2 countdown-medium-number">0</span>
            </div>



            <div class="hidden-xs" id="stempel"></div>

            <div class="clear-float"></div>

            <div id="wettenmidden">
                <?php
                //3 huidige wetsvoorstellen ophalen, zijn aangeduid met isActive
                $resultTop3 = $mysqli->query("SELECT * FROM propositions WHERE isActive=1;");
                $intRow = 0;
                while ($data = $resultTop3->fetch_assoc()) {
                    $intRow +=1;
                ?>

                    <div class="huidigeWet">
                        <div class="lblwetsnr"><span class="bullet bullet-left">&bullet;</span></span><span><?php echo $intRow; ?></span><span class="bullet bullet-right">&#8226;</span></span></div>
                        <div class="lblwetbeschrijving"><span"><?php echo stripslashes($data['description']);?></span></div>
                    </div>
                <?php } ?>
            </div>
        </div>


        <h2 class="hidden-xs col-lg-offset-1 text-center">Nieuwe Wetsvoorstellen</h2>
        <h2 class="visible-xs text-center">Nieuwe Wetsvoorstellen</h2>

        <div id="oplijsting">

            <?php function isGestemd($arg){
                if(isset($_COOKIE["WET"][$arg])){
                    return "disabled";
                }else{
                    return;
                }
            }?>

            <?php
            //alle wetsvoorstellen ophalen uit de database
            $result = $mysqli->query("SELECT * FROM propositions WHERE isActive !=1 AND isOld=0;");
            $intRow = 0;
            while ($data = $result->fetch_assoc()) {
                $intRow +=1;
                $desc = $data['description'];
                $id = $data['id'];
            ?>
                <!--
                xs: wetbeschrijving over de eerste rij = 12; tweede rij: offset 5 + button 2 +5 = 12
                sm md: alles 1 rij: wetbeschrijving 8 +offset1 + button 2 + 1span = 12
                lg : alles 1 rij: offset 1 + wetbeschrijving 6 +offset2 + button 1 = 12
                -->
                <div class="panel panel-default wetbeschrijving"> <!-- classes panel en panel-default -->
                    <div class=" visible-xs lblwetsnr col-xs-12 "><span>#<?php echo $intRow; ?></span></div>
                    <div class="hidden-xs lblwetsnr col-sm-offset-1 col-sm-1 "><span>#<?php echo $intRow; ?></span></div>

                    <div class="visible-xs lblwetbeschrijving col-xs-12"><span"><?php echo stripslashes($desc); ?></span></div>
                    <div class="hidden-xs lblwetbeschrijving center-vertical col-sm-7 col-lg-7"><span><?php echo stripslashes($desc); ?></span></div>

                    <button class="<?php echo isGestemd($id); ?> visible-xs btn btn-default btnVotes" type="button" value="<?php echo $id ?>" id="btn<?php echo $id; ?>"><span>Stem</span></button>
                    <button class="<?php echo isGestemd($id); ?>  hidden-xs center-vertical col-lg-2 btn btn-default btnVotes" type="button" value="<?php echo $id ?>" id="btn<?php echo $id ?>"><span>Stem</span></button>
                </div>


            <?php } //afsluiten while loop ?>

        </div>
        <!-- VERWIJDEREN VOOR PRODUCTIE: kijken hoe groot scherm is -->
        <div class="col-md-6 col-lg-3">
            <div class="visible-lg text-success">Large Devices!</div>
            <div class="visible-md text-warning">Medium Devices!</div>
            <div class="visible-sm text-danger"> Small Devices</div>
            <div class="visible-xs text-danger">Extra Small</div>
        </div>

    </div>

    <div class="contentFadeOut"></div>
    <div id="footer">
        <img src="../images/footer.png">
        <span>Copyright &#169; 2014 - Kortrijk Conge</span>
    </div>

    </div>

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/countdown.js"></script>
<script>
    var auto_refresh_countdown = setInterval(function (){
        $('#countdownscript').load('../ajax/reloadcountdown.php');
    }, 10 * 1000); // refresh every 10 seconds
</script>
<div id="countdownscript">
    <script>
        calcRemainingSeconds(<?php echo $parameters['currentLawStartTime']; ?>, <?php echo $parameters['lawDurationInMins']; ?>, <?php echo time(); ?>);
        setResetcount(<?php echo $parameters['resetCount']; ?>);
        startTimer();
    </script>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        //object van cookie array maken, bij iedere click voegen we de waarde bij zodat de pagina niet gerefreshed moet worden
        var objCookie = <?php if(isset($_COOKIE["WET"])){ echo json_encode($_COOKIE["WET"]);}else{echo " ";} ?>

        $('.btnVotes').click(function () {
            var btnVal = $(this).val(); //value = id van de stem

            //checken of de er al gestemd is geweest op de button en returnen
            if(objCookie[btnVal]==1 || objCookie[btnVal]=="1"){
                alert("cookie waarde gevonde");
                return;
            }

            // bij het klikken van de stemknop de waarde doorgeven aan php funtie en aantal votes voor het voorstel te updaten
            var ajaxurl = '../includes/database.php', data = {'selectedwet': btnVal};
            $.post(ajaxurl, data, function (response) {
                objCookie[btnVal] = 1;
                alert(response);
            });
            $(this).delay(100).queue(function () {
               //$(this).addClass("disabled");
               //$(this).attr("disabled", "disabled");
                $(this).dequeue();
            });
        });
    });
</script>
</body>
</html>