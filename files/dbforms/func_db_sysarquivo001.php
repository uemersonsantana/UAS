<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_stdlib        = new libs\db_stdlib; 
$db_funcoes       = new dbforms\db_funcoes;

$cl_sysarquivo  = new classes\cl_sysarquivo;
$cl_sysarquivo->rotulo->label("codarq");
$cl_sysarquivo->rotulo->label("nomearq");

$Services_Skins   = new libs\Services_Skins;

//  Pega um vetor e cria variáveis globais pelo índice do vetor.
$db_stdlib->db_postmemory($_GET);

$GLOBALS['db_corcabec']   = '';
$GLOBALS['cor1']          = '';
$GLOBALS['cor2']          = '';


//  BEGIN: HTML
include $Services_Skins->getPathFile('dashboard','html_start.php');
    //  BEGIN: Head
    include $Services_Skins->getPathFile('dashboard','head.php');
    //  END: Head

    include $Services_Skins->getPathFile('dashboard','body_start.php');
?>
  <div class="card-body">
      <form class="form form-horizontal" name="form2">
        <input type="hidden" name="funcao_js" value="<?php echo $funcao_js; ?>" />
        <div class="form-body center">
          <h5></h5>
        </div>
        <div class="form-body">
            <div class="form-group row" style="margin-bottom:0;">
                <label class="col-md-3 label-control" for="codarq" title="<?=$Tcodmod?>"><?=$Lcodarq?></label>
                <div class="col-md-9">
                  <?php $db_funcoes->db_input("codarq",4,$Icodarq,true,"text",4,"","chave_codarq"); ?>
                </div>
            </div>
        </div>
        <div class="form-body">
            <div class="form-group row" style="margin-bottom:0;">
                <label class="col-md-3 label-control" for="nomearq" title="<?=$Tnomemod?>"><?=$Lnomearq?></label>
                <div class="col-md-9">
                  <?php $db_funcoes->db_input("nomearq",40,$Inomearq,true,"text",4,"","chave_nomearq"); ?>
                </div>
            </div>
        </div>

        <div class="form-actions center" style="margin-top:0;padding:0;padding-top:10px;">
          <input class="btn btn-sm btn-info" name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
          <input class="btn btn-sm btn-info" name="limpar" type="reset" id="limpar" value="Limpar" />
          <input type="button" name="fechar" id="fechar" class="btn btn-sm btn-info" onclick="parent.js_fecharModal('#xlarge');return false" value="Fechar" />
        </div>
      </form>
  </div>
  <table class="table table-responsive-lg">
    <tr> 
      <td align="center" valign="top"> 
        <?php
          if( !isset($pesquisa_chave) ) {
            if( isset($campos)==false ) {
                if( file_exists($_SERVER['DOCUMENT_ROOT']."/files/dbinputs/db_func_db_sysarquivo.php") == true ) {
                  include($_SERVER['DOCUMENT_ROOT']."/files/dbinputs/db_func_db_sysarquivo.php");
                } else {
                  $campos = "db_sysarquivo.*";
                }
            }
            if( isset($chave_codarq) and (trim($chave_codarq)!="") ) {
               $sql = $cl_sysarquivo->sql_query($chave_codarq,$campos,"codarq");
            }else if(isset($chave_nomearq) and (trim($chave_nomearq)!="") ){
               $sql = $cl_sysarquivo->sql_query("",$campos,"nomearq"," nomearq like '$chave_nomearq%' ");
            }else{
               $sql = $cl_sysarquivo->sql_query("",$campos,"codarq","");
            }
            $db_stdlib->db_lovrot($sql,15,"()","",( isset($funcao_js) ? $funcao_js : null ) );
          }else{
            if( $pesquisa_chave != null and $pesquisa_chave != "" ) {
              $result = $cl_sysarquivo->sql_record($cl_sysarquivo->sql_query($pesquisa_chave));
              if($cl_sysarquivo->numrows!=0){
                $db_stdlib->db_fieldsmemory($result,0);
                echo "<script>".$funcao_js."('$nomearq',false);</script>";
              }else{
               echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
              }
            }else{
             echo "<script>".$funcao_js."('',false);</script>";
            }
          }
          ?>
       </td>
     </tr>
  </table>
<?php
    //  END: Body
    include $Services_Skins->getPathFile('dashboard','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 