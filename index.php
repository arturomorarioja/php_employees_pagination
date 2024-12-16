<?php 

include 'view/header.php';

$view = trim($_GET['v'] ?? '');

if ($view === 'e') {
    include 'view/employee.php';
} else {
    include 'view/department.php';
}

include 'view/footer.php';