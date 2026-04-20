<?php
/*
Model

This is the model component. This module reads in and processes the data given. 
Also provides helper functions to sort data in different ways 

*/

//Prevents direct access to the file
if (!defined('CourseSchedApp')) exit();

//Creates records array that will contain the data and reads the data into it
function createRec() {
	$records = array();
	$file = 'inventory.txt';
	$infile = fopen($file, "r");
	$line = fgets($infile);
	$records = readDB();
	return $records;
}

//Creates and returns a new sorted array containing all parts
function loadParts ($allRecords) {
	$parts = array();
	
	//iterates over the array containg all data and extracts parts
	for ($i=0; $i < count($allRecords); $i++) {
		$part = $allRecords[$i]["partType"];
		
		//Checks if part is already added and adds it if not
		$result = in_array($part, $parts);
		if ($result == False)
			$parts[] = $part;
	}
	sort($parts);
	return $parts;
}

//Creates and returns a new sorted array containing all brands
function loadBrands($allRecords) { 
	$parts = array();
	
	//iterates over the array containg all data and extracts brands
	for($i=0; $i < count($allRecords); $i++) { 
		$brand = $allRecords[$i]['brand'];
		
		//Checks if brand is already added and adds it if not
		$result = in_array($brand, $parts); 
		if($result == False) { 
			$parts[] = $brand;
		}
	}
	sort($parts);
	return $parts;
}
//filters though the data based on the part and brand from the form and returns
//a new array containing the desired parts.
function filter($allRecords) {

        $filterArray = array();
        $results = array();

        //If parts is not all
        if($_POST['partType'] != '0') {

                for($i = 0; $i < sizeof($allRecords); $i++){

                        //add all entries that have the same part as the one selected
                        if($_POST['partType'] == $allRecords[$i]['partType']){
                                $filterArray[] = $allRecords[$i];
                        }
                }
        }
        // otherwise add all parts
        else {
                $filterArray = $allRecords;
        }

        // If a brand was selected
        if($_POST['brand'] != "0") {

                for($i = 0; $i < sizeof($filterArray); $i++){

                        //add each part with the desired brand
                        if($_POST['brand'] == $filterArray[$i]['brand']) {
                                $results[] = $filterArray[$i];
                        }
                }
        }

        //If All brands are selected
        else {

                // Sort by logic, by price or part number
                if($_POST['sort'] == 1) {

                        usort($filterArray, "byPrice");
                        return $filterArray;
                }
                else {
                        usort($filterArray, "byPartNo");
                        return $filterArray;
                }
        }
        // Sort by price or part number
        if($_POST['sort'] == 1) {

                usort($results, "byPrice");
                return $results;
        }
        else {
                usort($results, "byPartNo");
                return $results;

        }

}

// sorts price from lowest to highest
function byPrice($a, $b) {
        //Retrieves the price for each part, remove dollar sign, and turn into a float
        $aNum = explode("$", $a["price"]);
        $bNum = explode("$", $b["price"]);
        $aNum = floatval($aNum[1]);
        $bNum = floatval($bNum[1]);

        //sort the parts
        if($aNum < $bNum) {
                return -1;
        }
        else if($aNum > $bNum) {
                return 1;
        }
        else {
                return 0;
        }
}

// sorts by part number alphanumerically
function byPartNo($a, $b) {

        //Retrieves the part number for each part
        $aNum = $a["partNum"];
        $bNum = $b["partNum"];

        if($aNum < $bNum) {
                return -1;
        }
        else if($aNum > $bNum) {
                return 1;
        }
        else {
                return 0;
        }
}

	


function verifyUser($uname, $pass) { //if user and pass are a part of database (sqlite)
	//Check username against database
	if ($pass == 'guest') {
		return True;
	}
	//Generates and adds salt, returns hashed password
	$location = "atown2.db";
	$query = "select * from users;";
	
	$handle = sqlite_open($location);
	$result = sqlite_query($handle, $query);
	$records = sqlite_fetch_array($result); 

	$hpass = $records[0][1];
	
	// check if hash was done correctly 
	if (password_verify($pass, $hpass)){ 
		return true;
	}
	else { 
		return false;
	}
}
	
// adds a part to the database
function inventoryInsert() {
	$location = "db/atown2.db";
	$part = $_POST['partTypetxt'];
	$brand = $_POST['brandtxt'];
	$partNum = $_POST['partNotxt'];
	$price = $_POST['pricetxt'];
	
	//creates and handles the query
	$query = "INSERT INTO inventory (partType, brand, partNum, price) VALUES('$part', '$brand', '$partNum', '$price');";
	$handle = sqlite_open($location);
	$handle->exec($query);
}


//removes a part from the database
function inventoryRemove() {
	$location = "db/atown2.db";
	$partNum = $_POST['partNotxt'];
	
	//creates and handles the query
	$query = "DELETE FROM inventory WHERE partNum=$partNum;";
	
	$handle = sqlite_open($location);
	$result = sqlite_query($handle, $query);
}

//Edits part information
function inventoryModify() {
	$location = "db/atown2.db";
	$partNum = $_POST['partNotxt'];
	$column = $_POST['column'];
	
	//switches colomun value to the actual string
	if ($column == 0) 
		$column = 'part';
	else if ($column == 1)
		$column = 'brand';
	else if ($column == 2)
		$column = 'partNum';
	else if ($column == 3)
		$column = 'price';
	
	$changeTo = $_POST['edittxt'];
	
	//creates and handles the query
	$query = "UPDATE inventory SET '$column'='$changeTo' WHERE partNum=$partNum;";
	$handle = sqlite_open($location);
	$result = sqlite_query($handle, $query);
	
}

//opens the database
function sqlite_open($location)
{
    $handle = new SQLite3($location);
    return $handle;
}

//handles the query
function sqlite_query($dbhandle,$query)
{
    $result = $dbhandle->query($query);
    return $result;
}

//fetches each row of the database
function sqlite_fetch_array(&$result)
{
   //Get Columns so names can be checked
   // $i = 0;
   /* while ($result->columnName($i))
    {
        $columns[ ] = $result->columnName($i);
        $i++;
    }
    */
    while ($row = $result->fetchArray()){
    	$records[] = $row;
    }
    return $records;
}

//Uses all functions to read and retrieve each part in the database
function readDB() {
	$location = "db/atown2.db";
	$query = "select * from inventory;";
	$handle = sqlite_open($location);
	$result = sqlite_query($handle, $query);
    $records = sqlite_fetch_array($result);
    return $records;
}
