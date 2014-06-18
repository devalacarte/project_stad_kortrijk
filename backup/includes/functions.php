<?php
/*
 * DBFunctions-class
 * All database manipulations happen in here
 *
 */

class DBFunctions {
	// var for database connection
	private $conn;

	function __construct() {
		/* Constructor: code executed when creating a new instance of DBFunctions */

		// create db connection (host, user, pass, db, port)
		$this->conn = new mysqli('127.0.0.1', 'root', 'luna1991', 'thomasvandyeew3o', 3306);
	}

	function __destruct() {
		/* Destructor: code executed when an instance has no more references */

		// close db connection
		$this->conn->close();
	}

	function getParameters() {
		/* Gets parameters from db */

		// execute query
		$parameters = $this->conn->query(
			'SELECT *
			FROM parameters'
		);
		// return the first row in an array
		return $parameters->fetch_array();
	}

	function getCurrentLaws() {
		/* Gets the current laws from db */

		// execute query
		// -> filter on active laws
		// -> order by amount of votes
		$laws = $this->conn->query(
			'SELECT *
			FROM propositions
			WHERE isActive = 1
			ORDER BY votes DESC'
		);
		// return current laws
		return $laws;
	}

	function getCurrentPropositions() {
		/* Gets the current propositions from db */

		// execute query
		// -> filter on new propositions
		$propositions = $this->conn->query(
			'SELECT *
			FROM propositions
			WHERE isOld = 0'
		);
		// return current propositions
		return $propositions;
	}

	function getUsers() {
		/* Gets all users from db */

		// execute query
		$users = $this->conn->query(
			'SELECT id, userName
			FROM accounts'
		);
		// return users
		return $users;
	}

	function getUserInfo($userId) {
		/* Get info from specific user */

		$user = array(); // returntype = array

		// prepare the query
		// -> filter on userid
		$prep = $this->conn->prepare(
			'SELECT id, userName, cocktailAmount
			FROM accounts
			WHERE id = ?'
		);
		// bind parameters (userid)
		$prep->bind_param('i', $userId);
		// execute prepared query
		$prep->execute();
		// bind result (userid, username, cocktailCount)
		$prep->bind_result($user['id'], $user['name'], $user['cocktails']);
		// fetch first row
		$prep->fetch();
		// close prepared statement
		$prep->close();

		// return array with userinfo
		return $user;
	}

	function getPlayerCurrentBets($userId) {
		/* Gets the bets a player has placed on current propositions */

		// prepare query
		$prep = $this->conn->prepare(
			'SELECT propositions.id, propositions.description, bets.position
			FROM bets
			INNER JOIN propositions
				ON bets.propositionId = propositions.id
			WHERE bets.userId = ?
				AND propositions.isOld = 0'
		);
		// bind parameters (userid)
		$prep->bind_param('i', $userId);
		// execute prepared query
		$prep->execute();
		// bind result of prepared query to an array
		$result = $this->bind_result_array($prep);

		// result will be an array
		$propositions = array();
		// cycle all returned rows
		while($prep->fetch()) {
			// array with results
			$propositions[$result['id']] = $this->getCopy($result);
		}

		// close prepared statement
		$prep->close();

		// return the result
		return $propositions;
	}

	function getPlayerCurrentBetCount($userId) {
		/* Gets the amount of bets a user has placed on current propositions */

		// prepare query
		$prep = $this->conn->prepare(
			'SELECT COUNT(*)
			FROM bets
			INNER JOIN propositions
				ON bets.propositionId = propositions.id
			WHERE propositions.isOld = 0
				AND bets.userId = ?'
		);
		// bind parameters (userid)
		$prep->bind_param('i', $userId);
		// execute prepared query
		$prep->execute();
		// bind result (betcount)
		$prep->bind_result($betCount);
		// fetch first row
		$prep->fetch();
		// close prepared statement
		$prep->close();

		// return amount of bets
		return $betCount;
	}

	function getPositionWhereUsersGetCokatil($propositionId) {
		/* Calculates the position on a proposition where a users will get a cocktail (cocktailrate / 'cocktailkoers') */

		// prepare query
		$prep = $this->conn->prepare(
			'SELECT FLOOR( /* level down the result */
				/* chance to win a cocktail */
				(
					/* amount of gamblers on proposition */
					(SELECT COUNT(id) FROM bets WHERE propositionId = ?)
					/
					/* amount of gamblers */
					(SELECT COUNT(id) FROM bets)
				)
				*
				/* total amount of cocktails to give away */
				(
					/* amount of gamblers */
					(SELECT COUNT(id) FROM bets)
					*
					/* payout percentage */
					(SELECT payoutPercentage FROM parameters WHERE id = 1)
				)
			)'
		);
		// bind parameters (propositionid)
		$prep->bind_param('i', $propositionId);
		// execute prepared query
		$prep->execute();
		// bind result (cocktailrate)
		$prep->bind_result($cocktailRate);
		// fetch first row
		$prep->fetch();
		// close prepared query
		$prep->close();

		// return the cocktailrate
		return $cocktailRate;
	}

	function getMaxBets() {
		/*
		 * Gets the maximum amount of bets a user can place in this round
		 * -> amount of propositions * percentage
		 */

		// execute query
		$query = $this->conn->query(
			'SELECT ROUND( /* round the result */
				/* amount of propositions */
				(
					SELECT COUNT(*)
					FROM propositions
					WHERE isOld = 0
				)
				*
				/* percentage of bets to be created */
				(
					SELECT currentBetCreditsPercentage
					FROM parameters
					WHERE id = 1
				)
			)
			/* give an alias */
			AS maxBets'
		);

		// fetch first row into an array
		$result = $query->fetch_array();

		// return the amount of maximum bets
		return $result['maxBets'];
	}

	function setCurrentLawsInactive() {
		/* Marks active laws as inactive */

		// execute query
		// -> sets isActive = 0 on all propositions where isActive = 1
		$this->conn->query(
			'UPDATE propositions
			SET isActive = 0
			WHERE isActive = 1'
		);
	}

	function setAllPropositionsOld() {
		/* Marks all propositions as old */

		// execute query
		// -> sets isOld = 1 on all propositions
		$this->conn->query(
			'UPDATE propositions
			SET isOld = 1'
		);
	}

	function playerHasEnoughBetCredits($userId, $betCost) {
		/* Checks if a player has enough 'betcredits' left */

		// calculate the creditamount: (max bets a user can place - bets the user already placed) - betcost
		$credits = ($this->getMaxBets() - $this->getPlayerCurrentBetCount($userId)) - $betCost;
		// check if the user has enough credits to place the bet
		if ($credits < 0) {
			// not enough credits - return false
			return false;
		}
		// enough credits - return true
		return true;
	}

	function playerHasBetOnProposition($userId, $propositionId) {
		/* Checks if a player has bet on a proposition */

		// prepare query
		$prep = $this->conn->prepare(
			'SELECT id
			FROM bets
			WHERE userId = ?
			AND propositionId = ?'
		);
		// bind parameters (userid, propositionid)
		$prep->bind_param('ii', $userId, $propositionId);
		// execute prepared query
		$prep->execute();
		// bind result (betid)
		$prep->bind_result($id);
		// fetch first row
		$prep->fetch();
		// close prepared statement
		$prep->close();

		// check if the return of the query was empty
		if (empty($id)) {
			// player has not bet yet on this proposition - return false
			return false;
		}
		// player has bet on this proposition - return true
		return true;
	}

	function bet($userId, $ids) {
		/* Code executed when placing a bet */

		// cost of bets = amount of bets placed
		$betCost = count($ids);

		// check if player has enough credits
		if (!$this->playerHasEnoughBetCredits($userId, $betCost)) {
			// return with error message
			return 'Niet genoeg credits om gokken te registreren!';
		}

		// cycle through bets
		foreach ($ids as $id) {
			// prepare query
			// -> gets amount of bets placed on a proposition
			$prep = $this->conn->prepare(
				'SELECT COUNT(*)
				FROM bets
				WHERE propositionId = ?'
			);
			// bind parameters (propositionid)
			$prep->bind_param('i', $id);
			// execute prepared query
			$prep->execute();
			// bind result (current amount of bets)
			$prep->bind_result($position);
			// fetch first row of result
			$prep->fetch();
			// close prepared statement
			$prep->close();

			// calculate the current position of the bet (amount of bets on proposition + 1)
			$position++;

			// prepare insert query
			// -> insert a new bet
			$prep = $this->conn->prepare(
				'INSERT INTO bets (userId, propositionId, position)
				VALUES(?, ?, ?)'
			);
			// bind parameters (userid, propositionid, position)
			$prep->bind_param('iii', $userId, $id, $position);
			// execute prepared statement
			$prep->execute();
			// close prepared query
			$prep->close();
		}

		// return true if everything went ok :)
		return true;
	}

	function startNewCycle($newResetCount) {
		/* Code executed when a new round starts */

		// execute query
		// -> get the previous law start time & law duration
		$query = $this->conn->query(
			'SELECT currentLawStartTime, lawDurationInMins
			FROM parameters'
		);
		// fetch result
		$result = $query->fetch_array();

		// set the next law start time equal to the previous start time
		$nextLawStartTime = $result['currentLawStartTime'];
		// while the next law start time is less than the current time
		while ($nextLawStartTime < (time() - ($result['lawDurationInMins'] * 60))) {
			// add the amount of law duration minutes in seconds to the next law start time
			$nextLawStartTime += ($result['lawDurationInMins'] * 60);
		}

		// prepare update query
		$prep = $this->conn->prepare(
			'UPDATE parameters
			SET
				currentLawStartTime = ?,
				resetCount = ?,
				currentBetCreditsPercentage = nextBetCreditsPercentage
			WHERE id = 1'
		);
		// bind parameters (next law start time, new resetcount)
		$prep->bind_param('ii', $nextLawStartTime, $newResetCount);
		// execute prepared query
		$prep->execute();
		// close prepared statement
		$prep->close();

		// mark all propositions as old
		$this->setAllPropositionsOld();
		// mark current laws inactive
		$this->setCurrentLawsInactive();
	}

	function test() {
		// execute query
		// -> get the previous law start time & law duration
		$query = $this->conn->query(
			'SELECT currentLawStartTime, lawDurationInMins
			FROM parameters'
		);
		// fetch result
		$result = $query->fetch_array();

		print_r($result['currentLawStartTime'] - time());
	}

	function payout() {
		/* Pay the winning players! Returns the amount of winners */

		// prepare update query
		// it's really quite complicated :o
		// lots of workarounds and stuff
		$this->conn->query(
			'UPDATE accounts
			/* join my awesome query that selects the users and the amount of cocktails they get */
			INNER JOIN
			(
				/* select the userid and the cocktailcount */
				SELECT a.id AS userId, COUNT(a.id) AS cocktailCount
				/* from accounts */
				FROM accounts a
				/* join table bets */
				INNER JOIN bets b
					ON a.id = b.userId
				/* join table propositions */
				INNER JOIN propositions c
					ON b.propositionId = c.id
				/* on active laws */
				WHERE c.isActive = 1
					/* where the users position is less or equal to the cocktailrate */
					AND b.position <= FLOOR(
						/* chance to win a cocktail */
						(
							/* amount of gamblers on proposition */
							(SELECT COUNT(id) FROM bets WHERE propositionId = c.id)
							/
							/* amount of gamblers */
							(SELECT COUNT(id) FROM bets)
						)
						*
						/* total amount of cocktails to give away */
						(
							/* amount of gamblers */
							(SELECT COUNT(id) FROM bets)
							*
							/* payout percentage */
							(SELECT payoutPercentage FROM parameters WHERE id = 1)
						)
					)
				/* group by userid (for the count :D) */
				GROUP BY a.id
			) AS cocktails /* give it an alias */
				/* join where the userIds match */
				ON accounts.id = cocktails.userId
			/* set the new cocktailamount */
			SET accounts.cocktailAmount = accounts.cocktailAmount + cocktails.cocktailCount'
		);


		// return number of affected queries
		return $this->conn->affected_rows;
	}

	function tombola($countWinners) {
		/*
		 * Gives random cocktails to people that have placed a good bet but didn't get paid out because they where too late to place it.
		 * Returns the amount of cocktails that were given.
		 *
		 */

		// check if the amount of winners given is a number
		if (!is_numeric($countWinners)) {
			// return errormsg
			return 'Het aantal winnaars moet een getal zijn.';
		}
		// check if the amount of winners given is a positive number
		if ($countWinners <= 0) {
			// return errormsg
			return 'Het aantal winnaars moet hoger zijn dan 0.';
		}

		// prepare select query
		// -> selects userids of users who will receive a cocktail in a random order
		// it's quite complicated :o
		$prep = $this->conn->prepare(
			'SELECT a.id FROM accounts a
			/* join bets table */
			INNER JOIN bets b
				ON a.id = b.userId
			/* join propositions table */
			INNER JOIN propositions c
				ON b.propositionId = c.id
			/* on active laws */
			WHERE c.isActive = 1
				/* where the position is larger than the cocktailrate */
				AND position > CEILING(
					/* chance to win a cocktail */
					(
						/* amount of gamblers on proposition */
						(SELECT COUNT(id) FROM bets WHERE propositionId = c.id)
						/
						/* amount of gamblers */
						(SELECT COUNT(id) FROM bets)
					)
					*
					/* total amount of cocktails to give away */
					(
						/* amount of gamblers */
						(SELECT COUNT(id) FROM bets)
						*
						/* payout percentage */
						(SELECT payoutPercentage FROM parameters WHERE id = 1)
					)
				)
			/* order by random position */
			ORDER BY RAND()
			/* return x rows (specified in function params) */
			LIMIT ?'
		);
		// bind parameters (amount of winners)
		$prep->bind_param('i', $countWinners);
		// execute prepated query
		$prep->execute();
		//bind result (userid)
		$prep->bind_result($userId);
		
		// winnerids is an array
		$winnerIds = array();
		// questionmarks is an array
		$questionmarks = array();
		// cycle all rows
		while ($prep->fetch()) {
			// foreach row:
			// add a row to the array winnerids with the userid
			$winnerIds[] = $userId;
		}
		// close prepared query
		$prep->close();
		
		// check if anyone can win the tombola
		if (empty($winnerIds)) {
			// return errormsg - no persons can win any cocktails
			return 'Er zijn geen personen die in aanmerking komen om mee te doen met de tombola.';
		}

		// query parameters is an array
		$arrParams = array();
		// cycle all winnerIds
		foreach($winnerIds as $key => $value) {
			// add a parameter to parameterarray with the winnerid (byref)
			$arrParams[] = &$winnerIds[$key];
		}

		// get the amount of winners
		$countParams = count($arrParams);

		// all parameters for bind_param-function are ints; repeat for as many parameters there are
		$int = str_repeat('i', $countParams);
		// prepend $int to the parameterarray
		array_unshift($arrParams, $int); 

		// fill an array with questionmarks (same array count as amount of parameters)
		$q = array_fill(0, $countParams, '?');
		// implode the array to a string eg: ?,?,?,?
		$params = implode(',', $q);

		// prepare update query
		$prep = $this->conn->prepare(
			// add the questionmarks to the query
			sprintf(
				'UPDATE accounts
				SET cocktailAmount = cocktailAmount + 1
				WHERE id IN (%s)'
				, $params)
		);
		// call bind_param with the array of parameters
		call_user_func_array(array($prep, 'bind_param'), $arrParams);
		// execute prepared query
		$prep->execute();
		// close prepared query
		$prep->close();

		// count the amount of winnerids
		$affected_rows = count($winnerIds);
		// return amount of winners
		return $affected_rows;
	}






	/*
	 * Utility function to automatically bind columns from selects in prepared statements to
	 * an array
	 */
	function bind_result_array($stmt)
	{
	    $meta = $stmt->result_metadata();
	    $result = array();
	    while ($field = $meta->fetch_field())
	    {
	        $result[$field->name] = NULL;
	        $params[] = &$result[$field->name];
	    }
	 
	    call_user_func_array(array($stmt, 'bind_result'), $params);
	    return $result;
	}
	 
	/**
	 * Returns a copy of an array of references
	 */
	function getCopy($row)
	{
	    return array_map(create_function('$a', 'return $a;'), $row);
	}
}


?>