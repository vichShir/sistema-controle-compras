/* STORED PROCEDURES */

/* Cadastro de uma pessoa juridica */
CREATE PROCEDURE ins_pessoa_juridica
@nome VARCHAR(60),
@CNPJ CHAR(14),
@nomefantasia VARCHAR(50),
@estado CHAR(2),
@municipio VARCHAR(20),
@bairro VARCHAR(30),
@logradouro VARCHAR(40)
AS
BEGIN TRANSACTION
	INSERT INTO pessoa (nome) VALUES (@nome)
	DECLARE @codpessoa AS INT = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
	IF @@rowcount > 0 /* inserção de pessoa bem sucedida */
	BEGIN
		INSERT INTO pessoa_juridica VALUES (@codpessoa, @CNPJ, @nomefantasia)
		IF @@rowcount > 0 /* inserção de pessoa_juridica bem sucedida */
		BEGIN
			INSERT INTO endereco (estado, municipio, bairro, logradouro)
			VALUES (@estado, @municipio, @bairro, @logradouro)
			DECLARE @codendereco AS INT = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
			IF @@rowcount > 0 /* inserção de endereco bem sucedida */
			BEGIN
				INSERT INTO enderecopessoa VALUES (@codendereco, @codpessoa)
				IF @@rowcount > 0 /* inserção de enderecopessoa bem sucedida */
				BEGIN
					commit transaction
			    	return 1
		    	END
		    	ELSE
			    BEGIN
			    	rollback transaction
			        return 0
			    END
	    	END
	    	ELSE
		    BEGIN
		    	rollback transaction
		        return 0
		    END
		END
		ELSE
	    BEGIN
	    	rollback transaction
	        return 0
	    END
    END
    ELSE
    BEGIN
    	rollback transaction
        return 0
    END
GO

/* Cadastro de uma pessoa fisica */
CREATE PROCEDURE ins_pessoa_fisica
@nome VARCHAR(60),
@CPF CHAR(11),
@email VARCHAR(80),
@telefone CHAR(11),
@estado CHAR(2),
@municipio VARCHAR(20),
@bairro VARCHAR(30),
@logradouro VARCHAR(40)
AS
BEGIN TRANSACTION
	INSERT INTO pessoa (nome) VALUES (@nome)
	DECLARE @codpessoa AS INT = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
	IF @@rowcount > 0 /* inserção de pessoa bem sucedida */
	BEGIN
		INSERT INTO pessoa_fisica VALUES (@codpessoa, @CPF, @email, @telefone)
		IF @@rowcount > 0 /* inserção de pessoa_fisica bem sucedida */
		BEGIN
			INSERT INTO endereco (estado, municipio, bairro, logradouro)
			VALUES (@estado, @municipio, @bairro, @logradouro)
			DECLARE @codendereco AS INT = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
			IF @@rowcount > 0 /* inserção de endereco bem sucedida */
			BEGIN
				INSERT INTO enderecopessoa VALUES (@codendereco, @codpessoa)
				IF @@rowcount > 0 /* inserção de enderecopessoa bem sucedida */
				BEGIN
					commit transaction
			    	return 1
		    	END
		    	ELSE
			    BEGIN
			    	rollback transaction
			        return 0
			    END
	    	END
	    	ELSE
		    BEGIN
		    	rollback transaction
		        return 0
		    END
		END
		ELSE
	    BEGIN
	    	rollback transaction
	        return 0
	    END
    END
    ELSE
    BEGIN
    	rollback transaction
        return 0
    END
GO

/* Cadastro de uma fatura */
CREATE PROCEDURE ins_fatura
@dtvencimento DATETIME,
@dtpagamento DATETIME,
@valor NUMERIC(8, 2),
@numnota BIGINT,
@forma CHAR(2),
@codcartao SMALLINT
AS
BEGIN TRANSACTION
	DECLARE @codpagamento AS INT;
	IF NOT EXISTS(SELECT * FROM pagamento WHERE forma = @forma AND codcartao = @codcartao) /* forma de pagamento não existente */
	BEGIN
		INSERT INTO pagamento (forma, codcartao) VALUES (@forma, @codcartao)
		SET @codpagamento = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
		IF @@rowcount = 0 /* inserção de pagamento mal sucedida */
	    BEGIN
	    	rollback transaction
	        return 0
	    END
	END
	ELSE
	BEGIN
		SET @codpagamento = (SELECT codpagamento FROM pagamento WHERE forma = @forma AND codcartao = @codcartao);
	END

	INSERT INTO fatura (dtvencimento, dtpagamento, valor, codpagamento, numnota) VALUES (@dtvencimento, @dtpagamento, @valor, @codpagamento, @numnota)
	IF @@rowcount > 0 /* inserção de fatura bem sucedida */
	BEGIN
		commit transaction
		return 1
    END
    ELSE
    BEGIN
    	rollback transaction
        return 0
    END
GO

/* Cadastro de uma nota fiscal */
CREATE PROCEDURE ins_notafiscal
@valorpagar NUMERIC(8, 2),
@data DATETIME,
@desconto NUMERIC(5, 2),
@codpessoa INT,
@estado CHAR(2),
@municipio VARCHAR(20),
@bairro VARCHAR(30),
@logradouro VARCHAR(40)
AS
BEGIN TRANSACTION
	DECLARE @codendereco AS INT;
	IF NOT EXISTS(SELECT * FROM endereco WHERE estado = @estado AND municipio = @municipio AND bairro = @bairro AND logradouro = @logradouro) /* endereco não existente */
	BEGIN
		INSERT INTO endereco (estado, municipio, bairro, logradouro) VALUES (@estado, @municipio, @bairro, @logradouro)
		SET @codendereco = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
		IF @@rowcount = 0 /* inserção de endereco mal sucedida */
	    BEGIN
	    	rollback transaction
	        return 0
	    END
	END
	ELSE
	BEGIN
		SET @codendereco = (SELECT codendereco FROM endereco WHERE estado = @estado AND municipio = @municipio AND bairro = @bairro AND logradouro = @logradouro);
	END

	INSERT INTO notafiscal (valorpagar, data, desconto, codendereco, codpessoa) VALUES (@valorpagar, @data, @desconto, @codendereco, @codpessoa)
	IF @@rowcount > 0 /* inserção de notafiscal bem sucedida */
	BEGIN
		commit transaction
		return 1
    END
    ELSE
    BEGIN
    	rollback transaction
        return 0
    END
GO

/* Cadastro de um item de nota fiscal */
CREATE PROCEDURE ins_itemnotafiscal
@numnota BIGINT,
@valortotal NUMERIC(8, 2),
@unidademedida CHAR(2),
@quantidade NUMERIC(6, 3),
@desconto NUMERIC(5, 2),
@codigo VARCHAR(16),
@descricao VARCHAR(100),
@valorunitario NUMERIC(7, 2)
AS
BEGIN TRANSACTION
	DECLARE @codproduto AS INT;
	IF NOT EXISTS(SELECT * FROM produto WHERE codigo = @codigo AND descricao = @descricao AND valorunitario = @valorunitario) /* produto não existente */
	BEGIN
		INSERT INTO produto (codigo, descricao, valorunitario) VALUES (@codigo, @descricao, @valorunitario)
		SET @codproduto = (SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]);
		IF @@rowcount = 0 /* inserção de produto mal sucedida */
	    BEGIN
	    	rollback transaction
	        return 0
	    END
	END
	ELSE
	BEGIN
		SET @codproduto = (SELECT codproduto FROM produto WHERE codigo = @codigo AND descricao = @descricao AND valorunitario = @valorunitario);
	END

	INSERT INTO itemnotafiscal  VALUES (@numnota, @codproduto, @valortotal, @unidademedida, @quantidade, @desconto)
	IF @@rowcount > 0 /* inserção de itemnotafiscal bem sucedida */
	BEGIN
		commit transaction
		return 1
    END
    ELSE
    BEGIN
    	rollback transaction
        return 0
    END
GO

SELECT * FROM cartao
SELECT * FROM pagamento
SELECT * FROM produto
SELECT * FROM pessoa
SELECT * FROM pessoa_juridica
SELECT * FROM pessoa_fisica
SELECT * FROM endereco
SELECT * FROM enderecopessoa
SELECT * FROM notafiscal
SELECT * FROM fatura
SELECT * FROM itemnotafiscal

/*
DROP PROCEDURE ins_pessoa_juridica, ins_pessoa_fisica, ins_fatura, ins_notafiscal, ins_itemnotafiscal
*/