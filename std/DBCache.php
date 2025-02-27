<?php
/**
 * Classe que realiza cache de conteudo.
 * 
 * @author Maurício Costa
 * @author Vitor Rocha
 */

namespace std;

class DBCache {

  const PATH_CACHE = "cache/";

  /**
   * Verifica a existencia do cache no sistema.
   * Caso informado o tempo do cache, verifica se o cache foi expirado.
   * @param  string $sCache Nome do arquivo de cache
   * @param  mixed $time Tempo de vida do cache - default false para tempo indeterminado
   * @return boolean
   */
  public static function check($sCache, $time = false) {

    if ( file_exists(self::PATH_CACHE . $sCache) ) {

      if ($time !== false) {
        $oFile = file(self::PATH_CACHE . $sCache);
        $creation = $oFile[0];

        $maxtime = strtotime($time, intval($creation));
        return time() <= $maxtime;
      }

      return true;
    }

    return false;
  }

  /**
   * Apaga o arquivo de cache informado.
   * @param  Mixed $mCache Nome do arquuivo de cache ou um array com varios arquivos
   * @return void
   */
  public static function remove($mCache) {

    if (is_array($mCache)) {
      foreach ($mCache as $sFile) {
        self::remove($sFile);
      }

      return;
    }

    if (self::check($mCache)) {
      unlink(self::PATH_CACHE . $mCache);
    }

    return;    
  }

  /**
   * Escreve o cache de um determinado conteudo informado.
   * @param  string $sCache Nome do arquivo de cache
   * @param  mixed $mConteudo Conteudo do arquivo de cache, eh feito serialize do valor.
   * @return boolean
   */
  public static function write($sCache, $mConteudo) {

    self::remove($sCache);

    $sPath = self::PATH_CACHE . $sCache;

    if (preg_match('/(.*)\/.*$/', $sPath, $aPath) && !is_dir($aPath[1])) {
      
      mkdir($aPath[1], 0775, true);
    }

    $return = file_put_contents($sPath, time() . "\n" . serialize($mConteudo));

    return $return !== false;
  }

  /**
   * Faz a leitura do cache informado, caso esteja valido
   * @param  string  $sCache Nome do arquivo de cache
   * @param  mixed $time Tempo de vida do cache
   * @return mixed - Retorna o conteudo do arquivo, caso o cache esteja invalido, retorna false.
   */
  public static function read($sCache, $time = false) {

    if (self::check($sCache, $time)) {

      $aConteudo = file(self::PATH_CACHE . $sCache);

      $content = unserialize($aConteudo[1]);

      return $content;
    }

    return false;
  }

  /**
   * Escaneia o cache em busca dos arquivos que baterem com a expressão passada
   * @param  String $sPath
   * @param  String $sPattern
   * @return mixed - Retorna um array com os arquivos encontrados ou um array vazio, ou false em caso de erro
   */
  public static function scanCache($sPath, $sPattern) {

    if (is_dir(self::PATH_CACHE . $sPath)) {

      $aDirFiles = scandir(self::PATH_CACHE . $sPath);
      $aRetorno  = array();

      if (empty($aDirFiles)) {
        return $aRetorno;
      }

      foreach ( $aDirFiles as $sFile ) {

        if (is_file(self::PATH_CACHE . $sPath . $sFile) && preg_match($sPattern, $sFile)) {
          $aRetorno[] = $sPath . $sFile;
        }
      }

      return $aRetorno;
    }

    return false;
  }

}

?>