<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a ticket</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <script>
        //insert datepicker jQuery

        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function() {
                depa = $("#depa").datepicker()
                arr = $("#arr").datepicker()

                function getDate(element) {
                    var date;
                    try {
                        date = $.datepicker.parseDate(dateFormat, element.value);
                    } catch (error) {
                        date = null;
                    }
                    return date;
                }
            });
        });
    </script>
</head>
<body>
    
<?php
include "checksession.php";
checkUser();
loginStatus(); 
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);



if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
  exit; //stop processing the page further
}


//function to clean input but not validate type and content
function cleanInput($data)
{
  return htmlspecialchars(stripslashes(trim($data)));
}


//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Book')) {

#code
  $room = cleanInput($_POST['room']);
  $depa = $_POST['depa'];
  $arr = $_POST['arr'];
  $contact = cleanInput($_POST['contactnumber']);
  $booking = cleanInput($_POST['bookingextras']);

  $error = 0;
  $msg ="Error:";

  $in = new DateTime($depa);
  $out = new DateTime($arr);

  if( $in >= $out){
      $error++;
      $msg .= "Arrival date cannot be earlier or equal to departure date";
      $arr = '';
  }

  if($error ==0)
  {
      $query ="INSERT INTO booking (checkoutdate, checkindate,contactnumber,
      bookingextras) VALUES (?,?,?,?)";

      $stmt = mysqli_prepare($DBC,$query);

      mysqli_stmt_bind_param($stmt,'ssis',$room,$depa,$arr,$contact,$booking);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      echo "<h5>Booking added successfully.</h5>";
  }else{
      echo "<h5>$msg</h5>" .PHP_EOL;
  }

}




$query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

$query1 = 'SELECT customerID, firstname, lastname, email FROM customer ORDER BY customerID';
$result1 = mysqli_query($DBC, $query1);
$rowcount1 = mysqli_num_rows($result1);
?>
<h1>Book a ticket</h1>
    <h2>
        <a href='ticketslisting.php'>[Return to the Tickets listing]</a>
        <a href="index.php">[Return to main page]</a>
    </h2>

    <div>

        <form method="POST">
            <div>
                <label for="flights">Room:</label>
                <select name="room" id="room">
                <?php
                    if ($rowcount > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['roomID']; ?>

                            <option value="<?php echo $row['roomID']; ?>">
                                <?php echo $row['roomname'] . ' '
                                    . $row['roomtype'] . ' '
                                   . $row['beds']

                                ?>
                            </option>
                    <?php }
                    } else echo "<option>No Rooms found</option>";
                    mysqli_free_result($result);
                    ?>

                </select>
            </div>


            <br>
            <div>
                <label for="customers">Customers:</label>
                <select name="customers" id="customers">
                    <?php
                    if ($rowcount1 > 0) {
                        while ($row = mysqli_fetch_assoc($result1)) {
                            $id = $row['flightcode']; ?>

                            <option value="<?php echo $row['customerID']; ?>">
                                <?php echo $row['customerID'] . ' '
                                    . $row['fname'] . ' '
                                    . $row['lname'] . ' - '
                                    . $row['email']

                                ?>
                            </option>
                    <?php }
                    } else echo "<option>No flights found</option>";
                    mysqli_free_result($result1);
                    ?>
                </select>
            </div>
            <br>
            <div>
                <label for="depa">Departure Date:</label>
                <input type="text" id="depa" name="depa" required>
            </div>
            <br>
            <div>
                <label for="arr">Arrival Date:</label>
                <input type="text" id="arr" name="arr" required>
            </div>
            <br>
            <div>
                <label for="price">Price:</label>
                <input type="text" id="price" name="price" required>
            </div>
            <br>
            <div>
                <label for="seats">Seat Options:</label>
                <input type="text" id="seat" name="seat">
            </div>
            <br>
            <div>
                <input type="submit" name="submit" value="Book">
            </div>

        </form>

        <hr>
 

        <h3>Search for tickets</h3>
<div>
    <form id="searchForm" method="get" name="searching">
        <input type="text" id="fromDate" name="fromDate" required placeholder="From Date">
        <input type="text" id="toDate" name="toDate" required placeholder="To Date">
        <input type="submit" value="Search">
    </form>
</div>
<br><br>
<div class="row">
    <table id="tblbookings" border="1">
        <thead>
            <tr>
                <th>Flight#</th>
                <th>Flight name</th>
                <th>Departure Location</th>
                <th>Destination Location</th>              
            </tr>
        </thead>
        <tbody id="result"></tbody> <!-- Display search result here -->
    </table>
</div>

<script>
$(document).ready(function(){
    $("#fromDate").datepicker({dateFormat:"yy-mm-dd"});
    $("#toDate").datepicker({dateFormat:"yy-mm-dd"});

    $("#searchForm").submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        var fromDate = $("#fromDate").val();
        var toDate = $("#toDate").val();
        
        if (fromDate > toDate) {
            alert("From date cannot be later than To date.");
            return false; // Prevents further execution
        }

        searchTickets(); // Call searchTickets function
    });
});

function searchTickets(){
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();

    $.ajax({
        url: "bookingsearch.php",
        method: "GET",
        data: {fromDate: fromDate, toDate: toDate},
        success: function(response) {
            $("#result").html(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
</script>
</body>
</html>
