<?php
//===========================================================================
//============================ Database Settings ============================
//===========================================================================

/**
 * Especifique as configurações do seu banco de dados aqui
 *
 * USING_SSL_CONNECTION = true -> especifique o caminho da chave p/ conexão SSL
 * USING_SSL_CONNECTION = false -> desabilitar conexão SSL
 */
define("DB_SERVER", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("USING_SSL_CONNECTION", false);
define("DB_PORT", "3306");
define("DB_DATABASE", "scc");

if(USING_SSL_CONNECTION)
{
    define("DB_SSL_FILEPATH", "misc/");
}
else
{
    define("DB_SSL_FILEPATH", "");
}
?>