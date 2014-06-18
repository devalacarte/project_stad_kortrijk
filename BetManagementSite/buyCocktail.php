<?php
    include_once('../includes/preheader.php');

    header('Content-Type: application/json');
    $_userId = $_POST["id"];

    getCocktailCl($_userId);

    function getCocktailCl($id)
    {
        $getUserSqlString = "SELECT userName, cocktailAmount FROM accounts WHERE id = ?";
        $getUserParams = array($id);
        $getUserReply = safeQuery($getUserSqlString, $getUserParams);
        $getUserRow = $getUserReply[0];

        $cocktailCl = $getUserRow["cocktailAmount"];
        $userName = $getUserRow["userName"];

        tryBuyCocktail($id, $userName, $cocktailCl);
    }

    function tryBuyCocktail($id, $userName, $cocktailAmount)
    {
        $cost = 1;
        //User does not have enough cl
        if($cocktailAmount < $cost)
        {
            echo json_encode(array("success" => 0, "message" => $userName . " kan geen cocktail krijgen."));
            return;
        }
        //User does have enough
        $cocktailAmount -= $cost;

        $updateClSqlString = "UPDATE accounts SET cocktailAmount = ? WHERE id = ?";
        $updateClParams = array($cocktailAmount, $id);
        $success = safeQuery($updateClSqlString, $updateClParams);

        $prefield = trim("accounts" . "cocktailAmount" . $id);
        
        if ($success)
		    echo json_encode(array("success" => 1, "message" => "accounts_row_$id|$cocktailAmount"));        	
	    else
	    	echo json_encode(array("success" => 0, "message" => "Error: De database kon niet worden aangepast."));
    }    		 
 ?>