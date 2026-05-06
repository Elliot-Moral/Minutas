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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR PUESTO','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR PUESTOxSUCURSAL','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR ELEMENTO','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR ELEMENTOxPUESTOxSUCURSAL','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR TIPO RECESO','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
		<tr class="border-b border-gray-200 hover:bg-blue-50/50 transition-colors duration-200">
			<td class="px-4 py-3 text-center text-sm font-medium text-gray-700"><?php echo $i;?></td>
			<td class="px-4 py-3">
				<span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-[12px] font-medium">
					<?php echo $Row['Elemento'];?>
				</span>
			</td>
			<td class="px-4 py-3 text-center">
			<span class="inline-flex items-center justify-center px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm font-semibold">
				<?php echo ($Row['CantidadReal']===NULL ? 'N/A' : $Row['CantidadReal']);?>
			</span>
			<input name="CantidadReal<?php echo $Row['IDElemento'];?>" id="CantidadReal<?php echo $Row['IDElemento'];?>" type="hidden" value="<?php echo ($Row['CantidadReal']===NULL ? 'N/A' : $Row['CantidadReal']);?>">
			</td>
			<td class="px-4 py-3">
			<input name="CantidadVerificada<?php echo $Row['IDElemento'];?>" 
					 id="CantidadVerificada<?php echo $Row['IDElemento'];?>" 
					 type="number" 
					 min="0" 
					 maxlength="6" 
					 value="<?php echo ($Row['CantidadVerificada']===NULL ? '' : $Row['CantidadVerificada']);?>" 
					 onChange="ValidarNumeros(this);EnviarMinutaElemento(this);"
					 placeholder="Cantidad"
					 class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
			</td>
			<td class="px-4 py-3">
			<input name="ObsVerifica<?php echo $Row['IDElemento'];?>" 
					 id="ObsVerifica<?php echo $Row['IDElemento'];?>" 
					 type="text" 
					 maxlength="100" 
					 value="<?php echo htmlspecialchars($Row['ObsVerifica']);?>" 
					 onChange="EnviarMinutaElemento(this);" 
					 placeholder="Observaciones..."
					 class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
			</td>
		</tr><?php
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR MINUTA IDENTIFICACION','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR MINUTA FIN RECORRIDO','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
	mysqli_close($mysqli);
	exit;
/******************************************************************************************************************************************
Retornar los Recesos en la Minuta
*******************************************************************************************************************************************/
} elseif ($_GET['TipoModificar'] == md5('Ajax5JorA6Recesos' . date('d'))) {
    $QueriMinuta = "SELECT Minuta.FinalizaRegistro 
                    FROM " . $PrefBD . "solicitudes.vigilanciaminuta Minuta 
                    WHERE Minuta.IDMinuta=" . intval($_GET['IDMinuta']) . " 
                    LIMIT 1";
    $ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
    $RowMinuta = $ResultMinuta->fetch_assoc();

    $Queri = "SELECT MinutaRecesos.*, Receso.Receso, Minuta.FinalizaRegistro 
              FROM " . $PrefBD . "solicitudes.vigilanciaminutarecesos MinutaRecesos 
              LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaminuta Minuta ON MinutaRecesos.IDMinuta=Minuta.IDMinuta 
              LEFT JOIN " . $PrefBD . "solicitudes.vigilanciareceso Receso ON MinutaRecesos.IDReceso=Receso.IDReceso 
              WHERE MinutaRecesos.IDMinuta=" . intval($_GET['IDMinuta']) . " AND MinutaRecesos.Borrada=0 
              ORDER BY MinutaRecesos.IDMinutaReceso";
    $Result = $mysqli->query($Queri) or die(mysqli_error($mysqli)); 
    
    $finalizado = ($RowMinuta['FinalizaRegistro'] > 0);
    ?>

    <!-- Contenedor de Tabla Moderna -->
    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white mb-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Inicio</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Actividad</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Personal Asume</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Fin</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Observación / Registro</th>
                    <?php if (!$finalizado): ?>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php
                $i = 0;
                while ($Row = $Result->fetch_assoc()) {
                    $i++; ?>
                    <tr id="DivReceso<?php echo $i; ?>" class="hover:bg-blue-50/30 transition-colors group">
                        <!-- Hora Inicio + Botón Editar -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-700"><?php echo $Row['HoraInicioReceso']; ?></span>
                                <?php if (!$finalizado): ?>
                                    <button type="button" onClick="EditarReceso(<?php echo $Row['IDMinutaReceso']; ?>);" class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar Registro">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.5 2.5 0 113.536 3.536L12 14.232l-4 1 1-4 9.414-9.414z" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Actividad -->
                        <td class="px-4 py-3 text-sm text-gray-600 font-medium">
                            <?php echo $Row['Receso']; ?>
                        </td>

                        <!-- Persona que asume -->
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-[10px] font-bold text-orange-500 border border-orange-200 uppercase">
                                    <?php echo substr($Row['VigilanteAsume'], 0, 1); ?>
                                </div>
                                <?php echo $Row['VigilanteAsume']; ?>
                            </div>
                        </td>

                        <!-- Hora Fin -->
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold ">
													<span class="text-green-700 p-1 bg-green-50 border border-green-100 rounded-lg">
                            <?php echo $Row['HoraFinReceso']; ?>
													</span>
                        </td>

                        <!-- Observación y Fecha -->
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600"><?php echo $Row['ObsReceso']; ?></span>
                                <span class="text-[10px] text-gray-400 mt-1 italic font-mono">
                                    <i class="far fa-clock"></i> <?php echo DarFechaHora($Row['FCrea']); ?>
                                </span>
                            </div>
                        </td>

                        <!-- Acciones (Borrar) -->
                        <?php if (!$finalizado): ?>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onClick="BorrarReceso(<?php echo $Row['IDMinutaReceso']; ?>);" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Eliminar Registro">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>

                <?php if ($i == 0): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm italic">
                            No hay registros de receso disponibles.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts de Control -->
    <script type="text/javascript">
        <?php if (!$finalizado): ?>
            $("#BotEditarReceso").show();
        <?php else: ?>
            $("#BotEditarReceso").hide();
        <?php endif; ?>
    </script>

    <?php
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR RECESO','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
} elseif ($_GET['TipoModificar'] == md5('Ajax7JorA6Novedades' . date('d'))) {
    $QueriMinuta = "SELECT Minuta.FinalizaRegistro 
                    FROM " . $PrefBD . "solicitudes.vigilanciaminuta Minuta 
                    WHERE Minuta.IDMinuta=" . intval($_GET['IDMinuta']) . " 
                    LIMIT 1";
    $ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
    $RowMinuta = $ResultMinuta->fetch_assoc();

    $Queri = "SELECT MinutaNovedades.* 
              FROM " . $PrefBD . "solicitudes.vigilanciaminutanovedades MinutaNovedades 
              LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaminuta Minuta ON MinutaNovedades.IDMinuta=Minuta.IDMinuta 
              WHERE MinutaNovedades.IDMinuta=" . intval($_GET['IDMinuta']) . " AND MinutaNovedades.Borrada=0 
              ORDER BY MinutaNovedades.IDMinutaNovedad";
    $Result = $mysqli->query($Queri) or die(mysqli_error($mysqli)); 
    
    $finalizado = ($RowMinuta['FinalizaRegistro'] > 0);
    ?>

    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white mb-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">Núm</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">Hora</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Descripción Novedad</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Comunicador Novedad</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cargo</th>
                    <?php if (!$finalizado): ?>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest w-20">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php
                $i = 0;
                while ($Row = $Result->fetch_assoc()) {
                    $i++; ?>
                    <tr id="DivNovedad<?php echo $i; ?>" class="hover:bg-blue-50/30 transition-colors group">
                        
                        <!-- Número y Editar -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php if (!$finalizado): ?>
                                <button onClick="EditarNovedad(<?php echo $Row['IDMinutaNovedad']; ?>);" class="flex items-center gap-2 group/btn cursor-pointer">
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md group-hover/btn:bg-blue-600 group-hover/btn:text-white transition-all">
                                        <?php echo $i; ?>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-400 opacity-0 group-hover/btn:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            <?php else: ?>
                                <span class="text-xs font-bold text-gray-400 px-2"><?php echo $i; ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Hora -->
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-700">
                            <?php echo $Row['HoraNovedad']; ?>
                        </td>

                        <!-- Descripción -->
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-gray-700 leading-relaxed"><?php echo $Row['DescripcionNovedad']; ?></span>
                                <span class="text-[10px] text-gray-400 font-mono italic">
                                    <i class="far fa-calendar-alt mr-1"></i> <?php echo DarFechaHora($Row['FCrea']); ?>
                                </span>
                            </div>
                        </td>

                        <!-- Comunicador -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-50 flex items-center justify-center text-[10px] font-bold text-indigo-500 border border-indigo-100 uppercase">
                                    <?php echo substr($Row['ComunicadorNovedad'], 0, 1); ?>
                                </div>
                                <span class="text-sm text-gray-700 font-medium"><?php echo $Row['ComunicadorNovedad']; ?></span>
                            </div>
                        </td>

                        <!-- Cargo -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-tight">
                                <?php echo $Row['CargoComunicador']; ?>
                            </span>
                        </td>

                        <!-- Acciones -->
                        <?php if (!$finalizado): ?>

													<td class="px-4 py-3 text-center">
                                <button type="button" onClick="BorrarNovedad(<?php echo $Row['IDMinutaNovedad']; ?>);" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Eliminar Registro">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>

                <?php if ($i == 0): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm italic">No se han registrado novedades.</span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        <?php if (!$finalizado): ?>
            $("#BotEditarNovedad").show();
        <?php else: ?>
            $("#BotEditarNovedad").hide();
        <?php endif; ?>
    </script>

    <?php
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
		$QueriMinuta="SELECT Puesto.Puesto, Sucursal.NomSucursal
				FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
				JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal ON Minuta.IDPuestoSucursal=PuestoSucursal.IDPuestoSucursal
				JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				JOIN ".$PrefBD."novasoft.sucursal Sucursal ON Minuta.Sucursal=Sucursal.Sucursal
				WHERE Minuta.IDMinuta='".$_POST['IDMinuta']."'
				LIMIT 1";
		$ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
		$RowMinuta=$ResultMinuta->fetch_assoc();
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
					'to' => '573112021603',//573167435154 LuisaMP3158932818
					'custom_uid' => 'NovMinuta-'.$mNuevoIDNovedad,
					'text' => "Se acaba de generar una novedad en la Minuta ID: ".$_POST['IDMinuta'].
								", Sucursal: ".$RowMinuta['NomSucursal'].
								", Puesto: ".$RowMinuta['Puesto'].
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
		print_r($Respuesta);
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
	//Genero el Evento
	$QueriEvento = "INSERT INTO ".$PrefBD."solicitudes.eventos(Usuario,Modulo,Tipo,Observaciones,Fecha,IP)
							VALUES
						('".$_SESSION['Usuario']."','VIGILANCIA','EDITAR NOVEDAD','".str_replace("'","´",$Queri)."',SYSDATE(),'".$_SESSION['IPAcceso']."')";
	$ResultEvento = $mysqli->query($QueriEvento);
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
		// if($Row=$Result->fetch_assoc()){
		$ss = true;
		if($ss){
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

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

<script src="../funciones.js"></script>
<style>
.colorBase{
	background-color: #104379;
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

function MostrarDivModales(nomModal) {
  const modal = document.getElementById(`modal-${nomModal}`);
  const backdrop = document.getElementById(`modal-${nomModal}-backdrop`);
  const container = document.getElementById(`modal-${nomModal}-container`);
  modal.classList.remove('hidden');
  const tl = gsap.timeline();
    tl.fromTo(backdrop, { opacity: 0 }, { opacity: 1, duration: 0.3, ease: 'power2.out' })
    .fromTo(container, { scale: 0.8, opacity: 0, y: -50 }, { scale: 1, opacity: 1, y: 0, duration: 0.4, ease: 'back.out(1.7)' }, '-=0.2');
 
  // añadir escucador para cerrar el modal al oprimir la tecla Escape
  document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
      CerrarDivModales(nomModal);
    }
  });
}

function CerrarDivModales(nomModal) {
  const modal = document.getElementById(`modal-${nomModal}`);
      const backdrop = document.getElementById(`modal-${nomModal}-backdrop`);
      const container = document.getElementById(`modal-${nomModal}-container`);
      const tl = gsap.timeline({
        onComplete: () => {
          modal.classList.add('hidden');
          gsap.set([backdrop, container], { clearProps: 'all' });
        }
      });
      tl.to(container, { scale: 0.8, opacity: 0, y: -50, duration: 0.3, ease: 'back.in(1.7)' })
        .to(backdrop, { opacity: 0, duration: 0.2 }, '-=0.1');
}
</script>
</head>
<body <?php if($_GET['DatoObser']){?>onLoad="MostrarDatoObser('<?php echo $_GET['DatoObser'];?>',true);"<?php }?>>

	<main class="">

		<div class="w-[95%] m-auto bg-green-500 text-white p-4 rounded-lg" id="DatoObser" style="z-index:1000000; display:<?php echo 'none';?>">
			<b>Success!</b> Indicates a successful or positive action.
		</div>

		<div class="myAlert-bottom alert alert-danger" id="DatoObserRed" style="z-index:1000000; display:<?php echo 'none';?>">
			<b>Danger!</b> This alert box could indicate a dangerous or potentially negative action.
		</div>

		<div class="relative h-screen  md:grid md:grid-cols-1 md:grid-cols-10">

			<nav class="overflow-auto w-[100vw] md:w-full md:h-full colorBase bottom-0 z-50 flex md:flex-col absolute md:static  md:col-span-2 navbar navbar-default navbar-fixed-top  text-white md:h-screen p-1 md:p-4 md:pt-[60px] transition-all duration-700">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="hidden md:block">
					<button type="button" class="" data-toggle="collapse" data-target=".navbar-ex6-collapse">
						<span class="sr-only">Desplegar navegación</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="text-white font-bold text-lg hidden md:block" href="#">Departamento De Seguridad</a>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="md:mt-8 flex md:flex-col p-2 gap-3 md:justify-between md:h-[calc(100vh-160px)]">
							<ul class="flex items md:flex-col  gap-6 md:gap-0">
											<li>
												<a href="index.php?TipoModificar=<?php echo md5('JorA1'.date('d'));?>">
													<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>	
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 24 24" >
																<path d="M19.5 3h-5c-.83 0-1.5.67-1.5 1.5v5c0 .83.67 1.5 1.5 1.5h5c.83 0 1.5-.67 1.5-1.5v-5c0-.83-.67-1.5-1.5-1.5M19 9h-4V5h4zM9.5 3h-5C3.67 3 3 3.67 3 4.5v15c0 .83.67 1.5 1.5 1.5h5c.83 0 1.5-.67 1.5-1.5v-15c0-.83-.67-1.5-1.5-1.5M9 19H5V5h4zm10.5-6h-5c-.83 0-1.5.67-1.5 1.5v5c0 .83.67 1.5 1.5 1.5h5c.83 0 1.5-.67 1.5-1.5v-5c0-.83-.67-1.5-1.5-1.5m-.5 6h-4v-4h4z"></path>
															</svg>
														</span>	
														<span class="text-center hidden md:block">Administrar Minutas</span>
													</button>
												</a>
											</li>
											<?php
												if($PuedeAdministrar){?>
												<li><a href="index.php?TipoModificar=<?php echo md5('JorA2'.date('d'));?>">
														<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>	
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30"	fill="#ffffff" viewBox="0 0 24 24" >
																<path d="M6 8.44c-.02 5.1 5.17 9.18 5.39 9.35.18.14.4.21.61.21s.43-.07.61-.21c.22-.17 5.41-4.25 5.39-9.35C18 4.89 15.31 2 12 2S6 4.89 6 8.44m10 0c.01 3.19-2.74 6.08-4 7.24-1.26-1.15-4.01-4.04-4-7.24C8 5.99 9.79 4 12 4s4 1.99 4 4.44"></path><path d="M12 6a2 2 0 1 0 0 4 2 2 0 1 0 0-4m6.02 8.73c-.4.64-.84 1.23-1.27 1.76C18.88 16.97 20 17.68 20 18c0 .51-2.75 2-8 2s-8-1.49-8-2c0-.32 1.12-1.03 3.25-1.51-.43-.53-.86-1.12-1.27-1.76C3.66 15.37 2 16.44 2 18c0 2.75 5.18 4 10 4s10-1.25 10-4c0-1.56-1.67-2.63-3.98-3.27"></path>
															</svg>
														</span>
														<span class="text-center hidden md:block">Puestos</span>
													</button>
												</a></li>
												<li><a href="index.php?TipoModificar=<?php echo md5('JorA3'.date('d'));?>">
													<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 24 24" >
																<path d="M19 3c-1.65 0-3 1.35-3 3 0 .5.14.97.35 1.38l-1.12 1.3c-.64-.43-1.41-.69-2.24-.69s-1.53.24-2.15.64l-2.2-1.65c.22-.45.35-.96.35-1.49 0-1.93-1.57-3.5-3.5-3.5s-3.5 1.57-3.5 3.5 1.57 3.5 3.5 3.5c.66 0 1.28-.2 1.81-.52l2.18 1.64c-.3.56-.49 1.2-.49 1.88 0 1 .38 1.9.99 2.6l-1.69 1.69.03.03c-.4-.2-.84-.32-1.32-.32-1.65 0-3 1.35-3 3s1.35 3 3 3 3-1.35 3-3c0-.48-.12-.92-.32-1.32l.03.03 1.95-1.95c.42.15.87.25 1.34.25 2.21 0 4-1.79 4-4 0-.64-.17-1.24-.44-1.78l1.25-1.46c.36.16.76.25 1.19.25 1.65 0 3-1.35 3-3s-1.35-3-3-3ZM7 20c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1M4 5.5C4 4.67 4.67 4 5.5 4S7 4.67 7 5.5 6.33 7 5.5 7 4 6.33 4 5.5m9 8.5c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2m6-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1"></path>
															</svg>
														</span>
														<span class="text-center hidden md:block">Puestos | Sucursal</span>
													</button></a></li>
												<li><a href="index.php?TipoModificar=<?php echo md5('JorA4'.date('d'));?>">
													<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 24 24" >
																<path d="m21.95 8.68-2-6A1 1 0 0 0 19 2H6c-.38 0-.73.21-.89.55l-3 6s0 .03-.01.04c0 .02-.01.04-.02.07q-.06.15-.06.3V20c0 .55.45 1 1 1h18c.55 0 1-.45 1-1V8.97c0-.1-.01-.19-.05-.29ZM6.62 4h11.66l1.33 4H4.62zM20 19H4v-9h16z"></path>
															</svg>
														</span>
														<span class="text-center hidden md:block">Elementos</span>
													</button></a></li>
												<li><a href="index.php?TipoModificar=<?php echo md5('JorA5'.date('d'));?>">
													<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 24 24" >
																<path d="M20 16.18V13c0-1.1-.9-2-2-2h-5V7.82c1.16-.41 2-1.51 2-2.82 0-1.65-1.35-3-3-3S9 3.35 9 5c0 1.3.84 2.4 2 2.82V11H6c-1.1 0-2 .9-2 2v3.18c-1.16.41-2 1.51-2 2.82 0 1.65 1.35 3 3 3s3-1.35 3-3c0-1.3-.84-2.4-2-2.82V13h5v3.18c-1.16.41-2 1.51-2 2.82 0 1.65 1.35 3 3 3s3-1.35 3-3c0-1.3-.84-2.4-2-2.82V13h5v3.18c-1.16.41-2 1.51-2 2.82 0 1.65 1.35 3 3 3s3-1.35 3-3c0-1.3-.84-2.4-2-2.82M12 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1M5 20c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1m7 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1m7 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1"></path>
															</svg>
														</span>
														<span class="text-center hidden md:block">Elem | Pue | Suc</span>
													</button></a></li>
												<li><a href="index.php?TipoModificar=<?php echo md5('JorA6'.date('d'));?>">
													<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 24 24" >
																<path d="M5 2H4v2h1v1c0 2.46 1.32 4.77 3.43 6.02.35.21.57.55.57.9v.16c0 .35-.21.69-.57.9A7.01 7.01 0 0 0 5 19v1H4v2h16v-2h-1v-1c0-2.46-1.32-4.77-3.43-6.02-.36-.21-.57-.55-.57-.9v-.16c0-.35.21-.69.57-.9A7.01 7.01 0 0 0 19 5V4h1V2zm12 3c0 1.76-.94 3.41-2.45 4.3-.97.57-1.55 1.55-1.55 2.62v.16c0 1.07.58 2.05 1.55 2.62 1.51.89 2.45 2.54 2.45 4.3v1H7v-1c0-1.76.94-3.41 2.45-4.3.97-.57 1.55-1.55 1.55-2.62v-.16c0-1.07-.58-2.05-1.55-2.62A5.01 5.01 0 0 1 7 5V4h10z"></path>
															</svg>
														</span>
														<span class="text-center hidden md:block">Tipos de Receso</span>
													</button></a></li><?php
											}?>
											
						</ul>
						<div>
							<a href="../home/index.php"><span class="">
								<button class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
												<span>
													<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ffffff" viewBox="0 0 24 24" >
														<path d="M15 11H8v2h7v4l6-5-6-5z"></path><path d="M5 21h7v-2H5V5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2"></path>
													</svg>
												</span>
												<span class="text-center hidden md:block">
													Menú Principal
												</span>
								</button>
							</a>
						</div>
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

		<!-- MODAL Minuta Pendiente -->
    <div id="modal-Confirmacion" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
      <div id="modal-Confirmacion-backdrop" class="modal-backdrop absolute inset-0 bg-black/30" onclick="CerrarDivModales('Confirmacion')"></div>
        <div id="modal-Confirmacion-container" class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden">
					<div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">

                  <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                    <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  fill="#ffffff" viewBox="0 0 24 24" >
                      <path d="M21.5 8H21V6c0-2.21-1.79-4-4-4H7C4.79 2 3 3.79 3 6v2h-.5c-.28 0-.5.22-.5.5v3c0 .28.22.5.5.5H3v6c0 .74.41 1.38 1 1.72v1.78c0 .28.22.5.5.5h2c.28 0 .5-.22.5-.5V20h10v1.5c0 .28.22.5.5.5h2c.28 0 .5-.22.5-.5v-1.78A2 2 0 0 0 21 18v-6h.5c.28 0 .5-.22.5-.5v-3c0-.28-.22-.5-.5-.5M19 18H5v-5h14zM5 11V8h6v3zm8 0V8h6v3zM7 4h10c1.1 0 2 .9 2 2H5c0-1.1.9-2 2-2"></path><path d="M7.5 14a1.5 1.5 0 1 0 0 3 1.5 1.5 0 1 0 0-3m9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 1 0 0-3"></path>
                    </svg>
              		</div>
             		<div>
                  <h3 id="ModalConfirmacionTitle" class="text-xl font-bold">Detalles Minuta</h3>
                  <p class="text-blue-100 text-sm">Cada uno de los detalles de la minuta se muestra a continuación.</p>
              </div>
            </div>
            <button onclick="CerrarDivModales('Confirmacion')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
      	</div>
 
      	<div  class="p-6 space-y-4 overflow-y-auto">
          <div class="grid grid-cols-1 gap-4">
            <div>

              <div id="ModalConfirmacionBody" class="space-y-1">
              </div>
              <div class="flex gap-3 pt-4">
									<button type="button" onclick="CerrarDivModales('Confirmacion')"
										class="bottonConfirmacion flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
										Cerrar
									</button>
									<button  type="button" id="BotAceptarModal"
										class="bottonConfirmacion flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Continuar</span>
									</button>
              </div>

            </div>
          </div>
				</div>
      </div>
   </div>

	</main>
</body>
</html><?php
mysqli_close($mysqli);
?>