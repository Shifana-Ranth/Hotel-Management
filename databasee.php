<?php
    $server="localhost";
    $user="root";
    $pass="";
    $dbase="sample";
    $conn="";

    try{
        $conn=mysqli_connect($server,$user,$pass,$dbase);
    }
    catch(mysqli_sql_exception){
        //echo "could not connect";
        echo '
            <style>
                .error-box {
                    max-width: 400px;
                    margin: 100px auto;
                    padding: 20px;
                    background-color: #ffe0e0;
                    color: #b30000;
                    border: 2px solid #ff4d4d;
                    border-radius: 10px;
                    text-align: center;
                    font-family: Arial, sans-serif;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }
            </style>
            <div class="error-box">
                <h2>Connection Failed</h2>
                <p>We’re having trouble connecting to the database right now.</p>
                <p>Please try again later.</p>
            </div>
            ';
            exit();
    }
?>