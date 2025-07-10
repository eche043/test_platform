<?php
include "_configs/Classes/UTILISATEURS.php";
include "_configs/Includes/Titles.php";
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'bootstrap/dist/css/bootstrap.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'@fortawesome/fontawesome-free/css/all.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'@fortawesome/fontawesome-free/css/fontawesome.min.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= CSS.'ecmu.css';?>" />
    <link rel="icon" type="image/ico" href="<?= IMAGES.'favicon.ico';?>" />
    <title><?= TITLE;?></title>
</head>
<body id="page">
<?php include "_configs/Includes/Menu.php";?>