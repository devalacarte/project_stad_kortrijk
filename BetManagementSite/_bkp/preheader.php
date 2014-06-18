<?php
    

	/* you SHOULD edit the database details below; fill in your database info */

	#this is the info for your database connection
    ####################################################################################
    #
	$MYSQL_HOST = "mysqlstudent"; //NIET localhost! mysqlstudent
	$MYSQL_LOGIN = "thomasvandyeew3o"; //thomasvandyeew3o
	$MYSQL_PASS = "eaPhiu3gaexe"; //eaPhiu3gaexe
	$MYSQL_DB = "thomasvandyeew3o"; // propositionbetting thomasvandyeew3o
    ##
    ####################################################################################

	/********* THERE SHOULD BE LITTLE NEED TO EDIT BELOW THIS LINE *******/

	####################################################################################

	#a session variable is set by class for much of the CRUD functionality -- eg adding a row
    session_start();
    error_reporting(E_ALL - E_NOTICE);
    //error_reporting(E_ALL);

	$useMySQLi = true;
	if (!class_exists("mysqli")){
		$useMySQLi = false; //mysqli is not enabled on this server; fallback to using mysql
	}

	if ($useMySQLi){
		$mysqliConn = new mysqli($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_PASS, $MYSQL_DB);
		/* check connection */
		if (mysqli_connect_errno()) {
			//logError("Connect failed in getMysqli(): ", mysqli_connect_error());
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$mysqliConn->set_charset("utf8");
	}
	else{
		/*
		   use this connection if your hosting config does NOT support mysqli
		   this code was for mySQL connections; was replaced in v8.6 with mysqli
		*/

		$db = @mysql_connect($MYSQL_HOST,$MYSQL_LOGIN,$MYSQL_PASS);

		if(!$db){
			echo('Unable to authenticate user. <br />Error: <b>' . mysql_error() . "</b>");
			exit;
		}
		$connect = @mysql_select_db($MYSQL_DB);
		if (!$connect){
			echo('Unable to connect to db <br />Error: <b>' . mysql_error() . "</b>");
			exit;
		}
		mysql_query("SET NAMES 'utf8'");
		//mysql_query("SET character_set_results = 'utf8_general_ci', character_set_client = 'utf8_general_ci', character_set_connection = 'utf8_general_ci', character_set_database = 'utf8_general_ci', character_set_server = 'utf8_general_ci'", $db);
	}

    if(!function_exists('safeQuery'))
    {
        function safeQuery($sqlString, $parameters)
        {
            global $mysqliConn;

            $statement = $mysqliConn->prepare($sqlString);
            if(!$statement)
                trigger_error("Wrong SQL: " . $sqlString . "Error: " . $mysqliConn->errno . " " . $mysqliConn->error, E_USER_ERROR);

            if(count($parameters) > 0)
            {
                $parameterString = createParameterTypeString($parameters);
                //Bind parameters to statement
                $references = array(&$parameterString);
                for($i = 0; $i < count($parameters); $i++)
                {
                    $parameters[$i] = htmlentities($parameters[$i]);
                    $references[] = &$parameters[$i];
                }
            
                call_user_func_array(array($statement, 'bind_param'), $references);
            }

            $statement->execute();

            //If the statement was not select, return if the query succeeded
            if(stristr(substr($sqlString,0,8),"delete") || stristr(substr($sqlString,0,8),"update"))
            {
                return $statement->num_rows() > 0;
            }
            //If the statement was insert, return the inserted id
            else if (stristr(substr($sqlString,0,8),"insert"))
            {
                return $statement->insert_id;
            }

            // Get metadata for field names
            $meta = $statement->result_metadata();

            //Create variables for each column
            while ($field = $meta->fetch_field()) { 
                $var = $field->name; 
                $$var = null; 
                $fields[$var] = &$$var;
            }

            // Bind Results
            call_user_func_array(array($statement,'bind_result'),$fields);

            // Fetch Results
            $i = 0;
            $results = array();
            while ($statement->fetch()) {
                $results[$i] = array();
                foreach($fields as $k => $v)
                    $results[$i][$k] = $v;
                $i++;
            }

            $statement->close();
            return $results;
        }

        function createParameterTypeString($parameters)
        {
            if(!is_array($parameters))
            {
                trigger_error("Error in createParameterTypeString(): parameter is not an array.");
                return "";
            }
             
            $parameterTypeString = '';   
            for($i = 0; $i < count($parameters); $i++)
            {
                if(is_int($parameters[$i]))
                    $parameterTypeString .= 'i';           
                else if(is_string($parameters[$i]))
                    $parameterTypeString .= 's';
                else if(is_double($parameters[$i]))
                    $parameterTypeString .= 'd';
                else
                    $parameterTypeString .= 'b';
            }

            return $parameterTypeString;
        }
    }

    if(!function_exists('getClause'))
    {
        function getClause($fullClause, $command)
        {
            if(!is_string($fullClause))
                return "";

            $clause = trim($fullClause);
            if(strlen($clause) == 0)
                return "";

            return trim(substr($fullClause, strlen($command) + 1));
        }
    }

?>