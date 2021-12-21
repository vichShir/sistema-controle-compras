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
define("DB_USERNAME", "SA");
define("DB_PASSWORD", "x9GCjKmGkV4EMLs");
define("USING_SSL_CONNECTION", false);
define("DB_PORT", "1433");
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