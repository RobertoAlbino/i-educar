<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Empresas" );
		$this->processoAp = array("41", "649");
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe da empresa";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_empresa = @$_GET['cod_empresa'];

		$objPessoaJuridica = new clsPessoaJuridica();
		list ($cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $nm_bairro, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $http, $tipo_pessoa, $razao_social, $ins_est, $capital_social, $ins_mun, $cidade, $idtlog) = $objPessoaJuridica->queryRapida($cod_empresa, "idpes","fantasia","cnpj","logradouro","cep","bairro","ddd_1","fone_1","ddd_2","fone_2","ddd_mov","fone_mov","ddd_fax","fone_fax","email","url","tipo","nome","insc_estadual","insc_municipal","cidade", "idtlog");
		$endereco = "$idtlog $endereco";
		$db = new clsBanco();
		
		$this->addDetalhe( array("Raz&atilde;o Social", $razao_social) );
		$this->addDetalhe( array("Nome Fantasia", $nm_pessoa) );
		$this->addDetalhe( array("CNPJ", int2CNPJ($id_federal)) );
		$this->addDetalhe( array("Endere&ccedil;o", $endereco) );
		$this->addDetalhe( array("CEP", $cep) );
		$this->addDetalhe( array("Bairro", $nm_bairro) );
		$this->addDetalhe( array("Cidade", $cidade) );
		
		$this->addDetalhe( array("Telefone 1", "({$ddd_telefone_1}) {$telefone_1}") );
		$this->addDetalhe( array("Telefone 2", "({$ddd_telefone_2}) {$telefone_2}") );
		$this->addDetalhe( array("Celular", "({$ddd_telefone_mov}) {$telefone_mov}") );
		$this->addDetalhe( array("Fax", "({$ddd_telefone_fax}) {$telefone_fax}") );
		
		$this->addDetalhe( array("Site", $http) );
		$this->addDetalhe( array("E-mail", $email) );
		
		if( ! $ins_est ) $ins_est = "isento";
		$this->addDetalhe( array("Inscri&ccedil;&atilde;o Estadual", $ins_est) );
		$this->addDetalhe( array("Capital Social", $capital_social) );

		$this->url_novo = "empresas_cad.php";
		$this->url_editar = "empresas_cad.php?idpes={$cod_empresa}";
		$this->url_cancelar = "empresas_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>