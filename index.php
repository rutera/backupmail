<?php
/**
 * Created by PhpStorm.
 * User: Neo
 * Date: 4/24/2018
 * Time: 5:50 PM
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');
$host = 'localhost';
$username = 'root';
$password = 'q3qPzQd13hp0';
$dbname = 'postfix';
$dbname2 = 'root_cwp';
$ok = '';


?>

<div class="container">

    <div class="panel-group">
        <h2>Panels with Backup Mails</h2>
        <h4>v. 1.0.0</h4>
    </div>
    <hr>

    <?php
    $conn = new mysqli($host, $username, $password, $dbname);
    $qq = $conn->query('SELECT * FROM mailbox');


    if ($_POST) {


        $user2 = '';
        $user2 = $conn->real_escape_string($_POST['user']);

        $conn2 = new mysqli($host, $username, $password, $dbname2);
        $qq2 = $conn2->query("SELECT * FROM user WHERE domain='$user2'");
        while ($row2 = $qq2->fetch_array()) {
            $user = $row2['username'];
            $dom = $row2['domain'];
        }

        if ($user2 == $dom) {
            $conn3 = new mysqli($host, $username, $password, $dbname);
            $qq3 = $conn3->query("SELECT * FROM mailbox WHERE domain='$user2'");
            echo '<div class="panel panel-default col-sm-10">';
            while ($row3 = $qq3->fetch_array()) {

                $today = date("YmdHi");

                $acc = $row3['local_part'];

                $rr = substr("/var/vmail/" . $row3['maildir'], 0, -1);
                shell_exec('tar -czf /home/' . $user . '/' . $today . '-' . $acc . '.tar.gz ' . $rr);
                shell_exec('mkdir /home/' . $user . '/backup_mail');
                shell_exec('mv /home/' . $user . '/' . $today . '-' . $acc . '.tar.gz /home/' . $user . '/backup_mail');
                shell_exec('chown -R ' . $user . ':' . $user . ' /home/' . $user . '/backup_mail');
                shell_exec('find /home/' . $user . ' -type f -name \'*.tar.gz\' | xargs chown ' . $user . ':' . $user . '');
                echo '<div class="panel-body">' . $acc . '@' . $dom . '</div>';
                $ok = '<br><br><div class="alert alert-success"><strong>Success!</strong> Backup is generated.</div>';
            }

            echo '</div>';

        }
    }


    ?>
    <form method="post">
        <div class="form-group col-sm-4">
            <label for="sel1">Select list (select one):</label>
            <select class="form-control" name="user">
                <?php
                $qq = $conn->query('SELECT * FROM domain');
                while ($row = $qq->fetch_array()) {
                    echo '<option>' . $row['domain'] . '</option>';
                }
                ?>

            </select>
            <br>
            <br>
            <button type="submit" class="btn btn-primary">Backup now</button>

            <?= $ok; ?>
        </div>
    </form>

</div>

<div class="container col-sm-10">
    <h2>Settings</h2>
    <hr>


</div>

</body>
</html>
