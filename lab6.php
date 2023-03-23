<?php
$title = "WEBD1201: Sticky Form/Data Validation";
$file = "Lab6.php";
$description = "Lab 6 Page";
$date = "2023-03-14";
$banner = "WEBD1206 - Self-referring Forms with Data Validation";


include "./header.php";

//empty out error and result regardless of method that got you here
$error = "";
$result = "";

define("MAX_ITERATIONS", 100);

if($_SERVER["REQUEST_METHOD"] == "GET"){
	//default mode when the page loads the first time
	//can be used to make decisions and initialize variables
    $start = "";
    $stop = "";
    $incr  = "";
}
else if($_SERVER["REQUEST_METHOD"] == "POST"){
	//the page got here from submitting the form, let's try to process
	$start = trim($_POST["inputted_number"]); //the name of the input box on the form, white-space removed
	$stop = trim($_POST["inputted_number1"]); //the name of the input box on the form, white-space removed
    $incr = trim($_POST["inputted_number2"]); //the name of the input box on the form, white-space removed
	
	if(!isset($start) || $start == ""){
		//means the user did not enter anything
		$error .= "You must enter something into the start text box.";
	}
	else if(!is_numeric($start)) {
		//means the user entered something, but not a number
		//give them a detailed message
		$error .= "The value entered <u>MUST</u> be a number";
		$start = "";
	}
	if(!isset($stop) || $stop == "") {
		//means the user did not enter anything
		$error .= "<br>You must enter something into the stop text box.";
	}
	else if(!is_numeric($stop)) {
		//means the user entered something, but not a number
		//give them a detailed message
		$error .= "The value entered <u>MUST</u> be a number";
		$stop = "";
	}
	if ($stop<$start) {
		$error .= "The stop value must be higher than the start value";
		$stop = "";
	}	
	if (!isset($incr) || $incr == "") {
		//means the user did not enter anything
		$error .= "<br>You must enter something into the increment text box.";
	}
	else if(!is_numeric($incr)) {
		//means the user entered something, but not a number
		//give them a detailed message
		$error .= "The value entered <u>MUST</u> be a number";
		$incr = "";
	}
		if($error == ""){  //if error is an empty string
            //no errors, do the math
            if($start <= $MAX_ITERATIONS && $stop<= $MAX_ITERATIONS && $incr<= $MAX_ITERATIONS){
                ?>
                <body>
                <table>
                    <tr>
                        <th>Celsius</th>
                        <th>Fahrenheit</th>
                    </tr>
                    <?php
                    echo "<tr>";
                    for ($x = $start; $x <= $stop;$x+=$incr) 
                    {
                        
                    
                        $y = 0;
                        echo "<tr>";
                        $y = 9.0/5.0*$x + 32;
                        echo "<td>". $x ,"&deg;"."</td>";
                        echo "<td>". $y ,"&deg;"."</td>";
                        
                    }
                    echo "<tr>";
                
                    ?>
                </table>
            </body>
            </td>
            
    <?php
    }
        else
        {
            //there were problems, concatentate the TRY AGAIN message
            $error .= "<br/>Please Try Again";
            
        }
    }
}
//NOTE: 
//the first two echos below show the errors or the result (these are empty the first time the page loads)
//the third of the following echo'es makes this page self-referring
//the name of the current file is outputted placed in the action of the form
//and the fourth of the following echo'es is what makes the form sticky, the
//number previously entered on the form, is automatically displayed in the value of the text input box
?>
This webpage will take user input for starting temperature how many times they would like to calculate it and how many jumps each calculation will take. 
<h2><?php echo $result; ?></h2>
<h3><?php echo $error; ?></h3>

<form action="<?php echo $_SERVER['PHP_SELF'];  ?>" method="POST" >
    Starting Temperature <input type="text" name="inputted_number" value="<?php echo $start;?>" size="5" /><br/>
    Stop Temperature: <input type="text" name="inputted_number1" value="<?php echo $stop;?>" size="5" /><br/>
    incrment: <input type="text" name="inputted_number2" value="<?php echo $incr;?>" size="5" /><br/>
    
	<br/><input type="submit" value="Create Temperature Conversion Table" />
</form>
<?php
	include "./footer.php";
?>