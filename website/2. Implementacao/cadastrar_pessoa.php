<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Cadastrar Pessoa</title>
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
        <h2>Cadastrar Pessoa</h2>
        <hr>
        
        <form name="formulario" action="cadastrar_pessoa.php" method="POST">

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
                $form->registrar_condicao('ps_estado', function() {
                    Form::$current_step = 1;
                });

                /* SESSOES */
                $form->registrar_sessao(0, (function() {
                    echo "<p class='form-input'>Cadastrar Pessoa Jurídica ou Física? (*)</p>
                            <div class='radio-option' id='radio-pf-nao'><input type='radio' class='radio-input' name='escolherpessoa' onclick='' value='juridica' required><p>Jurídica</p></div>
                            <div class='radio-option' id='radio-pf-sim'><input type='radio' class='radio-input' name='escolherpessoa' onclick='' value='fisica'><p>Física</p></div> .
                            <div id='form-append'></div>";
                    echo Form::form_endereco();
                }));

                $form->registrar_sessao(1, (function() {
                    # Armazenar os valores de pessoa
                    if(isset($_POST['pj_nome']))
                    {
                        $pj = new PessoaJuridica(
                            -1,
                            $_POST['pj_nome'] ?? '',
                            $_POST['pj_nomefantasia'] ?? '',
                            $_POST['pj_cnpj'] ?? '',
                            $_POST['ps_estado'] ?? '',
                            $_POST['ps_municipio'] ?? '',
                            $_POST['ps_bairro'] ?? '',
                            $_POST['ps_logradouro'] ?? ''
                        );

                        # Cadastrar dados no BD
                        $db = new Database();

                        // Pessoa Juridica
                        exec_pj_stored_procedure($db, $pj->pj_nome, $pj->pj_cnpj, $pj->pj_nomefantasia, $pj->pj_estado, $pj->pj_municipio, $pj->pj_bairro, $pj->pj_logradouro);
                        $result = $db->getAllRowsFromQuery("SELECT IDENT_CURRENT('pessoa')");
                        $pessoa_id = $result[0][''];
                        echo "<p>Pessoa Jurídica cadastrada com sucesso! Nº " . $pessoa_id . "</p><br>";
                    }
                    else
                    {
                        $pf = new PessoaFisica(
                            -1,
                            $_POST['pf_nome'] ?? '',
                            $_POST['pf_cpf'] ?? '',
                            $_POST['pf_email'] ?? '',
                            $_POST['pf_telefone'] ?? '',
                            $_POST['ps_estado'] ?? '',
                            $_POST['ps_municipio'] ?? '',
                            $_POST['ps_bairro'] ?? '',
                            $_POST['ps_logradouro'] ?? ''
                        );

                        # Cadastrar dados no BD
                        $db = new Database();

                        // Pessoa Fisica
                        exec_pf_stored_procedure($db, $pf->nome, $pf->cpf, $pf->email, $pf->telefone, $pf->estado, $pf->municipio, $pf->bairro, $pf->logradouro);
                        $result = $db->getAllRowsFromQuery("SELECT IDENT_CURRENT('pessoa')");
                        $pessoa_id = $result[0][''];
                        echo "<p>Pessoa Física cadastrada com sucesso! Nº " . $pessoa_id . "</p><br>";
                    }
                    
                    $db->close();

                    $_SESSION['enviado'] = 1;
                }));

                function exec_pj_stored_procedure($db, $pj_nome, $pj_cnpj, $pj_nomefantasia, $pj_estado, $pj_municipio, $pj_bairro, $pj_logradouro)
                {
                    $sth = $db->conn->prepare("SET NOCOUNT ON; EXEC ins_pessoa_juridica ?, ?, ?, ?, ?, ?, ?;");
                    $sth->bindParam(1, $pj_nome);
                    $sth->bindParam(2, $pj_cnpj);
                    $sth->bindParam(3, $pj_nomefantasia);
                    $sth->bindParam(4, $pj_estado);
                    $sth->bindParam(5, $pj_municipio);
                    $sth->bindParam(6, $pj_bairro);
                    $sth->bindParam(7, $pj_logradouro);
                    $sth->execute();
                    $sth->nextRowset();
                }

                function exec_pf_stored_procedure($db, $nome, $cpf, $email, $telefone, $estado, $municipio, $bairro, $logradouro)
                {
                    $sth = $db->conn->prepare("SET NOCOUNT ON; EXEC ins_pessoa_fisica ?, ?, ?, ?, ?, ?, ?, ?;");
                    $sth->bindParam(1, $nome);
                    $sth->bindParam(2, $cpf);
                    $sth->bindParam(3, $email);
                    $sth->bindParam(4, $telefone);
                    $sth->bindParam(5, $estado);
                    $sth->bindParam(6, $municipio);
                    $sth->bindParam(7, $bairro);
                    $sth->bindParam(8, $logradouro);
                    $sth->execute();
                    $sth->nextRowset();
                }
            ?>

            <script>
                $(document).ready(function() {
                    $("#radio-pf-sim").on('change', function () {
                        $('.form-pessoa').remove(); 
                        $('#form-append').append("<div class='form-pessoa'><h3>Pessoa Física</h3>\
                            <p class='form-input'>Nome (*)</p>\
                            <input type='text' name='pf_nome' placeholder='Nome' size='60' maxlength='60' required>\
                            <p class='form-input'>CPF (*)</p>\
                            <input type='text' name='pf_cpf' placeholder='00000000000' size='11' maxlength='11'>\
                            <p class='form-input'>Email (*)</p>\
                            <input type='email' name='pf_email' placeholder='user@email.com' size='80' maxlength='80' required>\
                            <p class='form-input'>Telefone (*)</p>\
                            <input type='text' name='pf_telefone' placeholder='11000000000' size='11' maxlength='11' required></div>");   
                    });

                    $("#radio-pf-nao").on('change', function () {
                        $('.form-pessoa').remove(); 
                        $('#form-append').append("<div class='form-pessoa'><h3>Pessoa Jurídica</h3>\
                            <p class='form-input'>Nome Jurídico (*)</p>\
                            <input type='text' name='pj_nome' placeholder='Nome' size='60' maxlength='60' required>\
                            <p class='form-input'>Nome Fantasia</p>\
                            <input type='text' name='pj_nomefantasia' placeholder='Nome Fantasia' size='50' maxlength='50'>\
                            <p class='form-input'>CNPJ (*)</p>\
                            <input type='text' name='pj_cnpj' placeholder='00000000000000' pattern='[0-9]{3}[0-9]{3}[0-9]{3}[0-9]{5}' size='14' maxlength='14' required></div>");  
                    });
                });
            </script>

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