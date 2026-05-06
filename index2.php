<?php
/*
Script para el departamento de seguridad y vigilancia
Las Opciones de TipoModificar que viene por GET Son:
	Sin parámetro Menu
	JorA1 Crear, Editar, Consultar Minutas

DROP TABLE IF EXISTS solicitudes.vigilanciapuesto;
CREATE TABLE solicitudes.vigilanciapuesto(
	IDPuesto SMALLINT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	Puesto VARCHAR(100) NOT NULL DEFAULT '',
	Borrada SMALLINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Listado de puestos que se pueden entregar/recibir en la minuta';

DROP TABLE IF EXISTS solicitudes.vigilanciapuestosucursal;
CREATE TABLE solicitudes.vigilanciapuestosucursal(
	IDPuestoSucursal SMALLINT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	Sucursal VARCHAR(2) NOT NULL DEFAULT '',
	IDPuesto SMALLINT(6) NOT NULL DEFAULT 0,
	ObsPuesto VARCHAR(250) NOT NULL DEFAULT '',
	Borrada SMALLINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Listado de puestos que se pueden entregar/recibir POR SUCURSAL';

DROP TABLE IF EXISTS solicitudes.vigilanciaelemento;
CREATE TABLE solicitudes.vigilanciaelemento(
	IDElemento SMALLINT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	Elemento VARCHAR(150) NOT NULL DEFAULT '',
	Grupo VARCHAR(20) NOT NULL DEFAULT '',
	Borrada SMALLINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Listado de elementos que se pueden entregar/recibir en la minuta de un puesto';

DROP TABLE IF EXISTS solicitudes.vigilanciaelementopuestosucursal;
CREATE TABLE solicitudes.vigilanciaelementopuestosucursal(
	IDElementoPuestoSucursal SMALLINT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	IDPuestoSucursal SMALLINT(6) NOT NULL DEFAULT 0,
	IDElemento SMALLINT(6) NOT NULL DEFAULT 0,
	Cantidad SMALLINT(6) NOT NULL DEFAULT 0,
	Borrada SMALLINT(1) NOT NULL DEFAULT 0,
	UNIQUE IDPuestoSucursal(IDPuestoSucursal,IDElemento)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Listado de puestos que se pueden entregar/recibir POR SUCURSAL';

DROP TABLE IF EXISTS solicitudes.vigilanciareceso;
CREATE TABLE solicitudes.vigilanciareceso(
	IDReceso SMALLINT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	Receso VARCHAR(150) NOT NULL DEFAULT '',
	Borrada SMALLINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Listado de tipos de receso que se pueden presentar';

DROP TABLE IF EXISTS solicitudes.vigilanciaminutaelemento;
CREATE TABLE solicitudes.vigilanciaminutaelemento(
	IDMinutaElemento INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	IDMinuta INT(11) NOT NULL DEFAULT 0,
	IDElementoPuestoSucursal INT(11) NOT NULL DEFAULT 0,
	CantidadReal SMALLINT(6) NOT NULL DEFAULT 0,	##Se almacena la cantidad Real al momento de crear la Minuta, Es posible que la cantidad real en la tabla elementopuesto cambie por nuevos elementos o por bajas
	CantidadVerificada SMALLINT(6) NOT NULL DEFAULT 0,
	Verificado SMALLINT(1) NOT NULL DEFAULT 0,
	ObsVerifica VARCHAR(100) NOT NULL DEFAULT '',
	Borrada SMALLINT(1) NOT NULL DEFAULT 0,
	UNIQUE IDMinuta(IDMinuta,IDElementoPuestoSucursal)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Elementos de cada puesto en una sucursal, revisados en cada MINUTA';

DROP TABLE IF EXISTS solicitudes.vigilanciaminuta;
CREATE TABLE solicitudes.vigilanciaminuta(
	IDMinuta INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	Sucursal VARCHAR(2) NOT NULL DEFAULT '',
	IDPuesto SMALLINT(6) NOT NULL DEFAULT 0,
	Turno VARCHAR(10) NOT NULL DEFAULT '',
	Elabora VARCHAR(12) NOT NULL DEFAULT '',
	Fecha DATETIME DEFAULT NULL,
	HoraInicio VARCHAR(5) NOT NULL DEFAULT '',
	VigilanteEntrante VARCHAR(12) NOT NULL DEFAULT '',
	VigilanteSaliente VARCHAR(12) NOT NULL DEFAULT '',
	RealizaRequisa SMALLINT(1) NOT NULL DEFAULT 0,
	ObsRequisa VARCHAR(250) NOT NULL DEFAULT '',
	ObsMMinuta VARCHAR(250) NOT NULL DEFAULT '',
	FinalizaRegistro DATETIME DEFAULT NULL,
	Borrada SMALLINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla principal donde se alojan los registros de minutas del departamento de seguridad';
*/
include ("../seguridad.php");
include('../funciones.php');
//Registro de los Usuarios que son Administradores del sistema
$PuedeAdministrar = in_array($_SESSION['Usuario'],array((InList($_SESSION["Perfil"],"ADMIN","SISTE") ? $_SESSION['Usuario'] : "Cualquiera"),
						'1018516798',//SALVADOR CABRERA
						'79879905',//Carlos león
						'26424949'//LUISA MARÍA PIEDRAHITA
						));
//Conectar a la Base de datos
include ("../conexion_servidor.php");
/***************************************************************
Tipos de Asistencia*********************************************/
$mGrupoElemento['AULAS']='AULAS';
$mGrupoElemento['CORREDORES']='CORREDORES';
$mGrupoElemento['EQUIPOS']='EQUIPOS';
$mGrupoElemento['OFICINAS']='OFICINAS';
$mGrupoElemento['SALAS']='SALAS';
$mGrupoElemento['SEGURIDAD']='SEGURIDAD';
$mGrupoElemento['VEHICULOS']='VEHICULOS';
$mGrupoElemento['OTROS']='OTROS';
//**********************************************************************************************************
//MódulO DE PERMISOS, DEBEN VENIR DOS PARAMETROS POR GET Permisos=1 Y Colegio
//**********************************************************************************************************
//OJO OPTIMIZADO PARA PERMISOS PARTICULARES EN COLEGIO
if($_GET['Permisos'] and $_GET['Colegio']=='22' and $PuedeAdministrar){
	//Ojo el registro de los Facilitadores Transversales se hace en Permisos PuedenMatricularNews
    $VarPermisos=array("FacilitadoresTransversales","UsuariosExternos");
	if($_POST['Enviar'] and $_POST['Usuario']=md5("Jor".$_SESSION['Usuario'].strftime("%d%B"))){
		foreach($VarPermisos as $VarPermiso){
			for($j=1; $j<=$_POST[$VarPermiso.'Reg'];$j++){
				if($_POST[$VarPermiso.$j.'Eli']){//eliminar
					$QueriGrabar = "UPDATE ".$PrefBD."estudiantes.srpausuarios
									SET Borrada=1
									WHERE usuario='".$_POST[$VarPermiso.$j.'Usuario']."'
									LIMIT 1";
					$ResultGrabar = $mysqli->query($QueriGrabar) or die(mysqli_error($mysqli));
				}else{
					$QueriGrabar = "INSERT INTO ".$PrefBD."estudiantes.srpausuarios(Usuario)
										VALUES('".$_POST[$VarPermiso.$j.'Usuario']."')
										ON DUPLICATE KEY UPDATE Borrada=0";
					$ResultGrabar = $mysqli->query($QueriGrabar) or die(mysqli_error($mysqli));
					$QueriGrabar = "UPDATE ".$PrefBD."estudiantes.srpausuarios
									SET NomUsuario='".$_POST[$VarPermiso.$j.'Nom']."',
										Clave='".$_POST[$VarPermiso.$j.'Clave']."',
										Tipo='".($VarPermiso=='FacilitadoresTransversales' ? 'FT' : 'UE')."',
										Centros='".$_POST[$VarPermiso.$j.'Centros']."'
									WHERE usuario='".$_POST[$VarPermiso.$j.'Usuario']."'
									LIMIT 1";
					echo $QueriGrabar;
					$ResultGrabar = $mysqli->query($QueriGrabar) or die(mysqli_error($mysqli));
				}
			}
		}
		header("Location: index.php?Permisos=1&Colegio=".$_GET['Colegio']);
		exit;
	}
	echo "<style type='text/css'>";
	echo "<!--";
	echo "body,td,th {";
	echo "font-family: Tahoma, Geneva, sans-serif;";
	echo "font-size: 11px;";
	echo "}";
	echo "-->";
	echo "</style>";?>
    <!-- Agrego la libreria acá también porque más abajo no se puede -->
    <script src="../librerias/jquery/jquery-1.10.2.js"></script>
	<SCRIPT language=JavaScript>
    function AgregarPermiso(mTabla){
        var tabla;
        if(isNaN(parseInt(document.getElementById(mTabla + 'Reg').value,10))){
            document.getElementById(mTabla + 'Reg').value = 0;
        }else{
            document.getElementById(mTabla + 'Reg').value = parseInt(document.getElementById(mTabla + 'Reg').value,10);
        }
		mRegOld = parseInt(document.getElementById(mTabla + 'Reg').value,10);
        mRegNew=mRegOld + 1;
		document.getElementById(mTabla + 'Reg').value = mRegNew;
		$('#'+mTabla+' tr:last').clone().find("input,select,a,td").each(function(){
			$(this).attr({
				'id': function(_, id) { return (id ? id.replace(mRegOld, (mRegNew)) : null)},
				'name': function(_, name) { return (name ? name.replace(mRegOld, (mRegNew)) : null)},
				'checked': false
			});
			$(this).val(($(this).is(':checkbox') ? 1 : ''));
		}).end().appendTo('#'+mTabla);
    }
    </SCRIPT>
    <form action="index.php?Permisos=1&Colegio=<?php echo $_GET['Colegio'];?>" method=post enctype="multipart/form-data" name='Form1' id='Form1' value="yes">
    <div align="center" style="font-weight:bold;font-style:italic;font-size:16px;"><b>Colegio</b>
    <select name="Colegio" id="Colegio" onChange="window.open('index.php?Permisos=1&Colegio='+this.value,'_self');"><?php
		$QueriCol="SELECT CodCole,NomCole
					FROM ".$PrefBD."estudiantes.colegio
					WHERE CodCole='22'
					ORDER BY CodCole";
		$ResultCol = $mysqli->query($QueriCol) or die(mysqli_error($mysqli));
		while($RowCol= $ResultCol->fetch_assoc()){?>
        <option value="<?php echo $RowCol['CodCole'];?>" <?php if($RowCol['CodCole']==$_GET['Colegio']){echo "selected";}?>><?php echo $RowCol['CodCole'].' - '.$RowCol['NomCole'];?></option><?php
		}?>
	</select></div><?php
    foreach($VarPermisos as $VarPermiso){?>
    <table align="center" id="<?php echo $VarPermiso;?>" border="1">
    <tr><td colspan="5" align="center" valign="middle"><b><?php echo ($VarPermiso=='PuedenMatricularNews' ? 'Facilitadores Transversales' : $VarPermiso);?></b><input name="Mas<?php echo $VarPermiso;?>" type="button" id="Mas<?php echo $VarPermiso;?>" value="Mas&gt;&gt;" onClick="AgregarPermiso('<?php echo $VarPermiso;?>')"/></td></tr>
    <tr>
    	<th><b>Documento</b></th>
        <th><b>Nombre</b></th>
        <th><b>Perfil</b></th>
        <th><b>Centros (Vacio para Todos)</b></th>
        <th><b>Clav/Borrar</b></th>
    </tr><?php
		$Queri = "SELECT U.Usuario, U.NomUsuario, U.Clave, U.Tipo, U.Centros,CONCAT(IF(E.Activo,'','<b style=\"color:red\">'),E.Apellido1,' ',E.Apellido2,' ',E.Nom,IF(E.Activo,'',' - Inactivo</b>')) AS Nombre,E.Perfil,E.Sucursal
					FROM ".$PrefBD."estudiantes.srpausuarios U
					LEFT JOIN ".$PrefBD."recursos.emplea E ON U.Usuario=E.Nit_CCE
					WHERE U.Borrada=0 AND U.Tipo='".($VarPermiso=='FacilitadoresTransversales' ? 'FT' : 'UE')."'
					ORDER BY Nombre, U.Nomusuario";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$mRegistros=0;
		while(($Result->num_rows > 0 ? $Row = $Result->fetch_assoc() : $mRegistros<>1)){
			$mRegistros++;?>
		<tr>
        	<td><input name="<?php echo $VarPermiso.$mRegistros.'Usuario';?>" type="text" id="<?php echo $VarPermiso.$mRegistros.'Usuario';?>" value="<?php echo $Row['Usuario'];?>" size="12" maxlength="12"></td>
			<td><?php
				if($VarPermiso=='FacilitadoresTransversales'){
					echo $Row['Nombre'];
				}else{?>
                <input name="<?php echo $VarPermiso.$mRegistros.'Nom';?>" type="text" id="<?php echo $VarPermiso.$mRegistros.'Nom';?>" value="<?php echo $Row['NomUsuario'];?>" size="30" maxlength="60"><?php
				}?></td>
            <td><?php echo $Row['Perfil'].' - '.$Row['Sucursal'];?></td>
            <td><input name="<?php echo $VarPermiso.$mRegistros.'Centros';?>" type="text" id="<?php echo $VarPermiso.$mRegistros.'Centros';?>" value="<?php echo $Row['Centros'];?>" size="30" maxlength="200"></td>
			<td><?php
				if($VarPermiso=='FacilitadoresTransversales'){
					//Nothing here
				}else{?>
                <input name="<?php echo $VarPermiso.$mRegistros.'Clave';?>" type="text" id="<?php echo $VarPermiso.$mRegistros.'Clave';?>" value="<?php echo $Row['Clave'];?>" size="8" maxlength="12"><?php
				}?>
                <input name="<?php echo $VarPermiso.$mRegistros.'Eli';?>" type="checkbox" id="<?php echo $VarPermiso.$mRegistros.'Eli';?>" value="1"></td>
        </tr><?php
		}?>
        </table>
        <input name="<?php echo $VarPermiso.'Reg';?>" type="hidden" id="<?php echo $VarPermiso.'Reg';?>" value="<?php echo $mRegistros;?>"><?php
	}//Fin del each array permisos?>
    <input name="Usuario" type="hidden" id="Usuario" value="<?php echo md5("Jor".$_SESSION['Usuario'].strftime("%d%B"));?>">
    <input name="Enviar" type="submit" id="Enviar" value="Enviar"/>
    </form><?php
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Retornar los datos de un Puesto
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1JorA2Puesto'.date('d')) and $_GET['IDPuesto']){
	$Queri = "SELECT *
				FROM ".$PrefBD."solicitudes.vigilanciapuesto
				WHERE IDPuesto=".intval($_GET['IDPuesto'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if($Row=$Result->fetch_assoc()){
		foreach ($Row as $Clave => $Valor){
			if(!$Clave or $Clave>0){//Es numércia, no la envío
				//Nothing here
			}elseif(((is_object($Valor) or strlen($Valor)==10) and DarFecha($Valor)>'0-0-0') or $Valor=='0000-00-00'){
				$Array[$Clave]=str_replace('/','-',DarFecha($Valor));
			}else{
				$Array[$Clave]=trim($Valor);
			}
		}
		echo json_encode($Array);
	}else{
		echo json_encode( array( "Mensaje"=>"Error"));
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
GRABAR los datos de un Puesto
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('JorA2'.date('d')) and $_POST['TipoGrabar']=='A2' and $_POST['IDPuesto']){
	$Retorno="Hecho";
	if($_POST['IDPuesto']=='Nuevo'){
		$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciapuesto(Puesto)
			VALUES('".strtoupper(OptimizarTexto($_POST['Puesto']))."')";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$_POST['IDPuesto']=$RowE['IDBase']=$mysqli->insert_id;
	}else{
		$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciapuesto
					SET Puesto='".strtoupper(OptimizarTexto($_POST['Puesto']))."',
						Borrada=".intval($_POST['Borrada'])."
					WHERE IDPuesto=".intval($_POST['IDPuesto'])."
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
/******************************************************************************************************************************************
Retornar los datos de un Puesto X Sucursal
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1JorA3PuestoSucursal'.date('d')) and $_GET['IDPuestoSucursal']){
	$Queri = "SELECT *
				FROM ".$PrefBD."solicitudes.vigilanciapuestosucursal
				WHERE IDPuestoSucursal=".intval($_GET['IDPuestoSucursal'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if($Row=$Result->fetch_assoc()){
		foreach ($Row as $Clave => $Valor){
			if(!$Clave or $Clave>0){//Es numércia, no la envío
				//Nothing here
			}elseif(((is_object($Valor) or strlen($Valor)==10) and DarFecha($Valor)>'0-0-0') or $Valor=='0000-00-00'){
				$Array[$Clave]=str_replace('/','-',DarFecha($Valor));
			}else{
				$Array[$Clave]=trim($Valor);
			}
		}
		echo json_encode($Array);
	}else{
		echo json_encode( array( "Mensaje"=>"Error"));
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
GRABAR los datos de un PuestoSucursal
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('JorA3'.date('d')) and $_POST['TipoGrabar']=='A3' and $_POST['IDPuestoSucursal']){
	$Retorno="Hecho";
	if($_POST['IDPuestoSucursal']=='Nuevo'){
		$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciapuestosucursal(Sucursal,IDPuesto,ObsPuesto)
			VALUES('".$_POST['Sucursal']."','".intval($_POST['IDPuesto'])."','".OptimizarTexto($_POST['ObsPuesto'])."')";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$_POST['IDPuestoSucursal']=$RowE['IDBase']=$mysqli->insert_id;
	}else{
		$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciapuestosucursal
					SET Sucursal='".$_POST['Sucursal']."',
						IDPuesto='".intval($_POST['IDPuesto'])."',
						ObsPuesto='".OptimizarTexto($_POST['ObsPuesto'])."',
						Borrada=".intval($_POST['Borrada'])."
					WHERE IDPuestoSucursal=".intval($_POST['IDPuestoSucursal'])."
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
/******************************************************************************************************************************************
Retornar los datos de un Elemento
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1JorA4Elemento'.date('d')) and $_GET['IDElemento']){
	$Queri = "SELECT *
				FROM ".$PrefBD."solicitudes.vigilanciaelemento
				WHERE IDElemento=".intval($_GET['IDElemento'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if($Row=$Result->fetch_assoc()){
		foreach ($Row as $Clave => $Valor){
			if(!$Clave or $Clave>0){//Es numércia, no la envío
				//Nothing here
			}elseif(((is_object($Valor) or strlen($Valor)==10) and DarFecha($Valor)>'0-0-0') or $Valor=='0000-00-00'){
				$Array[$Clave]=str_replace('/','-',DarFecha($Valor));
			}else{
				$Array[$Clave]=trim($Valor);
			}
		}
		echo json_encode($Array);
	}else{
		echo json_encode( array( "Mensaje"=>"Error"));
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
GRABAR los datos de un Elemento
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('JorA4'.date('d')) and $_POST['TipoGrabar']=='A4' and $_POST['IDElemento']){
	$Retorno="Hecho";
	if($_POST['IDElemento']=='Nuevo'){
		$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciaelemento(Elemento,Grupo)
			VALUES('".strtoupper(OptimizarTexto($_POST['Elemento']))."','".$_POST['Grupo']."')";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$_POST['IDElemento']=$RowE['IDBase']=$mysqli->insert_id;
	}else{
		$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaelemento
					SET Elemento='".strtoupper(OptimizarTexto($_POST['Elemento']))."',
						Grupo='".$_POST['Grupo']."',
						Borrada=".intval($_POST['Borrada'])."
					WHERE IDElemento=".intval($_POST['IDElemento'])."
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
	
/******************************************************************************************************************************************
Retornar los datos de un Tipo Receso
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1JorA6Receso'.date('d')) and $_GET['IDReceso']){
	$Queri = "SELECT *
				FROM ".$PrefBD."solicitudes.vigilanciareceso
				WHERE IDReceso=".intval($_GET['IDReceso'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if($Row=$Result->fetch_assoc()){
		foreach ($Row as $Clave => $Valor){
			if(!$Clave or $Clave>0){//Es numércia, no la envío
				//Nothing here
			}elseif(((is_object($Valor) or strlen($Valor)==10) and DarFecha($Valor)>'0-0-0') or $Valor=='0000-00-00'){
				$Array[$Clave]=str_replace('/','-',DarFecha($Valor));
			}else{
				$Array[$Clave]=trim($Valor);
			}
		}
		echo json_encode($Array);
	}else{
		echo json_encode( array( "Mensaje"=>"Error"));
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
GRABAR los datos de un Tipo Receso
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('JorA6'.date('d')) and $_POST['TipoGrabar']=='A6' and $_POST['IDReceso']){
	$Retorno="Hecho";
	if($_POST['IDReceso']=='Nuevo'){
		$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciareceso(Receso)
			VALUES('".OptimizarTexto($_POST['Receso'])."')";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$_POST['IDReceso']=$RowE['IDBase']=$mysqli->insert_id;
	}else{
		$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciareceso
					SET Receso='".OptimizarTexto($_POST['Receso'])."',
						Borrada=".intval($_POST['Borrada'])."
					WHERE IDReceso=".intval($_POST['IDReceso'])."
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
//**********************************************************************************************************
//AjaxJorA1BuscarCiudad Buscar Ciudad de acuerdo a un criterio (utilizando jquery ************************
//**********************************************************************************************************
}elseif($_GET['TipoModificar']==md5('AjaxJorA1BuscarCiudad'.date('d')) and $_GET['term']){//Consulta por Ajax para filtrar los articulos, term lo envía jquery
	echo "[";
	$Queri = "SELECT CONCAT(C.nom_ciu,'(',D.nom_dep,' - ',P.nom_pai,')') AS nom_ciu,CONCAT(C.cod_pai,'-',C.cod_dep,'-',C.cod_ciu) AS cod_ciu
			  FROM ".$PrefBD."novasoft.gen_ciudad C
			  JOIN ".$PrefBD."novasoft.gen_paises P ON C.cod_pai=P.cod_pai
			  JOIN ".$PrefBD."novasoft.gen_deptos D ON C.cod_pai=D.cod_pai AND C.cod_dep=D.cod_dep
			  WHERE CONCAT(C.cod_ciu,C.nom_ciu,D.nom_dep,P.nom_pai) LIKE '%".$_GET['term']."%'
			  ORDER BY C.nom_ciu";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$contador = 0;
	while($Row=$Result->fetch_assoc()){
		if ($contador++ > 0) print ",";
		echo '"'.$Row['nom_ciu']."|-|".$Row['cod_ciu'].'"';
	}
	mysqli_close($mysqli);
	echo "]";
	exit;
//**********************************************************************************************************
//Listado de Etnias autocompletar JSON *********************************************************************
//**********************************************************************************************************
}elseif($_GET['TipoModificar']==md5('AjaxJorA1Etnia'.date('d')) and $_GET['term']){
	echo "[";
	$Queri = "SELECT CONCAT(Et.nom_etnia,'|-|',Et.cod_etnia) AS Nombre
				FROM ".$PrefBD."novasoft.gen_etnia Et
				WHERE CONCAT(Et.nom_etnia,'|-|',Et.cod_etnia) LIKE '%".$_GET['term']."%'
				ORDER BY Nombre";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$contador = 0;
	while($Row=$Result->fetch_assoc()){
		if ($contador++ > 0) print ",";
		echo '"'.$Row['Nombre'].'"';
	}
	echo "]";
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
0	Datos Personales
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('Tipo1_0'.date('d')) and $_POST['TipoModificar']==md5('JorA1_0'.date('d'))){
	$mRetorno="";
	$mLNacio=explode('|-|',$_POST['LNacio']);
	$mLugarExped=explode('|-|',$_POST['LugarExped']);
	if($_POST['IDBase']=='Nuevo'){
		$QueriColegio = "SELECT CodCole,AnioActual
					FROM ".$PrefBD."estudiantes.colegio
					WHERE CodCole='22'
					LIMIT 1";
		$ResultColegio = $mysqli->query($QueriColegio);
		$RowColegio = $ResultColegio->fetch_assoc();
		$QueriE = "SELECT E.IDEstudiante, E.Codigo, B.IDBase
					FROM ".$PrefBD."estudiantes.estudiante E
					LEFT JOIN ".$PrefBD."estudiantes.base B ON E.IDEstudiante=B.IDEstudiante
					WHERE E.Cedula='".trim($_POST['Cedula'])."' OR(
						REPLACE(CONCAT(E.Ape1Alum,E.Ape2Alum,E.NomAlum),' ','')=REPLACE(CONCAT('".$_POST['Ape1Alum']."','".$_POST['Ape2Alum']."','".$_POST['NomAlum']."'),' ','')
						AND E.FNacio='".DarFechaSQL($_POST['FNacio'])."')
					LIMIT 1";
		$ResultE = $mysqli->query($QueriE) or die(mysqli_error($mysqli));
		if($RowE=$ResultE->fetch_assoc()){
			if(intval($RowE['IDBase'])>0){//Si reflejó un IDBase
				//Nothing here
			}else{//No reflejó un IDBase
				$QueriGrabar = "INSERT INTO ".$PrefBD."estudiantes.base(Anio,IDEstudiante,Codigo)
									VALUES('".$RowColegio['AnioActual']."','".$RowE['IDEstudiante']."','".$RowE['Codigo']."')
								ON DUPLICATE KEY UPDATE Codigo=Codigo";
				$ResultGrabar = $mysqli->query($QueriGrabar);
				$RowE['IDBase']=$mysqli->insert_id;
			}
			$mRetorno='EXISTE|-|'.$RowE['IDBase'];
		}else{
			$PrefVal=$RowColegio['CodCole'].substr(substr($RowColegio['AnioActual'],0,4),2,2);
			$QueriNE = "SELECT MAX(Tmp.Codigo) AS Codigo FROM(
							SELECT MAX(B.Codigo) As Codigo
							FROM ".$PrefBD."estudiantes.base B
							WHERE LEFT(B.Codigo,4)='".$PrefVal."' AND B.Codigo>999999
							UNION
							SELECT MAX(B.Codigo) As Codigo
							FROM ".$PrefBD."estudiantes.matriculasnuevos B
							WHERE LEFT(B.Codigo,4)='".$PrefVal."' AND B.Codigo>999999) AS Tmp";
			$ResultNE = $mysqli->query($QueriNE);
			$RowNE = $ResultNE->fetch_assoc();
			$RowNE['Codigo']=intval($RowNE['Codigo'])+1;
			if($RowNE['Codigo']==1){//Es el primer Estudiante en ese eano
				$RowNE['Codigo']=$PrefVal."0001";
			}else{//Ya existen otros estudiantes en ese ano
				$RowNE['Codigo']=str_pad($RowNE['Codigo'],8,"0",STR_PAD_LEFT);
			}
			$Queri= "INSERT INTO ".$PrefBD."estudiantes.estudiante(Codigo,Ape1Alum,Ape2Alum,NomAlum,
																   FNacio,LNacio,
																   RegistroC,Cedula,LugarExped)
				VALUES('".$RowNE['Codigo']."','".OptimizarTexto($_POST['Ape1Alum'])."','".OptimizarTexto($_POST['Ape2Alum'])."','".OptimizarTexto($_POST['NomAlum'])."',
					'".DarFechaSQL($_POST['FNacio'])."','".$mLNacio[1]."',
					'".OptimizarTexto($_POST['RegistroC'])."','".OptimizarTexto($_POST['Cedula'])."','".$mLugarExped[1]."')";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			$RowNE['IDEstudiante']=$mysqli->insert_id;
			$QueriGrabar = "INSERT INTO ".$PrefBD."estudiantes.base(Anio,IDEstudiante,Codigo)
								VALUES('".$RowColegio['AnioActual']."','".$RowNE['IDEstudiante']."','".$RowNE['Codigo']."')
							ON DUPLICATE KEY UPDATE Codigo=Codigo";
			$ResultGrabar = $mysqli->query($QueriGrabar);
			$_POST['IDBase']=$mysqli->insert_id;
			$mRetorno='CREADO|-|'.$_POST['IDBase'];
		}
	}
	$Queri= "UPDATE ".$PrefBD."estudiantes.base B
				JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
				SET E.Ape1Alum='".$_POST['Ape1Alum']."',
					E.Ape2Alum='".OptimizarTexto($_POST['Ape2Alum'])."',
					E.NomAlum='".OptimizarTexto($_POST['NomAlum'])."',
					E.FNacio='".DarFechaSQL($_POST['FNacio'])."',
					E.LNacio='".$mLNacio[1]."',
					E.RegistroC='".OptimizarTexto($_POST['RegistroC'])."',
					E.Cedula='".OptimizarTexto($_POST['Cedula'])."',
					E.LugarExped='".$mLugarExped[1]."'
			WHERE B.IDBase=".intval($_POST['IDBase']);
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	echo $mRetorno;
	exit;
/****************************************************************************************************************************************
1	Estado y Ubicabilidad
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('Tipo1_1'.date('d')) and $_POST['TipoModificar']==md5('JorA1_1'.date('d'))){
	$mEtnia=explode("|-|",$_POST['Etnia']);
	$mLocalidad=explode('|-|',$_POST['Localidad']);	//Ciudad de Residencia
	$Queri= "UPDATE ".$PrefBD."estudiantes.base B
				JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
				SET E.Sexo='".$_POST['Sexo']."',
					B.CabezaFamilia='".OptimizarTexto($_POST['CabezaFamilia'])."',
					E.Etnia='".$mEtnia[1]."',
					B.VictimaConflicto=".intval($_POST['VictimaConflicto']).",
					B.BarrAlum='".OptimizarTexto($_POST['BarrAlum'])."',
					B.DirAlum='".OptimizarTexto($_POST['DirAlum'])."',
					B.Localidad='".$mLocalidad[1]."',
					B.ZonaResidencia='".$_POST['ZonaResidencia']."',
					B.TelAlum='".OptimizarTexto($_POST['TelAlum'])."',
					B.Celular='".OptimizarTexto($_POST['Celular'])."',
					B.Mail='".OptimizarTexto($_POST['Mail'])."',
					B.PuntajeSisben='".OptimizarTexto($_POST['PuntajeSisben'])."',
					B.VictimaCual='".OptimizarTexto($_POST['VictimaCual0'].','.$_POST['VictimaCual1'])."',
					B.Compromiso='".OptimizarTexto($_POST['Compromiso'])."',
					B.CapExcepcionales='".OptimizarTexto($_POST['CapExcepcionales'])."'
			WHERE B.IDBase=".intval($_POST['IDBase']);
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
2	Información Académica
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('Tipo1_2'.date('d')) and $_POST['TipoModificar']==md5('JorA1_2'.date('d'))){
	$Queri= "UPDATE ".$PrefBD."estudiantes.base B
				JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
				SET B.Grado='".$_POST['Grado']."',
					B.Aula='".intval($_POST['Aula'])."',
					E.Grado='".$_POST['Grado']."',
					E.UACursado='".intval($_POST['Aula'])."',
					B.FIngreso='".DarFechaSQL($_POST['FIngreso'])."',
					B.FSIMAT='".DarFechaSQL($_POST['FSIMAT'])."',
					B.CodConvenio='".$_POST['CodConvenio']."',
					B.FMatri='".DarFechaSQL($_POST['FMatri'])."'
			WHERE B.IDBase=".intval($_POST['IDBase']);
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
3	Datos Familiares
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('Tipo1_3'.date('d')) and $_POST['TipoModificar']==md5('JorA1_3'.date('d'))){
	if(strlen($_POST['NumDocM'])>4){
		$QueriGrabar = "INSERT INTO ".$PrefBD."estudiantes.familiar(Documento)
							VALUES('".$_POST['NumDocM']."')
						ON DUPLICATE KEY UPDATE Documento=Documento";
		$ResultGrabar = $mysqli->query($QueriGrabar);
		$QueriGrabar = "UPDATE ".$PrefBD."estudiantes.familiar
						SET TipoDocumento='".$_POST['TipoDocumentoM']."',
							Ape1Fami='".OptimizarTexto($_POST['Ape1FamiM'])."',
							Ape2Fami='".OptimizarTexto($_POST['Ape2FamiM'])."',
							NomFami='".OptimizarTexto($_POST['NomFamiM'])."',
							Celular='".OptimizarTexto($_POST['CelularM'])."',
							Telefono='".OptimizarTexto($_POST['TelefonoM'])."',
							DirFami='".OptimizarTexto($_POST['DirFamiM'])."',
							BarrFami='".OptimizarTexto($_POST['BarrFamiM'])."'
						WHERE Documento='".$_POST['NumDocM']."'";
		$ResultGrabar = $mysqli->query($QueriGrabar);
	}
	if(strlen($_POST['NumDocP'])>4){
		$QueriGrabar = "INSERT INTO ".$PrefBD."estudiantes.familiar(Documento)
							VALUES('".$_POST['NumDocP']."')
						ON DUPLICATE KEY UPDATE Documento=Documento";
		$ResultGrabar = $mysqli->query($QueriGrabar);
		$QueriGrabar = "UPDATE ".$PrefBD."estudiantes.familiar
						SET TipoDocumento='".$_POST['TipoDocumentoP']."',
							Ape1Fami='".OptimizarTexto($_POST['Ape1FamiP'])."',
							Ape2Fami='".OptimizarTexto($_POST['Ape2FamiP'])."',
							NomFami='".OptimizarTexto($_POST['NomFamiP'])."',
							Celular='".OptimizarTexto($_POST['CelularP'])."',
							Telefono='".OptimizarTexto($_POST['TelefonoP'])."'
						WHERE Documento='".$_POST['NumDocP']."'";
		$ResultGrabar = $mysqli->query($QueriGrabar);
	}
	$Queri= "UPDATE ".$PrefBD."estudiantes.base B
				JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
				SET E.NumDocM='".(strlen($_POST['NumDocM'])>4 ? $_POST['NumDocM'] : '')."',
					E.NumDocP='".(strlen($_POST['NumDocP'])>4 ? $_POST['NumDocP'] : '')."',
					B.Estrato=".intval($_POST['Estrato']).",
					B.Sisben='".OptimizarTexto($_POST['Sisben'])."'
			WHERE B.IDBase=".intval($_POST['IDBase']);
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
4	Historial Académico y Documentos
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('Tipo1_4'.date('d')) and $_POST['TipoModificar']==md5('JorA1_4'.date('d'))){
	$mQueriGrados="";
	$mQueriAspectos="";
	foreach($mGrados as $Var){
		$mQueriGrados.=($mQueriGrados ? "," : "")."E.Anio".$Var."='".OptimizarTexto($_POST['Anio'.$Var])."',
												E.Ciudad".$Var."='".OptimizarTexto($_POST['Ciudad'.$Var])."',
												E.Colegio".$Var."='".OptimizarTexto($_POST['Colegio'.$Var])."'";
		if($_POST['Certificado'.$Var]){
			$mQueriAspectos.=($mQueriGrados ? "," : "").$Var;
		}
	}
	if($_POST['FotocopiaDI']){
		$mQueriAspectos.=($mQueriGrados ? "," : "").'FotocopiaDI';
	}
	if($_POST['EPS']){
		$mQueriAspectos.=($mQueriGrados ? "," : "").'EPS';
	}
	if($_POST['ActaMaterial']){
		$mQueriAspectos.=($mQueriGrados ? "," : "").'ActaMaterial';
	}
	$Queri= "UPDATE ".$PrefBD."estudiantes.base B
				JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
				SET B.Aspectos='".$mQueriAspectos."'".($mQueriGrados ? "," : "").$mQueriGrados."
			WHERE B.IDBase=".intval($_POST['IDBase']);
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
//**********************************************************************************************************
//Listado de Estudiantes para autocompletar JSON ***************************************
//**********************************************************************************************************
}elseif($_GET['TipoModificar']==md5('AjaxJor2Estudiante'.date('d')) and $_GET['IDGrupo']){
	echo "[";
	$Queri = "SELECT B.IDBase,B.Codigo,CONCAT(E.Ape1Alum,' ',E.Ape2Alum,' ',E.NomAlum) AS NomAlum,
					CONCAT('Ciclo: ',B.Grado,'; Grupo del Estudiante: ',C.NCorto,' - ',G.IDGrupo,' - ',G.LetraGrupo,' - ',G.Jornada,' - ',' - ',G.DesGrupo,'; Edad: ',IF(E.FNacio>0,(YEAR(CURDATE())-YEAR(E.FNacio))- (RIGHT(CURDATE(),5) < RIGHT(E.FNacio,5)),'')) AS Inf
				FROM ".$PrefBD."estudiantes.base B
				JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
				LEFT JOIN ".$PrefBD."estudiantes.srpagrupos G ON B.Aula=G.IDGrupo
				LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
				WHERE LEFT(B.Codigo,2)='22' AND G.Anio='".$_GET['Anio']."' AND CONCAT(B.Codigo,E.Ape1Alum,' ',E.Ape2Alum,' ',E.NomAlum) LIKE '%".$_GET['term']."%'
					AND B.FMatri>0 AND G.IDGrupo<>".intval($_GET['IDGrupo'])."
				ORDER BY CONCAT(E.Ape1Alum,' ',E.Ape2Alum,' ',E.NomAlum)";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$contador = 0;
	while($Row=$Result->fetch_assoc()){
		if ($contador++ > 0) print ",";
		echo '"'.$Row['NomAlum']."|-|".$Row['Codigo']."|-|".$Row['IDBase']."|-|".$Row['Inf'].'"';
	}
	echo "]";
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
Cuando viene por POST GENERAR REGISTRO DE ASISTENCIA
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('Jor2'.date('d')) and $_POST['TipoGrabar']==2 and $_POST['IDBase']){
	$Retorno="Hecho";
	$Queri= "INSERT INTO ".$PrefBD."estudiantes.srpaasistencia(IDBase,IDGrupo,Cod_Mate,FAsistencia,TipoAsiste,Responsable,FRegistro)VALUES
			(".intval($_POST['IDBase']).",".intval($_POST['IDGrupo']).",'".$_POST['Cod_Mate']."','".DarFechaSQL($_POST['FAsistencia'])."','".$_POST['Asiste']."','".$_SESSION['Usuario']."',SYSDATE())
			ON DUPLICATE KEY UPDATE TipoAsiste='".$_POST['Asiste']."',Borrada=0";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if(in_array($_POST['Asiste'],array('LIBERTAD','REINTEGROF'))){//Envío al estudiante al grupo especial dado que estos son estados Culminantes
		//Obtengo el IDGrupo, lo calculo para evitar sorpresas
		$QueriG = "SELECT G.IDGrupo
					FROM ".$PrefBD."estudiantes.srpagrupos G
					JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					WHERE C.CodCentro='LIB'
					LIMIT 1";
		$ResultG = $mysqli->query($QueriG) or die(mysqli_error($mysqli));
		$RowG=$ResultG->fetch_assoc();
		//Inserto el evento en el Historial
		$Queri = "INSERT INTO ".$PrefBD."estudiantes.srpahistorial(IDBase,IDGrupoNew,IDGrupoOld,FHistorial,ObsHistorial,Responsable)
						SELECT IDBase,".intval($RowG['IDGrupo']).",Aula,SYSDATE(),'Traslado automático ya que se le asigna al estudiante estado ".$_POST['Asiste']."','".$_SESSION['Usuario']."'
						FROM ".$PrefBD."estudiantes.base
						WHERE IDBase=".intval($_POST['IDBase'])."
						LIMIT 1
					ON DUPLICATE KEY UPDATE IDHistorial=IDHistorial;";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		//Actualizo el nuevo grupo del estudiante
		$Queri = "UPDATE ".$PrefBD."estudiantes.base
					SET Aula=".intval($RowG['IDGrupo'])."
					WHERE IDBase=".intval($_POST['IDBase'])."
					LIMIT 1;";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
/****************************************************************************************************************************************
AGREGAR EVENTO DE DESORDEN DISCIPLINARIO PARA UN GRUPO
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('Jor2_3'.date('d')) and $_POST['TipoGrabar']=='2_3' and $_POST['IDGrupo']){
	$Retorno="Hecho";
	$Queri= "INSERT INTO ".$PrefBD."estudiantes.srpacalendario(IDGrupo,FAsistencia,TipoAsiste,Responsable,FRegistro)VALUES
			(".intval($_POST['IDGrupo']).",'".DarFechaSQL($_POST['FAsistencia'])."','DESORDENDI','".$_SESSION['Usuario']."',SYSDATE())
			ON DUPLICATE KEY UPDATE TipoAsiste='DESORDENDI',Borrada=0";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
/******************************************************************************************************************************************
Retornar los datos de un CENTRO
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1Jor4Centro'.date('d')) and $_GET['CodCentro']){
	$Queri = "SELECT C.*, CONCAT(G.Nom,' ',G.Apellido1,'|-|',C.Gestor) AS NomGestor
				FROM ".$PrefBD."estudiantes.srpacentros C
				LEFT JOIN ".$PrefBD."recursos.emplea G ON C.Gestor=G.Nit_CCE
				WHERE C.CodCentro='".$_GET['CodCentro']."'
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if($Row=$Result->fetch_assoc()){
		foreach ($Row as $Clave => $Valor){
			if(!$Clave or $Clave>0){//Es numércia, no la envío
				//Nothing here
			}else{
				$Array[$Clave]=trim($Valor);
			}
		}
		echo json_encode($Array);
	}else{
		echo json_encode( array( "Mensaje"=>"Error"));
	}
	mysqli_close($mysqli);
	exit;
//**********************************************************************************************************
//Listado de Empleados Suc 22 autocompletar JSON Gestor o Facilitador **************************************
//**********************************************************************************************************
}elseif($_GET['TipoModificar']==md5('AjaxJor4Empleado'.date('d')) and $_GET['term']){
	echo "[";
	$Queri = "SELECT CONCAT(E.Nom,' ',E.Apellido1,' ',E.Apellido2,'|-|',E.Nit_CCE) AS Nombre
				FROM ".$PrefBD."recursos.emplea E
				WHERE CONCAT(E.Nit_CCE,E.Nom,' ',E.Apellido1,' ',E.Apellido2) LIKE '%".$_GET['term']."%' AND FIND_IN_SET('22',E.Sucursal)
				ORDER BY Nombre";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$contador = 0;
	while($Row=$Result->fetch_assoc()){
		if ($contador++ > 0) print ",";
		echo '"'.$Row['Nombre'].'"';
	}
	echo "]";
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Actualizo lo datos de un CENTRO
*******************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('Jor4'.date('d')) and $_POST['TipoGrabar']==4 and $_POST['CodCentro']){
	$Retorno="Hecho";
	//Optimizo la variable Gestor
	$mGestor = explode('|-|',$_POST['Gestor']);
	if($_POST['EsNuevo']==1){
		$Queri = "SELECT CodCentro
					FROM ".$PrefBD."estudiantes.srpacentros
					WHERE CodCentro='".$_POST['CodCentro']."'
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		if($Row = $Result->fetch_assoc()){
			$Retorno='EXISTENTE';
		}else{
			$Queri = "INSERT INTO ".$PrefBD."estudiantes.srpacentros(CodCentro,NomCentro,NCorto,DirCentro,Gestor)
					VALUES('".OptimizarTexto($_POST['CodCentro'])."','".OptimizarTexto($_POST['NomCentro'])."','".OptimizarTexto($_POST['NCorto'])."','".OptimizarTexto($_POST['DirCentro'])."','".$mGestor[1]."')
					ON DUPLICATE KEY UPDATE CodCentro=CodCentro";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		}
	}else{
		$Queri = "UPDATE ".$PrefBD."estudiantes.srpacentros
					SET NomCentro='".OptimizarTexto($_POST['NomCentro'])."',
						NCorto='".OptimizarTexto($_POST['NCorto'])."',
						DirCentro='".OptimizarTexto($_POST['DirCentro'])."',
						Gestor='".$mGestor[1]."'
					WHERE CodCentro='".$_POST['CodCentro']."'
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
/******************************************************************************************************************************************
Retornar los datos de un GRUPO
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1Jor8Grupo'.date('d')) and $_GET['IDGrupo']){
	$Queri = "SELECT G.*, CONCAT(F.Nom,' ',F.Apellido1,'|-|',G.Facilitador) AS NomFacilitador
				FROM ".$PrefBD."estudiantes.srpagrupos G
				LEFT JOIN ".$PrefBD."recursos.emplea F ON G.Facilitador=F.Nit_CCE
				WHERE G.IDGrupo=".intval($_GET['IDGrupo'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	if($Row=$Result->fetch_assoc()){
		foreach ($Row as $Clave => $Valor){
			if(!$Clave or $Clave>0){//Es numércia, no la envío
				//Nothing here
			}else{
				$Array[$Clave]=trim($Valor);
			}
		}
		$Queri = "SELECT CM.Cod_Mate,CONCAT(E.Nom,' ',E.Apellido1,' ',E.Apellido2,'|-|',E.Nit_CCE) AS Nom_Profe
					FROM ".$PrefBD."notas.curso_mate".$RowCol['SufijoTareas']." CM
					JOIN ".$PrefBD."recursos.emplea E ON CM.Doc_Profe=E.Nit_CCE
					WHERE CM.Aula=".intval($_GET['IDGrupo'])." AND CM.Doc_Profe<>''";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$Array['NumMaterias']=0;
		while($Row = $Result->fetch_assoc()){
			$Array['NumMaterias']++;
			$Array['Materias'][$Array['NumMaterias']]=array('Cod_Mate' => $Row['Cod_Mate'],
										'Nom_Profe' => $Row['Nom_Profe']);
		}
		echo json_encode($Array);
	}else{
		echo json_encode( array( "Mensaje"=>"Error"));
	}
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Actualizo lo datos de un GRUPO
*******************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('Jor8'.date('d')) and $_POST['TipoGrabar']==8 and $_POST['IDGrupo7']){
	$Retorno="Hecho";
	//Optimizo la variable Facilitador
	//$mFacilitador = explode('|-|',$_POST['Facilitador7']);
	$mFacilitador = explode('|-|',$_POST['Cod_Mate7ACA']);
	if($_POST['IDGrupo7']=='Nuevo'){
		$Queri = "INSERT INTO ".$PrefBD."estudiantes.srpagrupos(Anio,CodCentro,LetraGrupo,
																Jornada,DesGrupo,Facilitador)
			VALUES('".OptimizarTexto($_POST['Anio7'])."','".$_POST['CodCentro7']."','".OptimizarTexto($_POST['LetraGrupo7'])."',
					'".$_POST['Jornada7']."','".OptimizarTexto($_POST['DesGrupo7'])."','".$mFacilitador[1]."')";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}else{
		$Queri = "UPDATE ".$PrefBD."estudiantes.srpagrupos
					SET Anio='".OptimizarTexto($_POST['Anio7'])."',
						CodCentro='".$_POST['CodCentro7']."',
						LetraGrupo='".OptimizarTexto($_POST['LetraGrupo7'])."',
						Jornada='".$_POST['Jornada7']."',
						DesGrupo='".OptimizarTexto($_POST['DesGrupo7'])."',
						Facilitador='".$mFacilitador[1]."'
					WHERE IDGrupo=".intval($_POST['IDGrupo7'])."
					LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	foreach($_POST as $Variable => $Valor){
		if(substr($Variable,0,9)=='Cod_Mate7'){
			//Optimizo la variable mProfe
			$mProfe = explode('|-|',$Valor);
			$mCod_Mate=str_replace('Cod_Mate7','',$Variable);//Obtengo el código de la materia
			$Queri = "INSERT INTO ".$PrefBD."notas.curso_mate".$RowCol['SufijoTareas']."
						(Colegio, Anio, Aula, Cod_Mate, Tipo, Doc_Profe)
					VALUES('22','".OptimizarTexto($_POST['Anio7'])."',".intval($_POST['IDGrupo7']).",'".$mCod_Mate."','N','".$mProfe[1]."')
					ON DUPLICATE KEY UPDATE Doc_Profe='".$mProfe[1]."'";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		}
	}
	mysqli_close($mysqli);
	echo $Retorno;
	exit;
//**********************************************************************************************************
//GENERAR FORMATO DE ASISTENCIA PARA UN GRUPO **************************************************************
//**********************************************************************************************************
}elseif($_GET['TipoModificar']==md5('ImprimirAsistencia'.date('d')) and $_GET['IDGrupo']){
	if(!($_GET['FAsistencia'])){
		$_GET['FAsistencia'] = date('j-n-Y');
	}
	if($_GET['Cod_Mate']=='Varios'){
		$QueriGF = "SELECT C.NomCentro,CONCAT(Ge.Nom,' ',Ge.Apellido1) AS NomGestor,G.LetraGrupo,G.DesGrupo,G.Jornada,CONCAT(Fa.Nom,' ',Fa.Apellido1) AS NomFacilitador,M.Nom_Mate
					FROM ".$PrefBD."estudiantes.srpagrupos G
					LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					LEFT JOIN ".$PrefBD."notas.curso_mate".$RowCol['SufijoTareas']." CM ON '22'=CM.Colegio AND '".$_GET['Anio']."'=CM.Anio AND G.IDGrupo=CM.Aula
					JOIN ".$PrefBD.$RowCol['RutaNotas']."materia M ON CM.Cod_Mate=M.Cod_Mate
					LEFT JOIN ".$PrefBD."recursos.emplea Ge ON C.Gestor=Ge.Nit_CCE
					LEFT JOIN ".$PrefBD."recursos.emplea Fa ON CM.Doc_Profe=Fa.Nit_CCE
					WHERE CM.Doc_Profe='".$_GET['Documento']."' AND G.IDGrupo IN (".$_GET['IDGrupo'].")";
	}elseif($_GET['Cod_Mate']){
		$QueriGF = "SELECT C.NomCentro,CONCAT(Ge.Nom,' ',Ge.Apellido1) AS NomGestor,G.LetraGrupo,G.DesGrupo,G.Jornada,CONCAT(Fa.Nom,' ',Fa.Apellido1) AS NomFacilitador,M.Nom_Mate
					FROM ".$PrefBD."estudiantes.srpagrupos G
					LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					LEFT JOIN ".$PrefBD."notas.curso_mate".$RowCol['SufijoTareas']." CM ON '22'=CM.Colegio AND '".$_GET['Anio']."'=CM.Anio AND G.IDGrupo=CM.Aula AND '".$_GET['Cod_Mate']."'=CM.Cod_Mate
					JOIN ".$PrefBD.$RowCol['RutaNotas']."materia M ON CM.Cod_Mate=M.Cod_Mate
					LEFT JOIN ".$PrefBD."recursos.emplea Ge ON C.Gestor=Ge.Nit_CCE
					LEFT JOIN ".$PrefBD."recursos.emplea Fa ON CM.Doc_Profe=Fa.Nit_CCE
					WHERE G.IDGrupo=".intval($_GET['IDGrupo'])."
					LIMIT 1";
	}else{
		$QueriGF = "SELECT C.NomCentro,CONCAT(Ge.Nom,' ',Ge.Apellido1) AS NomGestor,G.LetraGrupo,G.DesGrupo,G.Jornada,CONCAT(Fa.Nom,' ',Fa.Apellido1) AS NomFacilitador
					FROM ".$PrefBD."estudiantes.srpagrupos G
					LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					LEFT JOIN ".$PrefBD."recursos.emplea Ge ON C.Gestor=Ge.Nit_CCE
					LEFT JOIN ".$PrefBD."recursos.emplea Fa ON G.Facilitador=Fa.Nit_CCE
					WHERE G.IDGrupo=".intval($_GET['IDGrupo'])."
					LIMIT 1";
	}
	$ResultGF = $mysqli->query($QueriGF) or die(mysqli_error($mysqli));
	$mFecha = strtotime($_GET['FAsistencia']);
	$mFecha1 = date("Y-m-d", strtotime('monday this week', $mFecha));	//Por alguna razón, solo funciona el lunes en windows
	$mFecha2 = date("Y-m-d", strtotime('+1 day',strtotime ($mFecha1)));
	$mFecha3 = date("Y-m-d", strtotime('+2 day',strtotime ($mFecha1)));
	$mFecha4 = date("Y-m-d", strtotime('+3 day',strtotime ($mFecha1)));
	$mFecha5 = date("Y-m-d", strtotime('+4 day',strtotime ($mFecha1)));
	//Llamo las librerias pdf
	require_once('../librerias/tcpdf_php4/config/lang/eng.php');
	require_once('../librerias/tcpdf_php4/tcpdf.php');
	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {	
		// Page footer
		public function Header() {
			// get the current page break margin
			$bMargin = $this->getBreakMargin();
			// get current auto-page-break mode
			$auto_page_break = $this->AutoPageBreak;
			// disable auto-page-break
			$this->SetAutoPageBreak(false, 0);
			// set bacground image
			$img_file ='../imagenes/srpa.jpg';
			$this->Image($img_file, 0, 0, 280, 216, 'JPG', '', '', false, 300, '', false, false, 0);
			// restore auto-page-break status
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
			// set the starting point for the page content
			$this->setPageMark();
    	}
	}
	$pdf = new MYPDF('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
	//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetMargins(5,5,5);//Jorge
//	$pdf->SetHeaderData('../../../imagenes/srpa_header.jpg', 280, '', '',0,0);//Los últimos 2 parámetros los agrego Jorgito también en tcpdf.php remitirse a LeftMargenLogo
	$pdf->setPrintHeader(true);//Desde el ejemplo 2 para no mostrar encabezado y pie
	$pdf->setPrintFooter(false);//Desde el ejemplo 2 para no mostrar encabezado y pie
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	//set margins
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetHeaderMargin(0);//Jorge
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	while($RowGF = $ResultGF->fetch_assoc()){
		// add a page
		$pdf->AddPage();
		// reset pointer to the last page
		$pdf->lastPage();
		//$pdf->Image('../imagenes/srpa.jpg', 0, 0, 280, 205, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// create some HTML content
		ob_start();?>
		<table width="952" style="font-size:24px" border="1px">
			<thead>
			  <tr>
				<td colspan="12" align="center" style="font-size:40px" height="60"><br><br><b>REGISTRO DE ASISTENCIA MODELO EDUCATIVO FLEXIBLE SEMILLAS MINUTO DE DIOS<br>
	CONVENIO No. 378266 DE 2018</b></td>
			  </tr>
			</thead>
			<tr>
				<td colspan="2" width="105">Componente: <?php echo ($RowGF['Nom_Mate'] ? $RowGF['Nom_Mate'] : 'Académico');?></td>
				<td width="85">Jornada: <b><?php echo $RowGF['Jornada'];?></b></td>
				<td colspan="4" width="287"><b><?php echo $RowGF['DesGrupo'];?></b></td>
				<td width="95">Cartilla:</td>
				<td width="95">Cartilla:</td>
				<td width="95">Cartilla:</td>
				<td width="95">Cartilla:</td>
				<td width="95">Cartilla:</td>
	  </tr>
			<tr>
				<td colspan="3">IEM Colegio:</td>
				<td colspan="4">Unidad atención: <b><?php echo $RowGF['NomCentro'];?></b></td>
				<td>Sesión:</td>
				<td>Sesión:</td>
				<td>Sesión:</td>
				<td>Sesión:</td>
				<td>Sesión:</td>
	  </tr>
			<tr>
				<td colspan="4">Facilitador(a): <b><?php echo $RowGF['NomFacilitador'];?></b></td>
				<td colspan="3">Gestor(a): <b><?php echo $RowGF['NomGestor'];?></b></td>
				<td><?php echo strftime("%a %e de %b / %Y",strtotime($mFecha1));?></td>
				<td><?php echo strftime("%a %e de %b / %Y",strtotime($mFecha2));?></td>
				<td><?php echo strftime("%a %e de %b / %Y",strtotime($mFecha3));?></td>
				<td><?php echo strftime("%a %e de %b / %Y",strtotime($mFecha4));?></td>
				<td><?php echo strftime("%a %e de %b / %Y",strtotime($mFecha5));?></td>
	  </tr>
			<tr align="center">
			  <th width="20">#</th>
			  <th width="85">Apellido 1</th>
			  <th width="85">Apellido 2</th>
			  <th width="85">Nombre 1</th>
			  <th width="85">Nombre 2</th>
			  <th width="85">N° de Documento</th>
			  <th width="32">Ciclo</th>
			  <th width="95">Firma Participante</th>
			  <th width="95">Firma Participante</th>
			  <th width="95">Firma Participante</th>
			  <th width="95">Firma Participante</th>
			  <th width="95">Firma Participante</th>
			</tr><?php
			$Queri1 = "";
			$Queri2 = "";
			for($Dia=1; $Dia<=5; $Dia++){
				$Queri1 .= ",IF(Cal".$Dia.".FAsistencia>0,Cal".$Dia.".TipoAsiste,Asis".$Dia.".TipoAsiste) AS TipoAsiste".$Dia;
				$Queri2 .= " LEFT JOIN ".$PrefBD."estudiantes.srpaasistencia Asis".$Dia." ON B.IDBase=Asis".$Dia.".IDBase
								AND ".intval($_GET['IDGrupo'])."=Asis".$Dia.".IDGrupo AND '".$_GET['Cod_Mate']."'=Asis".$Dia.".Cod_Mate AND '".${'mFecha'.$Dia}."'=Asis".$Dia.".FAsistencia
							LEFT JOIN ".$PrefBD."estudiantes.srpacalendario Cal".$Dia." ON G.IDGrupo=Cal".$Dia.".IDGrupo
								AND '".${'mFecha'.$Dia}."'=Cal".$Dia.".FAsistencia AND 'DESORDENDI'=Cal".$Dia.".TipoAsiste AND 0=Cal".$Dia.".Borrada";
			}
			$Queri = "DROP TABLE IF EXISTS ".$PrefBD."estudiantes.tmpsrpa";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			$Queri = "CREATE TEMPORARY TABLE ".$PrefBD."estudiantes.tmpsrpa(
					   SELECT B.IDBase,B.Codigo,E.RegistroC,E.Cedula,E.Ape1Alum,E.Ape2Alum,E.NomAlum, B.Grado,G.IDGrupo,
							IF(E.FNacio>0,(YEAR(CURDATE())-YEAR(E.FNacio))- (RIGHT(CURDATE(),5) < RIGHT(E.FNacio,5)),'') AS Edad".$Queri1."
						FROM ".$PrefBD."estudiantes.base B
						JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
						LEFT JOIN ".$PrefBD."estudiantes.srpagrupos G ON B.Aula=G.IDGrupo".$Queri2."
						WHERE LEFT(B.Codigo,2)='22' AND B.FMatri>0
							AND (G.IDGrupo=".intval($_GET['IDGrupo'])." OR
								B.IDBase IN (SELECT IDBase
											 FROM ".$PrefBD."estudiantes.srpaasistencia
											 WHERE IDGrupo=".intval($_GET['IDGrupo'])." AND Cod_Mate='".$_GET['Cod_Mate']."' AND FAsistencia IN('".$mFecha1."','".$mFecha2."','".$mFecha3."','".$mFecha4."','".$mFecha5."'))
							)
						)";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			$Queri = "SELECT * FROM ".$PrefBD."estudiantes.tmpsrpa
						ORDER BY Ape1Alum,Ape2Alum,NomAlum";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			if($Result->num_rows <= 35){
				$pdf->SetAutoPageBreak(TRUE,0);
			}else{
				$pdf->SetAutoPageBreak(TRUE,10);
			}
			$I = 0;
			while($Row = $Result->fetch_assoc()){
				$I++;?>
				<tr>
					<td align="right" height="15"><?php echo $I;?>&nbsp;</td>
					<td><?php echo $Row['Ape1Alum'];?></td>
					<td><?php echo $Row['Ape2Alum'];?></td>
					<td><?php echo SepararNombres($Row['NomAlum'],1);?></td>
					<td><?php echo SepararNombres($Row['NomAlum'],2);?></td>
					<td align="center"><?php echo $Row['Cedula'];?></td>
					<td align="center"><?php echo $Row['Grado'];?></td>
					<td>&nbsp;<?php echo ($Row['TipoAsiste1']=='ASISTE' ? 'Ok' : ucwords(strtolower(substr($Row['TipoAsiste1'],0,15))));?></td>
					<td>&nbsp;<?php echo ($Row['TipoAsiste2']=='ASISTE' ? 'Ok' : ucwords(strtolower(substr($Row['TipoAsiste2'],0,15))));?></td>
					<td>&nbsp;<?php echo ($Row['TipoAsiste3']=='ASISTE' ? 'Ok' : ucwords(strtolower(substr($Row['TipoAsiste3'],0,15))));?></td>
					<td>&nbsp;<?php echo ($Row['TipoAsiste4']=='ASISTE' ? 'Ok' : ucwords(strtolower(substr($Row['TipoAsiste4'],0,15))));?></td>
					<td>&nbsp;<?php echo ($Row['TipoAsiste5']=='ASISTE' ? 'Ok' : ucwords(strtolower(substr($Row['TipoAsiste5'],0,15))));?></td>
				</tr><?php
			}
			for($I=$Result->num_rows + 1; $I<=36; $I++){?>
				<tr>
					<td align="right" height="15"><?php echo $I;?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr><?php
			}?>
				<tr>
					<td colspan="2" rowspan="2"><b>NOVEDADES ASISTENCIA</b></td>
					<td><b>A</b>siste</td>
					<td><b>N</b>o <b>A</b>siste</td>
					<td><b>S</b>alud</td>
					<td><b>A</b>udiencia</td>
					<td colspan="3">&nbsp;</td>
					<td colspan="3" rowspan="2">&nbsp;</td>
				</tr>
				<tr>
	
					<td colspan="7">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="9" bgcolor="#CCCCCC"><b>Observación</b>: En la casilla de firma del participante, colocar en mayuscula la causa que corresponda</td>
					<td colspan="3" align="center"><b>FIRMA GESTOR</b></td>
				</tr>
		</table><?php
		$html=ob_get_clean();
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
	}
	mysqli_close($mysqli);
	//Close and output PDF document
	$pdf->Output('file.pdf', 'I');
	exit;
//**********************************************************************************************************
//REPORTES *************************************************************************************************
//**********************************************************************************************************
}else if ($_POST['TipoModificar']==md5('Jor9'.date('d')) and $_POST['TipoGrabar']==9 and $_POST['TipoReporte']){
	$QueriCampos='';
	foreach($_POST['CamposReporte'] as $Var){
		if($_POST['TipoReporte']=='6A' and $Var=='DiasAsistencia'){
			$mDiasAsistencia=1;
			unset($_POST['CamposReporte'][30]);	//Primero quito el elemnto del array para que no salga esa columna en el informe
		}else{
			$QueriCampos.=($QueriCampos ? ',' : '').$Var.' AS `'.$mCampos[$_POST['TipoReporte']][$Var].'`';
		}
	}
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="report.xlsx"');
	header('Cache-Control: max-age=0');
	set_time_limit(0);
	ini_set('memory_limit','-1');
	include("../librerias/PHPExcel/PHPExcel.php");
	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	$cacheSettings =    array( ' memoryCacheSize ' =>'8MB');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	//Rango de las columnas
	$RangeCols = range('A', 'Z');
	$azRange = range('A', 'Z');
	foreach($azRange as $V){
		$RangeCols[]="A".$V;	//Rango A hasta AZ
	}
	// Se crea el objeto PHPExcel
	$objPHPExcel = new PHPExcel();
	$estiloTituloReporte = array(
		'font' => array(
			'name'      => 'Verdana',
			'bold'      => true,
			'italic'    => false,
			'strike'    => false,
			'size' =>16,
			'color'     => array(
			'rgb' => 'EEEEEE'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
			'argb' => 'FF220835')
		),
		'borders' => array(
			'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_NONE
			)
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'rotation' => 0,
			'wrap' => TRUE
		)
	);
	$estiloTituloColumnas = array(
		'font' => array(
			'name'  => 'Tahoma',
			'size' =>8,
			'bold'  => true
		),
		'alignment' =>  array(
			'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'wrap'      => TRUE
		)
	);
	$estiloInformacion = new PHPExcel_Style();
	$estiloInformacion->applyFromArray(array(
		'font' => array(
			'name'  => 'Tahoma',
			'size' =>8,
			'color' => array(
			'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
			'argb' => 'CCDDEE')
		),
		'borders' => array(
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN ,
				'color' => array(
					'rgb' => '3a2a47'
				)
			)
		)
	));
	// Se asignan las propiedades del libro
	$objPHPExcel->getProperties()->setCreator("Codedrinks") // Nombre del autor
		->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modificó
		->setTitle("Reporte Excel con PHP y MySQL") // Titulo
		->setSubject("Reporte Excel con PHP y MySQL") //Asunto
		->setDescription("Reporte Cartera Académica") //Descripción
		->setKeywords("Reporte Cartera Académica") //Etiquetas
		->setCategory("Reporte excel"); //Categorias
	/***************************************************************************************************************************************
	6A
	***************************************************************************************************************************************/
	if($_POST['TipoReporte']=='6A'){//
		$Queri = "SET lc_time_names = 'es_ES'";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		//Para Organizar las consultas por si seleccionó Asistencia
		$mQueriAsistencia='';
		if($mDiasAsistencia){
			//Creo una tabla temporal con la asistencia de los últimos 12 meses
			$Queri = "DROP TABLE IF EXISTS ".$PrefBD."estudiantes.tmpasistencia";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			$Queri = "CREATE TEMPORARY TABLE ".$PrefBD."estudiantes.tmpasistencia(
						SELECT DISTINCT E.IDEstudiante, Asis.FAsistencia
						FROM estudiantes.srpaasistencia Asis
						JOIN estudiantes.base B ON Asis.IDBase=B.IDBase
						JOIN estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
						WHERE Asis.TipoAsiste='ASISTE' AND Asis.FAsistencia>=date_sub(CURDATE(),INTERVAL 12 month)
						)";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			//Obtengo los meses de la consulta, para agrupar por mes y para organizar los encabezados del reporte
			$Queri = "SELECT DISTINCT CONCAT(LEFT(MonthName(FAsistencia),3),YEAR(FAsistencia)) AS Mes
						FROM ".$PrefBD."estudiantes.tmpasistencia
						ORDER BY FAsistencia DESC";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			$mFields='';
			while($Row = $Result->fetch_assoc()){
				$mCampos[$_POST['TipoReporte']]=array_merge($mCampos[$_POST['TipoReporte']],array($Row['Mes']=>$Row['Mes']));	//Para los campos adicionales
				$_POST['CamposReporte']=array_merge($_POST['CamposReporte'],array($Row['Mes']));	//Para los campos adicionales
				$QueriCampos.=($QueriCampos ? ',' : '')." tmpAsis.".$Row['Mes'];
				$mFields.=", COUNT(IF(CONCAT(LEFT(MonthName(FAsistencia),3),YEAR(FAsistencia))= '".$Row['Mes']."',1,NULL)) AS ".$Row['Mes'];
			}
			$mQueriAsistencia="LEFT JOIN(
									SELECT IDEstudiante".$mFields."
									FROM ".$PrefBD."estudiantes.tmpasistencia
									GROUP BY IDEstudiante
									) AS tmpAsis ON E.IDEstudiante=tmpAsis.IDEstudiante";
		}
		//Obtengo los datos del Período, esto me sirve particularmente para saber el año, cuando consulte los estudiantes
		$Queri = "SELECT ".$QueriCampos."
					FROM ".$PrefBD."estudiantes.base B
					JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
					LEFT JOIN ".$PrefBD."notas.grado_pro GP ON B.Grado=GP.Grado
					LEFT JOIN ".$PrefBD."estudiantes.srpagrupos G ON B.Aula=G.IDGrupo
					LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					LEFT JOIN ".$PrefBD."recursos.emplea Ge ON C.Gestor=Ge.Nit_CCE
					LEFT JOIN ".$PrefBD."recursos.emplea Fa ON G.Facilitador=Fa.Nit_CCE
					".$mQueriAsistencia."
					WHERE LEFT(B.Codigo,2)='22' AND B.FMatri>0 AND B.Anio='".$_POST['Anio']."'";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		// Se agregan los titulos del reporte
		$QueriCampos='$objPHPExcel->setActiveSheetIndex(0)';
		$Col=0;
		/*
		print_r($_POST['CamposReporte']);
		print_r($mCampos['ReporteAsistencia']);
		print_r($mCampos[$_POST['TipoReporte']]);
		exit;
		*/
		foreach($_POST['CamposReporte'] as $Var){
			$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].'1",  "'.$mCampos[$_POST['TipoReporte']][$Var].'")';
			$Col++;
		}
		$LastCol = $Col -1;
		$QueriCampos.=';';
		eval($QueriCampos);
		//Se agregan los datos de los alumnos
		$Fil = 2; //Numero de fila donde se va a comenzar a rellenar
		while($Row = $Result->fetch_assoc()){
			$QueriCampos='$objPHPExcel->setActiveSheetIndex(0)';
			$Col=0;
			foreach($_POST['CamposReporte'] as $Var){
				$Valor=$Row[$mCampos[$_POST['TipoReporte']][$Var]];
				if(strlen($Valor)==10 and DarFecha($Valor)>0){
					$Valor=DarFecha($Valor);
				}
				$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].$Fil.'",  "'.$Valor.'")';
				$Col++;
			}
			$QueriCampos.=';';
			eval($QueriCampos);
			$Fil++;
		}
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Informe');
		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		// Autofiltro
		//$objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$RangeCols[$LastCol].($Fil-1));
	}elseif($_POST['TipoReporte']=='ReporteAsistencia'){//
		//Por si acaso, optimizo las variables para el filtro por fecha
		if($_POST['FiltroFecha1'] and $_POST['FiltroFecha2']){
			//Nothing bhere
		}elseif($_POST['FiltroFecha1']){
			$_POST['FiltroFecha2']=$_POST['FiltroFecha1'];
		}elseif($_POST['FiltroFecha2']){
			$_POST['FiltroFecha1']=$_POST['FiltroFecha2'];
		}
		$mCampos['ReporteAsistencia']=array_merge($mCampos['ReporteAsistencia'],array('Fecha'=>'Fecha'));	//Para los campos adicionales
		$_POST['CamposReporte']=array_merge($_POST['CamposReporte'],array('Fecha'=>'Fecha'));	//Para los campos adicionales
		$QueriCampos.=($QueriCampos ? ',' : '')." tmpAsis.FAsistencia AS `Fecha`";
		$Queri = "SELECT Cod_Mate, Nom_Mate
					FROM ".$PrefBD.$RowCol['RutaNotas']."materia
					ORDER BY Nom_Mate";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$mFields='';
		while($Row = $Result->fetch_assoc()){
			$mCampos['ReporteAsistencia']=array_merge($mCampos['ReporteAsistencia'],array($Row['Nom_Mate']=>$Row['Nom_Mate']));	//Para los campos adicionales
			$_POST['CamposReporte']=array_merge($_POST['CamposReporte'],array($Row['Nom_Mate']=>$Row['Nom_Mate']));	//Para los campos adicionales
			$QueriCampos.=($QueriCampos ? ',' : '')." tmpAsis.".$Row['Cod_Mate'].' AS `'.$Row['Nom_Mate'].'`';
			$mFields.=$Row['Cod_Mate']." varchar(10) NOT NULL DEFAULT '',";
		}
		//Creo una tabla temporal con las fechas de consulta como registros, esto se hace así, porque no sabemos en cual Bloque va a ir registrada la falla
		//Además es la forma más eficiente para obtener la consulta
		$Queri = "DROP TABLE IF EXISTS ".$PrefBD."estudiantes.tmpasistencia";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$Queri = "CREATE TEMPORARY TABLE ".$PrefBD."estudiantes.tmpasistencia(
							`IDBase` int(11) NOT NULL DEFAULT '0',
							`FAsistencia` date NOT NULL,".
							$mFields."
							EsCicloSuperior SMALLINT(1) NOT NULL DEFAULT 0,
							PRIMARY KEY (`IDBase`,`FAsistencia`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		//Paso todos los datos de la tabla de asistencia a la tabla temporal
		$Queri = "SELECT Asis.IDAsistencia,Asis.IDBase,Asis.IDGrupo,Asis.Cod_Mate,Asis.FAsistencia,IFNULL(Cal.TipoAsiste,Asis.TipoAsiste) AS TipoAsiste
				FROM ".$PrefBD."estudiantes.srpaasistencia Asis
				LEFT JOIN ".$PrefBD."estudiantes.srpacalendario Cal ON Asis.IDGrupo=Cal.IDGrupo AND Asis.FAsistencia=Cal.FAsistencia AND 'DESORDENDI'=Cal.TipoAsiste AND 0=Cal.Borrada
				WHERE Asis.Borrada=0 AND Asis.TipoAsiste<>'' AND Asis.FAsistencia BETWEEN '".DarFechaSQL($_POST['FiltroFecha1'])."' AND '".DarFechaSQL($_POST['FiltroFecha2'])."'";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		while($Row = $Result->fetch_assoc()){
			$QueriINS = "INSERT INTO ".$PrefBD."estudiantes.tmpasistencia(IDBase,FAsistencia,".$Row['Cod_Mate'].")VALUES(".intval($Row['IDBase']).",'".$Row['FAsistencia']."','".$Row['TipoAsiste']."')
						ON DUPLICATE KEY UPDATE ".$Row['Cod_Mate']."='".$Row['TipoAsiste']."'";
//			echo $QueriINS.';<br>';
			$ResultINS = $mysqli->query($QueriINS) or die(mysqli_error($mysqli));
			/*    Esto ya no aplicaría porque se optimizó el $QueriINS
			$QueriUPT="UPDATE ".$PrefBD."estudiantes.tmpasistencia
						SET ".$Row['Cod_Mate']."='".$Row['TipoAsiste']."'
						WHERE IDBase=".intval($Row['IDBase'])." AND FAsistencia='".$Row['FAsistencia']."'
						LIMIT 1";
//			echo $QueriUPT.';<br>';
			$ResultUPT = $mysqli->query($QueriUPT) or die(mysqli_error($mysqli));
			*/
		}
		$Filtrico="";
		//Obtengo los datos del Período, esto me sirve particularmente para saber el año, cuando consulte los estudiantes
		$Queri = "SELECT ".$QueriCampos."
					FROM ".$PrefBD."estudiantes.base B
					JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
					JOIN ".$PrefBD."estudiantes.srpagrupos G ON B.Aula=G.IDGrupo
					LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					LEFT JOIN ".$PrefBD."recursos.emplea Ge ON C.Gestor=Ge.Nit_CCE
					LEFT JOIN ".$PrefBD."recursos.emplea Fa ON G.Facilitador=Fa.Nit_CCE
					JOIN ".$PrefBD."estudiantes.tmpasistencia tmpAsis ON B.IDBase=tmpAsis.IDBase
					WHERE B.IDBase>0".$Filtrico."
					ORDER BY tmpAsis.FAsistencia";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		// Se agregan los titulos del reporte
		$QueriCampos='$objPHPExcel->setActiveSheetIndex(0)';
		$Col=0;
		foreach($_POST['CamposReporte'] as $Var){
			$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].'1",  "'.$mCampos[$_POST['TipoReporte']][$Var].'")';
			$Col++;
		}
		$LastCol = $Col -1;
		$QueriCampos.=';';
		eval($QueriCampos);
		//Se agregan los datos de los alumnos
		$Fil = 2; //Numero de fila donde se va a comenzar a rellenar
		while($Row = $Result->fetch_assoc()){
			$QueriCampos='$objPHPExcel->setActiveSheetIndex(0)';
			$Col=0;
			foreach($_POST['CamposReporte'] as $Var){
				$Valor=$Row[$mCampos[$_POST['TipoReporte']][$Var]];
				if(strlen($Valor)==10 and DarFecha($Valor)>0){
					$Valor=DarFecha($Valor);
				}
				$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].$Fil.'",  "'.$Valor.'")';
				$Col++;
			}
			$QueriCampos.=';';
			eval($QueriCampos);
			$Fil++;
		}
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Informe');
		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		// Autofiltro
		//$objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$RangeCols[$LastCol].($Fil-1));
	}elseif($_POST['TipoReporte']=='ReporteNotas'){//
		$QueriMat = "SELECT Cod_Mate, Nom_Mate
					FROM ".$PrefBD.$RowCol['RutaNotas']."materia
					ORDER BY Nom_Mate";
		$ResultMat = $mysqli->query($QueriMat) or die(mysqli_error($mysqli));
		$mFields='';
		while($RowMat = $ResultMat->fetch_assoc()){
			$mMateria[$RowMat['Cod_Mate']]=$RowMat['Nom_Mate'];
			$mCampos['ReporteNotas']=array_merge($mCampos['ReporteNotas'],array($RowMat['Cod_Mate'].'_CM1'=>$RowMat['Cod_Mate'].'_CM1',
																				$RowMat['Cod_Mate'].'_CM2'=>$RowMat['Cod_Mate'].'_CM2',
																				$RowMat['Cod_Mate'].'_CM3'=>$RowMat['Cod_Mate'].'_CM3',
																				$RowMat['Cod_Mate'].'_CM4'=>$RowMat['Cod_Mate'].'_CM4',
																				$RowMat['Cod_Mate'].'_CDM'=>$RowMat['Cod_Mate'].'_CDM'));	//Para los campos adicionales
			$_POST['CamposReporte']=array_merge($_POST['CamposReporte'],array($RowMat['Cod_Mate'].'_CM1'=>$RowMat['Cod_Mate'].'_CM1',
																			  $RowMat['Cod_Mate'].'_CM2'=>$RowMat['Cod_Mate'].'_CM2',
																			  $RowMat['Cod_Mate'].'_CM3'=>$RowMat['Cod_Mate'].'_CM3',
																			  $RowMat['Cod_Mate'].'_CM4'=>$RowMat['Cod_Mate'].'_CM4',
																			  $RowMat['Cod_Mate'].'_CDM'=>$RowMat['Cod_Mate'].'_CDM'));	//Para los campos adicionales
			$QueriCampos.=($QueriCampos ? ',' : '')." tmpNot.".$RowMat['Cod_Mate']."_CM1,
													  tmpNot.".$RowMat['Cod_Mate']."_CM2,
													  tmpNot.".$RowMat['Cod_Mate']."_CM3,
													  tmpNot.".$RowMat['Cod_Mate']."_CM4,
													  tmpNot.".$RowMat['Cod_Mate']."_CDM";
			$mFields.=$RowMat['Cod_Mate']."_CM1 decimal(3,1) DEFAULT NULL,".
					  $RowMat['Cod_Mate']."_CM2 decimal(3,1) DEFAULT NULL,".
					  $RowMat['Cod_Mate']."_CM3 decimal(3,1) DEFAULT NULL,".
					  $RowMat['Cod_Mate']."_CM4 decimal(3,1) DEFAULT NULL,".
					  $RowMat['Cod_Mate']."_CDM decimal(3,1) DEFAULT NULL,";
		}
		//Creo una tabla temporal con la Matríz de las Notas por Estudiante y por Materia Organizada hacia la derecha, es la forma más eficiente para obtener la consulta
		$Queri = "DROP TABLE IF EXISTS ".$PrefBD."estudiantes.tmpnotas";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$Queri = "CREATE TEMPORARY TABLE ".$PrefBD."estudiantes.tmpnotas(
							`Codigo` VARCHAR(8) NOT NULL DEFAULT '',".
							$mFields."
							PRIMARY KEY (`Codigo`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		//Paso todos los datos desde Notas a la tabla temporal
		$ResultMat->data_seek(0);
		while($RowMat = $ResultMat->fetch_assoc()){
			$QueriINS = "INSERT INTO ".$PrefBD."estudiantes.tmpnotas(Codigo,".$RowMat['Cod_Mate']."_CM1,"
																			 .$RowMat['Cod_Mate']."_CM2,"
																			 .$RowMat['Cod_Mate']."_CM3,"
																			 .$RowMat['Cod_Mate']."_CM4,"
																			 .$RowMat['Cod_Mate']."_CDM)
						SELECT EM.Codigo, EM.CM1 AS ".$RowMat['Cod_Mate']."_CM1,
									   EM.CM2 AS ".$RowMat['Cod_Mate']."_CM2,
									   EM.CM3 AS ".$RowMat['Cod_Mate']."_CM3,
									   EM.CM4 AS ".$RowMat['Cod_Mate']."_CM4,
									   EM.CDM AS ".$RowMat['Cod_Mate']."_CDM
						FROM ".$PrefBD.$RowCol['RutaNotas']."estu_mate EM
						WHERE EM.Cod_Mate='".$RowMat['Cod_Mate']."'
						ON DUPLICATE KEY UPDATE ".$RowMat['Cod_Mate']."_CM1=EM.CM1,
												".$RowMat['Cod_Mate']."_CM2=EM.CM2,
												".$RowMat['Cod_Mate']."_CM3=EM.CM3,
												".$RowMat['Cod_Mate']."_CM4=EM.CM4,
												".$RowMat['Cod_Mate']."_CDM=EM.CDM";
			$ResultINS = $mysqli->query($QueriINS) or die(mysqli_error($mysqli));
		}
		$Queri = "SELECT ".$QueriCampos."
					FROM ".$PrefBD."estudiantes.base B
					JOIN ".$PrefBD."estudiantes.estudiante E ON B.IDEstudiante=E.IDEstudiante
					JOIN ".$PrefBD."estudiantes.srpagrupos G ON B.Aula=G.IDGrupo
					LEFT JOIN ".$PrefBD."estudiantes.srpacentros C ON G.CodCentro=C.CodCentro
					LEFT JOIN ".$PrefBD."recursos.emplea Ge ON C.Gestor=Ge.Nit_CCE
					LEFT JOIN ".$PrefBD."recursos.emplea Fa ON G.Facilitador=Fa.Nit_CCE
					JOIN ".$PrefBD."estudiantes.tmpnotas tmpNot ON B.Codigo=tmpNot.Codigo
					WHERE LEFT(B.Codigo,2)='22' AND B.FMatri>0 AND B.Anio='".$_POST['Anio']."'";
//		echo $Queri.';<br>';
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		// Se agregan los titulos del reporte
		$QueriCampos='$objPHPExcel->setActiveSheetIndex(0)';
		$Col=0;
		$ColAutoAjuste=0;//Para saber desde qué columna autoajusto
		foreach($_POST['CamposReporte'] as $Var){
			//Títulos de las materias en la fila 1
			if(substr($Var,3,4)=='_CM1'){
				$ColAutoAjuste=($ColAutoAjuste>0 ? $ColAutoAjuste : $Col);
				$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].'1",  "'.$mMateria[substr($Var,0,3)].'")';
				$objPHPExcel->getActiveSheet()->mergeCells($RangeCols[$Col]."1:".$RangeCols[$Col+4]."1");
			}
			//Títulos de los campos en la fila 2
			$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].'2",  "'.(substr($Var,3,2)=='_C' ? ((substr($Var,6,1)>0 ? substr($Var,6,1) :'F')) : $mCampos[$_POST['TipoReporte']][$Var]).'")';
			$Col++;
		}
		$LastCol = $Col -1;
		$QueriCampos.=';';
		eval($QueriCampos);
		//Se agregan los datos de los alumnos
		$Fil = 3; //Numero de fila donde se va a comenzar a rellenar
		while($Row = $Result->fetch_assoc()){
			$QueriCampos='$objPHPExcel->setActiveSheetIndex(0)';
			$Col=0;
			foreach($_POST['CamposReporte'] as $Var){
				$Valor=$Row[$mCampos[$_POST['TipoReporte']][$Var]];
				if(strlen($Valor)==10 and DarFecha($Valor)>0){
					$Valor=DarFecha($Valor);
				}
				$QueriCampos.=' ->setCellValue("'.$RangeCols[$Col].$Fil.'",  "'.$Valor.'")';
				$Col++;
			}
			$QueriCampos.=';';
			eval($QueriCampos);
			$Fil++;
		}
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Informe');
		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$RangeCols[$LastCol].'2')->applyFromArray($estiloTituloColumnas);
		//Ancho automático para las columnas
		foreach(range($RangeCols[$ColAutoAjuste],$RangeCols[$LastCol]) as $Col){
			$objPHPExcel->getActiveSheet()->getColumnDimension($Col)->setAutoSize(true);
		}
		// Autofiltro
		$objPHPExcel->getActiveSheet()->setAutoFilter('A2:'.$RangeCols[$LastCol].($Fil-1));
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	mysqli_close($mysqli);
	$objPHPExcel->disconnectWorksheets();
	$objPHPExcel->garbageCollect();
	unset($objPHPExcel);
	exit;
/****************************************************************************************************************************************
FechasPlanillas	Fechas para edición de planillas
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']=='FechasPlanillas' and $_POST['TipoModificar']==md5('FechasPlanillas'.date('d'))){
	$Queri= "UPDATE ".$PrefBD."estudiantes.colegio
			SET P1FinDigi='".DarFechaSQL($_POST['P1FinDigi'])." ".$_POST['P1FinDigiH']."',
				P2FinDigi='".DarFechaSQL($_POST['P2FinDigi'])." ".$_POST['P2FinDigiH']."',
				P3FinDigi='".DarFechaSQL($_POST['P3FinDigi'])." ".$_POST['P3FinDigiH']."',
				P4FinDigi='".DarFechaSQL($_POST['P4FinDigi'])." ".$_POST['P4FinDigiH']."'
			WHERE CodCole='22' AND AnioActual='".$_GET['Anio']."'
			LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$Queri=str_replace("estudiantes.colegio", "estudiantes.colegioanio", $Queri);
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
ADJUNTAR ARCHIVO PDF AL REGISTRO DE LA BASE
****************************************************************************************************************************************/
}elseif ($_POST['TipoModificar']==md5('JorFile'.date('d')) and $_POST['TipoGrabar']=='File' and $_POST['FileName']){
	$upload_folder ='../../Archivos/Estudiantes';
	$nombre_archivo = $_POST['FileName'].".pdf";
	$archivador = $upload_folder . '/' . $nombre_archivo;
	$tmp_archivo = $_FILES['inputFile']['tmp_name'];
	move_uploaded_file($tmp_archivo, $archivador);
	mysqli_close($mysqli);
	exit;
}?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>:: <?php echo $Empresa;?> - Sistema de Responsabilidad Penal Adolescente::</title>
<link rel="stylesheet" href="../librerias/jquery/jquery-ui.css">
<script src="../librerias/jquery/jquery-1.10.2.js"></script>
<script src="../librerias/jquery/jquery-ui.js"></script>
<link rel="stylesheet" href="../librerias/bootstrap-3.3.7-dist/css/bootstrap.min.css">
<script src="../librerias/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<style>
<!--
/* Hide all steps by default: */
.tab {
  display: <?php echo 'none';?>;
}

/* Make circles that indicate the steps of the form: */
.step {
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbbbbb;
  border: none;  
  border-radius: 50%;
  display: inline-block;
  opacity: 0.5;
}

.step.active {
  opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
  background-color: #4CAF50;
}
.myAlert-top{
    position: fixed;
    top: 5px; 
    left:2%;
    width: 96%;
}
.myAlert-bottom{
    position: fixed;
    top: 100px;
/*    bottom: 200px;*/
    left:2%;
    width: 96%;
}
.fixed{
	height:200px;
	overflow:hidden;
	background:lightblue;
}
.scrollit{
	height:300px;
	overflow-y:scroll;
}
.container{
	width:99%;
}
#overlay {
	position:fixed;
	top: 0;
	left: 0;
	width: 100%;
	z-index:1;
	background-color: rgba(9, 77, 181, 0.7); /* CSS3 */
	background:#001f3f; /* IE Explorer */
	opacity:0.7; /* IE Explorer */
	filter:alpha(opacity=70); /* IE Explorer */
}
.ui-autocomplete{
	z-index:2147483647;
}
.numberCircle {
    border-radius: 50%;
    behavior: url(PIE.htc); /* remove if you don't care about IE8 */
    width: 30px;
    height: 23px;
    padding: 4px;
    background: #fff;
    border: 1px solid black;
    color: black;
    text-align: center;
    font: 10px Arial, sans-serif;
    display: inline-block;
}
-->
</style>
<SCRIPT language=JavaScript src="../funciones.js"></SCRIPT>
<script type="text/javascript">
//Esta función es para que en jquery contains busque independiente de mayúsculas o minúsculas
jQuery.expr[':'].contains = function(a, i, m) {
	  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};
$(function(){
	$('#Minuta').css({position : 'absolute'});
	$("#NomEstudiante1_2,#NomEstudiante2").autocomplete({
		source: "index.php?TipoModificar=<?php echo md5('AjaxJor2Estudiante'.date('d'));?>&IDGrupo=<?php echo $_GET['IDGrupo'];?>&Anio=<?php echo $_GET['Anio'];?>",
		minLength: 3,
		autoFocus: true,
		change: function (event, ui){
										mSuf = $(this).attr("id");
										mSuf = mSuf.replace("NomEstudiante", "");
										if(ui.item == null || ui.item == undefined){
											$(this).val("");
											document.getElementById('IDBase'+mSuf).value = '';
											document.getElementById('DivDatosEstudiante'+mSuf).innerHTML = '';
											document.getElementById('ObsHistorial'+mSuf).value = "";
										}else{
											var str = ui.item.value;
											var res = str.split("|-|");
											if(res[1] && res[2]){
												document.getElementById('IDBase'+mSuf).value = res[2];
												mTexto=res[3].replace(';','<br>');//Lo hago así porque o sino solo reemplaza el primer ;
												mTexto=mTexto.replace(';','<br>');//Lo hago así porque o sino solo reemplaza el primer ;
												mTexto=mTexto.replace(';','<br>');//Lo hago así porque o sino solo reemplaza el primer ;
												document.getElementById('DivDatosEstudiante'+mSuf).innerHTML = mTexto;
											}else{
												document.getElementById('IDBase'+mSuf).value = '';
												document.getElementById('DivDatosEstudiante'+mSuf).innerHTML = '';
												document.getElementById('ObsHistorial'+mSuf).value = "";
											}
									  	}
									}
	});
	$("#Gestor3,#Facilitador7").autocomplete({
		source: "index.php?TipoModificar=<?php echo md5('AjaxJor4Empleado'.date('d'));?>&Anio=<?php echo $_GET['Anio'];?>",
		minLength: 3,
		autoFocus: true,
		change: function (event, ui){
										if(ui.item == null || ui.item == undefined){
											$(this).val("");
									  	}
									}
	});
	$("#LNacio,#LugarExped,#Localidad").autocomplete({
		source: "index.php?TipoModificar=<?php echo md5('AjaxJorA1BuscarCiudad'.date('d'));?>",
		minLength: 3,
		autoFocus: true,
		change: function (event, ui){
										if(ui.item == null || ui.item == undefined){
											$(this).val("");
									  	}
									}
	});
	$("#Etnia").autocomplete({
		source: "index.php?TipoModificar=<?php echo md5('AjaxJorA1Etnia'.date('d'));?>",
		minLength: 3,
		autoFocus: true,
		change: function (event, ui){
										if(ui.item == null || ui.item == undefined){
											$(this).val("");
									  	}
									}
	});
});
$(document).ready(function(){
	$("select,input,textarea").change(function(){
		document.getElementById('HuboCambio').value = 1;
	});
});
$(function(){//Para Fechas
	$.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '&#x3c;Ant',
        nextText: 'Sig&#x3e;',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    }
	$.datepicker.setDefaults($.datepicker.regional['es']);
    $( "#FiltroFecha1,#FiltroFecha2,#FMatri,#FIngreso,#FSIMAT,#FNacio,#FAsistencia,#P1FinDigi,#P2FinDigi,#P3FinDigi,#P4FinDigi").datepicker({ dateFormat: 'dd-mm-yy' });
});
function MostrarDatoObser(msg, mTipo=false){
	if(mTipo){
		mDiv = 'DatoObser';
	}else{
		mDiv = 'DatoObserRed';
	}
	if(msg){
		document.getElementById(mDiv).innerHTML=msg;
	}
	$("#"+mDiv).show();
	setTimeout(function(){
				$("#"+mDiv).hide();
		}, 2000);
}
var currentTab = 0; // Current tab is set to be the first tab (0)
function IniciarMatricula(mIDBase){
	$("form").each(function(){
   		$(this).trigger("reset");
		$(this).find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	});
	$('input[id^="IDBase"]').each(function(){
		$(this).val(mIDBase);
	});
	currentTab = 0;
	$("form").each(function(){
   		$(this).trigger("reset");
		$(this).find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	});
	if(mIDBase!='Nuevo'){//Traigo los datos del estudiante que se quiere actualizar
		MostrarExistente(mIDBase);
	}else{
	}
	showTab(0);
	$("#ModalMatriculaEstudiante").modal({backdrop:'static',keyboard: false});
}
function MostrarExistente(mIDBase){
	$.ajax({
		type: "get",
		url: 'index.php',
		data: 'TipoModificar=<?php echo md5('Ajax1JorA1Estudiante'.date('d'));?>&IDBase='+mIDBase+'&Anio=<?php echo $_GET['Anio'];?>',
		cache: false,
		dataType: 'json',
		success: function(data){ //Si se ejecuta correctamente
			if(data.Mensaje=="Error"){
				MostrarDatoObser("Se presentó un error");
			}else{
				document.getElementById('Ape1Alum').value = data.Ape1Alum;
				document.getElementById('Ape2Alum').value = data.Ape2Alum;
				document.getElementById('NomAlum').value = data.NomAlum;
				document.getElementById('FNacio').value = data.FNacio;
				document.getElementById('LNacio').value = data.LNacio;
				document.getElementById('RegistroC').value = data.RegistroC;
				document.getElementById('Cedula').value = data.Cedula;
				document.getElementById('LugarExped').value = data.LugarExped;		//Ciudad y Depto de Expedición
				document.getElementById('Sexo').value = data.Sexo;
				document.getElementById('CabezaFamilia').value = data.CabezaFamilia;	//Estado Civil
				document.getElementById('Etnia').value = data.Etnia;
				document.getElementById('VictimaConflicto').value = data.VictimaConflicto;	//Personas a cargo
				document.getElementById('BarrAlum').value = data.BarrAlum;
				document.getElementById('DirAlum').value = data.DirAlum;
				document.getElementById('Localidad').value = data.Localidad;		//Ciudad Residencia
				document.getElementById('ZonaResidencia').value = data.ZonaResidencia;
				document.getElementById('TelAlum').value = data.TelAlum;
				document.getElementById('Celular').value = data.Celular;
				document.getElementById('Mail').value = data.Mail;
				document.getElementById('PuntajeSisben').value = data.PuntajeSisben;
				document.getElementById('VictimaCual0').checked = (data.Desplazado==1 ? true : false);	//Desplazado?
				document.getElementById('VictimaCual1').checked = (data.Desmovilizado==1 ? true : false);	//Desmovilizado?
				document.getElementById('Compromiso').value = data.Compromiso;		//Ocupación
				document.getElementById('CapExcepcionales').value = data.CapExcepcionales;	//Interés desempeño laboral
				document.getElementById('Grado').value = data.Grado;				//Ciclo
				document.getElementById('Grado').readOnly=(data.Grado ? true : false);
				document.getElementById('CodCentro').value = data.CodCentro;		//Viene Asociado al Grupo
				CambioCentro();
				document.getElementById('Aula').value = data.Aula;		//Grupo
				document.getElementById('FMatri').value = data.FMatri;
				document.getElementById('FIngreso').value = data.FIngreso;
				document.getElementById('FSIMAT').value = data.FSIMAT;
				document.getElementById('CodConvenio').value = data.CodConvenio;
				document.getElementById('TipoDocumentoM').value = data.TipoDocumentoM;
				document.getElementById('NumDocM').value = data.NumDocM;
				document.getElementById('Ape1FamiM').value = data.Ape1FamiM;
				document.getElementById('Ape2FamiM').value = data.Ape2FamiM;
				document.getElementById('NomFamiM').value = data.NomFamiM;
				document.getElementById('CelularM').value = data.CelularM;
				document.getElementById('TelefonoM').value = data.TelefonoM;
				document.getElementById('TipoDocumentoP').value = data.TipoDocumentoP;
				document.getElementById('NumDocP').value = data.NumDocP;
				document.getElementById('Ape1FamiP').value = data.Ape1FamiP;
				document.getElementById('Ape2FamiP').value = data.Ape2FamiP;
				document.getElementById('NomFamiP').value = data.NomFamiP;
				document.getElementById('CelularP').value = data.CelularP;
				document.getElementById('TelefonoP').value = data.TelefonoP;
				document.getElementById('DirFamiM').value = data.DirFamiM;
				document.getElementById('BarrFamiM').value = data.BarrFamiM;
				document.getElementById('Estrato').value = data.Estrato;
				document.getElementById('FotocopiaDI').checked = (data.FotocopiaDI==1 ? true : false);
				document.getElementById('EPS').checked = (data.EPS==1 ? true : false);
				document.getElementById('ActaMaterial').checked = (data.ActaMaterial==1 ? true : false);
			}
		},
		error: function(data){
			MostrarDatoObser("Se presentó un error");
			return false;
		}
	});
}
function showTab(n){
	// This function will display the specified tab of the form...
	var x = document.getElementsByClassName("tab");
	for(var i=0; i<x.length; i++){
		if(i==n){
			x[n].style.display = "block";
		}else{
			x[i].style.display = "none";
		}
	}
	//... and fix the Previous/Next buttons:
	if(currentTab == 0){//estaba n == 0
		document.getElementById("prevBtn").style.display = "none";
	}else{
		document.getElementById("prevBtn").style.display = "inline";
	}
	if(n == (x.length - 1)){
		document.getElementById("nextBtn").innerHTML = "Terminar";
	}else{
		document.getElementById("nextBtn").innerHTML = "Siguiente";
	}
	//... and run a function that will display the correct step indicator:
	fixStepIndicator(n)
}
function nextPrev(n){
	// This function will figure out which tab to display
	var x = document.getElementsByClassName("tab");
	// Exit the function if any field in the current tab is invalid:
	if(n == 1 && !validateForm()) return false;
	// Hide the current tab:
	x[currentTab].style.display = "none";
	/*Paso al tab requerido dependiendo de que existan cédula de papá y mamá o acudiente*/
	// Increase or decrease the current tab by 1:
	currentTab = currentTab + n;
	// if you have reached the end of the form...
	if(currentTab >= x.length){
		// ... the form gets submitted:
	}
	// Otherwise, display the correct tab:
	showTab(currentTab);
}

function validateForm(){
	/*
	0	Datos Personales
	1	Estado y Ubicabilidad
	2	Información Académica
	3	Datos Familiares
	4	Historial Académico y Documentos
	*/
	// This function deals with validation of the form fields
	var x, y, i, mRetorno = true;
    /*if(currentTab==0){	//0	Datos Personales
	}else if(currentTab==1){	//0	Datos Personales
	}else if(currentTab==2){	//0	Datos Personales
	}else if(currentTab==3){	//0	Datos Personales
	}else */
	if(currentTab==0){	//0	Datos Personales
		ele=document.getElementById('Ape1Alum');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('NomAlum');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('FNacio');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('LNacio');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('RegistroC');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('Cedula');if(ele.value.length>4){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm0');
			Frm.TipoGrabar.value='<?php echo md5('Tipo1_0'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('JorA1_0'.date('d'));?>';
			var myData = $("#Frm0").serialize();
			$.ajax({
				url:'index.php',
				type:'post',
				cache: false,
				data: myData
			}).done(function(html){
				var res = html.split("|-|");
				if(res[1]){
					$('input[id^="IDBase"]').each(function(){
						$(this).val(res[1]);
					});
					if(res[0]=='EXISTE'){
						MostrarDatoObser('Registro Existente, actualice el resto de datos',true);
						MostrarExistente(res[1]);
					}else{
						MostrarDatoObser('El Registro se creó, continue ingresando el resto de datos',true);
					}
				}else if(html==''){
					document.getElementById('HuboCambio').value=0;
					MostrarDatoObser('Datos Grabados',true);
				}else{
					MostrarDatoObser(html);
				}
			});
		}
	}else if(currentTab==1){	//1	Estado y Ubicabilidad
		ele=document.getElementById('Sexo');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('CabezaFamilia');if(ele.value!=''){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('Etnia');if(ele.value!=''){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('BarrAlum');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('DirAlum');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('PuntajeSisben');if(ele.value>=-1 && ele.value<=100){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm1');
			Frm.TipoGrabar.value='<?php echo md5('Tipo1_1'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('JorA1_1'.date('d'));?>';
			var myData = $("#Frm1").serialize();
			$.ajax({
				url:'index.php',
				type:'post',
				cache: false,
				data: myData
			}).done(function(html){
				if(html==''){
					document.getElementById('HuboCambio').value=0;
					MostrarDatoObser('Datos Grabados',true);
				}else{
					MostrarDatoObser(html);
				}
			});
		}
	}else if(currentTab==2){	//2	Información Académica
		ele=document.getElementById('Grado');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm2');
			Frm.TipoGrabar.value='<?php echo md5('Tipo1_2'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('JorA1_2'.date('d'));?>';
			var myData = $("#Frm2").serialize();
			$.ajax({
				url:'index.php',
				type:'post',
				cache: false,
				data: myData
			}).done(function(html){
				if(html==''){
					document.getElementById('HuboCambio').value=0;
					MostrarDatoObser('Datos Grabados',true);
				}else{
					MostrarDatoObser(html);
				}
			});
		}
	}else if(currentTab==3){	//3	Datos Familiares
		ele=document.getElementById('Grado');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('NumDocM');if(ele.value=="0" || ele.value.length>4){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		if(document.getElementById('NumDocM').value.length>4){
			ele=document.getElementById('TipoDocumentoM');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Ape1FamiM');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('NomFamiM');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			if(document.getElementById('CelularM').value || document.getElementById('TelefonoM').value){
				document.getElementById('CelularM').classList.remove("alert-danger");
				document.getElementById('TelefonoM').classList.remove("alert-danger");
			}else{
				ele=document.getElementById('CelularM');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			}
		}
		ele=document.getElementById('NumDocP');if(ele.value=="0" || ele.value.length>4){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		if(document.getElementById('NumDocP').value.length>4){
			ele=document.getElementById('TipoDocumentoP');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Ape1FamiP');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('NomFamiP');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			if(document.getElementById('CelularP').value || document.getElementById('TelefonoP').value){
				document.getElementById('CelularP').classList.remove("alert-danger");
				document.getElementById('TelefonoP').classList.remove("alert-danger");
			}else{
				ele=document.getElementById('CelularP');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			}
		}
		ele=document.getElementById('Estrato');if(ele.value!=""){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm3');
			Frm.TipoGrabar.value='<?php echo md5('Tipo1_3'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('JorA1_3'.date('d'));?>';
			var myData = $("#Frm3").serialize();
			$.ajax({
				url:'index.php',
				type:'post',
				cache: false,
				data: myData
			}).done(function(html){
				if(html==''){
					document.getElementById('HuboCambio').value=0;
					MostrarDatoObser('Datos Grabados',true);
				}else{
					MostrarDatoObser(html);
				}
			});
		}
	}else if(currentTab==4){	//4	Historial Académico y Documentos
		//Nothing here
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm4');
			Frm.TipoGrabar.value='<?php echo md5('Tipo1_4'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('JorA1_4'.date('d'));?>';
			var myData = $("#Frm4").serialize();
			$.ajax({
				url:'index.php',
				type:'post',
				cache: false,
				data: myData
			}).done(function(html){
				if(html==''){
					MostrarDatoObser("El registro se grabó satisfactoriamente.",true);
					setTimeout("location.reload()", 800);
				}else{
					MostrarDatoObser(html);
				}
			});
		}else{//Igul refresco así sea que en el último pantallazo no hubo cambios
			MostrarDatoObser("El registro se grabó satisfactoriamente.",true);
			setTimeout("location.reload()", 800);
		}
	}
	x = document.getElementsByClassName("tab");
	y = x[currentTab].getElementsByTagName("input");
	// If the mRetorno status is true, mark the step as finished and mRetorno:
	if(mRetorno){
		document.getElementsByClassName("step")[currentTab].className += " finish";
	}else{
		MostrarDatoObser("<b>Alerta</b> Hay inconsistencia en los datos, favor revisar.");
	}
	return mRetorno; // return the valid status
}
function fixStepIndicator(n){
	// This function removes the "active" class of all steps...
	var i, x = document.getElementsByClassName("step");
	for(i = 0; i < x.length; i++){
		x[i].className = x[i].className.replace(" active", "");
	}
	//... and adds the "active" class on the current step:
	x[n].className += " active";
}
function documentHeight(){//Obtener el alto de la página
    return Math.max(
        document.documentElement.clientHeight,
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight
    );
}
function CambioCentro(){//Para filtrar las bodegas Y los tipos de reteICA cuando se cambia la Sucursal
	mCentro=document.getElementById('CodCentro').value;
	mObjGrupo=document.getElementById('Aula');
	if(!mObjGrupo){
		return
	}
	mObjGrupo.length=1;<?php
	$Queri = "SELECT G.*
				FROM ".$PrefBD."estudiantes.srpagrupos G
				WHERE G.Borrada=0
				ORDER BY LetraGrupo";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	while($Row = $Result->fetch_assoc()){?>
	if(mCentro=='<?php echo $Row['CodCentro'];?>'){
		mObjGrupo.add(new Option('<?php echo $Row['LetraGrupo']." - ".$Row['DesGrupo'];?>', '<?php echo $Row['IDGrupo'];?>', null));
	}<?php
	}?>
	mObjGrupo.value='';
}
function ValidarFiltro(Frm){
	if(Frm.FiltroAnio.value &&(Frm.FiltroGrupo.value || Frm.FiltroCentro.value || Frm.FiltroFacilitador.value)){
		mRetorno=true;
	}else{
		mRetorno=false;
	}
	if(!mRetorno){
		MostrarDatoObser("Debe Asignar algún criterio de busqueda.");
	}else{
		Frm.submit();
	}
	return mRetorno;
}
function RegistrarAsistencia(Obj,mIDBase,mIDGrupo,mCod_Mate,mFAsistencia){
	if(Obj.type=='checkbox'){
		if(Obj.checked){
			$("#CmbAsiste"+mIDBase).hide();
			mAsiste='ASISTE';
		}else{
			$("#CmbAsiste"+mIDBase).show();
			$("#CmbAsiste"+mIDBase).val("");
			mAsiste='';
		}
	}else{
		if(Obj.value!=''){
			$("#CheckAsiste"+mIDBase).hide();
		}else{
			$("#CheckAsiste"+mIDBase).show();
		}
		mAsiste=Obj.value;
	}
	$.ajax({
		url:'index.php',
		type:'post',
		cache: false,
		data:{
			TipoGrabar:'2',
			TipoModificar:'<?php echo md5('Jor2'.date('d'));?>',
			IDBase:mIDBase,
			IDGrupo:mIDGrupo,
			Cod_Mate:mCod_Mate,
			FAsistencia:mFAsistencia,
			Asiste:mAsiste
		}
	}).done(function(html){
		if(html=='Hecho'){
			MostrarDatoObser("Los datos se grabaron.",true);
		}else{
			MostrarDatoObser(html);
		}
	});
}
function MostrarDivDesordenDi(mDesGrupo){
	$("#DivDesordenDi").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('IDGrupo1_3').value = '<?php echo $_GET['IDGrupo'];?>';
	document.getElementById('LabelDesordenDi').innerHTML = "En la fecha " + document.getElementById('FAsistencia').value + ".<br>Para el Grupo: " + mDesGrupo + "<br><b>RECUERDE QUE</b> este evento prevalece por encima de la toma de asistencia si la hubo.";
	$("#overlay").css({
		height:documentHeight()
	});
	$("#overlay,#DivDesordenDi").show();
}
function EnviarDesordenDi(){//Enviar los datos del pago en un ajax
	$("#BotsDesordenDi").hide();
	mRetorno=true;
	if(mRetorno){
		mIDGrupo=document.getElementById('IDGrupo1_3').value;
		mFAsistencia=document.getElementById('FAsistencia').value;
		$.ajax({
			url:'index.php?Anio=<?php echo $_GET['Anio'];?>',
			type:'post',
			cache: false,
			data:{
				TipoGrabar:'2_3',
				TipoModificar:'<?php echo md5('Jor2_3'.date('d'));?>',
				IDGrupo:mIDGrupo,
				FAsistencia:mFAsistencia
			}
		}).done(function(html){
			if(html=='Hecho'){
				MostrarDatoObser("Los datos se grabaron.",true);
				setTimeout("location.reload()", 500);
			}else{
				MostrarDatoObser(html);
			}
		});
	}else{
		$("#BotsDesordenDi").show();
		MostrarDatoObser("Los campos resaltados son obligatorios, o presentan algún inconveniente.");
	}
}
function ImprimirAsistencia(mIDGrupo,mCod_Mate){
	url ="index.php?TipoModificar=<?php echo md5('ImprimirAsistencia'.date('d'));?>&IDGrupo="+mIDGrupo+"&Cod_Mate="+mCod_Mate+"&FAsistencia="+document.getElementById('FAsistencia').value+'&Anio=<?php echo $_GET['Anio'];?>';
	window.open(url,"_blank")
}
function ImprimirAsistenciaGrupos(mDocumento){
	mGrupos='';
	$("[id*="+mDocumento+"]").each(function(){
		if($(this).is(":checked")){
			mGrupos+=(mGrupos ? ',' : '') + $(this).val();
		}
	});
	if(mGrupos){
		url ="index.php?TipoModificar=<?php echo md5('ImprimirAsistencia'.date('d'));?>&IDGrupo="+mGrupos+"&Cod_Mate=Varios&Anio=<?php echo $_GET['Anio'];?>&Documento="+mDocumento;
		window.open(url,"_blank")
	}else{
		MostrarDatoObser("No ha seleccionado grupos para imprimir.");
	}
}
function MostrarDivAgregarAlGrupo(){
	$("#FrmAgregarAlGrupo").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('IDGrupo2').value = '<?php echo $_GET['IDGrupo'];?>';
	document.getElementById('IDBase2').value = '';
	document.getElementById('NomEstudiante2').value = "";
	document.getElementById('ObsHistorial2').value = "";
	document.getElementById('DivDatosEstudiante2').innerHTML = "";
	document.getElementById('DivAgregarAlGrupo').style.top = document.body.scrollTop + 200;
	$("#overlay").css({
		height:documentHeight()
	});
	$("#overlay,#DivAgregarAlGrupo").show();
}
function EnviarAgregarAlGrupo(){//Enviar los datos del pago en un ajax
	$("#BotsAgregarAlGrupo").hide();
	mRetorno=true;
	ele=document.getElementById('IDBase2');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}//Se deja por precaución aunque es un campo oculto
	ele=document.getElementById('IDGrupo2');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}//Se deja por precaución aunque es un campo oculto
	ele=document.getElementById('NomEstudiante2');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('ObsHistorial2');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	if(mRetorno){
		$.ajax({
			url:'index.php?Anio=<?php echo $_GET['Anio'];?>',
			type:'post',
			cache: false,
			data:{
				TipoGrabar:'3',
				TipoModificar:'<?php echo md5('Jor3'.date('d'));?>',
				IDBase:document.getElementById('IDBase2').value,
				IDGrupo:document.getElementById('IDGrupo2').value,
				ObsHistorial:document.getElementById('ObsHistorial2').value
			}
		}).done(function(html){
			if(html=='Hecho'){
				MostrarDatoObser("Los datos se grabaron.",true);
				setTimeout("location.reload()", 500);
			}else{
				MostrarDatoObser(html);
			}
		});
	}else{
		$("#BotsAgregarAlGrupo").show();
		MostrarDatoObser("Los campos resaltados son obligatorios, o presentan algún inconveniente.");
	}
}
function MostrarDivFiltroEstudiante(){
	$("#FrmFiltroEstudiante").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('IDGrupo2').value = '<?php echo $_GET['IDGrupo'];?>';
	document.getElementById('IDBase2').value = '';
	document.getElementById('NomEstudiante2').value = "";
	document.getElementById('ObsHistorial2').value = "";
	document.getElementById('DivDatosEstudiante2').innerHTML = "";
	document.getElementById('DivFiltroEstudiante').style.top = document.body.scrollTop + 200;
	$("#overlay").css({
		height:documentHeight()
	});
	$("#overlay,#DivFiltroEstudiante").show();
}
function EnviarFiltroEstudiante(){//Enviar los datos del pago en un ajax
	$("#BotsFiltroEstudiante").hide();
	mRetorno=true;
	ele=document.getElementById('FiltroNomEstudiante');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	if(mRetorno){
		document.getElementById('FrmFiltroEstudiante').TipoModificar.value='<?php echo md5('Jor6'.date('d'));?>';
		document.getElementById('FrmFiltroEstudiante').submit();
	}else{
		$("#BotsFiltroEstudiante").show();
		MostrarDatoObser("Los campos resaltados son obligatorios, o presentan algún inconveniente.");
		return false;
	}
}
function CambioFAsistencia(Obj){
	ValidarFecha(Obj);
	if(Obj.value){
		location.href = "index.php?TipoModificar=<?php echo md5('Jor2');?>&Anio=<?php echo $_GET['Anio'];?>&IDGrupo=<?php echo $_GET['IDGrupo'];?>&Cod_Mate=<?php echo $_GET['Cod_Mate'];?>&FAsistencia="+Obj.value;
	}
}
function MostrarDivCentro(mCodCentro){
	$("#FrmCentro").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	if(mCodCentro=='Nuevo'){
		document.getElementById('FrmCentro').reset();
		document.getElementById('EsNuevo3').value = 1;
		document.getElementById('CodCentro3').readOnly=false;
		$("#overlay").css({
			height:documentHeight()
		});
		$("#overlay,#DivCentro").show();
	}else{
		document.getElementById('EsNuevo3').value = 0;
		document.getElementById('CodCentro3').readOnly=true;
		$.ajax({
			type: "get",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax1Jor4Centro'.date('d'));?>&CodCentro='+mCodCentro,
			cache: false,
			dataType: 'json',
			success: function(data){ //Si se ejecuta correctamente
				if(data.Mensaje=="Error"){
					MostrarDatoObser("Se presentó un error");
				}else{
					document.getElementById('CodCentro3').value = data.CodCentro;
					document.getElementById('NomCentro3').value = data.NomCentro;
					document.getElementById('NCorto3').value = data.NCorto;
					document.getElementById('DirCentro3').value = data.DirCentro;
					document.getElementById('Gestor3').value = data.NomGestor;
					document.getElementById('DivCentro').style.top = document.body.scrollTop + 100;
					$("#overlay").css({
						height:documentHeight()
					});
					$("#overlay,#DivCentro").show();
				}
			},
			error: function(data){
				MostrarDatoObser("Se presentó un error");
				return false;
			}
		});
	}
}
function EnviarCentro(){//Enviar los datos del pago en un ajax
	$("#BotsCentro").hide();
	mRetorno=true;
	ele=document.getElementById('CodCentro3');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}//Se deja por precaución aunque es un campo oculto
	ele=document.getElementById('NomCentro3');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}//Se deja por precaución aunque es un campo oculto
	ele=document.getElementById('NCorto3');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('DirCentro3');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('Gestor3');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	if(mRetorno){
		$.ajax({
			url:'index.php?Anio=<?php echo $_GET['Anio'];?>',
			type:'post',
			cache: false,
			data:{
				TipoGrabar:'4',
				TipoModificar:'<?php echo md5('Jor4'.date('d'));?>',
				EsNuevo:document.getElementById('EsNuevo3').value,
				CodCentro:document.getElementById('CodCentro3').value,
				NomCentro:document.getElementById('NomCentro3').value,
				NCorto:document.getElementById('NCorto3').value,
				DirCentro:document.getElementById('DirCentro3').value,
				Gestor:document.getElementById('Gestor3').value
			}
		}).done(function(html){
			if(html=='EXISTENTE'){
				MostrarDatoObser("Ese código ya existe. Los datos no se grabaron.");
			}else if(html=='Hecho'){
				MostrarDatoObser("Los datos se grabaron.",true);
				setTimeout("location.reload()", 500);
			}else{
				MostrarDatoObser(html);
			}
		});
	}else{
		$("#BotsCentro").show();
		MostrarDatoObser("Los campos resaltados son obligatorios, o presentan algún inconveniente.");
	}
}
function MostrarDivGrupo(mIDGrupo){
	$("#FrmGrupo").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('FrmGrupo').reset();
	if(mIDGrupo=='Nuevo'){
		document.getElementById('IDGrupo7').value = 'Nuevo';
		document.getElementById('Anio7').value = '<?php echo $_GET['Anio'];?>';
		$("#overlay").css({
			height:documentHeight()
		});
		$("#overlay,#DivGrupo").show();
	}else{
		$.ajax({
			type: "get",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax1Jor8Grupo'.date('d'));?>&IDGrupo='+mIDGrupo,
			cache: false,
			dataType: 'json',
			success: function(data){ //Si se ejecuta correctamente
				if(data.Mensaje=="Error"){
					MostrarDatoObser("Se presentó un error");
				}else{
					document.getElementById('IDGrupo7').value = data.IDGrupo;
					document.getElementById('Anio7').value = data.Anio;
					document.getElementById('CodCentro7').value = data.CodCentro;
					document.getElementById('LetraGrupo7').value = data.LetraGrupo;
					document.getElementById('Jornada7').value = data.Jornada;
					document.getElementById('DesGrupo7').value = data.DesGrupo;
					//document.getElementById('Facilitador7').value = data.NomFacilitador;
					document.getElementById('DivGrupo').style.top = document.body.scrollTop + 50;
					for(i=1; i <= data.NumMaterias; i++){
						mCod_Mate=data.Materias[i].Cod_Mate;
						mNom_Profe=data.Materias[i].Nom_Profe;
						mObj = document.getElementById('Cod_Mate7'+mCod_Mate);
						if(mObj){
							mObj.value=mNom_Profe;
						}
					}
					$("#overlay").css({
						height:documentHeight()
					});
					$("#overlay,#DivGrupo").show();
				}
			},
			error: function(data){
				MostrarDatoObser("Se presentó un error");
				return false;
			}
		});
	}
}
function EnviarGrupo(){//Enviar los datos del pago en un ajax
	$("#BotsGrupo").hide();
	mRetorno=true;
	ele=document.getElementById('IDGrupo7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('Anio7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('CodCentro7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('LetraGrupo7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('Jornada7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('DesGrupo7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	//ele=document.getElementById('Facilitador7');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	ele=document.getElementById('Cod_Mate7ACA');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	if(mRetorno){
		document.getElementById('FrmGrupo').TipoGrabar.value='8';
		document.getElementById('FrmGrupo').TipoModificar.value='<?php echo md5('Jor8'.date('d'));?>';
		var myData = $("#FrmGrupo").serialize();
		$.ajax({
			url:'index.php?Anio=<?php echo $_GET['Anio'];?>',
			type:'post',
			cache: false,
			data: myData
		}).done(function(html){
			if(html=='Hecho'){
				MostrarDatoObser("Los datos se grabaron.",true);
				setTimeout("location.reload()", 500);
			}else{
				MostrarDatoObser(html);
			}
		});
	}else{
		$("#BotsGrupo").show();
		MostrarDatoObser("Los campos resaltados son obligatorios, o presentan algún inconveniente.");
	}
}
function ClickCamposReporte(mTipoClick){
	if(mTipoClick==1){//Click en el optión para marcar o desmarcar todos
		if(document.getElementById('TodosCamposReporte').checked){
			$("#CamposReporte option").prop("selected",true);
		}else{
			$("#CamposReporte option").prop("selected",false);
		}
	}else{//Click en el listado de campos
		mNoSeleccionados=0;
		$("#CamposReporte option").each(function(){
			if($(this).prop("selected")==false){
				mNoSeleccionados++;
			}
		});
		if(mNoSeleccionados==0){
			document.getElementById('TodosCamposReporte').checked=true;
		}else{
			document.getElementById('TodosCamposReporte').checked=false;
		}
	}
}
function EnviarReporte(){
	var mRetorno = true;
	ele=document.getElementById('CamposReporte');
	mSeleccionados=0;
	$("#CamposReporte option").each(function(){
		if($(this).prop("selected")){
			mSeleccionados++;
		}
	});
	if(mSeleccionados>0){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
//	ele=document.getElementById('FiltroFecha1');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
	if(mRetorno){
		Frm=document.getElementById('FrmReportes');
		Frm.TipoGrabar.value='9';
		Frm.Anio.value=document.getElementById('FiltroAnio').value;
		Frm.submit();
	}else{
		MostrarDatoObser("<b>Alerta</b> Hay inconsistencia en los datos, favor revisar.");
	}
}
function FiltrarPlanillas(){
	mFiltroCentro = document.getElementById('FiltrarPlanillasCentro').value.toUpperCase();
	mFiltroCiclo = document.getElementById('FiltrarPlanillasCiclo').value.toUpperCase();
	mFiltroComponente = document.getElementById('FiltrarPlanillasComponente').value.toUpperCase();
	mFiltroFacilitador = document.getElementById('FiltrarPlanillasFacilitador').value.toUpperCase();
	if(mFiltroCentro || mFiltroCiclo || mFiltroComponente || mFiltroFacilitador){
		$("#TBodyPlanillas").find("tr").hide();
		$("#TBodyPlanillas").find("tr").each(function(){
			if((mFiltroCentro ? $(this).find("td:eq(0):contains('"+mFiltroCentro+"')").length : true) &&
				(mFiltroCiclo ? $(this).find("td:eq(1):contains('"+mFiltroCiclo+"')").length : true) &&
				(mFiltroComponente ? $(this).find("td:eq(2):contains('"+mFiltroComponente+"')").length : true) &&
			   	(mFiltroFacilitador ? $(this).find("td:eq(3):contains('"+mFiltroFacilitador+"')").length : true)){
				$(this).show();
			}
		});
	}else{
		$("#TBodyPlanillas").find("tr").show();
	}
}
function MostrarDivFechasPlanillas(){
	$("#FrmFechasPlanillas").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('DivFechasPlanillas').style.top = document.body.scrollTop + 200;
	$("#overlay").css({
		height:documentHeight()
	});
	$("#overlay,#DivFechasPlanillas").show();
}
function EnviarFechasPlanillas(){//Enviar los datos del pago en un ajax
	$("#BotsFechasPlanillas").hide();
	mRetorno=true;
	if(mRetorno){
		Frm=document.getElementById('FrmFechasPlanillas');
		Frm.TipoGrabar.value='FechasPlanillas';
		Frm.TipoModificar.value='<?php echo md5('FechasPlanillas'.date('d'));?>';
		var myData = $("#FrmFechasPlanillas").serialize();
		$.ajax({
			url:'index.php?Anio=<?php echo $_GET['Anio'];?>',
			type:'post',
			cache: false,
			data: myData
		}).done(function(html){
			if(html){
				MostrarDatoObser(html);
			}else{
				MostrarDatoObser("Los datos se grabaron.",true);
				$("#BotsFechasPlanillas").show();
				$("#overlay,#DivFechasPlanillas").hide();
			}
		});
	}else{
		$("#BotsFechasPlanillas").show();
		MostrarDatoObser("Los campos resaltados son obligatorios, o presentan algún inconveniente.");
	}
}
function Adjuntar(mFileName){
	var input = document.getElementById("inputFile");
	if(input.files[0].type=="application/pdf"){
		var f =   new FormData();
		f.append("inputFile",document.getElementById("inputFile").files[0]);
		f.append("FileName",mFileName);
		f.append("TipoModificar",'<?php echo md5('JorFile'.date('d'));?>');
		f.append("TipoGrabar",'File');
		$.ajax({
			url: "index.php",
			type:'post',
			cache: false,
			data: f,
			processData: false,
			contentType: false
		}).done(function(html){
			if(html){
				MostrarDatoObser(html);
			}else{
				window.location.reload();
			}
		});
	}else{
		MostrarDatoObser("¡Error!.Recuerde que solo se admiten archivos en formato PDF");
	}
}
</script>
</head>
<body style="padding-top: 50px;">
<div id="overlay" style="position:absolute;display:<?php echo "none";?>;"></div>
<div class="myAlert-top alert alert-success" id="DatoObser" style="z-index:1000000; display:<?php echo 'none';?>">
	<b>Success!</b> Indicates a successful or positive action.
</div>
<div class="myAlert-bottom alert alert-danger" id="DatoObserRed" style="z-index:1000000; display:<?php echo 'none';?>">
  <b>Danger!</b> This alert box could indicate a dangerous or potentially negative action.
</div>
<div class="container">
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex6-collapse">
			<span class="sr-only">Desplegar navegación</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="#">DEPARTAMENTO DE SEGURIDAD</a>
	</div>
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse navbar-ex6-collapse">
        <ul class="nav navbar-nav">
            <li><a href="../indexadmon.php"><span class="glyphicon glyphicon-retweet"></span>Regresar</a></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Minutas<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorA1'.date('d'));?>">Administrar Minutas</a></li><?php
					if($PuedeAdministrar){?>
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorA2'.date('d'));?>">Puestos</a></li>
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorA3'.date('d'));?>">Puestos X Sucursal</a></li>
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorA4'.date('d'));?>">Elementos</a></li>
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorA5'.date('d'));?>">Elementos X Puesto X Sucursal</a></li>
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorA6'.date('d'));?>">Tipos de Receso</a></li><?php
					}?>
                </ul>
            </li>
                <!--
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Parámetros por Colegio<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?TipoModificar=<?php echo md5('JorB4'.date('d')).($_GET['Colegio']<>'' ? ('&Colegio='.$_GET['Colegio']) : '').($_GET['Anio']<>'' ? ('&Anio='.$_GET['Anio']) : '');?>">Períodos X Colegio</a></li>
                </ul>
            </li>
            	-->
            <li><a href="../salir.php"><span class="glyphicon glyphicon-eject"></span>Salir</a></li>
		</ul>
	</div><!-- /.navbar-collapse -->
</nav>
<div class="<?php echo "modal fade";?>" id="ModalMatriculaEstudiante" role="dialog">
	<input name="HuboCambio" type="hidden" id="HuboCambio">
    <div class="modal-dialog" style="width:90%;">
    	<!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Actualización de datos - Proceso de matrícula</h4>
            </div>
            <div class="modal-body">
                <!-- One "tab" for each step:	0 Datos Personales -->
                <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm0' id='Frm0'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDBase" type="hidden" id="IDBase">
                    <h4 class="modal-title"><b>Datos Personales</b></h4>
                    <div class="col-sm-4">
                        <label for="Ape1Alum">Primer Apellido</label>
                        <input name="Ape1Alum" id="Ape1Alum" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Primer Apellido">
                    </div>
                    <div class="col-sm-4">
                        <label for="Ape2Alum">Segundo Apellido</label>
                        <input name="Ape2Alum" id="Ape2Alum" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Segundo Apellido">
                    </div>
                    <div class="col-sm-3">
                        <label for="NomAlum">Nombre</label>
                        <input name="NomAlum" id="NomAlum" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Nombre">
                    </div>
                    <div class="col-sm-3">
                        <label for="FNacio">Fecha de nacimiento</label>
                        <input name="FNacio" id="FNacio" maxlength="10" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarFecha(this)" placeholder="Fecha de nacimiento">
                    </div>
                    <div class="col-sm-6">
                        <label for="LNacio">Ciudad donde nació</label>
                        <input name="LNacio" id="LNacio" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Ciudad donde nació">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-3">
                        <label for="RegistroC">Tipo.Doc</label>
                    </div>
                    <div class="col-sm-3">
                        <label for="Cedula">#Documento</label>
                        <input name="Cedula" id="Cedula" maxlength="12" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="#Documento">
                    </div>
                    <div class="col-sm-6">
                        <label for="LugarExped">Ciudad Expedición</label>
                        <input name="LugarExped" id="LugarExped" maxlength="25" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Ciudad Expedición">
                    </div>
                </form>
                </div>
                <!-- 1 Estado y Ubicabilidad -->
                <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm1' id='Frm1'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDBase" type="hidden" id="IDBase">
                    <h4 class="modal-title"><b>Datos Generales y Ubicabilidad</b></h4>
                    <div class="col-sm-2">
                        <label for="Sexo">Género</label>
                        <select name="Sexo" class="form-control" id="Sexo">
                            <option value="">-seleccione-</option>
                            <option value="F">Femenino</option>
                            <option value="M">Masculino</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label for="CabezaFamilia">Estado Civil</label>
                        <select name="CabezaFamilia" class="form-control" id="CabezaFamilia">
                            <option value="">-seleccione-</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                       <label for="Etnia" class="active">Étnia</label>
                       <input name="Etnia" id="Etnia" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="" placeholder="Etnia">
                    </div>
                    <div class="col-sm-4">
                        <label for="VictimaConflicto">Personas a cargo</label>
                        <input name="VictimaConflicto" id="VictimaConflicto" maxlength="1" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNumeros(this)" placeholder="Personas a cargo">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <label for="BarrAlum">Barrio/Localidad/Vereda</label>
                        <input name="BarrAlum" id="BarrAlum" maxlength="40" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Barrio/Localidad/Vereda">
                    </div>
                    <div class="col-sm-4">
                        <label for="DirAlum">Dirección</label>
                        <input name="DirAlum" id="DirAlum" maxlength="120" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Dirección">
                    </div>
                    <div class="col-sm-2">
                        <label for="Localidad">Ciudad Reside</label>
                        <input name="Localidad" id="Localidad" maxlength="100" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Ciudad Reside">
                    </div>
                    <div class="col-sm-2">
                        <label for="ZonaResidencia">Zona en que reside</label>
                        <select name="ZonaResidencia" class="form-control" id="ZonaResidencia">
                            <option value="">-seleccione-</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <label for="TelAlum">Teléfono</label>
                        <input name="TelAlum" id="TelAlum" maxlength="30" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Teléfono">
                    </div>
                    <div class="col-sm-4">
                        <label for="Celular">Celular</label>
                        <input name="Celular" id="Celular" maxlength="30" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Celular">
                    </div>
                    <div class="col-sm-4">
                        <label for="Mail">EMail</label>
                        <input name="Mail" id="Mail" maxlength="100" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="EMail">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-2">
                        <label for="PuntajeSisben">Puntaje Sisben</label>
                        <input name="PuntajeSisben" id="PuntajeSisben" maxlength="10" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarConDecimales(this);" placeholder="Puntaje Sisben">
                    </div>
                    <div class="col-sm-2">
                        <label for="VictimaCual0">Desplazado?</label>
                        <input name="VictimaCual0" type="checkbox" id="VictimaCual0" value="Desplazado"><br>
                        <label for="VictimaCual1">Desmovilizado?</label>
                        <input name="VictimaCual1" type="checkbox" id="VictimaCual1" value="Desmovilizado">
                    </div>
                    <div class="col-sm-4">
                        <label for="Compromiso">Ocupación</label>
                        <input name="Compromiso" id="Compromiso" maxlength="150" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Ocupación">
                    </div>
                    <div class="col-sm-4">
                        <label for="CapExcepcionales">Interés desempeño laboral</label>
                        <input name="CapExcepcionales" id="CapExcepcionales" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Interés desempeño laboral">
                    </div>
                </form>
                </div>
                <!-- 2 Información Académica-->
                <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm2' id='Frm2'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDBase" type="hidden" id="IDBase">
                    <h4 class="modal-title"><b>Información Académica</b></h4>
                    <div class="col-sm-2">
                        <label for="Grado">Solicitud para el Ciclo</label>
                        <input name="Grado" id="Grado" maxlength="1" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNumeros(this)" placeholder="Ciclo">
                    </div>
                    <div class="col-sm-2">
                        <label for="FMatri">Fecha Matrícula</label>
                        <input name="FMatri" id="FMatri" maxlength="10" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarFecha(this)" placeholder="Fecha Matrícula">
                    </div>
                    <div class="col-sm-2">
                        <label for="FIngreso">Fecha Ingreso</label>
                        <input name="FIngreso" id="FIngreso" maxlength="10" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarFecha(this)" placeholder="Fecha Ingreso">
                    </div>
                    <div class="col-sm-2">
                        <label for="FSIMAT">Fecha SIMAT</label>
                        <input name="FSIMAT" id="FSIMAT" maxlength="10" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarFecha(this)" placeholder="Fecha SIMAT">
                    </div>
                    <div class="col-sm-4">
                        <label for="CodConvenio">Tipo</label>
                        <select name="CodConvenio" class="form-control" id="CodConvenio">
                            <option value="0">Estudiante Regular</option>
                            <option value="99">Asistente</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="CodCentro">Centro</label>
                        <select name="CodCentro" class="form-control" id="CodCentro" onChange="CambioCentro();">
                            <option value="">-seleccione-</option><?php
                            $Queri = "SELECT C.*
                                        FROM ".$PrefBD."estudiantes.srpacentros C
                                        ORDER BY C.NomCentro";
                            $Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
                            while($Row=$Result->fetch_assoc()){?>
                            <option value="<?php echo $Row['CodCentro'];?>"><?php echo $Row['NomCentro'];?></option><?php
                            }?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="Aula">Grupo</label>
                        <select name="Aula" class="form-control" id="Aula">
                            <option value="">-seleccione-</option>
                        </select>
                    </div>
                </form>
                </div>
                <!-- 3 Datos Familiares -->
                <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm3' id='Frm3'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDBase" type="hidden" id="IDBase">
                   	<h4 class="modal-title"><b>Datos Familiares</b></h4>
                    <div class="col-sm-1">
                        <label for="TipoDocumentoM">TipDoc</label>
                        <select name="TipoDocumentoM" class="form-control" id="TipoDocumentoM">
                            <option value="">--Seleccione--</option>
                            <option value="1">CC Cédula</option>
                            <option value="2">CE Cédula Extranjería</option>
                            <option value="3">PA Pasaporte</option>
                            <option value="9">Otro</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label for="NumDocM">#Doc Madre</label>
                        <input name="NumDocM" id="NumDocM" maxlength="12" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="#Documento" title="Digite CERO (0) si no hay presencia de figura Materna">
                    </div>
                    <div class="col-sm-2">
                        <label for="Ape1FamiM">Primer Apellido Madre</label>
                        <input name="Ape1FamiM" id="Ape1FamiM" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Primer Apellido Madre">
                    </div>
                    <div class="col-sm-2">
                        <label for="Ape2FamiM">Segundo Apellido Madre</label>
                        <input name="Ape2FamiM" id="Ape2FamiM" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Segundo Apellido Madre">
                    </div>
                    <div class="col-sm-3">
                        <label for="NomFamiM">Nombre Madre</label>
                        <input name="NomFamiM" id="NomFamiM" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Nombre Madre">
                    </div>
                    <div class="col-sm-1">
                        <label for="CelularM">Celular</label>
                        <input name="CelularM" id="CelularM" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Celular">
                    </div>
                    <div class="col-sm-1">
                        <label for="TelefonoM">Teléfono</label>
                        <input name="TelefonoM" id="TelefonoM" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Teléfono Madre">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-1">
                        <label for="TipoDocumentoP">TipDoc</label>
                        <select name="TipoDocumentoP" class="form-control" id="TipoDocumentoP">
                            <option value="">--Seleccione--</option>
                            <option value="1">CC Cédula</option>
                            <option value="2">CE Cédula Extranjería</option>
                            <option value="3">PA Pasaporte</option>
                            <option value="9">Otro</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label for="NumDocP">#Doc Padre</label>
                        <input name="NumDocP" id="NumDocP" maxlength="12" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="#Documento" title="Digite CERO (0) si no hay presencia de figura Paterna">
                    </div>
                    <div class="col-sm-2">
                        <label for="Ape1FamiP">Primer Apellido Padre</label>
                        <input name="Ape1FamiP" id="Ape1FamiP" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Primer Apellido Padre">
                    </div>
                    <div class="col-sm-2">
                        <label for="Ape2FamiP">Segundo Apellido Padre</label>
                        <input name="Ape2FamiP" id="Ape2FamiP" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Segundo Apellido Padre">
                    </div>
                    <div class="col-sm-3">
                        <label for="NomFamiP">Nombre Padre</label>
                        <input name="NomFamiP" id="NomFamiP" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" onBlur="ValidarNombres(this)" placeholder="Nombre Padre">
                    </div>
                    <div class="col-sm-1">
                        <label for="CelularP">Celular</label>
                        <input name="CelularP" id="CelularP" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Celular">
                    </div>
                    <div class="col-sm-1">
                        <label for="TelefonoP">Teléfono</label>
                        <input name="TelefonoP" id="TelefonoP" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Teléfono Padre">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-6">
                        <label for="DirFamiM">Dirección</label>
                        <input name="DirFamiM" id="DirFamiM" maxlength="60" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Dirección">
                    </div>
                    <div class="col-sm-2">
                        <label for="BarrFamiM">Barrio</label>
                        <input name="BarrFamiM" id="BarrFamiM" maxlength="30" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Barrio">
                    </div>
                    <div class="col-sm-2">
                        <label for="Estrato">Estrato</label>
                        <select name="Estrato" class="form-control" id="Estrato">
                            <option value="">-seleccione-</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label for="Sisben">Sisben</label>
                        <input name="Sisben" id="Sisben" maxlength="5" class="form-control" data-toggle="tooltip" data-placement="top" placeholder="Sisben">
                    </div>
                </form>
                </div>
                <!-- 4 Historial Académico y Documentos-->
                <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm4' id='Frm4'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDBase" type="hidden" id="IDBase">
                    <h4 class="modal-title"><b>Historial Académico</b></h4>
                    <div class="col-sm-2">
                        <label>Grado</label>
                    </div>
                    <div class="col-sm-2">
                        <label>Año</label>
                    </div>
                    <div class="col-sm-4">
                        <label>Ciudad</label>
                    </div>
                    <div class="col-sm-4">
                        <label>Colegio</label>
                    </div>
                    <div class="clearfix"></div>
                    <h4 class="modal-title"><b>Certificados aprobados originales de:</b></h4>
                    <div class="col-sm-2">
                        <label>EPS</label>
                        <input name="EPS" type="checkbox" id="EPS" value="1">
                    </div>
                    <div class="col-sm-2">
                        <label>Acta Entrega Material</label>
                        <input name="ActaMaterial" type="checkbox" id="ActaMaterial" value="1">
                    </div>
                    <div class="col-sm-3">
                        <label>Fotocopia Doc.Identidad</label>
                        <input name="FotocopiaDI" type="checkbox" id="FotocopiaDI" value="1">
                    </div>
                </form>
                </div>
            </div>
            <div class="modal-footer">
              <div style="overflow:auto;">
            <div style="overflow:auto;">
                <div style="float:right;">
                    <button type="button" id="prevBtn" onClick="nextPrev(-1)">Anterior</button>
                    <button type="button" id="nextBtn" onClick="nextPrev(1)">Siguiente</button>
                </div>
            </div>
              </div>
            <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
              <!-- Circles which indicates the steps of the form: -->
              <div style="text-align:center;margin-top:5px;">
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
              </div>
            </div>
        </div>
    </div>
</div>
<div id="DivAgregarAlGrupo" name="DivAgregarAlGrupo" style="display:<?php echo "none";?>;">
  <form  method="post" enctype="multipart/form-data" class="form_validar" name="FrmAgregarAlGrupo" id="FrmAgregarAlGrupo">
	<div class="col-sm-12">
        	<H4 id="DivTituloAgregarAlGrupo">
            AGREGAR UN ESTUDIANTE MATRICULADO, AL GRUPO QUE ESTÁ EN LA PANTALLA ACTUALMENTE
            </H4>
        <input name="TipoGrabar" type="hidden" id="TipoGrabar">
        <input name="TipoModificar" type="hidden" id="TipoModificar">
        <input name="IDBase2" type="hidden" id="IDBase2">
        <input name="IDGrupo2" type="hidden" id="IDGrupo2">
	</div>
	<div class="col-sm-12">
    	<H5>Nombre del Estudiante</H5>
        <input name="NomEstudiante2" id="NomEstudiante2" maxlength="100" class="form-control" data-toggle="tooltip" data-placement="top" title="Buscar por nombre de estudiante">
	</div>
    <div class="col-sm-12" id="DivDatosEstudiante2">
    	&nbsp;
    </div>
    <div class="col-sm-12">
    	<H5>Descripción de la novedad (Motivo traslado)</H5>
        <input name="ObsHistorial2" id="ObsHistorial2" maxlength="100">
	</div>
    <div class="col-sm-12">
    	<div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-4" id="BotsAgregarAlGrupo">
        	<input type="button" name="BotAceptarAgregarAlGrupo" id="BotAceptarAgregarAlGrupo" onClick="javascript:return EnviarAgregarAlGrupo();" value="Aceptar">
            <input name="BotCancelarAgregarAlGrupo" type="button" id="BotCancelarAgregarAlGrupo" onClick='$("#DivAgregarAlGrupo,#overlay").hide();' value="Cancelar">
        </div>
    	<div class="col-sm-4">&nbsp;</div>
	</div>
  </form>
</div>
<div id="DivDesordenDi" name="DivDesordenDi" style="display:<?php echo "none";?>;">
	<div class="col-sm-12">
        	<H4 id="DivTituloDesordenDi">
            ASIGNAR EVENTO DE DESORDEN DISCIPLINARIO
            </H4>
        <input name="IDGrupo1_3" type="hidden" id="IDGrupo1_3">
	</div>
	<div class="col-sm-12" id="LabelDesordenDi">
	</div>
    <div class="col-sm-12">
    	<div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-4" id="BotsDesordenDi">
        	<input type="button" name="BotAceptarDesordenDi" id="BotAceptarDesordenDi" onClick="javascript:return EnviarDesordenDi();" value="Aceptar">
            <input name="BotCancelarDesordenDi" type="button" id="BotCancelarDesordenDi" onClick='$("#DivDesordenDi,#overlay").hide();' value="Cancelar">
        </div>
    	<div class="col-sm-4">&nbsp;</div>
	</div>
</div>
<div id="DivFiltroEstudiante" name="DivFiltroEstudiante" style="display:<?php echo "none";?>;">
  <form action="index.php?Anio=<?php echo $_GET['Anio'];?>"  method="get" enctype="multipart/form-data" name="FrmFiltroEstudiante" class="form_validar" id="FrmFiltroEstudiante">
	<div class="col-sm-12">
		<H4 id="DivTituloFiltroEstudiante">BUSCAR POR NOMBRE DE ESTUDIANTE PARA EL AÑO <?php echo $_GET['Anio'];?></H4>
		<input name="TipoModificar" type="hidden" id="TipoModificar">
	</div>
    <div class="col-sm-12 form-group">
    	<input name="FiltroNomEstudiante" id="FiltroNomEstudiante" class="form-control" maxlength="100" value="<?php echo $_GET['FiltroNomEstudiante'];?>">
	</div>
    <div class="col-sm-4">&nbsp;</div>
    <div class="col-sm-4" id="BotsFiltroEstudiante">
        <input type="submit" name="BotAceptarFiltroEstudiante" id="BotAceptarFiltroEstudiante" onClick="return EnviarFiltroEstudiante();" value="Aceptar" class="btn btn-primary">
        <input name="BotCancelarFiltroEstudiante" type="button" id="BotCancelarFiltroEstudiante" onClick='$("#DivFiltroEstudiante,#overlay").hide();' value="Cancelar" class="btn btn-primary">
    </div>
    <div class="col-sm-4">&nbsp;</div>
  </form>
</div>
<div id="DivCentro" name="DivCentro" style="display:<?php echo "none";?>;">
  <form  method="post" enctype="multipart/form-data" class="form_validar" name="FrmCentro" id="FrmCentro">
	<div class="col-sm-12">
        	<H4 id="DivTituloCentro">
            EDITAR CENTRO
            </H4>
        <input name="TipoGrabar" type="hidden" id="TipoGrabar">
        <input name="TipoModificar" type="hidden" id="TipoModificar">
        <input name="EsNuevo3" type="hidden" id="EsNuevo3">
	</div>
	<div class="col-sm-12">
   	  <H5>Código (Debe ser único)</H5>
        <input name="CodCentro3" id="CodCentro3" maxlength="3" class="form-control" data-toggle="tooltip" data-placement="top" title="Código del Centro" onBlur="ValidarNombres(this)" readonly>
	</div>
  	<div class="col-sm-12">
    	<H5>Nombre del Centro</H5>
        <input name="NomCentro3" id="NomCentro3" maxlength="100" class="form-control" data-toggle="tooltip" data-placement="top" title="Nombre del Centro">
	</div>
  	<div class="col-sm-12">
    	<H5>Nombre Corto</H5>
        <input name="NCorto3" id="NCorto3" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" title="Nombre Corto del Centro">
	</div>
  	<div class="col-sm-12">
    	<H5>Dirección</H5>
        <input name="DirCentro3" id="DirCentro3" maxlength="50" class="form-control" data-toggle="tooltip" data-placement="top" title="Dirección del Centro">
	</div>
  	<div class="col-sm-12">
    	<H5>Gestor</H5>
    	<input name="Gestor3" id="Gestor3" class="form-control" maxlength="100">
	</div>
    <div class="col-sm-12">
    	<div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-4" id="BotsCentro">
        	<input type="button" name="BotAceptarCentro" id="BotAceptarCentro" onClick="javascript:return EnviarCentro();" value="Aceptar">
            <input name="BotCancelarCentro" type="button" id="BotCancelarCentro" onClick='$("#DivCentro,#overlay").hide();' value="Cancelar">
        </div>
    	<div class="col-sm-4">&nbsp;</div>
	</div>
  </form>
</div>
<div id="DivFechasPlanillas" name="DivFechasPlanillas" style="display:<?php echo "none";?>;">
  <form  method="post" enctype="multipart/form-data" class="form_validar" name="FrmFechasPlanillas" id="FrmFechasPlanillas">
	<div class="col-sm-12">
        	<H4 id="DivTituloFechasPlanillas">
            FECHAS PARA DE EDICIÓN DE PLANILLAS AÑO <?php echo $_GET['Anio']?>
            </H4>
        <input name="TipoGrabar" type="hidden" id="TipoGrabar">
        <input name="TipoModificar" type="hidden" id="TipoModificar">
	</div>
    <H5>Fecha y hora máximas para diligenciar primer período</H5>
	<div class="col-sm-4">
        <input name="P1FinDigi" id="P1FinDigi" maxlength="10" class="form-control" onBlur="ValidarFecha(this)" placeholder="Fecha" value="<?php echo DarFechaHora($RowCol['P1FinDigi'],1);?>">
	</div>
	<div class="col-sm-2">
        <input name="P1FinDigiH" id="P1FinDigiH" maxlength="5" class="form-control" onBlur="ValidarHora(this)" placeholder="Hora" value="<?php echo DarFechaHora($RowCol['P1FinDigi'],2);?>">
	</div>
    <div class="clearfix"></div>
    <H5>Fecha y hora máximad para diligenciar segundo período</H5>
	<div class="col-sm-4">
        <input name="P2FinDigi" id="P2FinDigi" maxlength="10" class="form-control" onBlur="ValidarFecha(this)" placeholder="Fecha" value="<?php echo DarFechaHora($RowCol['P2FinDigi'],1);?>">
	</div>
	<div class="col-sm-2">
        <input name="P2FinDigiH" id="P2FinDigiH" maxlength="5" class="form-control" onBlur="ValidarHora(this)" placeholder="Hora" value="<?php echo DarFechaHora($RowCol['P2FinDigi'],2);?>">
	</div>
    <div class="clearfix"></div>
    <H5>Fecha y hora máximad para diligenciar tercer período</H5>
	<div class="col-sm-4">
        <input name="P3FinDigi" id="P3FinDigi" maxlength="10" class="form-control" onBlur="ValidarFecha(this)" placeholder="Fecha" value="<?php echo DarFechaHora($RowCol['P3FinDigi'],1);?>">
	</div>
	<div class="col-sm-2">
        <input name="P3FinDigiH" id="P3FinDigiH" maxlength="5" class="form-control" onBlur="ValidarHora(this)" placeholder="Hora" value="<?php echo DarFechaHora($RowCol['P3FinDigi'],2);?>">
	</div>
    <div class="clearfix"></div>
    <H5>Fecha y hora máximad para diligenciar cuarto período</H5>
	<div class="col-sm-4">
        <input name="P4FinDigi" id="P4FinDigi" maxlength="10" class="form-control" onBlur="ValidarFecha(this)" placeholder="Fecha" value="<?php echo DarFechaHora($RowCol['P4FinDigi'],1);?>">
	</div>
	<div class="col-sm-2">
        <input name="P4FinDigiH" id="P4FinDigiH" maxlength="5" class="form-control" onBlur="ValidarHora(this)" placeholder="Hora" value="<?php echo DarFechaHora($RowCol['P4FinDigi'],2);?>">
	</div>
    <div class="clearfix"></div>
    <div class="col-sm-12">
    	<div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-4" id="BotsFechasPlanillas">
        	<input type="button" name="BotAceptarFechasPlanillas" id="BotAceptarFechasPlanillas" onClick="javascript:return EnviarFechasPlanillas();" value="Aceptar">
            <input name="BotCancelarFechasPlanillas" type="button" id="BotCancelarFechasPlanillas" onClick='$("#DivFechasPlanillas,#overlay").hide();' value="Cancelar">
        </div>
    	<div class="col-sm-4">&nbsp;</div>
	</div>
  </form>
</div>
<?php
/************************************************************************************************************************************************
Listado de estudiantes en un grupo para toma de asistencia
************************************************************************************************************************************************/
if($_GET['TipoModificar']==md5('JorA1'.date('d'))){
	include ("minuta.php");
/************************************************************************************************************************************************
Administrar Puestos
************************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('JorA2'.date('d'))){
	include ("puesto.php");
/************************************************************************************************************************************************
Administrar Puestos X Sucursal
************************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('JorA3'.date('d'))){
	include ("puestosucursal.php");
/************************************************************************************************************************************************
Administrar Elementos
************************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('JorA4'.date('d'))){
	include ("elemento.php");
/************************************************************************************************************************************************
Administrar Elementos X Puestos X Sucursal
************************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('JorA5'.date('d'))){
	include ("elementopuestosucursal.php");
/************************************************************************************************************************************************
Administrar Tipos de Receso
************************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('JorA6'.date('d'))){
	include ("receso.php");
}//Fin de si Se enviaron datos de consulta para filtro?>
</div>
</body>
</html><?php
mysqli_close($mysqli);?>