<?php

require_once 'vendor/autoload.php';

use leoshtika\libs\Pagination;
use leoshtika\libs\DB;
use leoshtika\libs\UserFaker;

// ---------- MySQL -------------------
// DB::instance()->connectMysql('localhost', 'db', 'root', '');
// ------------------------------------

// ---------- SQLite ------------------
// Create a new sqlite db if not exists and load some dummy data
$sqliteFile = 'demo.sqlite';
if (!file_exists($sqliteFile)) {
	UserFaker::create($sqliteFile);
}
DB::instance()->connectSqlite($sqliteFile);
// ------------------------------------

$countHandler = DB::instance()->dbh()->prepare('SELECT count(*) AS count FROM user');
$countHandler->execute();
$totalRecords = $countHandler->fetch(PDO::FETCH_ASSOC);
$pagination = new Pagination($_GET['page'], $totalRecords['count'], 10);


$sth = DB::instance()->dbh()->prepare('SELECT * FROM user LIMIT :offset, :records');
$sth->bindValue(':records', $pagination->getRecordsPerPage(), PDO::PARAM_INT);
$sth->bindValue(':offset', $pagination->offset(), PDO::PARAM_INT);
$sth->execute();
$users = $sth->fetchAll(PDO::FETCH_OBJ);

// @TODO: Fix bug in leoshtika/database
//$users = DB::instance()->query('SELECT * FROM user LIMIT ?, ?', array(
//    $pagination->getRecordsPerPage(),
//    $pagination->offset()
//));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pagination</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body>
    <h1>Pagination!</h1>
    <table class="table table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Phone</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo $user->id; ?></td>
                <td><?php echo $user->name; ?></td>
                <td><?php echo $user->email; ?></td>
                <td><?php echo $user->address; ?></td>
                <td><?php echo $user->phone; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php echo $pagination->nav(); ?>
</body>
</html>

