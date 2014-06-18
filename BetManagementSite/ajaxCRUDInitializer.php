<?php
	include_once '../includes/settings.php';
    include_once '../includes/db_connect.php';
    include_once('../includes/preheader.php');
    include_once('../includes/ajaxCRUD.class.php');

    ini_set('display_errors', 'On');
     error_reporting(E_ALL - E_NOTICE);
    //error_reporting(E_ALL);

    $tblAccounts = new ajaxCRUD("gokker", "accounts", "id", "");
    $tblAccounts->addText = "Nieuwe";
    $tblAccounts->deleteText = "Verwijder";
    $tblAccounts->cancelText = "Annuleer";

    $tblAccounts->omitPrimaryKey(); //Don't display primary key in the table
    $tblAccounts->omitField("salt1");
    $tblAccounts->omitField("salt2");
    $tblAccounts->displayAddFormTop();

    $tblAccounts->displayAs("userName", "Gebruikersnaam");
    $tblAccounts->displayAs("cocktailAmount", "Aantal cocktails");

    $tblAccounts->disallowEdit("cocktailAmount");
    $tblAccounts->disallowEdit("userName");

    $tblAccounts->omitFieldCompletely("password");
    $tblAccounts->omitAddField("cocktailAmount");
    $tblAccounts->omitAddField("salt1");
    $tblAccounts->omitAddField("salt2");

    $tblAccounts->addValueOnInsert("password", "");

    //Add a button to subtract the cl amount of a cocktail
    $tblAccounts->addButtonToRow("Koop cocktail", "", "", "buyCocktail");

    $tblAccounts->onAddExecuteCallBackFunction("afterAddCallback"); //Check for duplicate entries

    $tblAccounts ->addAjaxFilterBox("userName");

    

    function formatDate($value)
    {
        return date("H:i:s", $value);
    }

    //Used to enforce unique user names.
    function afterAddCallback($values)
    {
        $userName = $values['userName']; 
        $userNameSqlString = "SELECT id FROM accounts WHERE userName = ?";
        $userNameParams = array($userName);
        $results = safeQuery($userNameSqlString, $userNameParams);
        if(count($results) <= 1) //This was the first username of its kind
            return;

        $lastEntryIdRow = $results[count($results) - 1];
        $lastEntryId = $lastEntryIdRow['id'];
        $deleteQueryString = "DELETE FROM accounts WHERE userName = ? AND id = ?";
        $deleteParams = array($userName, $lastEntryId);
        safeQuery($deleteQueryString, $deleteParams);
        echo "<script>alert('$userName wordt reeds als naam gebruikt. Kies een andere!')</script>";
    }

?>

<script>
    function handleCocktailReply(response)
    {
        var params = "";
        try
        {
            params = JSON.parse(response);
        }
        catch (ex)
        {
            //Not json: error message
            console.log(ex.message);
        }

        if (params == "")
            return;

        //User has too little cl
        if (params["success"] != 1)
        {
            alert(params["message"]);
            return;
        }

        var messageParams = params["message"].split("|");

        //Update table
        var colIndex = 2; //const value to represent what column the value is in

        //Get the table row
        $('#' + messageParams[0] + ' td:nth-child(' + colIndex + ')').text(messageParams[1]);
    }

    function buyCocktail(id)
    {
        if (confirm("Ben je zeker dat je een cocktail wil kopen voor deze persoon?"))
        {
            $.ajax({
                type: "post",
                url: "buyCocktail.php/",
                data: "id=" + id,
                dataType: "text",
                success: function (response)
                {
                    handleCocktailReply(response);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log("failed to send request: " + errorThrown);
                }
            });
        }
    }


</script>
