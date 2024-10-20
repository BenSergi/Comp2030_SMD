<?php 
$home = array(
    'Administrator' => 'manage.php',
    'Auditor' => 'users.php',
    'Factory Manager' => 'factory.php',
    'Production Operator' => 'factory.php'
);

if (isset($_POST['login'])) { 
    require_once '../include/database.php';
    $sql = 'SELECT personid, firstname, lastname, position, pin FROM Person WHERE NOT isArchived;'; // Do not get archived users
    $result = mysqli_query($conn, $sql);
    if ($result && $rows = mysqli_num_rows($result)) {
        while ($assoc = mysqli_fetch_assoc($result)) {
            $pin;
            if ($_GET['machineID'] == 0) {
                $pin = $_POST['pin-1'] . $_POST['pin-2'] . $_POST['pin-3'] . $_POST['pin-4'];
            } else {
                $pin = $_POST['pin'];
            }
            if (password_verify($pin, $assoc['pin'])) {
                session_start();
                $_SESSION['id'] = $assoc['personid']; 
                $_SESSION['username'] = $assoc['firstname'] . ' ' . $assoc['lastname'];
                $_SESSION['position'] = $assoc['position'];
                $_SESSION['home'] = $home[$_SESSION['position']];
                header("location: ../pages/{$_SESSION['home']}?machineID={$_GET['machineID']}");
                mysqli_free_result($result);
                mysqli_close($conn);
                exit;
            }
        }
    }
    mysqli_free_result($result);
}
if ($_GET['machineID'] == 0) {
    header('location: ../pages/login-desktop.php?machineID=0&bad_pin=1');
} else {
    header("location: ../pages/login.php?machineID={$_GET['machineID']}&bad_pin=1");
}
mysqli_close($conn);
exit;
