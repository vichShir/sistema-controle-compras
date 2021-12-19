# SCC - Sistema de Controle de Compras 💸

![MSSQL Server](https://img.shields.io/badge/Microsoft%20SQL%20Server-CC2927?style=for-the-badge&logo=microsoft%20sql%20server&logoColor=white) ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white) ![CSS](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white) ![Javascript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E) ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

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
- **Website**
  - Página de Cadastro da Nota Fiscal (index.php)


### 🔨 Projeto

#### **1. Como armazenar os dados?**

A princípio, os dados coletados serão armazenados em um esquema de **banco de dados relacional** utilizando MS SQL Server.

**✔️ Vantagens:**

* Garantia, a princípio, da integridade dos dados
* Esquema estruturado
* Consultas relacionais

**❌ Desvantagens:**

* Limitação para um grande número de registros a longo prazo
* Necessidade de seguir a estrutura do projeto

#### **2. Como inserir os dados manualmente?**

A **inserção manual** dos dados de **nota fiscal** serão coletados a partir de uma **interface web com formulários**. Para tanto, é necessário seguir a seguinte **ordem de inserção** estruturado no _Projeto SQL_:

**🎲 Ordem de inserção dos dados**:

1. **Pessoa (Jurídica ou Física)** associada à nota.
2. **Nota Fiscal**.
3. **Cartão** associado à fatura.
4. **Faturas** associadas à nota.
5. **Itens de Nota Fiscal** associados à nota.

**📝 Formulários**:

* Cadastro de **Pessoa (Jurídica ou Física).**
* Cadastro de **Cartão**.
* Cadastro de **Nota Fiscal**:
  * Associar **Endereço** à nota.
  * Associar **Pessoa** à nota.
  * Associar **Itens de Nota Fiscal** à nota.
  * Associar **Faturas** à nota.

### ⚙ Configurando Projeto

**[1. MS SQL Server no Docker](https://docs.microsoft.com/pt-br/sql/linux/quickstart-install-connect-docker?view=sql-server-ver15&pivots=cs1-bash)**

Execute os seguintes comandos **(PowerShell)**:

```shell
docker pull mcr.microsoft.com/mssql/server:2019-latest
```

```PowerShell
docker run -e "ACCEPT_EULA=Y" -e "SA_PASSWORD=<YourStrong@Passw0rd>" `
   -p 1433:1433 --name scc_sqlserver -h scc_sqlserver `
   -d mcr.microsoft.com/mssql/server:2019-latest
```

```shell
docker exec -it scc_sqlserver "bash"
```

```shell
/opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "<YourStrong@Passw0rd>"
```

**SQL Commands**

Ver a versão do SQL Server:

```sql
SELECT @@VERSION
GO
```

Criar o banco de dados:

```sql
CREATE DATABASE scc
GO
```

Ver os banco de dados do SGBD:

```sql
SELECT Name from sys.Databases
GO
```

Conectar-se de fora do contêiner:

```shell
sqlcmd -S localhost,1433 -U SA -P "<YourStrong@Passw0rd>"
```

### 📖 Referências

* [Length of a JavaScript object - Stack Overflow](https://stackoverflow.com/questions/5223/length-of-a-javascript-object)
* [jQuery get value of select onChange - Stack Overflow](https://stackoverflow.com/questions/11179406/jquery-get-value-of-select-onchange)
* [Joins (SQL Server) - SQL Server | Microsoft Docs](https://docs.microsoft.com/pt-br/sql/relational-databases/performance/joins?view=sql-server-ver15)
* [PDOStatement::rowCount - PHP drivers for SQL Server | Microsoft Docs](https://docs.microsoft.com/pt-br/sql/connect/php/pdostatement-rowcount?view=sql-server-ver15)
* [php - PDO - Qual é melhor: columnCount ou rowCount? - Stack Overflow em Português](https://pt.stackoverflow.com/questions/256668/pdo-qual-é-melhor-columncount-ou-rowcount)
* [PHP: PDOStatement::rowCount - Manual](https://www.php.net/manual/pt_BR/pdostatement.rowcount.php)
* [SQL SERVER - @@IDENTITY vs SCOPE_IDENTITY() vs IDENT_CURRENT - Retrieve Last Inserted Identity of Record - SQL Authority with Pinal Dave](https://blog.sqlauthority.com/2007/03/25/sql-server-identity-vs-scope_identity-vs-ident_current-retrieve-last-inserted-identity-of-record/)
* [PHP: PDOStatement::nextRowset - Manual](https://www.php.net/manual/pt_BR/pdostatement.nextrowset.php)
* [PHP: PDO::lastInsertId - Manual](https://www.php.net/manual/pt_BR/pdo.lastinsertid.php)
* [PHP MySQL Get Last Inserted ID](https://www.w3schools.com/php/php_mysql_insert_lastid.asp)
* [PHP: round - Manual](https://www.php.net/manual/pt_BR/function.round.php)
* [PHP round() Function](https://www.w3schools.com/php/func_math_round.asp)
* [PDOStatement::bindParam - PHP drivers for SQL Server | Microsoft Docs](https://docs.microsoft.com/pt-br/sql/connect/php/pdostatement-bindparam?view=sql-server-ver15)
* [PHP: DateTime::format - Manual](https://www.php.net/manual/pt_BR/datetime.format.php)
* [PHP Sessions](https://www.w3schools.com/php/php_sessions.asp)
