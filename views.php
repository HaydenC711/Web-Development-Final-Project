<?php
/*
View

This module contains the functions that buld each view of the web application.
These view functions build the form, table, page system, and homepage.
*/

//Prevents direct access to module and includes model functions
if (!defined('CourseSchedApp')) exit();
include 'model.php';


//Builds the course schedule request form and returns it after construction
function buildForm() {
	
	//creates needed arrays and loads in the data into records
	$output = array();
	$records = array();
	$parts = array();
	$brands = array();
	$records = createRec();
	
	//form header
	$header = "A-Town Repairs Part Inventory";
	$output[] = "<h3>$header</h3>";
	
	//form creation
	$output[] = "<form method='post' action='index.php?action=table'>\n";
	$output[] = "<table id='form'>\n";
	$output[] = "<tr>\n";

	//brand options
	$output[] = "<td class='heading'>Brand:</td>\n";
	$output[] = "<td class='body'><select name='brand'>\n";
	$output[] = "<option value='0'>All</option>\n";
	
	$brands = loadBrands($records);

	for ($i=0; $i < count($brands); $i++) {
		$temp = $brands[$i];
		$output[] = "<option>$temp</option>\n";
	}

	$output[] = "</select>\n";
	$output[] = "</td>\n";
	$output[] = "</tr>\n";
	
	//Part options
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Part:</td>\n";
	$output[] = "<td class='body'><select name='partType'>\n";
	$output[] = "<option value='0'>All</option>\n";

	//Inserts part options into form

	$parts = loadParts($records);

	for ($i=0; $i < count($parts); $i++) {
		$temp2 = $parts[$i];
		$output[] = "<option>$temp2</option>\n";
	}

	$output[] = "</select>\n";
	$output[] = "</td>\n";
	$output[] = "</tr>\n";
	
	//Inserts filter radio buttons
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Sort By:</td>\n";
	$output[] = "<td class='body'><input type='radio' name='sort' value='1' checked>Price";
	$output[] = "<input type='radio' name='sort' value='2'>Part Number</td>";
	$output[] = "</tr>\n";
	
	//Inserts page number 
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Part Per Page:</td>";
	$output[] = "<td class='body'><input type='text' name='limit' placeholder='Enter part limit'></td>";
	$output[] = "</tr>\n";
	
	//Insers the page hidden field and gives it the default value of 1
	$output[] = "<input type='hidden' name='page' value='1'/>\n";

	//Inserts submit button
	$output[] = "<tr>\n";
	$output[] = "<td class='submit' colspan='2'><input type='submit' value='Search'/></td>";
	$output[] = "</tr>\n";

	$output[] = "</table>\n";
	$output[] = "</form>\n";
	
	if ($_SESSION['uname'] == 'admin') {
		$output[] = "<a class='navButton' href='index.php?action=modify'>Modify Inventory</a>";
	}
	
	return $output;
	
	
}

// Gets filtered data and builds the table based on requests made. 
function buildTable($results) {
	
	//Table headings
	$table[] = "<table id='table'>"; 
	$table[] = "<tr> <th>Brand</th> <th>Part</th> <th>Part Number</th> <th>Price</th> </tr>\n";
	
	//iterates over filtered data and inserts it into the table
	for($i = 0; $i < sizeof($results); $i++) { 
		
		//table data variable creation
		$brand = $results[$i]['brand'];
		$part = $results[$i]['partType'];
		$partNum = $results[$i]['partNum'];
		$price = $results[$i]['price'];
			
		//adds class to the table
		$table[] = "<tr class = filterData> <td>$brand</td> <td>$part</td> <td>$partNum</td>
							<td>$price</td> </tr>";
	}
						
		$table[] = "</table>\n";
		
		//creates search again button
		$table[] = "<form class='button2' method='post' action='index.php?action=inventory'>\n";
		$table[] = "<input type = 'submit' value = 'Search Again'>\n";
		$table[] = "</form>\n";
	return $table; 
}

//creates the paging system using a given limit, page number, and array of filtered courses.
function courseLimit($limit, $page, $results) {
	
	//creates lastpage boolean, array of courses displayed on each page, and course counter
	$lastPage = false;
	$limitArray = array();
	$counter = $limit * $page;
	
	
	//If limit is bigger than number of courses to display
	if ($limit > count($results)) {
		//display all courses
		for ($j=0;$j<count($results); $j++) {
			$limitArray[] = $results[$j];
		}
	}
	//If counter is greater than limit, reduce it by limit and display 
	//the remainder of courses
	else if($counter >= count($results)) {   
		$counter = $counter-$limit;
		for($i = $counter; $i < count($results); $i++) 
			$limitArray[] = $results[$i];
		$lastPage = true;
		
	}
	//If not last page, display data
	else if ($counter < count($results)){ 
		for($x = $counter-$limit; $x < $counter; $x++) {
			$limitArray[] = $results[$x];
		}
	}
	//if last page, display data and change boolean value
	else  {
		for($i = $counter; $i < count($results); $i++) {
			$limitArray[] = $results[$i];
		$lastPage = true;
		}
	}
	
	//print the maps and paging buttons
	$table = array();
	$table = buildTable($limitArray);
	$table[] = "<form method='post' action='index.php?action=table'>\n";
	
	//If first page only display next button
	if ($page == 1) 
		$table[] = "<button type='submit' class='next1' name='pageB' value='Next'>Next</button>\n";
	//If last page only display previous button
	else if ($lastPage) {
		$table[] = "<button type='submit' class='prev' name='pageB' value='Previous'>Previous</button>\n";
	}
	//Otherwise display both
	else {
		$table[] = "<button type='submit' class='prev' name='pageB' value='Previous'>Previous</button>\n";
		$table[] = "<button type='submit' class='next' name='pageB' value='Next'>Next</button>\n";
	}
	
	//Transfer data from page to page using hidden fields
	$part = $_POST['partType'];
	$table[] = "<input type='hidden' name='partType' value='$part'/>\n";
	$brand = $_POST['brand'];
	$table[] = "<input type='hidden' name='brand' value='$brand'/>\n";
	$sort = $_POST['sort'];
	$table[] = "<input type='hidden' name='sort' value='$sort'/>\n";
	$page = $_POST['page'];
	
	$table[] = "<input type='hidden' name='page' value='$page'/>\n";
	$limit = $_POST['limit'];
	$table[] = "<input type='hidden' name='limit' value='$limit'/>\n";
	$table[] = "</form>\n";
	
	return $table;
}

//Builds the homepage
function homePageBody() { 
	
	$contents = array();
	$script = file_get_contents('onclick.js');
	$page[] = "<script>\n$script\n</script>\n";
	$contents[] = "<audio hidden controls autoplay src='../finalProj/lobby.mp3' id='lobby'></audio>\n";
	$contents[] = "<h2 onClick='muteMe()'> Welcome to A-Town Repairs!</h2>";
	$contents[] = "<p>We offer many services and parts to keep your vehicle running for years!</p>\n";
	$contents[] = "<p>Our A+ services can be viewed <a href = 'index.php?action=services'>here</a></p>\n";
	$contents[] = "<p>If you're looking for a specific part, check our inventory <a href = 'index.php?action=inventory'>here</a></p>\n";
	$contents[] = "<p>If you need further assistance, visit our <a href = 'index.php?action=contact'>Contact Us</a> page</p>\n";
				
	return $contents;
	
}

function buildModify() {
	$output = array();
	
	//Insert the part
	$output[] = "<p>Add a Part</p>\n";
	$output[] = "<form method='post' action='index.php?action=add'>\n";
	$output[] = "<table id='form'>\n";
	$output[] = "<tr>\n";

	//Part text box
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Part:</td>\n";
	$output[] = "<td class='body'><input type='text' name='partTypetxt'/><td>";
	$output[] = "</tr>\n";
	
	//brand text box
	$output[] = "<td class='heading'>Brand:</td>\n";
	$output[] = "<td class='body'><input type='text' name='brandtxt'/></td>\n";
	$output[] = "</tr>\n";
	
	//partNo text box
	$output[] = "<td class='heading'>Part No.:</td>\n";
	$output[] = "<td class='body'><input type='text' name='partNotxt'/></td>\n";
	$output[] = "</tr>\n";
	
	//price text box
	$output[] = "<td class='heading'>Price:</td>\n";
	$output[] = "<td class='body'><input type='text' name='pricetxt'/></td>\n";
	$output[] = "</tr>\n";
	
	//Inserts submit button
	$output[] = "<tr>\n";
	$output[] = "<td class='submit' colspan='2'><input type='submit' value='Insert'/></td>";
	$output[] = "</tr>\n";

	$output[] = "</table>\n";
	$output[] = "</form>\n";
	
	//Remove part
	$output[] = "<p>Remove a Part by Part Number</p>\n";
	//text box part number
	$output[] = "<form method='post' action='index.php?action=remove'>\n";
	$output[] = "<table id='form'>\n";
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Part No.:</td>\n";
	$output[] = "<td class='body'><input type='text' name='partNotxt'/></td>\n";
	$output[] = "</tr>\n";
	
	//Inserts submit button
	$output[] = "<tr>\n";
	$output[] = "<td class='submit' colspan='2'><input type='submit' value='Remove'/></td>";
	$output[] = "</tr>\n";
	
	$output[] = "</table>\n";
	$output[] = "</form>\n";
	
	//Modify the part
	$output[] = "<p>Edit Part Information</p>\n";
	//text box part number
	$output[] = "<form method='post' action='index.php?action=edit'>\n";
	$output[] = "<table id='form'>\n";
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Original Part Number:</td>\n";
	$output[] = "<td class='body'><input type='text' name='partNotxt'/></td>\n";
	
	//Create Column Drop Down
	$output[] = "<tr>\n";
	$output[] = "<td class='heading'>Data Being Changed:</td>\n";
	$output[] = "<td class='body'><select name='column'>\n";
	$output[] = "<option value='0'>Part</option>\n";
	$output[] = "<option value='1'>Brand</option>\n";
	$output[] = "<option value='2'>Part Number</option>\n";
	$output[] = "<option value='3'>Price</option>\n";
	$output[] = "</select>\n";
	$output[] = "</tr>\n";
	
	$output[] = "<td class='heading'>Change To:</td>\n";
	$output[] = "<td class='body'><input type='text' name='edittxt'/></td>\n";
	$output[] = "</tr>\n";
	
	//Inserts submit button
	$output[] = "<tr>\n";
	$output[] = "<td class='submit' colspan='2'><input type='submit' value='Change'/></td>";
	$output[] = "</tr>\n";
	
	$output[] = "</table>\n";
	$output[] = "</form>\n";
	
	return $output;
}

//Builds the service page
function buildServices() {
	$page = array();
	$script = file_get_contents('onclick.js');
	$page[] = "<script>\n$script\n</script>\n";
	$page[] = "<audio hidden autoplay src='../finalProj/air_wrench.mp3' id='wrench'></audio>\n";
	$page[] = "<p>The majority of our services are listed below! If the service you're looking for isn't listed, visit the <a href = 'index.php?action=contact'>Contact Us</a> page and give us a call!</p>\n";
	$page[] = "<p>The below service prices are only estimates that will vary per car model.</p>\n";
	$page[] = "<table id='services'>\n";
	$page[] = "<tr>\n";
	$page[] = "<th class='heading'>Service</th>\n";
	$page[] = "<th class='heading'>Time</th>\n";
	$page[] = "<th class='heading'>Cost</th>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Oil Change</td>\n";
	$page[] = "<td class='body'>30 min</td>\n";
	$page[] = "<td class='body'>$50</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Oil Change w/ Filter Replacement</td>\n";
	$page[] = "<td class='body'>1 hr</td>\n";
	$page[] = "<td class='body'>$175</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Brake Pad Replacement Front and Rear</td>\n";
	$page[] = "<td class='body'>2 hrs</td>\n";
	$page[] = "<td class='body'>$400</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Battery Replacement</td>\n";
	$page[] = "<td class='body'>30 min</td>\n";
	$page[] = "<td class='body'>$350</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Fuel System Cleaning</td>\n";
	$page[] = "<td class='body'>45 min</td>\n";
	$page[] = "<td class='body'>$65</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Vehicle Alignment</td>\n";
	$page[] = "<td class='body'>1 hr</td>\n";
	$page[] = "<td class='body'>$150</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>AC Evacuation and Recharge</td>\n";
	$page[] = "<td class='body'>1.5 hrs</td>\n";
	$page[] = "<td class='body'>$275</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Windshield Repair</td>\n";
	$page[] = "<td class='body'>1 hr</td>\n";
	$page[] = "<td class='body'>$200-$400</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>State Vehicle Inspections</td>\n";
	$page[] = "<td class='body'>1 hr</td>\n";
	$page[] = "<td class='body'>$40</td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>\n";
	$page[] = "<td class='body'>Engine Diagnostic Services</td>\n";
	$page[] = "<td class='body'>1.5 hrs</td>\n";
	$page[] = "<td class='body'>$120</td>\n";
	$page[] = "</tr>\n";
	$page[] = "</table>\n";
	return($page);
}

//Build the contact page
function buildContact() {
	$page = array();
	$script = file_get_contents('onclick.js');
	$page[] = "<p>Thank you for choosing A-Town Repairs! As a small business, we value our clients and 
	work to provide great services to get your vehicle working like a charm!</p>";
	$page[] = "<p>If you have any questions regarding our services or avalibility, don't hesitate
	to contact us!</p>\n";
	
	//uses javascript to play audio
	$page[] = "<audio src='../finalProj/lambo.mp3' id='car'></audio>\n";
	$page[] = "<audio src='../finalProj/air_wrench.mp3' id='wrench'></audio>\n";
	$page[] = "<script>\n$script\n</script>\n";
	$page[] = "<button onClick='playSound()'><strong>Phone:</strong> (804)382-4324</button>";
	$page[] = "<button onClick='playSoundOther()'><strong>Email:</strong> customerservice@atownrepairs.com</button>";
	return($page);
}

//builds the login page
function loginPage() {
	$page = array();
	$page[] = "<p>Thank you for visiting the A-Town Repairs website! If you have an account, login here, otherwise please login as guest!</p>\n";
	$page[] = "<form method ='post' action='index.php?action=login'> \n";
	$page[] = "<table id = 'form'>\n";
	$page[] = "<tr>";
	$page[] = "<td class='heading'>Username</td>\n";
	$page[] = "<td class='body'><input type='text' name='uname'/></td>\n";
	$page[] = "</tr>\n";
	$page[] = "<tr>";
	$page[] = "<td class='heading'>Password</td>\n";
	$page[] = "<td class='body'><input type='password' name='pass'/></td>\n";
	$page[] = "</tr>";
	$page[] = "<tr>";
	$page[] = "<td class='logintd' colspan='2'><input type = 'submit' class='loginButton' value='Login'></td>\n";
	$page[] = "</tr>";
	$page[] = "</table>";
	$page[] = "</form>\n";
	
	//allows guest login
	$page[] = "<form method ='post' action='index.php?action=guest'> \n";
	$page[] = "<button type='submit' class='loginButton' value='guest'>Login as Guest</button>\n";
	
	return $page;
}

