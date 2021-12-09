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
            $decision;

            if(isset($_POST['pessoa_associada']))
            {
                $current_step = 1;
                if($_POST['pessoa_associada'] === "novo")
                {
                    $decision = false;
                }
                else
                {
                    $decision = true;
                }

                unset($_POST['estado']);
            }

            if(isset($_POST['pj_nome']))
            {
                $current_step = 2;
                if($_POST['msmendereco'] === "nao")
                {
                    $decision = false;
                }
                else
                {
                    $decision = true;
                }
            }

            if(isset($_POST['estado']))
            {
                $current_step = 3;
            }

            if($current_step === 0)
            {
                echo $current_step;
                echo $form->form_notafiscal();
                echo "<p class='form-input'>Deseja utilizar o mesmo endereço da pessoa cadastrada? (*)</p>
                <div class='radio-option' id='radio-endereco-sim'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='sim'><p>Sim</p></div>
                <div class='radio-option' id='radio-endereco-nao'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='nao'><p>Não</p></div>";
                echo "<div id='form-append'></div>";
            }
            else if($current_step === 1)
            {
                echo $current_step;
                if(!$decision)
                {
                    echo $form->form_pessoajuridica();
                    echo "<p class='form-input'>Deseja utilizar o mesmo endereço da nota fiscal? (*)</p>
                <div class='radio-option'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='sim'><p>Sim</p></div>
                <div class='radio-option'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='nao'><p>Não</p></div>";
                }
                else
                {
                    echo $form->show_itemnotafiscal();
                }
            }
            else if($current_step === 2)
            {
                echo $current_step;
                if(!$decision)
                {
                    echo $form->form_endereco();
                }
                else
                {
                    echo $form->show_itemnotafiscal();
                }
            }
            else if($current_step === 3)
            {
                echo $current_step;
                echo $form->show_itemnotafiscal();
            }
        ?>

            <!--div id='form-nf'>
                <h3>Nota Fiscal</h3>
                <p class='form-input'>Data (*)</p>
                <input type='date' name='nf_data' required>
                <p class='form-input'>Desconto (*)</p>
                <input type='number' name='nf_desconto' placeholder='R$000,00' min='0.00' max='999.999' step='0.01' required>
                <p class='form-input'>Deseja utilizar o endereço para a nota fiscal? (*)</p>
                <div class='radio-option'><input type='radio' class='radio-input' name='msmendereco' onclick="" value='sim'><p>Sim</p></div>
                <div class='radio-option'><input type='radio' class='radio-input' name='msmendereco' onclick="" value='nao'><p>Não</p></div>
                <p class='form-input'>Pessoa associada (*)</p>
                <select name='pessoa_associada' required>
                    <option value='novo'>CADASTRAR NOVO</option>
                    <option value='enxuto'>ENXUTO</option>
                    <option value='santa-rita'>SANTA RITA</option>
                </select>
            </div-->

            <script>
                $(document).ready(function(){
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

            <p><input id='form-button' type='submit' value='Próximo'></p>

        <!--
            <h3>Pessoa Jurídica</h3>
            <p class="form-input">Nome Jurídico (*)</p>
            <input type="text" name="pj_nome" placeholder="Nome" size="60" maxlength="60" required>
            <p class="form-input">Nome Fantasia</p>
            <input type="text" name="pj_nomefantasia" placeholder="Nome Fantasia" size="50" maxlength="50">
            <p class="form-input">CNPJ (*)</p>
            <input type="text" name="pj_cnpj" placeholder="00000000000000" pattern="[0-9]{3}[0-9]{3}[0-9]{3}[0-9]{5}" size="14" maxlength="14" required>

            <h3>Endereço</h3>
            <p class="form-input">Estado (*)</p>
            <input type="text" name="estado" placeholder="SP" size="2" maxlength="2" required>
            <p class="form-input">Município (*)</p>
            <input type="text" name="municipio" placeholder="São Paulo" size="20" maxlength="20" required>
            <p class="form-input">Bairro (*)</p>
            <input type="text" name="bairro" placeholder="Centro" size="30" maxlength="30" required>
            <p class="form-input">Logradouro (*)</p>
            <input type="text" name="logradouro" placeholder="Av. Paulista, 123" size="40" maxlength="40" required>

            <h3>Item da Nota Fiscal</h3>
            <p class="form-input">Código (*)</p>
            <input type="text" name="inf_cod" placeholder="0000000000000000" size="16" maxlength="16" required>
            <p class="form-input">Unidade (*)</p>
            <select name="inf_unidade" required>
              <option value="unitario">UNITÁRIO</option>
              <option value="quilograma">KG</option>
            </select>
            <p class="form-input">Quantidade (*)</p>
            <input type="number" name="inf_quantidade" placeholder="000,000" min="0.000" max="999.999" step="0.001" required>
            <p class="form-input">Desconto (*)</p>
            <input type="number" name="inf_desconto" placeholder="R$000,00" min="0.00" max="999.999" step="0.01" required>
            <p class="form-input">Descrição (*)</p>
            <input type="text" name="inf_descricao" placeholder="Aveia" size="100" maxlength="100" required>
            <p class="form-input">Valor Unitário (*)</p>
            <input type="number" name="inf_valorunitario" placeholder="R$00000,00" min="0.00" max="99999.99" step="0.01" required>

            <h3>Fatura</h3>
            <p class="form-input">Forma de Pagamento (*)</p>
            <select name="ft_pagamento" required>
              <option value="unitario">DÉBITO</option>
              <option value="quilograma">CRÉDITO</option>
              <option value="quilograma">À VISTA</option>
            </select>
            <p class="form-input">Data de Vencimento (*)</p>
            <input type="date" name="ft_dtvencimento" required>
            <p class="form-input">Data de Pagamento</p>
            <input type="date" name="ft_dtpagamento">
            <p class="form-input">Valor (*)</p>
            <input type="number" name="ft_valor" placeholder="R$000000,00" min="0.00" max="999999.99" step="0.01" required>
        -->
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