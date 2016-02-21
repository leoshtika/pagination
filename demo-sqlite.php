<?php

require_once 'vendor/autoload.php';

use leoshtika\libs\Pagination;
use leoshtika\libs\Sqlite;
use leoshtika\libs\UserFaker;

$sqliteFile = 'demo.sqlite';

// Create a new sqlite db if not exists and load some dummy data. 
// After the database is created, you don't need this line of code anymore
UserFaker::create($sqliteFile, 120);

$dbh = Sqlite::connect($sqliteFile);

// Get the total number of records
$countSth = $dbh->prepare('SELECT count(*) FROM user');
$countSth->execute();
$totalRecords = $countSth->fetch(PDO::FETCH_COLUMN);

// Instantiate the Pagination
$pagination = new Pagination($_GET['page'], $totalRecords, 10);

// Get records using the pagination
$sth = $dbh->prepare('SELECT * FROM user LIMIT :offset, :records');
$sth->bindValue(':records', $pagination->getRecordsPerPage(), PDO::PARAM_INT);
$sth->bindValue(':offset', $pagination->offset(), PDO::PARAM_INT);
$sth->execute();
$users = $sth->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pagination</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body class="container-fluid">
    
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

