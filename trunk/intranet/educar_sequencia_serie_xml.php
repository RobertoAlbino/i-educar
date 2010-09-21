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
	header( 'Content-type: text/xml' );

	require_once( "include/clsBanco.inc.php" );
	require_once( "include/funcoes.inc.php" );
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";
	if( is_numeric( $_GET["cur"]  ) )
	{
		$db = new clsBanco();
		$consulta = "SELECT DISTINCT
							ss.ref_serie_origem
							,so.nm_serie
						FROM
							pmieducar.sequencia_serie ss
							, pmieducar.serie so
							, pmieducar.serie sd
						WHERE
							ss.ativo = 1
							AND ref_serie_origem = so.cod_serie
							AND ref_serie_destino = sd.cod_serie
							AND ( so.ref_cod_curso = {$_GET["cur"]} OR sd.ref_cod_curso = {$_GET["cur"]} )

						UNION

						SELECT DISTINCT
							ss.ref_serie_destino
							,sd.nm_serie
						FROM
							pmieducar.sequencia_serie ss
							, pmieducar.serie so
							, pmieducar.serie sd
						WHERE
							ss.ativo = 1
							AND ref_serie_origem = so.cod_serie
							AND ref_serie_destino = sd.cod_serie
							AND ( so.ref_cod_curso = {$_GET["cur"]} OR sd.ref_cod_curso = {$_GET["cur"]} )

						UNION

						SELECT DISTINCT
							s.cod_serie
							,s.nm_serie
						FROM   pmieducar.serie s
						WHERE
							s.ativo = 1
						   AND s.ref_cod_curso = {$_GET["cur"]}
						";

		$db->Consulta( $consulta );
		while ( $db->ProximoRegistro() )
		{
			list( $serie,$nm_serie ) = $db->Tupla();
				if($_GET['ser_dif'] != $serie){
					echo "	<serie cod_serie=\"$serie\">{$nm_serie}</serie>\n";
					//echo "	<item>{$serie}</item>\n";
					//echo "	<item>{$nm_serie}</item>\n";
				}
		}
	}
	echo "</query>";
?>