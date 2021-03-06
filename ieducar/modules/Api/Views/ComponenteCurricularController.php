<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';

/**
 * Class ComponenteCurricularController
 * @deprecated Essa versão da API pública será descontinuada
 */
class ComponenteCurricularController extends ApiCoreController
{
  // search options

  protected function searchOptions() {
    return array('namespace' => 'modules', 'idAttr' => 'id');
  }

  // subescreve para pesquisar %query%, e nao query% como por padrão
  protected function sqlsForStringSearch() {
    return "select distinct id, nome as name from modules.componente_curricular
            where lower((nome)) like '%'||lower(($1))||'%' order by nome limit 15";
  }

  // subscreve formatResourceValue para não adicionar 'id -' a frente do resultado
  protected function formatResourceValue($resource) {
    return mb_strtoupper($resource['name'], 'UTF-8');
  }

  function getComponentesCurricularesSearch(){

    $sql = 'SELECT componente_curricular_id FROM modules.professor_turma_disciplina WHERE professor_turma_id = $1';

    $array = array();

    $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($this->getRequest()->id) ));

    foreach ($resources as $reg) {
      $array[] = $reg['componente_curricular_id'];
    }

    return array('componentecurricular' => $array);
  }

  function canGetComponentesCurriculares(){
    return  $this->validatesPresenceOf('instituicao_id');
  }

  function getComponentesCurriculares(){
    if($this->canGetComponentesCurriculares()){

      $instituicaoId = $this->getRequest()->instituicao_id;
      $areaConhecimentoId = $this->getRequest()->area_conhecimento_id;

      $areaConhecimentoId ? $where = 'AND area_conhecimento_id = '. $areaConhecimentoId : '';
      
      $sql = 'SELECT componente_curricular.id, componente_curricular.nome, area_conhecimento_id, area_conhecimento.nome AS nome_area, ordenamento
                FROM modules.componente_curricular
               INNER JOIN modules.area_conhecimento ON (area_conhecimento.id = componente_curricular.area_conhecimento_id)
                WHERE componente_curricular.instituicao_id = $1
                ' . $where . '
                ORDER BY nome ';
      $disciplinas = $this->fetchPreparedQuery($sql, array($instituicaoId));

      $attrs = array('id', 'nome', 'area_conhecimento_id', 'nome_area', 'ordenamento');
      $disciplinas = Portabilis_Array_Utils::filterSet($disciplinas, $attrs);

      foreach ($disciplinas as &$disciplina){
        $disciplina['nome'] = Portabilis_String_Utils::toUtf8($disciplina['nome']);
      }

      return array('disciplinas' => $disciplinas);
    }
  }

function getComponentesCurricularesPorSerie(){
    if($this->canGetComponentesCurriculares()){

      $instituicaoId = $this->getRequest()->instituicao_id;
      $serieId       = $this->getRequest()->serie_id;
      
      $sql = 'SELECT componente_curricular.id, componente_curricular.nome, carga_horaria::int, tipo_nota, area_conhecimento_id, area_conhecimento.nome AS nome_area
                FROM modules.componente_curricular
               INNER JOIN modules.componente_curricular_ano_escolar ON (componente_curricular_ano_escolar.componente_curricular_id = componente_curricular.id)
               INNER JOIN modules.area_conhecimento ON (area_conhecimento.id = componente_curricular.area_conhecimento_id)
                WHERE componente_curricular.instituicao_id = $1
                  AND ano_escolar_id = ' . $serieId . '
                ORDER BY nome ';
      $disciplinas = $this->fetchPreparedQuery($sql, array($instituicaoId));

      $attrs = array('id', 'nome', 'carga_horaria', 'tipo_nota', 'area_conhecimento_id', 'nome_area');
      $disciplinas = Portabilis_Array_Utils::filterSet($disciplinas, $attrs);

      return array('disciplinas' => $disciplinas);
    }
  }

    protected function getComponentesCurricularesForMultipleSearch() {
    if ($this->canGetComponentesCurriculares()) {
      $turmaId       = $this->getRequest()->turma_id;
      $ano           = $this->getRequest()->ano;

      $sql = "SELECT cc.id,
                       cc.nome
                  FROM pmieducar.turma
                 INNER JOIN modules.componente_curricular_turma cct ON (cct.turma_id = turma.cod_turma
                                                                    AND cct.escola_id = turma.ref_ref_cod_escola)
                 INNER JOIN modules.componente_curricular cc ON (cc.id = cct.componente_curricular_id)
                 INNER JOIN modules.area_conhecimento ac ON (ac.id = cc.area_conhecimento_id)
                 INNER JOIN pmieducar.escola_ano_letivo al ON (al.ref_cod_escola = turma.ref_ref_cod_escola)
                 WHERE turma.cod_turma = $1
                   AND al.ano = $2
                 ORDER BY ac.secao,
                          ac.nome,
                          cc.ordenamento,
                          cc.nome";


      $componentesCurriculares = $this->fetchPreparedQuery($sql, array($turmaId, $ano));

      if(count($componentesCurriculares) < 1){
        $sql = "SELECT cc.id,
                       cc.nome
                  FROM pmieducar.turma AS t
                INNER JOIN pmieducar.escola_serie_disciplina esd ON (esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                                                                 AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                                                                 AND esd.ativo = 1)
                INNER JOIN modules.componente_curricular cc ON (cc.id = esd.ref_cod_disciplina)
                INNER JOIN modules.area_conhecimento ac ON (ac.id = cc.area_conhecimento_id)
                INNER JOIN pmieducar.escola_ano_letivo al ON (al.ref_cod_escola = esd.ref_ref_cod_escola
                                                          AND al.ativo = 1)
                WHERE t.cod_turma = $1
                  AND al.ano = $2
                  AND t.ativo = 1
                ORDER BY ac.secao,
                         ac.nome,
                         cc.ordenamento,
                         cc.nome";

        $componentesCurriculares = $this->fetchPreparedQuery($sql, array($turmaId, $ano));
      }

      $componentesCurriculares = Portabilis_Array_Utils::setAsIdValue($componentesCurriculares, 'id', 'nome');

      return array('options' => $componentesCurriculares);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'componente_curricular-search'))
      $this->appendResponse($this->search());
    elseif ($this->isRequestFor('get', 'componentecurricular-search'))
      $this->appendResponse($this->getComponentesCurricularesSearch());
    elseif ($this->isRequestFor('get', 'componentes-curriculares'))
      $this->appendResponse($this->getComponentesCurriculares());
    elseif ($this->isRequestFor('get', 'componentes-curriculares-serie'))
      $this->appendResponse($this->getComponentesCurricularesPorSerie());
    elseif($this->isRequestFor('get', 'componentes-curriculares-for-multiple-search'))
      $this->appendResponse($this->getComponentesCurricularesForMultipleSearch());
    else
      $this->notImplementedOperationError();
  }
}
