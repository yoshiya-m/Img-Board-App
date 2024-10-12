<?php
use Helpers\Settings;
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style.css">
    <title>Image Board Service</title>
</head>

<body>
    <main class="container-fluid  px-0">
        <div class="d-flex align-items-center justify-content-around bg-info text-center py-2">
             <h1><a href="<?= Settings::env('BASE_URL')?>" class="text-decoration-none text-dark">Image Board Service</a></h1>
            <a href="<?= Settings::env('BASE_URL') . "/post-form" ?>" type="button" class="btn btn-success text-center" >投稿作成</a>
        </div>