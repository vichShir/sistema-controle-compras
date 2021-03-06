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
                    echo "<a href='cadastrar_pessoa.php'>Cadastrar Pessoa</a>
                            <a href='cadastrar_cartao.php'>Cadastrar Cartão</a>";
                    echo Form::form_notafiscal();
                }));

                $form->registrar_sessao(1, (function() {
                    # Armazenar os valores de NF
                    try_to_store_nf();

                    if($_POST['pessoa_associada'] === "novo-pj")
                        echo Form::form_pessoajuridica();
                    else if($_POST['pessoa_associada'] === "novo-pf")
                        echo Form::form_pessoafisica();

                    echo Form::form_endereco();
                }));

                $form->registrar_sessao(2, (function() {
                    # Armazenar os valores de NF
                    try_to_store_nf();

                    # Armazenar os valores de pessoa juridica
                    try_to_store_pj();

                    # Armazenar os valores do item de nota fiscal
                    try_to_store_inf();

                    echo Form::show_itemnotafiscal();
                }));

                $form->registrar_sessao(3, (function() {
                    # Armazenar os valores do item de nota fiscal
                    try_to_store_inf();

                    # Armazenar os valores de fatura
                    try_to_store_ft();

                    echo Form::show_fatura();
                }));

                $form->registrar_sessao(4, (function() {
                    # Armazenar os valores de fatura
                    try_to_store_ft();

                    $nota = unserialize($_SESSION['nota_fiscal']);
                    $pj = unserialize($_SESSION['pessoa_juridica']);
                    $all_items = $_SESSION['all_items'];
                    $all_faturas = $_SESSION['all_faturas'];
                    
                    # Print nota fiscal
                    print_nf($nota, $pj);

                    # Cadastrar dados no BD
                    $db = new StoredProcedure();

                    /*  Pegar:
                    codpessoa de pessoa juridica ou fisica
                    numnota da nota atual
                    codcartao se for debito ou credito
                    */
                    $codpessoa;
                    $numnota;

                    // Pessoa Juridica
                    if(isset($_SESSION['gravar_pj']))
                    {
                        $db->store_pessoa_juridica($pj);
                        $codpessoa = $db->getLastIDFrom('pessoa');
                        echo "New record created successfully. Last inserted ID pessoa is: " . $codpessoa . "<br>";
                    }

                    // Nota Fiscal
                    if($nota->msmendereco === 'sim')
                    {
                        $nota->endereco->setEstado($pj->endereco->getEstado());
                        $nota->endereco->setMunicipio($pj->endereco->getMunicipio());
                        $nota->endereco->setBairro($pj->endereco->getBairro());
                        $nota->endereco->setLogradouro($pj->endereco->getLogradouro());
                    }

                    $nota->set_codpessoa(isset($codpessoa) ? $codpessoa : $pj->codpessoa);
                    $db->store_nota_fiscal($nota);
                    $numnota = $db->getLastIDFrom('notafiscal');
                    echo "New record created successfully. Last inserted ID numnota is: " . $numnota;

                    // Fatura
                    foreach ($all_faturas as $fatura_un) 
                    {
                        $fatura = unserialize($fatura_un);
                        $db->store_fatura($fatura, $numnota);
                    }

                    // Itens de nota fiscal
                    foreach ($all_items as $item_un) 
                    {
                        $item = unserialize($item_un);
                        $db->store_item_nota_fiscal($item, $numnota);
                    }

                    $db->close();

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

                function try_to_store_nf()
                {
                    # Armazenar os valores de NF
                    if(!isset($_SESSION['nota_fiscal']))
                    {
                        $nota = new NotaFiscal(
                            $_POST['nf_data'] ?? '',
                            $_POST['nf_desconto'] ?? '',
                            $_POST['nf_estado'] ?? '',
                            $_POST['nf_municipio'] ?? '',
                            $_POST['nf_bairro'] ?? '',
                            $_POST['nf_logradouro'] ?? '',
                            $_POST['msmendereco'] ?? ''
                        );
                        $_SESSION['nota_fiscal'] = serialize($nota);
                    }
                }

                function try_to_store_pj()
                {
                    if(!isset($_SESSION['pessoa_juridica']))
                    {
                        # Criar PJ pelos dados do formulario
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
                            $_SESSION['gravar_pj'] = 1;
                        }
                        else # Ou pegar no BD
                        {
                            $db = new Database();
                            $sql = "SELECT p.codpessoa, p.nome, j.CNPJ, j.nomefantasia, e.estado, e.municipio, e.bairro, e.logradouro FROM pessoa p
                                        INNER JOIN pessoa_juridica j
                                            ON p.codpessoa = j.codpessoa
                                            INNER JOIN enderecopessoa ep
                                            ON ep.codpessoa = p.codpessoa
                                            INNER JOIN endereco e
                                            ON ep.codendereco = e.codendereco
                                        WHERE p.codpessoa = " . $_POST['pessoa_associada'];
                            $pessoa = $db->getRowFromQuery($sql);

                            $pj = new PessoaJuridica(
                                $pessoa['codpessoa'] ?? '',
                                $pessoa['nome'] ?? '',
                                $pessoa['nomefantasia'] ?? '',
                                $pessoa['CNPJ'] ?? '',
                                $pessoa['estado'] ?? '',
                                $pessoa['municipio'] ?? '',
                                $pessoa['bairro'] ?? '',
                                $pessoa['logradouro'] ?? ''
                            );
                        }
                        $_SESSION['pessoa_juridica'] = serialize($pj);
                    }
                }

                function try_to_store_inf()
                {
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
                        $_SESSION['all_items'] = $all_items;
                    }
                }

                function try_to_store_ft()
                {
                    $fatura = new Fatura(
                        $_POST['ft_pagamento'] ?? '',
                        $_POST['ft_dtvencimento'] ?? '',
                        $_POST['ft_dtpagamento'] ?? '',
                        $_POST['ft_valor'] ?? '',
                        $_POST['ft_cartao'] ?? ''
                    );

                    if(isset($_POST['ft_pagamento']))
                    {
                        $all_faturas = retrieve_array('all_faturas');
                        array_push($all_faturas, serialize($fatura));
                        $_SESSION['all_faturas'] = $all_faturas;
                    }
                }

                function print_nf($nota, $pj)
                {
                    echo "<h3>" . $pj->nome . "</h3>
                    <p>CNPJ: " . $pj->cnpj . "</p>
                    <p>" . $pj->endereco->getLogradouro() . ", " . $pj->endereco->getBairro() . ", " . $pj->endereco->getMunicipio() . ", " . $pj->endereco->getEstado() . "</p>
                    <p>" . $nota->data . "</p>
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
                        {
                            $item = $_SESSION['all_items'][$i];
                            $item_nf = unserialize($item);
                            $total = round($item_nf->quantidade * $item_nf->valorunitario, 2);
                            $desconto = $item_nf->desconto;
                            array_push($itens_total, $total);
                            array_push($itens_descontos, $desconto);
                            echo "<tr>";
                            echo "
                            <th scope='row'>" . ($i+1) . "</th>
                            <td>" . $item_nf->cod . "</td>
                            <td>" . $item_nf->descricao . "</td>
                            <td>" . $item_nf->quantidade . "</td>
                            <td>" . $item_nf->unidade . "</td>
                            <td>R$" . $item_nf->valorunitario . "</td>
                            <td>R$" . $total . "</td>";
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

                    echo "
                    <table class='table table-dark table-striped table-hover'>
                        <thead>
                            <tr>
                                <th scope='col'>#</th>
                                <th scope='col'>Forma de pagamento</th>
                                <th scope='col'>Data Vencimento</th>
                                <th scope='col'>Data Pagamento</th>
                                <th scope='col'>Valor</th>
                            </tr>
                        </thead>
                        <tbody>";
                        for($i = 0; $i < sizeof($_SESSION['all_faturas']); $i++)
                        {
                            $fatura_serialize = $_SESSION['all_faturas'][$i];
                            $fatura = unserialize($fatura_serialize);
                            echo "<tr>";
                            echo "
                            <th scope='row'>" . ($i+1) . "</th>
                            <td>" . $fatura->ft_pagamento . "</td>
                            <td>" . $fatura->ft_dtvencimento . "</td>
                            <td>" . $fatura->ft_dtpagamento . "</td>
                            <td>R$" . $fatura->ft_valor . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>
                    </table>";
                }
            ?>

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

                    $(document).ready(function () {
                        if($("#ft_pagamento").val() === 'DB' || $("#ft_pagamento").val() === 'CR')
                        {
                            enviarDados('enviar');
                        }
                    });

                    $('#ft_pagamento').on('change', function() {
                        if(this.value === 'DB' || this.value === 'CR')
                        {
                            enviarDados('enviar');
                        }
                        else
                        {
                            $('.form-cartao').remove();
                        }
                    });
                });

                let xhttp;
                function enviarDados(cartao)
                {
                    xhttp = new XMLHttpRequest();
                    
                    if (!xhttp) 
                    {
                        alert('Não foi possível criar um objeto XMLHttpRequest.');
                        return false;
                    }
                    xhttp.onreadystatechange = mostraResposta;
                    xhttp.open('POST', 'resources/php/retrieve_cartao.php', true);
                    xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhttp.send('cartao=' + encodeURIComponent(cartao));
                }

                function mostraResposta() 
                {
                    try
                    {
                        if (xhttp.readyState === XMLHttpRequest.DONE)
                        {
                            if (xhttp.status === 200)
                            {
                                let resposta = JSON.parse(xhttp.responseText);

                                let options = "<div class='form-cartao'><p class='form-input'>Associar Cartão (*)</p>\
                                    <select name='ft_cartao' required>";

                                var size = Object.keys(resposta['cartoes']).length;
                                for(var i = 0; i < size; i++)
                                {
                                    options += "<option value='" + resposta['cartoes'][i]['codcartao'] + "'>" + (resposta['cartoes'][i]['final'] + " | " + resposta['cartoes'][i]['bandeira']) + "</option>";
                                }

                                $('.form-cartao').remove(); 
                                $('#ft-cartao').append(options + "</select></div>");
                            }
                            else
                            {
                                alert('Um problema ocorreu.');
                            }
                        }
                    } 
                    catch (e)
                    {
                        alert("Ocorreu uma exceção: " + e.description);
                    }
                }
            </script>

            <p><input id='form-button' type='submit' value=<?php echo isset($_SESSION['submeter']) ? 'Enviar' : 'Próximo' ?>></p>
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