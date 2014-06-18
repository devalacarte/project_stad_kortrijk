<?php
    include_once($_SERVER['DOCUMENT_ROOT']."thomas.van.den.ber1/includes/preheader.php");

    if(isset($_POST["function"]))
    {
    	if($_POST["function"] == "getPropsAndTotalRewards")
    	{
    		getCurrentPropositionsAndTotalRewards();
    	}
    }

    function getCurrentPropositionsAndTotalRewards()
    {
        $currentProps = getCurrentPropositions(); //get array with current props
        $totalAmountOfRewards = calcAmountOfRewards();

        $reply = array("rewardsAmount"=>$totalAmountOfRewards, "props"=>$currentProps);
        echo json_encode($reply);
    }

    function calcAmountOfRewards()
    {
       //Amount of rewards = payout percentage * total amount of bets
       $payoutPercentage = getPayoutPercentage();
       $totalBetCount = getAmountOfBets();

       return floor($payoutPercentage * $totalBetCount);
    }

    //Gets the current propositions and returns them as JSON
    function getCurrentPropositions()
    {
        //Get the current prop
        $getPropsSql = "SELECT * FROM propositions WHERE isOld = 0";
        $propsReply = safeQuery($getPropsSql);

        //Get the total amount of current bets
        $totalAmountOfBets = getAmountOfBets();

        //Calculate the total amount of rewards
        $payoutPercentage = getPayoutPercentage();
        $amountOfRewards = floor($payoutPercentage  * $totalAmountOfBets);

        //Construct the reply
        $reply = array();
        //Loop over every row
        foreach($propsReply as $row)
        {
            //Get the amount of bets for this proposition
            $amountOfBets = getAmountOfBets($row["id"]);
            //Calculate the chance to win (0 if there are no bets yet at all)
            $chanceToWin = 0;
            if($totalAmountOfBets != 0)
                $chanceToWin = $amountOfBets / $totalAmountOfBets;
            //Calculate the amount of winners ("cocktailkoers") on this proposition
            $amountOfWinners = floor($chanceToWin * $amountOfRewards);

            //Add the row to the reply
            $reply[] = array("id"=>$row["id"], "description"=>stripslashes($row["description"]), "amountOfWinners"=>$amountOfWinners);
        }

        return $reply;
    }

    function getAmountOfBets($propId = 0)
    {
        $amountOfBetsSql = "SELECT COUNT(b.id) FROM bets b JOIN propositions p ON b.propositionId = p.id WHERE p.isOld = 0";
        $amountOfBetsParams = array();

        //Only get the amount of bets for a specific proposition if so desired
        if($propId != 0)
        {
            $amountOfBetsSql .= " AND p.id = ?";
            $amountOfBetsParams[] = $propId;
        }

        $amountOfBetsReply = safeQuery($amountOfBetsSql, $amountOfBetsParams);
        $amountOfBetsRow = $amountOfBetsReply[0];
        $amountOfBets = $amountOfBetsRow["COUNT(b.id)"];
        
        return $amountOfBets;
    }

    //Used to calculate the amount of rewards
    function getPayoutPercentage()
    {
        $payoutPercentageSqlString = "SELECT payoutpercentage FROM parameters";
        $payoutReply = safeQuery($payoutPercentageSqlString);
        $payoutRow = $payoutReply[0];
        $payoutPercentage = $payoutRow['payoutpercentage'];

        return $payoutPercentage;
    }

?>
