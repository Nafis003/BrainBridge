<?php
    include_once 'database.php';
    session_start();
    if(!(isset($_SESSION['email'])))
    {
        header("location:login.php");
    }
    else
    {
        $name = $_SESSION['name'];
        $email = $_SESSION['email'];
        include_once 'database.php';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome | BrainBridge</title>
    <link  rel="stylesheet" href="css/bootstrap.min.css"/>
    <link  rel="stylesheet" href="css/bootstrap-theme.min.css"/>    
    <link rel="stylesheet" href="css/welcome.css?v=2">
    <link  rel="stylesheet" href="css/font.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js"  type="text/javascript"></script>
</head>
<body>
    <nav class="navbar navbar-default title1">
        <div class="container-fluid">
            <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        <a class="navbar-brand" href="#"><b>BrainBridge</b></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-left">
            <!-- 1. Study Materials: hide while a quiz is in progress -->
            <?php if(!(@$_GET['q']=='quiz' && @$_GET['step']==2)) { ?>
            <li <?php if(@$_GET['q']==1) echo'class="active"'; ?> >
                <a href="welcome.php?q=1">
                    <span class="glyphicon glyphicon-book" aria-hidden="true"></span>&nbsp;Study Materials
                </a>
            </li>
            <?php } ?>

            <!-- 2. Quiz -->
            <li <?php if(@$_GET['q']==2) echo'class="active"'; ?> >
                <a href="welcome.php?q=2">
                    <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>&nbsp;Quiz
                    <span class="sr-only">(current)</span>
                </a>
            </li>

            <!-- 3. Ranking -->
            <li <?php if(@$_GET['q']==3) echo'class="active"'; ?> >
                <a href="welcome.php?q=3">
                    <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>&nbsp;Ranking
                </a>
            </li>

            <!-- 4. History -->
            <li <?php if(@$_GET['q']==4) echo'class="active"'; ?> >
                <a href="welcome.php?q=4">
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;History
                </a>
            </li>

            <!-- 5. Feedback -->
            <li <?php if(@$_GET['q']==5) echo 'class="active"'; ?>>
                <a href="welcome.php?q=5">
                    <span class="glyphicon glyphicon-comment"></span>
                    &nbsp;Feedback
                </a>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
        <li <?php echo''; ?> >
            <a href="logout.php?q=welcome.php">
                <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Log out
            </a>
        </li>
        </ul>
       
        </div>
    </div>
    </nav>

    <br><br>

    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <?php 
                if(@$_GET['q']==2) 
                {
                    $result = mysqli_query($con,"SELECT * FROM quiz ORDER BY date DESC") or die('Error');

                    echo  '<div class="panel">
                    <div class="table-responsive">
                    <table class="table table-striped title1">

                    <tr>
                    <td><center><b>S.N.</b></center></td>
                    <td><center><b>Topic</b></center></td>
                    <td><center><b>Total question</b></center></td>
                    <td><center><b>Marks</center></b></td>
                    <td><center><b>Action</b></center></td>
                    </tr>';

                    $c=1;

                    while($row = mysqli_fetch_array($result)) 
                    {
                        $title = $row['title'];
                        $total = $row['total'];
                        $sahi = $row['sahi'];
                        $eid = $row['eid'];

                        $q12=mysqli_query($con,"SELECT score FROM history WHERE eid='$eid' AND email='$email'" )or die('Error98');

                        $rowcount=mysqli_num_rows($q12);	

                        if($rowcount == 0)
                        {
                            echo '<tr>
                            <td><center>'.$c++.'</center></td>
                            <td><center>'.$title.'</center></td>
                            <td><center>'.$total.'</center></td>
                            <td><center>'.($total * 2).'</center></td>

                            <td>
                            <center>
                            <b>
                            <a href="welcome.php?q=quiz&step=2&eid='.$eid.'&n=1&t='.$total.'" 
                            class="btn btn-start">

                            <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>&nbsp;
                            <span class="title1"><b>Start</b></span>
                            </a>
                            </b>
                            </center>
                            </td>
                            </tr>';
                        }
                        else
                        {
                            echo '<tr class="done-row">
                            <td><center>'.$c++.'</center></td>

                            <td>
                            <center>
                            '.$title.'&nbsp;
                            <span title="This quiz is already solve by you" 
                            class="glyphicon glyphicon-ok" 
                            aria-hidden="true"></span>
                            </center>
                            </td>

                            <td><center>'.$total.'</center></td>
                            <td><center>'.($total * 2).'</center></td>

                            <td>
                            <center>
                            <b>
                            <a href="update.php?q=quizre&step=25&eid='.$eid.'&n=1&t='.$total.'" 
                            class="btn btn-restart">

                            <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;
                            <span class="title1"><b>Restart</b></span>
                            </a>
                            </b>
                            </center>
                            </td>
                            </tr>';
                        }
                    }

                    $c=0;
                    echo '</table></div></div>';
                }
                ?>

                <?php
                    if(@$_GET['q']== 'quiz' && @$_GET['step']== 2) 
                    {
                        $eid=@$_GET['eid'];
                        $sn=@$_GET['n'];
                        $total=@$_GET['t'];

                        $q=mysqli_query($con,"SELECT * FROM questions WHERE eid='$eid' AND sn='$sn' ");

                        echo '<div class="panel quiz-card">';

                        while($row=mysqli_fetch_array($q))
                        {
                            $qns=$row['qns'];
                            $qid=$row['qid'];

                            echo '<div class="question-no">Question '.$sn.'</div><div class="question-text">'.$qns.'</div>';
                        }

                        $q=mysqli_query($con,"SELECT * FROM options WHERE qid='$qid' ");

                        echo '<form action="update.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'&qid='.$qid.'" method="POST" class="form-horizontal">
                        <br />';

                        while($row=mysqli_fetch_array($q))
                        {
                            $option=$row['option'];
                            $optionid=$row['optionid'];

                            echo'<label class="option"><input type="radio" name="ans" value="'.$optionid.'"><span>'.$option.'</span></label>';
                        }

                        echo'<br />
                        <button type="submit" class="btn btn-primary">
                        <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>&nbsp;Submit
                        </button>
                        </form>
                        </div>';
                    }

                    if(@$_GET['q']== 'result' && @$_GET['eid']) 
                    {
                        $eid=@$_GET['eid'];

                        $q=mysqli_query($con,"SELECT * FROM history WHERE eid='$eid' AND email='$email' ")or die('Error157');

                        echo  '<div class="panel">
                        <h2 class="result-title">Result</h2>

                        <table class="table title1 result-table">';

                        while($row=mysqli_fetch_array($q))
                        {
                            $s=$row['score'];
                            $w=$row['wrong'];
                            $r=$row['sahi'];
                            $qa=$row['level'];

                            echo '<tr class="r-total">
                            <td>Total Questions</td>
                            <td>'.$qa.'</td>
                            </tr>

                            <tr class="r-right">
                            <td>Right Answer&nbsp;
                            <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></td>
                            <td>'.$r.'</td>
                            </tr> 

                            <tr class="r-wrong">
                            <td>Wrong Answer&nbsp;
                            <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></td>
                            <td>'.$w.'</td>
                            </tr>

                            <tr class="r-score">
                            <td>Score&nbsp;
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span></td>
                            <td>'.$s.'</td>
                            </tr>';
                        }

                        $q=mysqli_query($con,"SELECT * FROM rank WHERE email='$email' ")or die('Error157');

                        while($row=mysqli_fetch_array($q))
                        {
                            $s=$row['score'];

                            echo '<tr class="r-overall">
                            <td>Overall Score&nbsp;
                            <span class="glyphicon glyphicon-stats" aria-hidden="true"></span></td>
                            <td>'.$s.'</td>
                            </tr>';
                        }

                        echo '</table></div>';
                    }
                ?>

                <?php
                    if(@$_GET['q']==4) 
                    {
                        $q=mysqli_query($con,"SELECT * FROM history WHERE email='$email' ORDER BY date DESC ")or die('Error197');

                        echo  '<div class="panel title">
                        <table class="table table-striped title1">

                        <tr>
                        <td><center><b>S.N.</b></center></td>
                        <td><center><b>Quiz</b></center></td>
                        <td><center><b>Question Solved</b></center></td>
                        <td><center><b>Right</b></center></td>
                        <td><center><b>Wrong<b></center></td>
                        <td><center><b>Score</b></center></td>
                        </tr>';

                        $c=0;

                        while($row=mysqli_fetch_array($q))
                        {
                            $eid=$row['eid'];
                            $s=$row['score'];
                            $w=$row['wrong'];
                            $r=$row['sahi'];
                            $qa=$row['level'];

                            $q23=mysqli_query($con,"SELECT title FROM quiz WHERE eid='$eid' ")or die('Error208');

                            while($row=mysqli_fetch_array($q23))
                            {  
                                $title=$row['title'];  
                            }

                            $c++;

                            echo '<tr>
                            <td><center>'.$c.'</center></td>
                            <td><center>'.$title.'</center></td>
                            <td><center>'.$qa.'</center></td>
                            <td><center>'.$r.'</center></td>
                            <td><center>'.$w.'</center></td>
                            <td><center>'.$s.'</center></td>
                            </tr>';
                        }

                        echo'</table></div>';
                    }

                    if(@$_GET['q']==3) 
                    {
                        $q=mysqli_query($con,"SELECT * FROM rank ORDER BY score DESC ")or die('Error223');

                        echo  '<div class="panel title">
                        <div class="table-responsive">

                        <table class="table table-striped title1">

                        <tr>
                        <td><center><b>Rank</b></center></td>
                        <td><center><b>Name</b></center></td>
                        <td><center><b>Email</b></center></td>
                        <td><center><b>Score</b></center></td>
                        </tr>';

                        $c=0;

                        while($row=mysqli_fetch_array($q))
                        {
                            $e=$row['email'];
                            $s=$row['score'];

                            $q12=mysqli_query($con,"SELECT * FROM user WHERE email='$e' ")or die('Error231');

                            while($row=mysqli_fetch_array($q12))
                            {
                                $name=$row['name'];
                            }

                            $c++;

                            echo '<tr>
                            <td><center><b>'.$c.'</b></center></td>
                            <td><center>'.$name.'</center></td>
                            <td><center>'.$e.'</center></td>
                            <td><center>'.$s.'</center></td>
                            </tr>';
                        }

                        echo '</table></div></div>';
                    }


                    if(@$_GET['q']==1)
                    {
                        echo '
                        <div class="panel">
                            <h2 class="text-center">
                                <span class="glyphicon glyphicon-book"></span>
                                Study Materials
                            </h2>

                            <hr>

                            <div class="list-group">
                            <a href="notes/Data-Communications-and-Network-5e.pdf" target="_blank" class="list-group-item">
                                    <h4 class="list-group-item-heading">Data Communications and Networking E-Book</h4>
                                    <p class="list-group-item-text">
                                        Comprehensive guide covering network models, data transmission, routing, and internet security.
                                    </p>
                                </a>

                                <a href="notes/ip_networking_basics.pdf" target="_blank" class="list-group-item">
                                    <h4 class="list-group-item-heading">Ip Networking Basics PDF</h4>
                                    <p class="list-group-item-text">
                                        Learn Ip Networking fundamentals and basic networking concepts.
                                    </p>
                                </a>

                                <a href="notes/PHP & MySQLi Notes.pdf" target="_blank" class="list-group-item">
                                    <h4 class="list-group-item-heading">PHP & MySQLi Notes</h4>
                                    <p class="list-group-item-text">
                                        Learn PHP programming, MySQLi database connection, and dynamic web development.

                                    </p>
                                </a>

                                <a href="https://youtu.be/EgoclyVN8Z4?si=Xbu5hpIKT_QHzOEl/" target="_blank" class="list-group-item">
                                    <h4 class="list-group-item-heading">Software Engineering and System Analysis Tutorials</h4>
                                    <p class="list-group-item-text">
                                        Software development, SRS design, system modeling, and software testing mechanisms learning materials.
                                    </p>
                                </a>

                            </div>
                        </div>';
                    }
                ?>

                <?php

                if(@$_GET['q']==5)
                {
    
                    // SUBMIT FEEDBACK

                    if(isset($_POST['submit_feedback']))
                    {
                        $feedback_name = mysqli_real_escape_string($con, $_POST['feedback_name']);
                        $msg = $_POST['message'];

                        mysqli_query($con,"
                        INSERT INTO feedback(name,email,message)
                        VALUES('$feedback_name','$email','$msg')
                        ") or die('Error');

                        echo '
                        <div class="alert alert-success">
                            Feedback submitted successfully!
                        </div>';
                    }

                    echo '

                    <div class="panel">

                        <h2 class="text-center">
                            Student Feedback
                        </h2>

                        <hr>

                        <form method="POST">

                            <div class="form-group">

                                <label>Your Name</label>

                                <input type="text"
                                name="feedback_name"
                                class="form-control"
                                value="'.$name.'"
                                required>

                            </div>

                            <div class="form-group">

                                <label>Your Email</label>

                                <input type="text"
                                class="form-control"
                                value="'.$email.'"
                                readonly>

                            </div>

                            <div class="form-group">

                                <label>Your Feedback</label>

                                <textarea name="message"
                                class="form-control"
                                rows="5"
                                placeholder="Write your feedback here..."
                                required></textarea>

                            </div>

                            <br>

                            <button type="submit"
                            name="submit_feedback"
                            class="btn btn-primary">

                                <span class="glyphicon glyphicon-send"></span>
                                Submit Feedback

                            </button>

                        </form>

                    </div>';
                }

                ?>

            </div>
        </div>
    </div>

</body>
</html>
