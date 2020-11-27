<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$title_key = "";
require_once "config.php";

if(isset($_POST['renewal'])){
    mysqli_query($link, "set sql_safe_updates = 0");
    mysqli_query($link, "update returndate set duedate = date_add(duedate, interval 14 day), renewalstatus = 'RENEWED' where renewalstatus = 'NOT' and duedate > now()");
    header("location: main.php");
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        table {margin-left: auto; margin-right: auto; width:90%;}
        th{font-weight: bold;}
        td{font-weight: normal;}
        table, th, td{border: 1px solid black;}
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Welcome back, <b><?php echo htmlspecialchars($_SESSION["loginId"]); ?></h1>
    </div>
    <div>
        <?php
        $sql = "SELECT firstname, lastname, title, authno, r.* 
        FROM reader rt, returndate r, book b, login l, authentication a 
        WHERE (rt.rollno = r.rollno and l.rollno = r.rollno and l.loginId = a.loginId and b.isbn = r.isbn and a.loginId = ?);";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_loginId);
            
            // Set parameters
            $param_loginId = $_SESSION["loginId"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $f, $l, $t, $ano, $isbn1, $rollno, $borroweddate, $duedate, $renewal);
        
            }
            else{
                echo "OOPS something is wrong here";
            }
        }
        else{
            echo "OOPS something is wrong";
        } 
        echo '<table>
        <tr>
        <th>Roll number</th>
        <th>First name</th>
        <th>Last name</th>
        <th>Title</th>
        <th>Author no</th>
        <th>ISBN number</th>
        <th>Borrowed Date</th>
        <th>Due Date</th>
        <th>status</th>
        <th> Fine <th>
        </tr>';
        $total_fine = 0;
        while($row = mysqli_stmt_fetch($stmt)){
            print "<tr>";
            echo "<td>" . $rollno . "</td>";
            echo "<td>" . $f . "</td>";
            echo "<td>" . $l . "</td>";
            echo "<td>" . $t . "</td>";
            echo "<td>" . $ano . "</td>";
            print "<td>" . $isbn1 . "</td>";
            echo "<td>" . $borroweddate . "</td>";
            echo "<td>" . $duedate . "</td>";
            echo "<td>" . $renewal . "</td>";
            $date1 = new DateTime("now");
            $date2 = new DateTime($duedate);
            if($date1 > $date2)
            $fine = $date1->diff($date2);
            else
            $fine = $date1->diff($date1);
            $total_fine = $total_fine + $fine->days;
            echo "<td> Rs. " .$fine->days. "</td>";
            echo "</tr>";
        }
        mysqli_stmt_close($stmt);
        echo '</table>';
        echo "<br><br><h4>Total fine : Rs. " .$total_fine. "</h4>";
        ?>
    </div>
    <div>
    <form method = "post">
    <input type = "submit" name = "renewal" value = "Renew all"><br><br><br><br>
    <label>Enter title of book</label>
    <input type = "text" name = "title_key">
    <input type = "submit" name = "search" value = "search">
    </form>
    </div><br><br>
    <div>
    <?php
    if(isset($_POST['search'])){
        $sql = "SELECT * 
        from book b natural join publishes pr natural join publisher p natural join edition e natural join category c 
        where b.title like ? and b.isbn not in (select isbn from returndate where renewalstatus in ('NOT', 'RENEWED'))";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_key);
        
            // Set parameters
            $title_key = $_POST["title_key"];
            $param_key = '%'. trim($title_key) .'%';
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $title, $edition, $publisherid, $isbn2, $authno, $name, $price, $category);
                
            }
            else{
                echo "OOPS something is wrong here";
            }
            
        }
        print "<p> List of books available to borrow</p><table>
        <tr>
        <th>Title</th>
        <th>Edition</th>
        <th>Publisher Id</th>
        <th>ISBN</th>
        <th>Author no</th>
        <th>Publisher name</th>
        <th>Price</th>
        <th>Categoy</th>
        </tr>";
        while($row = mysqli_stmt_fetch($stmt))
        {
            print "<tr>";
            echo "<td>" . $title . "</td>";
            echo "<td>" . $edition . "</td>";
            echo "<td>" . $publisherid . "</td>";
            echo "<td>" . $isbn2 . "</td>";
            echo "<td>" . $authno . "</td>";
            print "<td>" . $name . "</td>";
            echo "<td>" . $price . "</td>";
            echo "<td>" . $category . "</td>";
            echo "</tr>";
        }mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
    ?>
    </table>
    </div><br><br>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>