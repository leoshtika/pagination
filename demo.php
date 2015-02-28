<?php

require_once 'vendor/autoload.php';


use leoshtika\libs\DB;
use leoshtika\libs\Pagination;

// DB::instance()->connectMysql('localhost', 'db', 'root', '');
DB::instance()->connectSqlite('data/db.sqlite');

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

<?php








//$faker = Faker\Factory::create();
//try {
//    
//    for ($i=1; $i<=123; $i++) {
//        $sth = DB::instance()->dbh()->prepare('INSERT INTO user (name, email, address, phone) VALUES (:name, :email, :address, :phone)');
//        $sth->bindParam(':name', $faker->name, PDO::PARAM_STR);
//        $sth->bindParam(':email', $faker->email, PDO::PARAM_STR);
//        $sth->bindParam(':address', $faker->address, PDO::PARAM_STR);
//        $sth->bindParam(':phone', $faker->phoneNumber, PDO::PARAM_STR);
//        $sth->execute();
//    }
//    
//} catch (PDOException $ex) {
//	echo 'There is a problem with your query';
//	Logger::add($ex->getMessage(), Logger::LEVEL_WARNING);
//}

