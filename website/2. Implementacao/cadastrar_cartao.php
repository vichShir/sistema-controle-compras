<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Cadastrar Cartão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="resources/css/main-style.css" rel="stylesheet" type="text/css"/>
    <link href="resources/css/form-style.css" rel="stylesheet" type="text/css"/>
    <link href="resources/css/footer-style.css" rel="stylesheet" type="text/css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </head>

  <body>

    <!-- Cabeçalho -->
    <header style="height: 90px"><h1>Sistema de Controle de Compras</h1></header>

    <!-- Formulário de Login -->
    <section class="sec-panel sec-form">
        <h2>Cadastrar Cartão</h2>
        <hr>
        
        <form name="formulario" action="cadastrar_cartao.php" method="POST">

            <?php
                require("resources/php/database.php");
                require("resources/php/form.php");

                $form = new Form();
                Form::$current_step = 0;
                #session_unset();

                if(isset($_SESSION['enviado']))
                {
                    session_unset();
                }

                /* CONDICOES */
                $form->registrar_condicao('ct_digitos', function() {
                    Form::$current_step = 1;
                });

                /* SESSOES */
                $form->registrar_sessao(0, (function() {
                    echo Form::show_cartao();
                }));

                $form->registrar_sessao(1, (function() {
                    # Armazenar os valores de cartao
                    $cartao = new Cartao(
                        $_POST['ct_digitos'] ?? '',
                        $_POST['ct_bandeira'] ?? '',
                        $_POST['ct_banco'] ?? '',
                        $_POST['ct_dtvalidade'] ?? '',
                    );

                    # Cadastrar dados no BD
                    $db = new Database();

                    // Cartao
                    $db->executeCommand("INSERT INTO cartao (final, bandeira, bancoemissor, dtvalidade)
                                        VALUES ('" . $cartao->lastdigits . "',
                                                '" . $cartao->bandeira . "',
                                                '" . $cartao->bancoemissor . "',
                                                '" . $cartao->dtvalidade . "')");

                    $cartao_id = $db->getLastID();
                    echo "<p>Cartão cadastrado com sucesso! Nº " . $cartao_id . "</p><br>";
                    
                    $db->close();

                    $_SESSION['enviado'] = 1;
                }));
            ?>

            <p><input id='form-button' type='submit' value=<?php echo isset($_SESSION['submeter']) ? 'Enviar' : 'Próximo' ?>></p>
        </form>
        <p><a href="index.php">Voltar</a></p>
    </section>

    <!-- Rodapé -->
    <footer>
        <!-- Rodapé principal -->
        <div class="ft-topics">
            <!-- About -->
            <section class="ft-about">
                <h3>SOBRE</h3>
                <p>Website para preenchimento manual do banco de dados das informações da nota fiscal.</p>
            </section>
            <!-- Devs -->
            <section class="ft-devs">
                <h3>DESENVOLVEDOR</h3>
                <ul>
                    <li>vichShir</li>
                </ul>
            </section>
        </div>
      
        <!-- Rodapé inferior -->
        <div class="ft-info">
            <p>SCC - Sistema de Controle de Compras - 2021</p>
        </div>
    </footer>

  </body>
</html>