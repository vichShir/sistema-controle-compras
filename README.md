# SCC - Sistema de Controle de Compras 💸

![MSSQL Server](https://img.shields.io/badge/Microsoft%20SQL%20Server-CC2927?style=for-the-badge&logo=microsoft%20sql%20server&logoColor=white) 

### 📃 Descrição

O objetivo do projeto é desenvolver um **sistema que unifique todos os dados de notas fiscais de compra** que um usuário realiza a partir de uma **interface web** ou **coleta automática dos dados da foto da nota com Machine Learning (OCR)**, que vise **controlar os gastos** ou outras questões a partir de análises sobre os dados coletados.

### 😵 Problema

Por muitas vezes, quando compramos, não sabemos:

* **Quanto** estamos gastando por **semana/mês/ano**;
* **O que** estamos comprando;
* **De quem** estamos comprando;
* **Quando** compramos algo;
* **Os preços** de determinados produtos que compramos;
* **Como** pagamos.

Estas são apenas algumas indagações iniciais que podemos realizar para o nosso problema.

### 📚 Conteúdo

- **Projeto do Banco de Dados (SGBD) com MS SQL Server**
  - Projeto Conceitual
  - Projeto Lógico
  - Projeto Físico
    - Criação das Tabelas
    - Criação de Stored Procedures
    - Criação de Triggers

### 🔨 Projeto

**1. Como armazenar os dados?**

A princípio, os dados coletados serão armazenados em um esquema de **banco de dados relacional**.

**✔️ Vantagens:**

* Garantia, a princípio, da integridade dos dados
* Esquema estruturado
* Consultas relacionais

**❌ Desvantagens:**

* Limitação para um grande número de registros a longo prazo
* Necessidade de seguir a estrutura do projeto

### ⚙ Configurando Projeto

**[1. MS SQL Sever no Docker](https://docs.microsoft.com/pt-br/sql/linux/quickstart-install-connect-docker?view=sql-server-ver15&pivots=cs1-bash)**

Execute os seguintes comandos **(PowerShell)**:

```docker pull mcr.microsoft.com/mssql/server:2019-latest```

```PowerShell
docker run -e "ACCEPT_EULA=Y" -e "SA_PASSWORD=<YourStrong@Passw0rd>" `
   -p 1433:1433 --name scc_sqlserver -h scc_sqlserver `
   -d mcr.microsoft.com/mssql/server:2019-latest
```

```docker exec -it scc_sqlserver "bash"```

```/opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "<YourNewStrong@Passw0rd>"```

**SQL Commands**

Ver a versão do SQL Server:

``SELECT @@VERSION``
``GO``

Criar o banco de dados:

```CREATE DATABASE scc```

```GO```

Ver os banco de dados do SGBD:

```SELECT Name from sys.Databases```

```GO```

Conectar-se de fora do contêiner:

```sqlcmd -S localhost,1433 -U SA -P "<YourNewStrong@Passw0rd>"```
