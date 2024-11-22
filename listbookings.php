<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse bookings</title>
</head>

<body>

    <?php
    include "config.php";
    include "checksession.php";

    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
    if (mysqli_connect_errno()) {
        echo "Error:unable to connect to Mysql." . mysqli_connect_error();
        exit; //stop processing the page further
    }


    //prepare a query and send it to the server
    $query = 'SELECT booking.bookingID, booking.checkindate, booking.checkoutdate, customer.firstname, customer.lastname, room.roomname
    FROM booking, customer, room
    WHERE booking.customerID = customer.customerID AND booking.roomID = room.roomID
    ORDER BY bookingID';

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);


    ?>

    <h1>Current Bookings</h1>
    <h2><a href="makeabooking.php">[Make a booking]</a><a href="index.php">[Return to main page]</a></h2>

    <table border="1">
        <thead>
            <tr>
                <th>Booking (room, dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>

        <?php
        if ($rowcount > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $id = $row['bookingID'];
                echo '<tr><td>' . $row['roomname'] . ','." " . $row['checkindate'] . ',' ." ". $row['checkoutdate'] . '</td>' .
                    '<td>' . $row['firstname'] . ','." " . $row['lastname'] . '</td>' .
                    '<td><a href="bookingdetails.php?id=' . $id . '">[view]</a>' ."_".
                    '<a href="editbooking.php?id=' . $id . '">[edit]</a>' ."_".
                    '<a href="editroom.php?id=' . $id . '">[Manage Review]</a>' ."_".
                    '<a href="deletebooking.php?id=' . $id . '">[delete]</a></td>';
                echo '</tr>' . PHP_EOL;
            }
        } else
            echo "<h2>No tickets found!</h2>"; //suitable feedback
        
        mysqli_free_result($result);
        mysqli_close($DBC);

        ?>

    </table>



</body>

</html>