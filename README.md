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

### 📚 Conteúdo

- **Projeto do Banco de Dados (SGBD) com MS SQL Server**
  - Projeto Conceitual
  - Projeto Lógico
  - Projeto Físico
    - Criação das Tabelas
    - Criação de Stored Procedures
    - Criação de Triggers

