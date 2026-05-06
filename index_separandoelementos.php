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
	IDPuestoSucursal SMALLINT(6) NOT NULL DEFAULT 0,
	Turno VARCHAR(10) NOT NULL DEFAULT '',
	Elabora VARCHAR(12) NOT NULL DEFAULT '',
	FElabora DATETIME DEFAULT NULL,
	Fecha DATE DEFAULT NULL,
	HoraInicio VARCHAR(5) NOT NULL DEFAULT '',
	VigilanteEntrante VARCHAR(12) NOT NULL DEFAULT '',
	VigilanteSaliente VARCHAR(12) NOT NULL DEFAULT '',
	RealizaRequisa SMALLINT(1) NOT NULL DEFAULT 0,
	ObsRequisa VARCHAR(250) NOT NULL DEFAULT '',
	ObsMMinuta VARCHAR(250) NOT NULL DEFAULT '',
	FinalizaRegistro DATETIME DEFAULT NULL,
	Borrada SMALLINT(1) NOT NULL DEFAULT 0,
	UNIQUE IDMinuta(Sucursal,IDPuestoSucursal,Fecha,VigilanteSaliente)
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
	$Queri = "SELECT ElementoPuestoSucursal.IDElementoPuestoSucursal, PuestoSucursal.IDPuestoSucursal, Elemento.IDElemento,
					ElementoPuestoSucursal.Cantidad, IFNULL(ElementoPuestoSucursal.Borrada,1) AS Borrada,
					Sucursal.NomSucursal, Puesto.Puesto, Elemento.Grupo, Elemento.Elemento,  PuestoSucursal.ObsPuesto
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
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	$i=0;
	$Cant=0;
	while($Row = $Result->fetch_assoc()){
		$i++;
		$Cant++;
		$Tabs=0;
		if($Cant==1){
			$Tabs++;?>
            <div class="tab row"><?php
		}?>
                <div class="form-group col-sm-10">
                    <label>Núm <?php echo $i;?> - Espacios y/o elemento expuesto (Visuales)</label>
                    <input name="Fecha" type="text" class="form-control" id="Fecha" value="<?php echo $Row['Elemento'];?>" readonly>
                </div>
                <div class="form-group col-sm-2">
                    <label>Cantidad Existente</label>
                    <input name="Fecha" type="text" class="form-control" id="Fecha" value="<?php echo $Row['Cantidad'];?>" readonly>
                </div>
                <div class="form-group col-sm-3">
                    <label>Cantidad Verificada</label>
                    <input name="CantidadVerificada<?php echo $Row['IDElementoPuestoSucursal'];?>" id="Cantidad<?php echo $Row['IDElementoPuestoSucursal'];?>" maxlength="6" class="form-control" value="<?php echo ($Row['CantidadVerificada']>0 ? $Row['CantidadVerificada'] : '');?>" onBlur="EnviarPuestoSucursalElemento(this,<?php echo intval($Row['IDElemento']).",".$Row['IDElementoPuestoSucursal'];?>);" <?php echo ($Row['Borrada']==1 ? 'disabled' : '');?>>
                </div>
                <div class="form-group col-sm-3">
                    <label>Verificado</label>
                    <select name='Verificado' id='Verificado' class="form-control">
                        <option value=0 <?php if($Row['Verificado']==0){echo 'selected';}?>></option>
                        <option value=1 <?php if($Row['Verificado']==1){echo 'selected';}?>>Si</option>
                        <option value=-1 <?php if($Row['Verificado']==-1){echo 'selected';}?>>No</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label>Observación en la verificación de puesto</label>
                    <input name="CantidadVerificada<?php echo $Row['IDElementoPuestoSucursal'];?>" id="Cantidad<?php echo $Row['IDElementoPuestoSucursal'];?>" maxlength="6" class="form-control" value="<?php echo ($Row['CantidadVerificada']>0 ? $Row['CantidadVerificada'] : '');?>" onBlur="EnviarPuestoSucursalElemento(this,<?php echo intval($Row['IDElemento']).",".$Row['IDElementoPuestoSucursal'];?>);" <?php echo ($Row['Borrada']==1 ? 'disabled' : '');?>>
                </div>
                <div class="clearfix"></div><?php
		if($Cant==3 or $i==$Result->num_rows){
			$Cant=0;?>
            </div><?php
		}
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
Retornar Verificar si el usuario actual tiene Minutas pendientes por finalizar
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
IDENTIFICACIÓN
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA6Tipo0'.date('d')) and $_POST['TipoModificar']==md5('Tipo0JorA6'.date('d'))){
	echo 1;
	exit;
	//Optimizo variables
	$mVigilanteSaliente=explode('|-|',$_POST['VigilanteSaliente']);
	$mVigilanteEntrante=explode('|-|',$_POST['VigilanteEntrante']);
	if(intval($_POST['IDMinuta'])==0){//Se trata de una nueva minuta
		$Queri= "INSERT INTO ".$PrefBD."solicitudes.vigilanciaminuta(Sucursal,IDPuestoSucursal,Fecha,VigilanteSaliente,Elabora,FElabora)
					VALUES('".$_POST['Sucursal']."','".$_POST['IDPuestoSucursal']."','".DarFechaSQL($_POST['Fecha'])."','".$mVigilanteSaliente[1]."','".$_SESSION['Usuario']."',SYSDATE())
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
				SET Sucursal='".$_POST['Sucursal']."',
					IDPuestoSucursal='".$_POST['IDPuestoSucursal']."',
					Turno='".$_POST['Turno']."',
					Fecha='".DarFechaSQL($_POST['Fecha'])."',
					HoraInicio='".$_POST['HoraInicio']."',
					Turno='".$_POST['Turno']."',
					VigilanteSaliente='".$mVigilanteSaliente[1]."',
					VigilanteEntrante='".$mVigilanteEntrante[1]."'
				WHERE IDMinuta=".intval($_POST['IDMinuta'])."
				LIMIT 1";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	echo intval($_POST['IDMinuta']);
	mysqli_close($mysqli);
	exit;
}?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Departamento de Seguridad</title>
<link rel="stylesheet" href="../librerias/jquery/jquery-ui.css">
<script src="../librerias/jquery/jquery-1.10.2.js"></script>
<script src="../librerias/jquery/jquery-ui.js"></script>
<link rel="stylesheet" href="../librerias/bootstrap-3.3.7-dist/css/bootstrap.min.css">
<script src="../librerias/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
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