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
	IDElemento INT(11) NOT NULL DEFAULT 0,
	CantidadReal SMALLINT(6) NOT NULL DEFAULT 0,	##Se almacena la cantidad Real al momento de crear la Minuta, Es posible que la cantidad real en la tabla elementopuesto cambie por nuevos elementos o por bajas
	CantidadVerificada SMALLINT(6) NOT NULL DEFAULT 0,
	Verificado SMALLINT(1) NOT NULL DEFAULT 0,
	ObsVerifica VARCHAR(100) NOT NULL DEFAULT '',
	Borrada SMALLINT(1) NOT NULL DEFAULT 0,
	UNIQUE IDMinuta(IDMinuta,IDElemento)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Elementos de cada puesto en una sucursal, revisados en cada MINUTA';

DROP TABLE IF EXISTS solicitudes.vigilanciaminuta;
CREATE TABLE solicitudes.vigilanciaminuta(
	IDMinuta INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	Sucursal VARCHAR(2) NOT NULL DEFAULT '',
	IDPuestoSucursal SMALLINT(6) NOT NULL DEFAULT 0,
	Turno VARCHAR(10) NOT NULL DEFAULT '',
	Elabora VARCHAR(12) NOT NULL DEFAULT '',
	FElabora DATETIME DEFAULT NULL,
	Fecha DATE DEFAULT NULL,
	HoraInicio VARCHAR(5) NOT NULL DEFAULT '',
	VigilanteEntrante VARCHAR(12) NOT NULL DEFAULT '',
	FFirmaEntrante DATETIME DEFAULT NULL,
	ObsFirmaEntrante VARCHAR(200) NOT NULL DEFAULT '',
	VigilanteSaliente VARCHAR(12) NOT NULL DEFAULT '',
	FFirmaSaliente DATETIME DEFAULT NULL,
	ObsFirmaSaliente VARCHAR(200) NOT NULL DEFAULT '',
	RealizaRequisa SMALLINT(1) NOT NULL DEFAULT 0,
	ObsRequisa VARCHAR(250) NOT NULL DEFAULT '',
	HoraFinalizaRecorrido VARCHAR(5) NOT NULL DEFAULT '',
	ObsMinuta VARCHAR(250) NOT NULL DEFAULT '',
	FinalizaRegistro DATETIME DEFAULT NULL,
	Borrada SMALLINT(1) NOT NULL DEFAULT 0,
	UNIQUE IDMinuta(Sucursal,IDPuestoSucursal,Fecha,VigilanteSaliente)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla principal donde se alojan los registros de minutas del departamento de seguridad';

DROP TABLE IF EXISTS solicitudes.vigilanciaminutarecesos;
CREATE TABLE solicitudes.vigilanciaminutarecesos(
	IDMinutaReceso INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	IDMinuta INT(11) NOT NULL DEFAULT 0,
	IDReceso SMALLINT(6) NOT NULL DEFAULT 0,
	HoraInicioReceso VARCHAR(5) NOT NULL DEFAULT '',
	VigilanteAsume VARCHAR(60) NOT NULL DEFAULT '',
	HoraFinReceso VARCHAR(5) NOT NULL DEFAULT '',
	ObsReceso VARCHAR(200) NOT NULL DEFAULT '',
	FCrea DATETIME DEFAULT NULL,
	Borrada SMALLINT(1) NOT NULL DEFAULT 0,
	UNIQUE IDMinuta(IDMinuta,IDReceso)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Recesos asociados a cada Minuta';

DROP TABLE IF EXISTS solicitudes.vigilanciaminutanovedades;
CREATE TABLE solicitudes.vigilanciaminutanovedades(
	IDMinutaNovedad INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	IDMinuta INT(11) NOT NULL DEFAULT 0,
	HoraNovedad VARCHAR(5) NOT NULL DEFAULT '',
	DescripcionNovedad VARCHAR(200) NOT NULL DEFAULT '',
	ComunicadorNovedad VARCHAR(60) NOT NULL DEFAULT '',
	CargoComunicador VARCHAR(50) NOT NULL DEFAULT '',
	FCrea DATETIME DEFAULT NULL,
	Borrada SMALLINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Recesos asociados a cada Minuta';

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
$mTurno['Diurno']='Diurno';
$mTurno['Nocturno']='Nocturno';
$mTurno['Por Evento']='Por Evento';
/******************************************************************************************************************************************
Retornar los datos de un Puesto
*******************************************************************************************************************************************/
if($_GET['TipoModificar']==md5('Ajax1JorA2Puesto'.date('d')) and $_GET['IDPuesto']){
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
		$_POST['IDPuesto']=$mysqli->insert_id;
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
		$_POST['IDElemento']=$mysqli->insert_id;
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
Retornar los datos de elementos asignados a un Puesto de una Sucursal elementopuestosucursal
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1JorA5PuestoSucursalElemento'.date('d')) and $_GET['Sucursal'] and $_GET['IDPuestoSucursal']){
	/*
	$Queri = "SELECT ElementoPuestoSucursal.*, Sucursal.NomSucursal, Puesto.Puesto, Elemento.Grupo, Elemento.Elemento, PuestoSucursal.ObsPuesto
				FROM ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal
				JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				JOIN ".$PrefBD."novasoft.sucursal Sucursal ON PuestoSucursal.Sucursal=Sucursal.Sucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal ElementoPuestoSucursal ON PuestoSucursal.IDPuestoSucursal=ElementoPuestoSucursal.IDPuestoSucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaelemento Elemento ON ElementoPuestoSucursal.IDElemento=Elemento.IDElemento
				WHERE PuestoSucursal.IDPuestoSucursal=".intval($_GET['IDPuestoSucursal'])."
				ORDER BY Puesto.Puesto,Elemento.Grupo,Elemento.Elemento";
	*/
	$Queri = "SELECT ElementoPuestoSucursal.IDElementoPuestoSucursal, PuestoSucursal.IDPuestoSucursal, Elemento.IDElemento,
					ElementoPuestoSucursal.Cantidad, IFNULL(ElementoPuestoSucursal.Borrada,1) AS Borrada,
					Sucursal.NomSucursal, Puesto.Puesto, Elemento.Grupo, Elemento.Elemento,  PuestoSucursal.ObsPuesto
				FROM ".$PrefBD."solicitudes.vigilanciaelemento Elemento
				JOIN  ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal	##SIN RELACIONAR PARA QUE SALGAN TODOS LOS ELEMENTOS
				JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				JOIN ".$PrefBD."novasoft.sucursal Sucursal ON PuestoSucursal.Sucursal=Sucursal.Sucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal ElementoPuestoSucursal ON PuestoSucursal.IDPuestoSucursal=ElementoPuestoSucursal.IDPuestoSucursal
																										AND Elemento.IDElemento=ElementoPuestoSucursal.IDElemento
				WHERE PuestoSucursal.IDPuestoSucursal=".intval($_GET['IDPuestoSucursal'])."
				ORDER BY Puesto.Puesto,Elemento.Grupo,Elemento.Elemento";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$i=0;
	while($Row = $Result->fetch_assoc()){
		$i++;?>
    <TR>
      <TD><?php echo $i;?></TD>
      <TD><?php echo $Row['IDElementoPuestoSucursal'];?></TD>
      <TD><?php echo $Row['NomSucursal'];?></TD>
      <TD><?php echo $Row['Puesto'].' '.$Row['ObsPuesto'];?></TD>
      <TD><?php echo $Row['Grupo'];?></TD>
      <TD><?php echo $Row['Elemento'];?></TD>
      <TD><input name="Cantidad<?php echo $Row['IDElemento'];?>" id="Cantidad<?php echo $Row['IDElemento'];?>" maxlength="6" class="form-control" value="<?php echo ($Row['Cantidad']>0 ? $Row['Cantidad'] : '');?>" onBlur="EnviarPuestoSucursalElemento(this,<?php echo intval($Row['IDElemento']).",".$Row['IDElementoPuestoSucursal'];?>);" <?php echo ($Row['Borrada']==1 ? 'disabled' : '');?>></TD>
      <TD><input name="Borrada<?php echo $Row['IDElemento'];?>" type="checkbox" id="Borrada<?php echo $Row['IDElemento'];?>" value="1" onClick="EnviarPuestoSucursalElemento(this,<?php echo intval($Row['IDElemento']).",".$Row['IDElementoPuestoSucursal'];?>);" <?php echo ($Row['Borrada']==1 ? '' : 'checked');//Cconcepto al contrario Borrada/Activo?>></TD>
    </TR><?php
	}//fin del while
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
GRABAR los datos de un elemento asignado a un Puesto de una Sucursal elementopuestosucursal
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('JorA5'.date('d')) and $_POST['TipoGrabar']=='A5'){
	$Queri = "";
	if($_POST['IDElementoPuestoSucursal']>0){//Para actualizar
		if(isset($_POST['Borrada'])){
			$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal
						SET Borrada=".intval($_POST['Borrada'])."
						WHERE IDElementoPuestoSucursal=".$_POST['IDElementoPuestoSucursal']."
						LIMIT 1";
		}elseif(isset($_POST['Cantidad'])){
			$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal
						SET Cantidad=".intval($_POST['Cantidad'])."
						WHERE IDElementoPuestoSucursal=".$_POST['IDElementoPuestoSucursal']."
						LIMIT 1";
		}
	}elseif($_POST['IDElemento'] and $_POST['IDPuestoSucursal']){
		if(isset($_POST['Borrada'])){
			$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal(IDElemento,IDPuestoSucursal,Borrada)
						VALUES(".intval($_POST['IDElemento']).",".intval($_POST['IDPuestoSucursal']).",".intval($_POST['Borrada']).")
						ON DUPLICATE KEY UPDATE Borrada=".intval($_POST['Borrada']);
		}elseif(isset($_POST['Cantidad'])){
			$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal(IDElemento,IDPuestoSucursal,Cantidad)
						VALUES(".intval($_POST['IDElemento']).",".intval($_POST['IDPuestoSucursal']).",".intval($_POST['Cantidad']).")
						ON DUPLICATE KEY UPDATE Cantidad=".intval($_POST['Cantidad']);
		}
	}
	if($Queri){
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
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
		$_POST['IDReceso']=$mysqli->insert_id;
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
/******************************************************************************************************************************************
Retornar Listado de empleados Vigilantes
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax1JorA6ListadoVigilantes'.date('d'))){
	echo "[";
	$Queri = "SELECT CONCAT(E.Nom,' ',E.Apellido1,' ',E.Apellido2,'|-|',E.Nit_CCE) AS Nombre
				FROM ".$PrefBD."recursos.emplea E
				WHERE CONCAT(E.Nit_CCE,E.Nom,' ',E.Apellido1,' ',E.Apellido2) LIKE '%".$_GET['term']."%' AND E.Cargo LIKE '%VIGILA%'
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
Retornar los datos de elementos asignados a un Puesto de una Sucursal elementopuestosucursal para Crear o Editar Minuta
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax2JorA6ElementosEditarMinuta'.date('d')) and $_GET['Sucursal'] and $_GET['IDPuestoSucursal']){
	/*
	ESTA CONSULTA ES PARA TRAER SOLO LOS ELEMENTOS ASIGNADOS AL PUESTO
	SE CAMBIA PORQUE SE DETERMINA QUE DEBEN SALIR TODOS LOS ELEMENTOS ASIGNADOS O NO.
	$Queri = "SELECT ElementoPuestoSucursal.IDElementoPuestoSucursal, PuestoSucursal.IDPuestoSucursal, Elemento.IDElemento,
					IFNULL(MinutaElemento.CantidadReal,ElementoPuestoSucursal.Cantidad) AS CantidadReal,
					Sucursal.NomSucursal, Puesto.Puesto, Elemento.Grupo, Elemento.Elemento, PuestoSucursal.ObsPuesto,
					MinutaElemento.CantidadVerificada, MinutaElemento.Verificado, MinutaElemento.ObsVerifica
				FROM ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal
				JOIN ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal ElementoPuestoSucursal ON PuestoSucursal.IDPuestoSucursal=ElementoPuestoSucursal.IDPuestoSucursal
				JOIN ".$PrefBD."solicitudes.vigilanciaelemento Elemento ON ElementoPuestoSucursal.IDElemento=Elemento.IDElemento
				JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				JOIN ".$PrefBD."novasoft.sucursal Sucursal ON PuestoSucursal.Sucursal=Sucursal.Sucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaminuta Minuta ON ".intval($_GET['IDMinuta'])."=Minuta.IDMinuta
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaminutaelemento MinutaElemento ON Minuta.IDMinuta=MinutaElemento.IDMinuta
																				AND ElementoPuestoSucursal.IDElementoPuestoSucursal=MinutaElemento.IDElementoPuestoSucursal
				WHERE PuestoSucursal.IDPuestoSucursal=".intval($_GET['IDPuestoSucursal'])." AND (ElementoPuestoSucursal.Borrada=0 OR MinutaElemento.IDMinuta IS NOT NULL)
				ORDER BY Puesto.Puesto,Elemento.Grupo,Elemento.Elemento";
	*/
	$Queri = "SELECT ElementoPuestoSucursal.IDElementoPuestoSucursal, PuestoSucursal.IDPuestoSucursal, Elemento.IDElemento,
					IFNULL(MinutaElemento.CantidadReal,ElementoPuestoSucursal.Cantidad) AS CantidadReal,
					Sucursal.NomSucursal, Puesto.Puesto, Elemento.Grupo, Elemento.Elemento, PuestoSucursal.ObsPuesto,
					MinutaElemento.CantidadVerificada, MinutaElemento.Verificado, MinutaElemento.ObsVerifica
				FROM ".$PrefBD."solicitudes.vigilanciaelemento Elemento
				JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal ON ".intval($_GET['IDPuestoSucursal'])."=PuestoSucursal.IDPuestoSucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaelementopuestosucursal ElementoPuestoSucursal ON PuestoSucursal.IDPuestoSucursal=ElementoPuestoSucursal.IDPuestoSucursal
																				AND Elemento.IDElemento=ElementoPuestoSucursal.IDElemento
																				AND 0=ElementoPuestoSucursal.Borrada
				JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				JOIN ".$PrefBD."novasoft.sucursal Sucursal ON PuestoSucursal.Sucursal=Sucursal.Sucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaminuta Minuta ON ".intval($_GET['IDMinuta'])."=Minuta.IDMinuta
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaminutaelemento MinutaElemento ON Minuta.IDMinuta=MinutaElemento.IDMinuta
																				AND Elemento.IDElemento=MinutaElemento.IDElemento
																				AND 0=MinutaElemento.Borrada
				WHERE Elemento.Borrada=0
				ORDER BY Puesto.Puesto,Elemento.Grupo,Elemento.Elemento";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$i=0;
	while($Row = $Result->fetch_assoc()){
		$i++;?>
    <TR>
      <TD><?php echo $i;?></TD>
      <TD><?php echo $Row['Elemento'];?></TD>
      <TD><?php echo $Row['CantidadReal'];?><input name="CantidadReal<?php echo $Row['IDElemento'];?>" id="CantidadReal<?php echo $Row['IDElemento'];?>" type="hidden" value="<?php echo ($Row['CantidadReal']===NULL ? 'N/A' : $Row['CantidadReal']);?>"></TD>
      <TD><input name="CantidadVerificada<?php echo $Row['IDElemento'];?>" id="CantidadVerificada<?php echo $Row['IDElemento'];?>" maxlength="6" class="form-control" value="<?php echo ($Row['CantidadVerificada']===NULL ? '' : $Row['CantidadVerificada']);?>" onChange="ValidarNumeros(this);EnviarMinutaElemento(this);"></TD>
      <TD><input name="ObsVerifica<?php echo $Row['IDElemento'];?>" id="ObsVerifica<?php echo $Row['IDElemento'];?>" maxlength="100" class="form-control" value="<?php echo $Row['ObsVerifica'];?>" onChange="EnviarMinutaElemento(this);"></TD>
    </TR><?php
	}//fin del while
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Verificar si el usuario actual tiene Minutas pendientes por finalizar
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax3JorA6VerificarMinutaPrevia'.date('d'))){
	$Queri="SELECT Minuta.IDMinuta, Minuta.Fecha, Puesto.Puesto, Sucursal.NomSucursal,
					CONCAT(Elabora.Nom,' ',Elabora.Apellido1) AS NomElabora,
					CONCAT(VEntrante.Nom,' ',VEntrante.Apellido1) AS NomVigilanteEntrante,
					CONCAT(VSaliente.Nom,' ',VSaliente.Apellido1) AS NomVigilanteSaliente
			FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
			JOIN ".$PrefBD."recursos.emplea Elabora ON Minuta.Elabora=Elabora.Nit_CCE
			JOIN ".$PrefBD."recursos.emplea VEntrante ON Minuta.VigilanteEntrante=VEntrante.Nit_CCE
			JOIN ".$PrefBD."recursos.emplea VSaliente ON Minuta.VigilanteSaliente=VSaliente.Nit_CCE
			JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal ON Minuta.IDPuestoSucursal=PuestoSucursal.IDPuestoSucursal
			JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
			JOIN ".$PrefBD."novasoft.sucursal Sucursal ON Minuta.Sucursal=Sucursal.Sucursal
			WHERE Minuta.Elabora='".$_SESSION['Usuario']."' AND FinalizaRegistro IS NULL
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
		echo json_encode( array( "IDMinuta"=>0));
	}
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Retornar Los datos de una Minuta
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax4JorA6RetornarMinuta'.date('d')) and $_GET['IDMinuta']){
	$Queri="SELECT Minuta.*,CONCAT(VEntrante.Nom,' ',VEntrante.Apellido1,'|-|',Minuta.VigilanteEntrante) AS NomVigilanteEntrante,
				CONCAT(VSaliente.Nom,' ',VSaliente.Apellido1,'|-|',Minuta.VigilanteSaliente) AS NomVigilanteSaliente
			FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
			JOIN ".$PrefBD."recursos.emplea VEntrante ON Minuta.VigilanteEntrante=VEntrante.Nit_CCE
			JOIN ".$PrefBD."recursos.emplea VSaliente ON Minuta.VigilanteSaliente=VSaliente.Nit_CCE
			WHERE Minuta.IDMinuta=".intval($_GET['IDMinuta'])."
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
		echo json_encode( array( "IDMinuta"=>0));
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
0  IDENTIFICACIÓN (CREAR MINUTA)
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6Tipo0'.date('d')) and $_POST['TipoModificar']==md5('Tipo0JorA6'.date('d'))){
	//Optimizo variables
	$mVigilanteSaliente=explode('|-|',$_POST['VigilanteSaliente']);
	$mVigilanteEntrante=explode('|-|',$_POST['VigilanteEntrante']);
	if(intval($_POST['IDMinuta'])==0){//Se trata de una nueva minuta
		$Queri= "INSERT INTO ".$PrefBD."solicitudes.vigilanciaminuta(Sucursal,IDPuestoSucursal,Fecha,HoraInicio,VigilanteSaliente,Elabora,FElabora)
					VALUES('".$_POST['Sucursal']."','".$_POST['IDPuestoSucursal']."',SYSDATE(),DATE_FORMAT(SYSDATE(),'%H:%i'),'".$mVigilanteSaliente[1]."','".$_SESSION['Usuario']."',SYSDATE())
				ON DUPLICATE KEY UPDATE Borrada=0";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$_POST['IDMinuta']=$mysqli->insert_id;
		if(!($_POST['IDMinuta']>0)){
			$Queri = "SELECT IDMinuta
						FROM ".$PrefBD."solicitudes.vigilanciaminuta
						WHERE Sucursal='".$_POST['Sucursal']."' AND
							IDPuestoSucursal='".$_POST['IDPuestoSucursal']."' AND
							Fecha='".DarFechaSQL($_POST['Fecha'])."' AND
							VigilanteSaliente='".$mVigilanteSaliente[1]."'
						LIMIT 1";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
			if($Row=$Result->fetch_assoc()){
				$_POST['IDMinuta']=$Row['IDMinuta'];
			}else{
				echo 'Error';
				mysqli_close($mysqli);
				exit;
			}
		}
	}
	$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaminuta
				SET Turno='".$_POST['Turno']."',
					VigilanteSaliente='".$mVigilanteSaliente[1]."',
					VigilanteEntrante='".$mVigilanteEntrante[1]."'
				WHERE IDMinuta=".intval($_POST['IDMinuta'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	echo intval($_POST['IDMinuta']);
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
1  LISTA DE CHEQUEO - Verificación de sede de acuerdo a puesto asumido - GRABAR los datos de un elemento asignado a una MINUTA
****************************************************************************************************************************************/
}else if ($_POST['TipoModificar']==md5('JorA6Tipo1'.date('d')) and $_POST['TipoGrabar']==md5('Tipo1JorA6'.date('d'))){
	$Queri = "";
	if($_POST['IDElemento'] and $_POST['IDMinuta']){
		$Queri = "INSERT INTO ".$PrefBD."solicitudes.vigilanciaminutaelemento(IDMinuta,IDElemento,".$_POST['Campo'].",CantidadReal)
					VALUES(".intval($_POST['IDMinuta']).",".intval($_POST['IDElemento']).",'".OptimizarTexto($_POST['Valor'])."',".intval($_POST['CantidadReal']).")
					ON DUPLICATE KEY UPDATE ".$_POST['Campo']."='".OptimizarTexto($_POST['Valor'])."',
							CantidadReal=".intval($_POST['CantidadReal']).",
							Borrada=0";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}else{
		echo "Error";
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
2	REQUISA A VIGILANTE SALIENTE  - Finalizar Crear Minuta por Puesto y Turno
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6Tipo2'.date('d')) and $_POST['TipoModificar']==md5('Tipo2JorA6'.date('d'))){
	$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaminuta
				SET RealizaRequisa=".intval($_POST['RealizaRequisa']).",
					ObsRequisa='".OptimizarTexto($_POST['ObsRequisa'])."',
					HoraFinalizaRecorrido='".$_POST['HoraFinalizaRecorrido']."'
				WHERE IDMinuta=".intval($_POST['IDMinuta'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Retornar los Recesos en la Minuta
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax5JorA6Recesos'.date('d'))){
	$QueriMinuta = "SELECT Minuta.FinalizaRegistro
					FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
					WHERE Minuta.IDMinuta=".intval($_GET['IDMinuta'])."
					LIMIT 1";
	$ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
	$RowMinuta = $ResultMinuta->fetch_assoc();
	$Queri = "SELECT MinutaRecesos.*, Receso.Receso, Minuta.FinalizaRegistro
				FROM ".$PrefBD."solicitudes.vigilanciaminutarecesos MinutaRecesos
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaminuta Minuta ON MinutaRecesos.IDMinuta=Minuta.IDMinuta
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciareceso Receso ON MinutaRecesos.IDReceso=Receso.IDReceso
				WHERE MinutaRecesos.IDMinuta=".intval($_GET['IDMinuta'])." AND MinutaRecesos.Borrada=0
				ORDER BY MinutaRecesos.IDMinutaReceso";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));?>
    <div class="row">
        <div class="form-group col-sm-1">
            <label>Hora Inicio</label>
        </div>
        <div class="form-group col-sm-3">
            <label>Actividad Autorizada</label>
        </div>
        <div class="form-group col-sm-4">
            <label>Vigilante o persona autorizada para asumir</label>
        </div>
        <div class="form-group col-sm-1">
            <label>Hora Fin</label>
        </div>
        <div class="form-group col-sm-3">
            <label>Observación Receso</label>
        </div>
	</div><?php
	$i=0;
	while($Row = $Result->fetch_assoc()){
		$i++;?>
        <div class="row" id="DivReceso<?php echo $i;?>" <?php if($Result->num_rows==0) echo 'style="display:none"';?>>
            <div class="form-group col-sm-1">
                <?php echo $Row['HoraInicioReceso'];
				if(!($RowMinuta['FinalizaRegistro']>0)){?>
                <a onClick="EditarReceso(<?php echo $Row['IDMinutaReceso'];?>);" style="cursor:pointer" title="Ediar Registro"><span class="glyphicon glyphicon-new-window"></span></a><?php
				}?>
            </div>
            <div class="form-group col-sm-3">
                <?php echo $Row['Receso'];?>
            </div>
            <div class="form-group col-sm-4">
                <?php echo $Row['VigilanteAsume'];?>
            </div>
            <div class="form-group col-sm-1"><?php
            	echo $Row['HoraFinReceso'];
				if(!($RowMinuta['FinalizaRegistro']>0)){?>
                <a onClick="BorrarReceso(<?php echo $Row['IDMinutaReceso'];?>);" style="cursor:pointer" title="Eliminar Registro"><img src="../imagenes/eliminar.png" width="16" height="16" border="0"></a><?php
				}?>
            </div>
            <div class="form-group col-sm-3">
                <?php echo $Row['ObsReceso']."(".DarFechaHora($Row['FCrea']).")";?>
            </div>
        </div><?php
	}//fin del while
	if(!($RowMinuta['FinalizaRegistro']>0)){?>
		<script type="text/javascript">
        $("#BotEditarReceso").show();
        </script>
	<?php
	}else{?>
		<script type="text/javascript">
        $("#BotEditarReceso").hide();
        </script>
	<?php
	}
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
RETORNAR LOS DATOS DE UN RECESO PARA EDICIÓN
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax6JorA6TraerReceso'.date('d'))){
	$Queri="SELECT MinutaRecesos.*
			FROM ".$PrefBD."solicitudes.vigilanciaminutarecesos MinutaRecesos
			WHERE MinutaRecesos.IDMinutaReceso=".intval($_GET['IDMinutaReceso'])."
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
GRABAR LOS DATOS DE UN RECESO
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6GrabarReceso'.date('d')) and $_POST['TipoModificar']==md5('GrabarRecesoJorA6'.date('d'))){
	if($_POST['IDMinutaReceso']=='Nuevo'){
		$Queri= "INSERT INTO ".$PrefBD."solicitudes.vigilanciaminutarecesos(IDMinuta,IDReceso,HoraInicioReceso,VigilanteAsume,HoraFinReceso,ObsReceso,FCrea)
					VALUES('".$_POST['IDMinuta']."','".$_POST['IDReceso']."','".$_POST['HoraInicioReceso']."','".OptimizarTexto($_POST['VigilanteAsume'])."','".$_POST['HoraFinReceso']."','".OptimizarTexto($_POST['ObsReceso'])."',SYSDATE())
				ON DUPLICATE KEY UPDATE HoraInicioReceso='".$_POST['HoraInicioReceso']."',
										VigilanteAsume='".OptimizarTexto($_POST['VigilanteAsume'])."',
										HoraFinReceso='".$_POST['HoraFinReceso']."',
										ObsReceso='".OptimizarTexto($_POST['ObsReceso'])."',
										Borrada=0";
	}else{
		$Queri= "UPDATE ".$PrefBD."solicitudes.vigilanciaminutarecesos SET
						HoraInicioReceso='".$_POST['HoraInicioReceso']."',
						VigilanteAsume='".OptimizarTexto($_POST['VigilanteAsume'])."',
						HoraFinReceso='".$_POST['HoraFinReceso']."',
						ObsReceso='".OptimizarTexto($_POST['ObsReceso'])."',
						Borrada=0
				WHERE IDMinutaReceso=".intval($_POST['IDMinutaReceso'])."
				LIMIT 1";
	}
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
BORRAR EL REGISTRO DE UN RECESO
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6BorrarReceso'.date('d')) and $_POST['TipoModificar']==md5('BorrarRecesoJorA6'.date('d'))){
	$Queri= "UPDATE ".$PrefBD."solicitudes.vigilanciaminutarecesos SET
					Borrada=1
			WHERE IDMinutaReceso=".intval($_POST['IDMinutaReceso'])."
			LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Retornar las Novedades en la Minuta
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax7JorA6Novedades'.date('d'))){
	$QueriMinuta = "SELECT Minuta.FinalizaRegistro
					FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
					WHERE Minuta.IDMinuta=".intval($_GET['IDMinuta'])."
					LIMIT 1";
	$ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
	$RowMinuta = $ResultMinuta->fetch_assoc();
	$Queri = "SELECT MinutaNovedades.*
				FROM ".$PrefBD."solicitudes.vigilanciaminutanovedades MinutaNovedades
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciaminuta Minuta ON MinutaNovedades.IDMinuta=Minuta.IDMinuta
				WHERE MinutaNovedades.IDMinuta=".intval($_GET['IDMinuta'])." AND MinutaNovedades.Borrada=0
				ORDER BY MinutaNovedades.IDMinutaNovedad";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));?>
    <div class="row">
        <div class="form-group col-sm-1">
            <label>Núm</label>
        </div>
        <div class="form-group col-sm-1">
            <label>Hora</label>
        </div>
        <div class="form-group col-sm-4">
            <label>Descripción Novedad</label>
        </div>
        <div class="form-group col-sm-3">
            <label>Comunicador Novedad</label>
        </div>
        <div class="form-group col-sm-3">
            <label>Cargo Comunicador</label>
        </div>
	</div><?php
	$i=0;
	while($Row = $Result->fetch_assoc()){
		$i++;?>
        <div class="row" id="DivNovedad<?php echo $i;?>" <?php if($Result->num_rows==0) echo 'style="display:none"';?>>
            <div class="form-group col-sm-1"><?php
            if(!($RowMinuta['FinalizaRegistro']>0)){?>
				<a onClick="EditarNovedad(<?php echo $Row['IDMinutaNovedad'];?>);" style="cursor:pointer" title="Ediar Registro"><?php echo $i;?></a><?php
			}else{
				echo $i;
			}?>
            </div>
            <div class="form-group col-sm-1">
                <?php echo $Row['HoraNovedad'];
                if(!($RowMinuta['FinalizaRegistro']>0)){?>
                <a onClick="BorrarNovedad(<?php echo $Row['IDMinutaNovedad'];?>);" style="cursor:pointer" title="Eliminar Registro"><img src="../imagenes/eliminar.png" width="16" height="16" border="0"></a><?php
				}?>
            </div>
            <div class="form-group col-sm-4">
                <?php echo $Row['DescripcionNovedad']."(".DarFechaHora($Row['FCrea']).")";?>
            </div>
            <div class="form-group col-sm-3">
                <?php echo $Row['ComunicadorNovedad'];?>
            </div>
            <div class="form-group col-sm-3"><?php
            	echo $Row['CargoComunicador'];?>
            </div>
        </div><?php
	}//fin del while
	if(!($RowMinuta['FinalizaRegistro']>0)){?>
		<script type="text/javascript">
        $("#BotEditarNovedad").show();
        </script>
	<?php
	}else{?>
		<script type="text/javascript">
        $("#BotEditarNovedad").hide();
        </script>
	<?php
	}
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
RETORNAR LOS DATOS DE UNA NOVEDAD PARA EDICIÓN
*******************************************************************************************************************************************/
}elseif($_GET['TipoModificar']==md5('Ajax7JorA6TraerNovedad'.date('d'))){
	$Queri="SELECT MinutaNovedades.*
			FROM ".$PrefBD."solicitudes.vigilanciaminutanovedades MinutaNovedades
			WHERE MinutaNovedades.IDMinutaNovedad=".intval($_GET['IDMinutaNovedad'])."
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
GRABAR LOS DATOS DE UNA NOVEDAD
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6GrabarNovedad'.date('d')) and $_POST['TipoModificar']==md5('GrabarNovedadJorA6'.date('d'))){
	if($_POST['IDMinutaNovedad']=='Nuevo'){
		$Queri= "INSERT INTO ".$PrefBD."solicitudes.vigilanciaminutanovedades(IDMinuta,HoraNovedad,DescripcionNovedad,ComunicadorNovedad,CargoComunicador,FCrea)
					VALUES('".$_POST['IDMinuta']."','".$_POST['HoraNovedad']."',
										'".OptimizarTexto($_POST['DescripcionNovedad'])."',
										'".OptimizarTexto($_POST['ComunicadorNovedad'])."',
										'".OptimizarTexto($_POST['CargoComunicador'])."',SYSDATE())
				ON DUPLICATE KEY UPDATE IDMinuta='".$_POST['IDMinuta']."',
										HoraNovedad='".$_POST['HoraNovedad']."',
										DescripcionNovedad='".OptimizarTexto($_POST['DescripcionNovedad'])."',
										ComunicadorNovedad='".OptimizarTexto($_POST['ComunicadorNovedad'])."',
										CargoComunicador='".OptimizarTexto($_POST['CargoComunicador'])."',
										Borrada=0";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		$mNuevoIDNovedad=$mysqli->insert_id;
		/*ENVIO EL MENSAJE A LA ENCARGADA DE SEGURIDAD*/
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.waboxapp.com/api/send/chat");
		$options = array(
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_SSL_VERIFYHOST	=> false,
		CURLOPT_POST			=> 1,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_MAXREDIRS		=> 5,
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_TIMEOUT			=> 20,
		CURLOPT_CONNECTTIMEOUT	=> 25
		);
		curl_setopt_array($ch, $options);
		$data=array('token' => 'c24124c275ef52ae9fd4a8b7e157a76c5df8d10c24edb',
					'uid' => '573112425848',
					'to' => '573167435154',
					'custom_uid' => 'NovMinuta-'.$mNuevoIDNovedad,
					'text' => "Se acaba de generar una novedad en la Minuta ID: ".$_POST['IDMinuta'].
								", Consecutivo Novedad: ".$mNuevoIDNovedad.
								", Hora: ".$_POST['HoraNovedad'].
								", Descripción: ".OptimizarTexto($_POST['DescripcionNovedad']).
								", Comunica: ".OptimizarTexto($_POST['ComunicadorNovedad']).
								", Cargo: ".OptimizarTexto($_POST['CargoComunicador']).
								", Reporta: ".$_SESSION['NomUsuario']
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$Respuesta=curl_exec($ch);
		curl_close($ch);
	}else{
		$Queri= "UPDATE ".$PrefBD."solicitudes.vigilanciaminutanovedades SET
						HoraNovedad='".$_POST['HoraNovedad']."',
						DescripcionNovedad='".OptimizarTexto($_POST['DescripcionNovedad'])."',
						ComunicadorNovedad='".OptimizarTexto($_POST['ComunicadorNovedad'])."',
						CargoComunicador='".OptimizarTexto($_POST['CargoComunicador'])."',
						Borrada=0
				WHERE IDMinutaNovedad=".intval($_POST['IDMinutaNovedad'])."
				LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	}
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
BORRAR EL REGISTRO DE UNA NOVEDAD
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6BorrarNovedad'.date('d')) and $_POST['TipoModificar']==md5('BorrarNovedadJorA6'.date('d'))){
	$Queri= "UPDATE ".$PrefBD."solicitudes.vigilanciaminutanovedades SET
					Borrada=1
			WHERE IDMinutaNovedad=".intval($_POST['IDMinutaNovedad'])."
			LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
4  Finalización Registro de Minuta por Puesto y Turno
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6GrabarCerrarNovedad'.date('d')) and $_POST['TipoModificar']==md5('GrabarCerrarNovedadJorA6'.date('d'))){
	$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaminuta
				SET ObsMinuta='".OptimizarTexto($_POST['ObsMinuta'])."',
					FinalizaRegistro=SYSDATE()
				WHERE IDMinuta=".intval($_POST['IDMinuta'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
Firma de la Minuta
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6Firma'.date('d')) and $_POST['TipoModificar']==md5('FirmaJorA6'.date('d'))){
	if($_POST['TipoFirma']=='Entrante' or $_POST['TipoFirma']=='Saliente'){
		$Queri="SELECT Emplea.Nit_CCE
				FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
				JOIN  ".$PrefBD."recursos.emplea Emplea ON Minuta.Vigilante".$_POST['TipoFirma']."=Emplea.Nit_CCE
				WHERE Minuta.IDMinuta=".intval($_POST['IDMinuta'])." AND Emplea.Clave='".$_POST['ClaveFirma']."'
				LIMIT 1";
		$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		if($Row=$Result->fetch_assoc()){
			$Queri = "UPDATE ".$PrefBD."solicitudes.vigilanciaminuta
						SET FFirma".$_POST['TipoFirma']."=SYSDATE(),
							ObsFirma".$_POST['TipoFirma']."='".OptimizarTexto($_POST['ObsFirma'])."'
						WHERE IDMinuta=".intval($_POST['IDMinuta'])."
						LIMIT 1";
			$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
		}else{
			echo "*Error* Clave no válida";
		}
	}else{
		echo "*Error* Parámetros no válido";
	}
	mysqli_close($mysqli);
	exit;
}?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<title>Departamento de Seguridad</title>
<link rel="stylesheet" href="../librerias/jquery/jquery-ui.css">
<script src="../librerias/jquery/jquery-1.10.2.js"></script>
<script src="../librerias/jquery/jquery-ui.js"></script>
<link rel="stylesheet" href="../librerias/bootstrap-3.3.7-dist/css/bootstrap.min.css">
<script src="../librerias/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<!--
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
-->
<script src="../funciones.js"></script>
<style>
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
</style>
<script type="text/javascript">
function MostrarDatoObser(msg, mTipo=false){
	$("#DatoObser,#DatoObserRed").hide();
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
function documentHeight(){//Obtener el alto de la página
    return Math.max(
        document.documentElement.clientHeight,
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight
    );
}
</script>
</head>
<body style="padding-top: 60px;" <?php if($_GET['DatoObser']){?>onLoad="MostrarDatoObser('<?php echo $_GET['DatoObser'];?>',true);"<?php }?>>
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
}//Fin de si Se enviaron datos de consulta para filtro
?>
</div>
<!-- Modal -->
<div class="modal fade" id="ModalConfirmacion" tabindex="-1" role="dialog" aria-labelledby="ModalConfirmacionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalConfirmacionTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ModalConfirmacionBody">
            ...
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-secondary" id="BotCancelarModal" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="BotAceptarModal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
</body>
</html><?php
mysqli_close($mysqli);
?>