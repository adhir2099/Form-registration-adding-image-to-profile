<?php
require './dbcon.php';
require './functions.php';

function post($key) {
    return $_POST[$key] ?? null;
}

$action = post('action');

if ($action === "formRegistration") {

    $userName = trim(post('userName'));
    $email    = trim(post('email'));
    $password = trim(post('entryPass'));

    if (!$userName || !$email || !$password) {
        echo 'err';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'invalid_email';
        exit;
    }

    $passHash  = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    $userImage = makeImage(strtoupper($userName[0]));

    $query = "INSERT INTO users (userName, email, pass, userImg, createdAt)
              VALUES (:userName, :email, :pass, :userImg, NOW())";

    $statement = $connect->prepare($query);

    $result = $statement->execute([
        ':userName' => $userName,
        ':email'    => $email,
        ':pass'     => $passHash,
        ':userImg'  => $userImage
    ]);

    echo $result ? 'ok' : 'err';
}

// Load latest user
if ($action === "Load") {

    $qry = "SELECT userName, userImg FROM users ORDER BY id DESC LIMIT 1";
    $statement = $connect->prepare($qry);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row) {

        $userName = htmlspecialchars($row["userName"], ENT_QUOTES, 'UTF-8');
        $userImg  = htmlspecialchars($row["userImg"], ENT_QUOTES, 'UTF-8');

        echo '
        <picture>
            <img src="'.$userImg.'" class="img-fluid img-thumbnail rounded-circle" width="140" height="140" alt="User image">
        </picture>
        <p>User name:</p>
        <h2 class="fw-normal text-capitalize">'.$userName.'</h2>
        <a href="../index.html" class="btn btn-primary">Return</a>';
        
    } else {
        echo '
        <div class="alert alert-danger">
            <strong>Error</strong>
            <hr>
            <p>No info to show</p>
        </div>';
    }
}