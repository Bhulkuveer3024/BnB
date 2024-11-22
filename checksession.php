<?php

/* 
// session_start() is a PHP function used to initialize a new session or resume an existing session.

// In web applications, sessions are used to maintain state information 
between page requests from the same user. 
When a user visits a website, a unique session ID is generated for that user. 
This session ID is typically stored as a cookie in the user's browser, 
although it can also be passed via URL parameters.

// session_start() must be called before any output is sent to the browser. 
It initializes the session and allows you to work with session variables ($_SESSION) in your PHP scripts.
 */
 
session_start();



// This PHP function checkUser() is intended to verify whether a user is logged in or not.


function checkUser(){
    $_SESSION['URI'] ='';
    if($_SESSION['loggedin'] == 1){
        return TRUE;
    }
    else{
        $_SESSION['URI'] ='/' .$_SERVER['REQUEST_URI'];
        header(('Location:/bnb/login.php'),true, 303);
    }
}

// $_SESSION['URI'] is being used to store the current URI (Uniform Resource Identifier), 
//This includes only the path component of the URL, without the domain name or protocol.

// $_SERVER['REQUEST_URI'] is used to obtain the full URL 
//(including the domain name, protocol, and path) of the current request.


function loginStatus(){
    $un = $_SESSION['username'];

    if($_SESSION['loggedin'] == 1){
        echo "<h6>Logged in as $un</h6>";
    }
    else{
        echo "<h6>Logged out</h6>";
    }
}

function login($id, $username){
    if($_SESSION['loggedin'] ==0 and !empty($_SESSION['URI'])){
        $uri = $_SESSION['URI'];
    }
    else{
        $_SESSION['URI'] ='/bnb/listrooms.php';
        $uri = $_SESSION['URI'];
    }

    header('Location: /bnb/listrooms.php',true, 303);
    $_SESSION['loggedin'] =1;
    $_SESSION['userid'] =$id;
    $_SESSION['username'] =$username;
    $_SESSION['URI'] ='';

}

/* Placing header() Outside the Conditional Block:
This approach ensures that the user is always redirected to the ticketslisting.php page after logging in, 
regardless of whether they attempted to access a different page before logging in. */

function logout(){
    $_SESSION['loggedin'] =0;
    $_SESSION['userid'] =-1;
    $_SESSION['username'] ='';
    $_SESSION['URI'] ='';
    header(('Location:/bnb/login.php'),true, 303);
}

?>