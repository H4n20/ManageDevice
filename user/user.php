<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thiết bị</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/menu.css">
    <link rel="stylesheet" href="../style/home.css">
    <link rel="stylesheet" href="../style/edit.php">
    <link rel="stylesheet" href="../style/search.php">

</head>
<body>

    <!-- Header -->
    <div id="header" style='text-align:center'>
        <?php include('../template/header2.php'); ?>
    </div>

    <!-- Main Content Area -->
    <div id="main">
        <!-- Left Sidebar/Menu -->
        <div id="menu" style="width: 25%;float:left;">
            <?php include('../template/menu2.php'); ?>
        </div>

        <!-- Main Content -->
        <div id="content" style="width: 75%;float:right;">
            <?php include('../template/center.php'); ?>
        </div>
    </div>

</body>
</html>

