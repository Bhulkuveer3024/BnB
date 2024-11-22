 <!DOCTYPE HTML>
<html><head><title>Add a new room</title> </head>
 <body>

<?php
//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
//if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
    include "config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

//validate incoming data - only the first field is done for you in this example - rest is up to you do
//roomname
    $error = 0; //clear our error flag
    $msg = 'Error: ';
    if (isset($_POST['roomname']) and !empty($_POST['roomname']) and is_string($_POST['roomname'])) {
       $fn = cleanInput($_POST['roomname']); 
       $roomname = (strlen($fn)>50)?substr($fn,1,50):$fn; //check length and clip if too big
       //we would also do context checking here for contents, etc       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid roomname '; //append eror message
       $roomname = '';  
    } 
 
//description
       $description = cleanInput($_POST['description']);        
//roomtype
       $roomtype = cleanInput($_POST['roomtype']);            
//beds    
       $beds = cleanInput($_POST['beds']);        
       
//save the room data if the error flag is still clear
    if ($error == 0) {
        $query = "INSERT INTO room (roomname,description,roomtype,beds) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'sssd', $roomname, $description, $roomtype,$beds); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>New room added to the list</h2>";        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
    mysqli_close($DBC); //close the connection once done
}
?>
<h1>Add a new room</h1>
<h2><a href='listrooms.php'>[Return to the room listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST" action="addroom.php">
  <p>
    <label for="roomname">Room name: </label>
    <input type="text" id="roomname" name="roomname" minlength="5" maxlength="50" required> 
  </p> 
  <p>
    <label for="description">Description: </label>
    <input type="text" id="description" size="100" name="description" minlength="5" maxlength="200" required> 
  </p>  
  <p>  
    <label for="roomtype">Room type: </label>
    <input type="radio" id="roomtype" name="roomtype" value="S"> Single 
    <input type="radio" id="roomtype" name="roomtype" value="D" Checked> Double 
   </p>
  <p>
    <label for="beds">Beds (1-5): </label>
    <input type="number" id="beds" name="beds" min="1" max="5" value="1" required> 
  </p> 
  
   <input type="submit" name="submit" value="Add">
 </form>
</body>
</html>
  