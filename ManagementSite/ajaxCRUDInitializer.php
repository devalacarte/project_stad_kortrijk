<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL - E_NOTICE);

    require_once('../includes/preheader.php');
    include ('../includes/ajaxCRUD.class.php');

    $tblPropositions = NULL;
    $tblParams = NULL;

    initPropositionsTable();
    initParametersTable();

    function initPropositionsTable()
    {
        global $tblPropositions;

        $tblPropositions = new ajaxCRUD("wetsvoorstel", "propositions", "id");
        $tblPropositions->addText = "Nieuw";
        $tblPropositions->deleteText = "Verwijder";
        $tblPropositions->cancelText = "Annuleer";

        //$tblPropositions->omitPrimaryKey(); //Don't display primary key in the table
        $tblPropositions->displayAddFormTop();
        $tblPropositions->turnOffAjaxADD(); //Disable ajax adding to avoid incorrect drawing of filters

        $tblPropositions->displayAs("description", "Beschrijving");
        $tblPropositions->setTextareaHeight("description", 100);
        $tblPropositions->setTextboxWidth("description", 600);
        $tblPropositions->displayAs("votes", "Aantal stemmen");
        $tblPropositions->displayAs("timestamp", "Tijdstip");
        $tblPropositions->displayAs("isOld", "Huidig wetsvoorstel");

        $tblPropositions->displayAs("isActive", "Is actief");
        $tblPropositions->defineCheckbox("isActive");
        $tblPropositions->defineCheckbox("isOld", 0, 1); //invert isOld so you can filter to only show current propositions
        $tblPropositions ->addAjaxFilterBox("isOld",10,TRUE);
        $tblPropositions ->addAjaxFilterBox("isActive",10,TRUE);
    
        $tblPropositions->disallowEdit("timestamp");
        $tblPropositions->disallowEdit("id");

        $tblPropositions->omitAddField("timestamp");
        $tblPropositions->omitAddField("isActive");
        $tblPropositions->omitAddField("votes");
        $tblPropositions->omitAddField("isOld");

        $tblPropositions->addValueOnInsert("timestamp", time()); //Add timestamp
        $tblPropositions->addValueOnInsert("isActive", 0);
        $tblPropositions->addValueOnInsert("isOld", 0);
        $tblPropositions->addValueOnInsert("votes", 0);

        $tblPropositions->formatFieldWithFunction("timestamp", "formatDate");
    }

    function initParametersTable()
    {
        global $tblParams;

        $tblParams = new ajaxCRUD("parameter", "parameters", "id");
        $tblParams->disallowAdd();
        $tblParams->disallowDelete();
        $tblParams->omitPrimaryKey();
        
        $tblParams->displayAs("lawDurationInMins", "Duurtijd geldige wetten (minuten)");
        $tblParams->displayAs("payoutPercentage", "Globaal uitkeringspercentage");
        $tblParams->displayAs("currentBetCreditsPercentage", "Huidig percentage voor gokbeurten");
        $tblParams->displayAs("nextBetCreditsPercentage", "Volgend percentage voor gokbeurten");

        $tblParams->omitField("currentLawStartTime");
        $tblParams->omitField("resetCount");

        $tblParams->disallowEdit("currentBetCreditsPercentage");
    }

    function formatDate($value)
    {
        return date("H:i:s", $value);
    }

    /*function formatIsOld($value)
    {
        if($value == 0)
            return "Ja";
        
        return "Nee";
    }*/
?>
