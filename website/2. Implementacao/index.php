<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Cadastrar Nota Fiscal</title>
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
        <h2>Cadastrar Nota Fiscal</h2>
        <hr>
        
        <form name="formulario" action="index.php" method="POST">
            <?php
                require("resources/php/form.php");

                $form = new Form();
                $current_step = 0;

                $form->current_step = 0;
                #$form->registrar_sessao(0,
                #   form::SHOW_NOTAFISCAL(),
                #   
                #);

                #$form->registrar_sessao(1,
                #   form::SHOW_PESSOAJURIDICA(),
                #   form::SHOW_ENDERECO()
                #);

                if(isset($_POST['pessoa_associada']))
                {
                    if($_POST['pessoa_associada'] === "novo-pj")
                    {
                        #$current_step = 1;
                        $form->current_step = 1;
                    }
                    else
                    {
                        #$current_step = 2;
                        $form->current_step = 2;
                    }
                }

                if(isset($_POST['pj_nome']))
                {
                    #$current_step = 2;
                    $form->current_step = 2;
                }

                if(isset($_POST['inf_cod']))
                {
                    if($_POST['itemnotafiscal'] === 'sim')
                    {
                        #$current_step = 2;
                        $form->current_step = 2;
                    }
                    else
                    {
                        #$current_step = 3;
                        $form->current_step = 3;
                    }
                }

                if(isset($_POST['ft_pagamento']))
                {
                    if($_POST['maisfaturas'] === 'sim')
                    {
                        #$current_step = 3;
                        $form->current_step = 3;
                    }
                    else
                    {
                        #$current_step = 4;
                        $form->current_step = 4;
                        $_SESSION['submeter'] = 'sim';
                    }
                }

                $form->registrar_sessao(0, (function() {
                    echo Form::form_notafiscal();
                }));

                $form->registrar_sessao(1, (function() {
                    # Armazenar os valores de NF
                    $_SESSION['nf_data'] = $_POST['nf_data'];

                    if($_POST['pessoa_associada'] === "novo-pj")
                        echo Form::form_pessoajuridica();
                    else
                        echo Form::form_pessoafisica();

                    echo Form::form_endereco();
                }));

                $form->registrar_sessao(2, (function() {
                    echo Form::show_itemnotafiscal();
                }));

                $form->registrar_sessao(3, (function() {
                    echo Form::show_fatura();
                }));

                $form->registrar_sessao(4, (function() {
                    echo Form::form_endereco();
                    session_destroy();
                }));

                if($current_step === 0)
                {
                    #echo $form->form_notafiscal();
                }
                else if($current_step === 1)
                {
                    # Armazenar os valores de NF
                    #$_SESSION['nf_data'] = $_POST['nf_data'];

                    #if($_POST['pessoa_associada'] === "novo-pj")
                    #    echo $form->form_pessoajuridica();
                    #else
                    #    echo $form->form_pessoafisica();

                    #echo $form->form_endereco();
                }
                else if($current_step === 2)
                {
                    echo $form->show_itemnotafiscal();
                }
                else if($current_step === 3)
                {
                    echo $form->show_fatura();
                }
                else if($current_step === 4)
                {
                    echo $form->form_endereco();
                    session_destroy();
                }
            ?>

            <script>
                $(document).ready(function() {
                    $("#radio-endereco-nao").on('change', function () { 
                        $('#form-append').append("<div class='form-endereco'>\
                        <h3>Endereço na Nota</h3>\
                        <p class='form-input'>Estado (*)</p>\
                        <input type='text' name='estado' placeholder='SP' size='2' maxlength='2' required>\
                        <p class='form-input'>Município (*)</p>\
                        <input type='text' name='municipio' placeholder='São Paulo' size='20' maxlength='20' required>\
                        <p class='form-input'>Bairro (*)</p>\
                        <input type='text' name='bairro' placeholder='Centro' size='30' maxlength='30' required>\
                        <p class='form-input'>Logradouro (*)</p>\
                        <input type='text' name='logradouro' placeholder='Av. Paulista, 123' size='40' maxlength='40' required></div>");  
                    });

                    $("#radio-endereco-sim").on('change', function () { 
                        $('.form-endereco').remove();  
                    });
                });
            </script>

            <p><input id='form-button' type='submit' value=<?php echo ($_SESSION['submeter'] === 'sim') ? 'Enviar' : 'Próximo' ?>></p>
        </form>
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