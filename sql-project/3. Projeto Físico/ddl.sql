

CREATE TABLE cartao
(
	codcartao SMALLINT NOT NULL IDENTITY(1,1),
	final CHAR(4) NOT NULL,
	bandeira VARCHAR(16) NOT NULL,
	bancoemissor VARCHAR(30) NOT NULL,
	dtvalidade DATE NOT NULL,
	PRIMARY KEY(codcartao)
)
GO

CREATE TABLE pagamento
(
	codpagamento INT NOT NULL IDENTITY(1,1),
	forma CHAR(2) NOT NULL,
	codcartao SMALLINT NULL,
	PRIMARY KEY(codpagamento),
	FOREIGN KEY(codcartao) REFERENCES cartao
)
GO

CREATE INDEX ix_pagamento_cartao
ON pagamento(codcartao)
GO

CREATE TABLE produto
(
	codproduto BIGINT NOT NULL IDENTITY(1,1),
	codigo VARCHAR(16) NULL,
	descricao VARCHAR(100) NOT NULL,
	valorunitario NUMERIC(7, 2) NOT NULL,
	PRIMARY KEY(codproduto)
)
GO

CREATE TABLE pessoa
(
	codpessoa INT NOT NULL IDENTITY(1,1),
	nome VARCHAR(60) NOT NULL,
	PRIMARY KEY(codpessoa)
)
GO

CREATE TABLE pessoa_juridica
(
	codpessoa INT NOT NULL,
	CNPJ CHAR(14) NOT NULL,
	nomefantasia VARCHAR(50) NULL,
	PRIMARY KEY(codpessoa, CNPJ),
	FOREIGN KEY(codpessoa) REFERENCES pessoa
)
GO

CREATE TABLE pessoa_fisica
(
	codpessoa INT NOT NULL,
	CPF CHAR(11) NULL,
	email VARCHAR(80) NULL,
	telefone CHAR(11) NULL,
	PRIMARY KEY(codpessoa),
	FOREIGN KEY(codpessoa) REFERENCES pessoa
)
GO

CREATE TABLE endereco
(
	codendereco INT NOT NULL IDENTITY(1,1),
	estado CHAR(2) NOT NULL,
	municipio VARCHAR(20) NOT NULL,
	bairro VARCHAR(30) NULL,
	logradouro VARCHAR(40) NULL,
	PRIMARY KEY(codendereco)
)
GO

CREATE TABLE enderecopessoa
(
	codendereco INT NOT NULL,
	codpessoa INT NOT NULL,
	PRIMARY KEY(codendereco, codpessoa),
	FOREIGN KEY(codendereco) REFERENCES endereco,
	FOREIGN KEY(codpessoa) REFERENCES pessoa
)
GO

CREATE TABLE notafiscal
(
	numnota BIGINT NOT NULL IDENTITY(1,1),
	valorpagar NUMERIC(8, 2) NOT NULL,
	data DATETIME NOT NULL,
	desconto NUMERIC(5, 2) NOT NULL,
	codendereco INT NOT NULL,
	codpessoa INT NOT NULL,
	PRIMARY KEY(numnota),
	FOREIGN KEY(codendereco) REFERENCES endereco,
	FOREIGN KEY(codpessoa) REFERENCES pessoa
)
GO

CREATE INDEX ix_notafiscal_endereco
ON notafiscal(codendereco)
GO

CREATE INDEX ix_notafiscal_pessoa
ON notafiscal(codpessoa)
GO

CREATE TABLE fatura
(
	numfatura BIGINT NOT NULL IDENTITY(1,1),
	dtvencimento DATETIME NOT NULL,
	dtpagamento DATETIME NULL,
	valor NUMERIC(8, 2) NOT NULL,
	codpagamento INT NOT NULL,
	numnota BIGINT NOT NULL,
	PRIMARY KEY(numfatura),
	FOREIGN KEY(codpagamento) REFERENCES pagamento,
	FOREIGN KEY(numnota) REFERENCES notafiscal
)
GO

CREATE INDEX ix_fatura_pagamento
ON fatura(codpagamento)
GO

CREATE INDEX ix_fatura_notafiscal
ON fatura(numnota)
GO

CREATE TABLE itemnotafiscal
(
	numnota BIGINT NOT NULL,
	codproduto BIGINT NOT NULL,
	valortotal NUMERIC(8, 2) NOT NULL,
	unidademedida CHAR(2) NOT NULL,
	quantidade NUMERIC(6, 3) NOT NULL,
	desconto NUMERIC(5, 2) NOT NULL,
	PRIMARY KEY(numnota, codproduto),
	FOREIGN KEY(numnota) REFERENCES notafiscal,
	FOREIGN KEY(codproduto) REFERENCES produto
)
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
DROP TABLE itemnotafiscal, fatura, notafiscal, enderecopessoa, endereco,
	pessoa_fisica, pessoa_juridica, pessoa, produto, pagamento, cartao
*/