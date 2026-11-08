```php
<?php
include_once 'database.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("location:login.php");
    exit();
}

$email = $_SESSION['email'];


// ==========================================
// 1. DELETE STUDENT
// ==========================================

if (isset($_SESSION['key'])) {
    if (isset($_GET['demail']) && $_SESSION['key'] == 'suryapinky') {

        $demail = mysqli_real_escape_string($con, $_GET['demail']);

        mysqli_query($con, "DELETE FROM rank WHERE email='$demail'");
        mysqli_query($con, "DELETE FROM history WHERE email='$demail'");
        mysqli_query($con, "DELETE FROM user WHERE email='$demail'");

        header("location:dashboard.php?q=1");
        exit();
    }
}


// ==========================================
// 2. DELETE QUIZ
// ==========================================

if (isset($_SESSION['key'])) {
    if (isset($_GET['q']) && $_GET['q'] == 'rmquiz'
        && $_SESSION['key'] == 'suryapinky') {

        $eid = mysqli_real_escape_string($con, $_GET['eid']);

        $result = mysqli_query($con,
            "SELECT qid FROM questions WHERE eid='$eid'");

        while ($row = mysqli_fetch_array($result)) {

            $qid = $row['qid'];

            mysqli_query($con, "DELETE FROM options WHERE qid='$qid'");
            mysqli_query($con, "DELETE FROM answer WHERE qid='$qid'");
        }

        mysqli_query($con, "DELETE FROM questions WHERE eid='$eid'");
        mysqli_query($con, "DELETE FROM quiz WHERE eid='$eid'");
        mysqli_query($con, "DELETE FROM history WHERE eid='$eid'");

        header("location:dashboard.php?q=5");
        exit();
    }
}


// ==========================================
// 3. ADD QUIZ
// ==========================================

if (isset($_SESSION['key'])) {
    if (isset($_GET['q']) && $_GET['q'] == 'addquiz'
        && $_SESSION['key'] == 'suryapinky') {

        $name = mysqli_real_escape_string(
            $con,
            ucwords(strtolower($_POST['name']))
        );

        $total = (int)$_POST['total'];

        // Fixed marking system
        $sahi = 2;
        $wrong = 0;

        $id = uniqid();

        mysqli_query($con,
            "INSERT INTO quiz VALUES
            ('$id','$name','$sahi','$wrong','$total',NOW())"
        ) or die(mysqli_error($con));

        header("location:dashboard.php?q=4&step=2&eid=$id&n=$total");
        exit();
    }
}


// ==========================================
// 4. ADD QUESTIONS
// ==========================================

if (isset($_SESSION['key'])) {
    if (isset($_GET['q']) && $_GET['q'] == 'addqns'
        && $_SESSION['key'] == 'suryapinky') {

        $n = (int)$_GET['n'];
        $eid = mysqli_real_escape_string($con, $_GET['eid']);
        $ch = mysqli_real_escape_string($con, $_GET['ch']);

        for ($i = 1; $i <= $n; $i++) {

            $qid = uniqid();

            $qns = mysqli_real_escape_string($con, $_POST['qns'.$i]);

            mysqli_query($con,
                "INSERT INTO questions VALUES
                ('$eid','$qid','$qns','$ch','$i')"
            ) or die(mysqli_error($con));

            $oaid = uniqid();
            $obid = uniqid();
            $ocid = uniqid();
            $odid = uniqid();

            $a = mysqli_real_escape_string($con, $_POST[$i.'1']);
            $b = mysqli_real_escape_string($con, $_POST[$i.'2']);
            $c = mysqli_real_escape_string($con, $_POST[$i.'3']);
            $d = mysqli_real_escape_string($con, $_POST[$i.'4']);

            mysqli_query($con,
                "INSERT INTO options VALUES ('$qid','$a','$oaid')"
            ) or die(mysqli_error($con));

            mysqli_query($con,
                "INSERT INTO options VALUES ('$qid','$b','$obid')"
            ) or die(mysqli_error($con));

            mysqli_query($con,
                "INSERT INTO options VALUES ('$qid','$c','$ocid')"
            ) or die(mysqli_error($con));

            mysqli_query($con,
                "INSERT INTO options VALUES ('$qid','$d','$odid')"
            ) or die(mysqli_error($con));

            $e = isset($_POST['ans'.$i])
                ? strtolower($_POST['ans'.$i])
                : '';

            switch ($e) {

                case 'a':
                    $ansid = $oaid;
                    break;

                case 'b':
                    $ansid = $obid;
                    break;

                case 'c':
                    $ansid = $ocid;
                    break;

                case 'd':
                    $ansid = $odid;
                    break;

                default:
                    die("Invalid correct answer for question $i");
            }

            mysqli_query($con,
                "INSERT INTO answer VALUES ('$qid','$ansid')"
            ) or die(mysqli_error($con));
        }

        header("location:dashboard.php?q=0");
        exit();
    }
}


// ==========================================
// 5. QUIZ ANSWER CHECKING & MARKS CALCULATION
// ==========================================

if (isset($_GET['q']) && $_GET['q'] == 'quiz'
    && isset($_GET['step']) && $_GET['step'] == 2) {

    $eid = mysqli_real_escape_string($con, $_GET['eid']);

    $sn = (int)$_GET['n'];
    $total = (int)$_GET['t'];

    $qid = mysqli_real_escape_string($con, $_GET['qid']);

    // Check whether the question belongs to this quiz
    $question = mysqli_query($con,
        "SELECT qid FROM questions
         WHERE qid='$qid' AND eid='$eid'"
    );

    if (!$question || mysqli_num_rows($question) == 0) {
        die("Invalid question.");
    }

    // Get correct answer ID
    $q = mysqli_query($con,
        "SELECT ansid FROM answer WHERE qid='$qid'"
    );

    if (!$q || mysqli_num_rows($q) == 0) {
        die("Correct answer not found.");
    }

    $row = mysqli_fetch_assoc($q);
    $ansid = $row['ansid'];

    // Check submitted answer
    $ans = isset($_POST['ans']) ? $_POST['ans'] : '';

    // Correct answer = 2 marks
    // Wrong or unanswered = 0 marks

    $correct = ($ans !== '' && $ans == $ansid);

    $marks = $correct ? 2 : 0;

    // Initialize history for first question
    if ($sn == 1) {

        $check = mysqli_query($con,
            "SELECT * FROM history
             WHERE eid='$eid' AND email='$email'"
        );

        if (mysqli_num_rows($check) == 0) {

            mysqli_query($con,
                "INSERT INTO history
                (email,eid,score,level,sahi,wrong,date)
                VALUES ('$email','$eid',0,0,0,0,NOW())"
            ) or die(mysqli_error($con));
        }
    }

    // Get current history
    $q = mysqli_query($con,
        "SELECT * FROM history
         WHERE eid='$eid' AND email='$email'"
    ) or die(mysqli_error($con));

    if (mysqli_num_rows($q) == 0) {
        die("Quiz history not found.");
    }

    $row = mysqli_fetch_assoc($q);

    $s = (int)$row['score'];
    $r = (int)$row['sahi'];
    $w = (int)$row['wrong'];
    $level = (int)$row['level'];

    // Prevent duplicate submission
    if ($sn <= $level) {

        if ($level >= $total) {
            header("location:welcome.php?q=result&eid=$eid");
        } else {
            $next = $level + 1;

            header("location:welcome.php?q=quiz&step=2&eid=$eid&n=$next&t=$total");
        }

        exit();
    }

    // Ensure questions are answered in order
    if ($sn != $level + 1) {
        die("Invalid question sequence.");
    }

    // Update score
    if ($correct) {

        $s = $s + 2;
        $r++;

    } else {

        // No negative marking
        $w++;
    }

    // Save updated result
    mysqli_query($con,
        "UPDATE history SET
        score='$s',
        level='$sn',
        sahi='$r',
        wrong='$w',
        date=NOW()
        WHERE email='$email' AND eid='$eid'"
    ) or die(mysqli_error($con));


    // ======================================
    // NEXT QUESTION
    // ======================================

    if ($sn < $total) {

        $sn++;

        header("location:welcome.php?q=quiz&step=2&eid=$eid&n=$sn&t=$total");
        exit();
    }


    // ======================================
    // FINAL RESULT & RANKING
    // ======================================

    if (!isset($_SESSION['key'])
        || $_SESSION['key'] != 'suryapinky') {

        $q = mysqli_query($con,
            "SELECT score FROM rank WHERE email='$email'"
        ) or die(mysqli_error($con));

        if (mysqli_num_rows($q) == 0) {

            mysqli_query($con,
                "INSERT INTO rank VALUES ('$email','$s',NOW())"
            ) or die(mysqli_error($con));

        } else {

            mysqli_query($con,
                "UPDATE rank SET
                score = score + $s,
                time=NOW()
                WHERE email='$email'"
            ) or die(mysqli_error($con));
        }
    }

    header("location:welcome.php?q=result&eid=$eid");
    exit();
}


// ==========================================
// 6. RETAKE QUIZ
// ==========================================

if (isset($_GET['q']) && $_GET['q'] == 'quizre'
    && isset($_GET['step']) && $_GET['step'] == 25) {

    $eid = mysqli_real_escape_string($con, $_GET['eid']);

    $t = (int)$_GET['t'];

    // Get previous score
    $q = mysqli_query($con,
        "SELECT score FROM history
         WHERE eid='$eid' AND email='$email'"
    ) or die(mysqli_error($con));

    $s = 0;

    if (mysqli_num_rows($q) > 0) {

        $row = mysqli_fetch_assoc($q);
        $s = (int)$row['score'];
    }

    // Delete previous history
    mysqli_query($con,
        "DELETE FROM history
         WHERE eid='$eid' AND email='$email'"
    ) or die(mysqli_error($con));

    // Remove previous quiz score from ranking
    mysqli_query($con,
        "UPDATE rank SET
        score = GREATEST(score - $s, 0),
        time=NOW()
        WHERE email='$email'"
    ) or die(mysqli_error($con));

    // Restart quiz
    header("location:welcome.php?q=quiz&step=2&eid=$eid&n=1&t=$t");
    exit();
}

?>
```