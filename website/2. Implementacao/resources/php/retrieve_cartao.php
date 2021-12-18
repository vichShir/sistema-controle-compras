<?php
    require 'database.php';
    session_start();

    $cartao = (isset($_POST['cartao'])) ? $_POST['cartao'] : 'vazio';

    if(isset($_POST['cartao']))
    {
        // Gravar partida
        $db = new Database();
        $cartoes = $db->getAllRowsFromQuery("SELECT codcartao, final, bandeira FROM cartao");
        $db->close();
    }

    $result = [
        'cartoes' =>  $cartoes
    ];
    echo json_encode($result);
?>