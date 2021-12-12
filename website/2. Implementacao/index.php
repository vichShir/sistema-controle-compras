<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Cadastrar Nota Fiscal</title>
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
        <h2>Cadastrar Nota Fiscal</h2>
        <hr>
        
        <form name="formulario" action="index.php" method="POST">
            <?php
                require("resources/php/form.php");

                $form = new Form();
                Form::$current_step = 0;

                if($_SESSION['enviado'] === 1)
                {
                    session_unset();
                }

                /* CONDICOES */
                $form->registrar_condicao('pessoa_associada', function() {
                    if($_POST['pessoa_associada'] === "novo-pj")
                    {
                        Form::$current_step = 1;
                    }
                    else
                    {
                        Form::$current_step = 2;
                    }
                });

                $form->registrar_condicao('pj_nome', function() {
                    Form::$current_step = 2;
                });

                $form->registrar_condicao('inf_cod', function() {
                    if($_POST['itemnotafiscal'] === 'sim')
                    {
                        Form::$current_step = 2;
                    }
                    else
                    {
                        Form::$current_step = 3;
                    }
                });

                $form->registrar_condicao('ft_pagamento', function() {
                    if($_POST['maisfaturas'] === 'sim')
                    {
                        Form::$current_step = 3;
                    }
                    else
                    {
                        Form::$current_step = 4;
                        $_SESSION['submeter'] = 'sim';
                    }
                });

                /* SESSOES */
                $form->registrar_sessao(0, (function() {
                    echo Form::form_notafiscal();
                }));

                $form->registrar_sessao(1, (function() {
                    # Armazenar os valores de NF
                    $nota = new NotaFiscal(
                        $_POST['nf_data'] ?? '',
                        $_POST['nf_desconto'] ?? '',
                        $_POST['nf_estado'] ?? '',
                        $_POST['nf_municipio'] ?? '',
                        $_POST['nf_bairro'] ?? '',
                        $_POST['nf_logradouro'] ?? ''
                    );
                    $_SESSION['nota_fiscal'] = serialize($nota);

                    if($_POST['pessoa_associada'] === "novo-pj")
                        echo Form::form_pessoajuridica();
                    else
                        echo Form::form_pessoafisica();

                    echo Form::form_endereco();
                }));

                $form->registrar_sessao(2, (function() {
                    # Armazenar os valores de NF
                    if(!isset($_SESSION['nota_fiscal']))
                    {
                        $nota = new NotaFiscal(
                            $_POST['nf_data'] ?? '',
                            $_POST['nf_desconto'] ?? '',
                            $_POST['nf_estado'] ?? '',
                            $_POST['nf_municipio'] ?? '',
                            $_POST['nf_bairro'] ?? '',
                            $_POST['nf_logradouro'] ?? ''
                        );
                        echo 'Nota Fiscal: <br>';
                        print_r($nota);
                        echo '<br>';
                        $_SESSION['nota_fiscal'] = serialize($nota);
                    }

                    # Armazenar os valores de pessoa juridica
                    if(!isset($_SESSION['pessoa_juridica']))
                    {
                        $pj = new PessoaJuridica(
                            $_POST['pj_nome'] ?? '',
                            $_POST['pj_nomefantasia'] ?? '',
                            $_POST['pj_cnpj'] ?? '',
                            $_POST['ps_estado'] ?? '',
                            $_POST['ps_municipio'] ?? '',
                            $_POST['ps_bairro'] ?? '',
                            $_POST['ps_logradouro'] ?? ''
                        );
                        echo 'Pessoa Juridica: <br>';
                        print_r($pj);
                        echo '<br>';
                        $_SESSION['pessoa_juridica'] = serialize($pj);
                    }

                    # Armazenar os valores do item de nota fiscal
                    $item = new ItemNotaFiscal(
                        $_POST['inf_cod'] ?? '',
                        $_POST['inf_unidade'] ?? '',
                        $_POST['inf_quantidade'] ?? '',
                        $_POST['inf_desconto'] ?? '',
                        $_POST['inf_descricao'] ?? '',
                        $_POST['inf_valorunitario'] ?? ''
                    );

                    if(!is_empty($item))
                    {
                        $all_items = retrieve_array('all_items');
                        array_push($all_items, serialize($item));
                        echo 'Itens: <br>';
                        print_r($all_items);
                        $_SESSION['all_items'] = $all_items;
                    }

                    echo Form::show_itemnotafiscal();
                }));

                $form->registrar_sessao(3, (function() {
                    # Armazenar os valores do item de nota fiscal
                    $item = new ItemNotaFiscal(
                        $_POST['inf_cod'] ?? '',
                        $_POST['inf_unidade'] ?? '',
                        $_POST['inf_quantidade'] ?? '',
                        $_POST['inf_desconto'] ?? '',
                        $_POST['inf_descricao'] ?? '',
                        $_POST['inf_valorunitario'] ?? ''
                    );

                    if(!is_empty($item))
                    {
                        $all_items = retrieve_array('all_items');
                        array_push($all_items, serialize($item));
                        echo 'Itens: <br>';
                        print_r($all_items);
                        $_SESSION['all_items'] = $all_items;
                    }

                    # Armazenar os valores de fatura
                    $fatura = new Fatura(
                        $_POST['ft_pagamento'] ?? '',
                        $_POST['ft_dtvencimento'] ?? '',
                        $_POST['ft_dtpagamento'] ?? '',
                        $_POST['ft_valor'] ?? ''
                    );

                    if(isset($_POST['ft_pagamento']))
                    {
                        $all_faturas = retrieve_array('all_faturas');
                        array_push($all_faturas, serialize($fatura));
                        echo 'Faturas: <br>';
                        print_r($all_faturas);
                        $_SESSION['all_faturas'] = $all_faturas;
                    }

                    echo Form::show_fatura();
                }));

                $form->registrar_sessao(4, (function() {
                    # Armazenar os valores de fatura
                    $fatura = new Fatura(
                        $_POST['ft_pagamento'] ?? '',
                        $_POST['ft_dtvencimento'] ?? '',
                        $_POST['ft_dtpagamento'] ?? '',
                        $_POST['ft_valor'] ?? ''
                    );

                    $all_faturas = retrieve_array('all_faturas');
                    array_push($all_faturas, serialize($fatura));
                    $_SESSION['all_faturas'] = $all_faturas;

                    $nota = unserialize($_SESSION['nota_fiscal']);
                    $pj = unserialize($_SESSION['pessoa_juridica']);
                    # Print nota fiscal
                    echo "<h3>" . $pj->pj_nome . "</h3>
                    <p>CNPJ: " . $pj->pj_cnpj . "</p>
                    <p>" . $pj->pj_logradouro . ", " . $pj->pj_bairro . ", " . $pj->pj_municipio . ", " . $pj->pj_estado . "</p>
                    <table class='table table-dark table-striped table-hover'>
                        <thead>
                            <tr>
                                <th scope='col'>#</th>
                                <th scope='col'>Código</th>
                                <th scope='col'>Descrição</th>
                                <th scope='col'>Quantidade</th>
                                <th scope='col'>Unidade</th>
                                <th scope='col'>Valor Unitário</th>
                                <th scope='col'>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>";
                        $itens_total = array();
                        $itens_descontos = array();
                        for($i = 0; $i < sizeof($_SESSION['all_items']); $i++)
                        #foreach($_SESSION['all_items'] as $item)
                        {
                            $item = $_SESSION['all_items'][$i];
                            $item_nf = unserialize($item);
                            $total = $item_nf->quantidade * $item_nf->valorunitario;
                            $desconto = $item_nf->desconto;
                            array_push($itens_total, $total);
                            array_push($itens_descontos, $desconto);
                            echo "<tr>";
                            echo "
                            <th scope='row'>" . $i . "</th>
                            <td>" . $item_nf->cod . "</td>
                            <td>" . $item_nf->descricao . "</td>
                            <td>" . $item_nf->quantidade . "</td>
                            <td>" . $item_nf->unidade . "</td>
                            <td>" . $item_nf->valorunitario . "</td>
                            <td>" . $total . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>
                    </table>";
                    $itens_total = floatval(array_sum($itens_total));
                    $itens_descontos = floatval(array_sum($itens_descontos));

                    echo "<p>Qtd. total de itens: " . sizeof($_SESSION['all_items']) . "</p>";
                    echo "<p>Valor total R$: " . $itens_total . "</p>";
                    echo "<p>Descontos R$: " . $itens_descontos . "</p>";
                    echo "<p>Valor a pagar R$: " . ($itens_total - $itens_descontos) . "</p>";
                    $_SESSION['enviado'] = 1;
                }));

                function retrieve_array($index)
                {
                    return (isset($_SESSION[$index]) ? $_SESSION[$index] : array());
                }

                function array_push_object($array, $obj)
                {
                    return array_push($array, serialize($obj));
                }

                function is_empty($array)
                {
                    foreach($array as $field)
                    {
                        if($field === '')
                            return true;
                    }
                    return false;
                }
            ?>

            <!--h3>pj_nome</h3>
            <p>CNPJ: pj_cnpj</p>
            <p>pj_logradouro, pj_bairro, pj_municipio, pj_estado</p>

            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Código</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Quantidade</th>
                        <th scope="col">Unidade</th>
                        <th scope="col">Valor Unitário</th>
                        <th scope="col">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>cod</td>
                        <td>descricao</td>
                        <td>quantidade</td>
                        <td>unidade</td>
                        <td>valorunitario</td>
                        <td>quantidade * valorunitario</td>
                    </tr>
                </tbody>
            </table-->

            <script>
                $(document).ready(function() {
                    $("#radio-endereco-nao").on('change', function () { 
                        $('#form-append').append("<div class='form-endereco'>\
                        <h3>Endereço na Nota</h3>\
                        <p class='form-input'>Estado (*)</p>\
                        <input type='text' name='nf_estado' placeholder='SP' size='2' maxlength='2' required>\
                        <p class='form-input'>Município (*)</p>\
                        <input type='text' name='nf_municipio' placeholder='São Paulo' size='20' maxlength='20' required>\
                        <p class='form-input'>Bairro (*)</p>\
                        <input type='text' name='nf_bairro' placeholder='Centro' size='30' maxlength='30' required>\
                        <p class='form-input'>Logradouro (*)</p>\
                        <input type='text' name='nf_logradouro' placeholder='Av. Paulista, 123' size='40' maxlength='40' required></div>");  
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