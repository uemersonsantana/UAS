<?php
/**
 * Conexão com o banco de dados.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

use \PDO;

/*
  * Parametros de conexão e cofiguração.
*/ 
define("DB_VALIDA_REQUISITOS" , false);
define("lUtilizaCaptcha"      , false);

define("DB_FONTE_CODVERSAO"   , "4");
define("DB_FONTE_CODRELEASE"  , "850");

// Usuário do PostgreSQL
define("DB_USUARIO"           , "uas");
// Senha do usuário do PostgreSQL
define("DB_SENHA"             , "uas");
// Ip do servidor para a conexão com a base de dados
define("DB_SERVIDOR"          , "localhost");
// Porta para conexao com o banco de dados (porta do Pool de Conexoes quando utilizado)
define("DB_PORTA"             , "5432");
// Porta para conexao direta com PostgreSQL quando tivermos um pool de conexao          
define("DB_PORTA_ALT"         , "5432");
// Nome da base de dados
define("DB_BASE"              , "uas");
define("DB_SELLER"            , "");

/**
 * db_conecta
 */
class db_conecta extends db_stdlib
{

  private static $instance;

  public function __construct( $naoconecta = 0 ) {
    if (!isset($_SESSION)) {
      session_start();
    }
    
    if ( $naoconecta == 0 ) {
      self::conecta();
      //self::val_sessao();
    }
  }
  
  private static function getInstance() {
    if (!isset(self::$instance)) { 
      try { 
        self::$instance = new PDO('pgsql:host='.DB_SERVIDOR.';port='.DB_PORTA.';dbname='.DB_BASE, DB_USUARIO, DB_SENHA);
        //self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }

    return self::$instance;
  }

  public static function exec($sql) {
    return self::getInstance()->exec($sql);
  }

  public static function prepare($sql) {
    return self::getInstance()->prepare($sql);
  }

  public static function lastInsertId() {
    return self::getInstance()->lastInsertId();
  }

  private function conecta() {    
    /**
     * Habilita acesso apenas para usuarios do e-cidade usuext = 0 negando para:
     * 1 - Usuário Externo
     * 2 - Perfil
    */
    $sSql  = "select * from db_usuarios where usuarioativo <> '0' and usuext not in (1,2) and login = '".parent::db_getsession('DB_login')."'";
    $result = parent::db_query($sSql)->fetch();

    /**
     * Realiza a busca das preferências do usuário.
     */
    $oUsuarioSistema = new \model\UsuarioSistema( $result->id_usuario );
    $sPreferencias   = serialize($oPreferenciaUsuario = $oUsuarioSistema->getPreferenciasUsuario());
    parent::db_putsession("DB_preferencias_usuario", base64_encode($sPreferencias));

    //------------
    
    if ( !isset($_SESSION['DB_login']) || !isset($_SESSION['DB_id_usuario']) ) {
      session_destroy();
      echo "Sessão Inválida!(12)<br>Feche seu navegador e faça login novamente.\n";
      exit;
    }

    parent::db_logs();

    if (parent::db_getsession("DB_id_usuario") != 1 && parent::db_getsession("DB_administrador") != 1){

      $result1 = parent::db_query("select db21_ativo from db_config where prefeitura = true");

      if (!$result1) {
        print_r("Erro ao verificar se sistema está liberado! Contate suporte!! Erro: ". $result->errorInfo());
      }

      $ativo   = $result1->fetch()->db21_ativo;

      if ($ativo == 3) {

        echo "Sistema desativado pelo administrador!   <br>Sessão terminada, feche seu navegador!\n";
        session_destroy();
        exit;
      }
    }
  }

  //  Valida sessões
  private static function val_sessao() {
    $sess = 0;
    if(!$_SESSION["DB_modulo"])
      $sess = 1;
    if(!$_SESSION["DB_nome_modulo"])
      $sess = 1;
    if(!$_SESSION["DB_anousu"])
      $sess = 1;
    if(!$_SESSION["DB_instit"])
      $sess = 1;
    if(!$_SESSION["DB_uol_hora"])
      $sess = 1;
    if($sess == 1) {
      session_destroy();
      echo "Sessão Inválida!(14)<br>Feche seu navegador e faça login novamente.<Br>\n";
      exit;
    }
  }
  
}