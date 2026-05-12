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
						'98381246',//George jefe tecnología
						'1121939126',//Andres Morales
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
$mTurno['Apertura']='Apertura';
$mTurno['Cierre']='Cierre';
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
    <tr class="bg-white border-t border-gray-200" align=center>
      <td class="p-2">
				<div class="inline-flex items-center gap-3 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg group hover:bg-white transition-colors">
					<div class="flex flex-col">
						<span class="text-[12px] font-mono font-bold text-slate-700 leading-none tracking-tight">
							<?php echo $i;?>
						</span>
					</div>
				</div>
			</td>
      <td class="p-2"><?php echo $Row['IDElementoPuestoSucursal'];?></td>
      <td class="p-2"><?php echo $Row['NomSucursal'];?></td>
      <td class="p-2"><?php echo $Row['Puesto'].' '.$Row['ObsPuesto'];?></td>
      <td class="p-2"><?php echo $Row['Grupo'];?></td>
      <td class="p-2"><?php echo $Row['Elemento'];?></td>
      <td class="p-2"><input name="Cantidad<?php echo $Row['IDElemento'];?>" id="Cantidad<?php echo $Row['IDElemento'];?>" class="block w-full max-w-32 ps-9 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
			 value="<?php echo ($Row['Cantidad']>0 ? $Row['Cantidad'] : '');?>" onBlur="EnviarPuestoSucursalElemento(this,<?php echo intval($Row['IDElemento']).",".$Row['IDElementoPuestoSucursal'];?>);" <?php echo ($Row['Borrada']==1 ? 'disabled' : '');?>></td>
      <td class="p-2"><input name="Borrada<?php echo $Row['IDElemento'];?>" type="checkbox" id="Borrada<?php echo $Row['IDElemento'];?>" value="1"
			class="block w-full max-w-32 ps-9 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
			onClick="EnviarPuestoSucursalElemento(this,<?php echo intval($Row['IDElemento']).",".$Row['IDElementoPuestoSucursal'];?>);" <?php echo ($Row['Borrada']==1 ? '' : 'checked');//Cconcepto al contrario Borrada/Activo?>></td>
    </tr><?php
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
	$arrayIdElement = [];
	while($Row = $Result->fetch_assoc()){

		$i++;?>
		<tr id="Elemento_<?php echo $i;?>" name="Elemento_<?php echo $Row['IDElemento'];?>" class="<?php echo ($Row['CantidadReal'] <= 0 ? 'trElemento bg-red-50 border border-red-300 hidden elementovacio' : '')?>  'border-b border-gray-200 hover:bg-blue-50/50 transition-colors duration-200'">
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
		</tr>
		<?php
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
}elseif ($_GET['TipoModificar'] == md5('Ajax5JorA6Recesos' . date('d'))) {
    // 1. Consulta de la Minuta Principal para verificar estado
    $QueriMinuta = "SELECT FinalizaRegistro, FFirmaEntrante, FFirmaSaliente 
                    FROM " . $PrefBD . "solicitudes.vigilanciaminuta 
                    WHERE IDMinuta=" . intval($_GET['IDMinuta']) . " 
                    LIMIT 1";
    $ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
    $RowMinuta = $ResultMinuta->fetch_assoc();

    // 2. Definición global de estados
    $finalizado = ($RowMinuta['FinalizaRegistro'] > 0);
    $estaFirmado = (!empty($RowMinuta['FFirmaEntrante']) || !empty($RowMinuta['FFirmaSaliente']));
    $bloqueado = ($finalizado || $estaFirmado); // Si cualquiera es true, se bloquea

    // 3. Consulta de Recesos
    $Queri = "SELECT MinutaRecesos.*, Receso.Receso, Minuta.FinalizaRegistro
              FROM " . $PrefBD . "solicitudes.vigilanciaminutarecesos MinutaRecesos 
              LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaminuta Minuta ON MinutaRecesos.IDMinuta=Minuta.IDMinuta 
              LEFT JOIN " . $PrefBD . "solicitudes.vigilanciareceso Receso ON MinutaRecesos.IDReceso=Receso.IDReceso
              WHERE MinutaRecesos.IDMinuta=" . intval($_GET['IDMinuta']) . " AND MinutaRecesos.Borrada=0 
              ORDER BY MinutaRecesos.IDMinutaReceso";
    $Result = $mysqli->query($Queri) or die(mysqli_error($mysqli)); 
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
                    <?php if (!$bloqueado): // Solo muestra cabecera de acciones si NO está bloqueado ?>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php
                $i = 0;
                while ($Row = $Result->fetch_assoc()) {
                    $i++; 
                    ?>
                    <tr id="DivReceso<?php echo $i; ?>" class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-700"><?php echo $Row['HoraInicioReceso']; ?></span>
                                <?php if (!$bloqueado): // Oculta botón editar individual ?>
                                    <button type="button" onClick="EditarReceso(<?php echo $Row['IDMinutaReceso']; ?>);" class="text-blue-400 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.5 2.5 0 113.536 3.536L12 14.232l-4 1 1-4 9.414-9.414z" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-600 font-medium">
                            <?php echo $Row['Receso']; ?>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-[10px] font-bold text-orange-500 border border-orange-200 uppercase">
                                    <?php echo substr($Row['VigilanteAsume'], 0, 1); ?>
                                </div>
                                <?php echo $Row['VigilanteAsume']; ?>
                            </div>
                        </td>

                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">
                            <span class="text-green-700 p-1 bg-green-50 border border-green-100 rounded-lg">
                                <?php echo $Row['HoraFinReceso']; ?>
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600"><?php echo $Row['ObsReceso']; ?></span>
                                <span class="text-[10px] text-gray-400 mt-1 italic font-mono">
                                    <i class="far fa-clock"></i> <?php echo DarFechaHora($Row['FCrea']); ?>
                                </span>
                            </div>
                        </td>

                        <?php if (!$bloqueado): // Oculta columna de borrar individual ?>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onClick="BorrarReceso(<?php echo $Row['IDMinutaReceso']; ?>);" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
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
        <?php if ($bloqueado): ?>
            // Seleccionamos el contenedor del botón de agregar (generalmente fuera de este fragmento Ajax)
            const divBoton = document.getElementById("DivBotonAgregarReceso");
            if(divBoton) divBoton.style.display = "none";
            
            // Si tienes un botón específico de edición general
            const botEditar = document.getElementById("BotEditarReceso");
            if(botEditar) botEditar.style.display = "none";
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
    // 1. Consultamos el estado de finalización y las firmas
    $QueriMinuta = "SELECT FinalizaRegistro, FFirmaEntrante, FFirmaSaliente 
                    FROM " . $PrefBD . "solicitudes.vigilanciaminuta 
                    WHERE IDMinuta=" . intval($_GET['IDMinuta']) . " 
                    LIMIT 1";
    $ResultMinuta = $mysqli->query($QueriMinuta) or die(mysqli_error($mysqli));
    $RowMinuta = $ResultMinuta->fetch_assoc();

    // 2. Definimos la variable de bloqueo global
    $finalizado = ($RowMinuta['FinalizaRegistro'] > 0);
    $estaFirmado = (!empty($RowMinuta['FFirmaEntrante']) || !empty($RowMinuta['FFirmaSaliente']));
    $bloqueado = ($finalizado || $estaFirmado);

    // 3. Consulta de Novedades
    $Queri = "SELECT MinutaNovedades.* 
              FROM " . $PrefBD . "solicitudes.vigilanciaminutanovedades MinutaNovedades 
              WHERE MinutaNovedades.IDMinuta=" . intval($_GET['IDMinuta']) . " AND MinutaNovedades.Borrada=0 
              ORDER BY MinutaNovedades.IDMinutaNovedad";
    $Result = $mysqli->query($Queri) or die(mysqli_error($mysqli)); 
    
    ?>

    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white mb-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">Núm</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">Hora</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Descripción Novedad</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Comunicador</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cargo</th>
                    <?php if (!$bloqueado): ?>
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
                        
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php if (!$bloqueado): ?>
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

                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-700">
                            <?php echo $Row['HoraNovedad']; ?>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-gray-700 leading-relaxed"><?php echo $Row['DescripcionNovedad']; ?></span>
                                <span class="text-[10px] text-gray-400 font-mono italic">
                                    <i class="far fa-calendar-alt mr-1"></i> <?php echo DarFechaHora($Row['FCrea']); ?>
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-50 flex items-center justify-center text-[10px] font-bold text-indigo-500 border border-indigo-100 uppercase">
                                    <?php echo substr($Row['ComunicadorNovedad'], 0, 1); ?>
                                </div>
                                <span class="text-sm text-gray-700 font-medium"><?php echo $Row['ComunicadorNovedad']; ?></span>
                            </div>
                        </td>

                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-tight">
                                <?php echo $Row['CargoComunicador']; ?>
                            </span>
                        </td>

                        <?php if (!$bloqueado): ?>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onClick="BorrarNovedad(<?php echo $Row['IDMinutaNovedad']; ?>);" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
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
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400 italic">
                            No se han registrado novedades.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts de Control de Botones -->
    <script type="text/javascript">
        <?php if ($bloqueado): ?>
            // Si está firmado o finalizado, ocultamos los botones de agregar/editar
            $("#BotEditarNovedad").hide();
            $("#DivBotonAgregarNovedad").hide(); 
        <?php else: ?>
            // Si está abierto, los mostramos
            $("#BotEditarNovedad").show();
            $("#DivBotonAgregarNovedad").show();
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

	// verificar si ambas firmas están completas para marcar la minuta como finalizada
	$QueriCheck = "SELECT VM.FFirmaEntrante, VM.FFirmaSaliente, PuestoSucursal.ObsPuesto AS ObsPuesto, VM.FinalizaRegistro, PuestoS.Puesto
		FROM ".$PrefBD."solicitudes.vigilanciaminuta AS VM
		JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal AS PuestoSucursal ON VM.Sucursal=PuestoSucursal.Sucursal
		JOIN ".$PrefBD."solicitudes.vigilanciapuesto AS PuestoS ON PuestoSucursal.IDPuesto=PuestoS.IDPuesto
		WHERE IDMinuta=".intval($_POST['IDMinuta'])."
		LIMIT 1";
			$ResultCheck = $mysqli->query($QueriCheck) or die(mysqli_error($mysqli));
			if($RowCheck = $ResultCheck->fetch_assoc()){
				if($RowCheck['FFirmaEntrante'] && $RowCheck['FFirmaSaliente']){
        	NotificacionSolicitud($mysqli,$_POST['IDMinuta'],'Debe Autorizar', $RowCheck['ObsPuesto'], $RowCheck['Puesto']);
				}
		}

	mysqli_close($mysqli);
	exit;
/****************************************************************************************************************************************
Generar Reportes de Minutas
****************************************************************************************************************************************/
}elseif($_POST['TipoGrabar']==md5('JorA7GenerarReporte'.date('d')) and $_POST['TipoModificar']==md5('ConsultaReporteJorA7'.date('d'))){
		set_time_limit(0);
    ini_set('memory_limit', '-1');

    if ($_POST['TipoReporte'] == 'TodosLosRegistros') {

        // Filtro de seguridad según permisos
        if ($PuedeAdministrar or $PuedeCrear or $PuedeConsultar) {
            $Filtrico = " WHERE VM.IDMinuta ";
        }

        // // Filtro de Fechas Optimizado (Sin LEFT para usar índices)
        if ($_POST['FechaInicial'] or $_POST['FechaFinal']) {
            $f1 = $_POST['FechaInicial'] ?: $_POST['FechaFinal'];
            $f2 = $_POST['FechaInicial'] ?: $_POST['FechaFinal'];
            $Filtrico .= " AND VM.Fecha BETWEEN '" . DarFechaSQL($f1) . "' AND '" . DarFechaSQL($f2) . "' ";
        }

        // // Filtro de Estado
				if ($_POST['estadoReporte']) {
						$Filtrico .= " AND (
								CASE 
										WHEN VM.FinalizaRegistro IS NOT NULL AND VM.FinalizaRegistro <> '' AND VM.FFirmaEntrante IS NOT NULL AND VM.FFirmaSaliente IS NOT NULL 
												THEN 'Finalizada'
										WHEN VM.FFirmaEntrante IS NOT NULL AND VM.FFirmaSaliente IS NOT NULL AND (VM.FinalizaRegistro IS NULL OR VM.FinalizaRegistro = '') 
												THEN 'En Proceso'
										WHEN (VM.FFirmaEntrante IS NULL OR VM.FFirmaSaliente IS NULL) AND (VM.FinalizaRegistro IS NULL OR VM.FinalizaRegistro = '') 
												THEN 'Recibida'
										ELSE 'Recibida'
								END
						) = '" . $_POST['estadoReporte'] . "' ";
				}

        // // Filtro de Sucursal
        if ($_POST['SucursalFiltro']) {
            $Filtrico .= " AND FIND_IN_SET(VM.Sucursal,'" . $_POST['SucursalFiltro'] . "')";
        }

				$Queri = "
					SELECT 
						VM.IDMinuta, 
						VM.Turno, 
						PuestoSuc.Sucursal, 
						PuestoSuc.ObsPuesto, 
						Puesto.Puesto, 
						CONCAT(EElabora.Nom, ' ', EElabora.Apellido1, ' ', EElabora.Apellido2) AS NomElabora,
						VM.Fecha, 
						VM.HoraInicio,
						CONCAT(EVE.Nom, ' ', EVE.Apellido1, ' ', EVE.Apellido2) AS NomEVigilanteEn, 
						VM.FFirmaEntrante,  
						VM.ObsFirmaEntrante,        
						CONCAT(EVS.Nom, ' ', EVS.Apellido1, ' ', EVS.Apellido2) AS NomEVigilanteSAL, 
						VM.FFirmaSaliente, 
						VM.ObsFirmaSaliente,
						VM.RealizaRequisa, 
						VM.ObsRequisa, 
						VM.HoraFinalizaRecorrido, 
						VM.ObsMinuta, 
						VM.FinalizaRegistro,

						-- 1. Agrupación de RECESOS
						GROUP_CONCAT(DISTINCT 
								CONCAT(Recesos.Receso, ' (', MRecesos.HoraInicioReceso, ' - ', MRecesos.HoraFinReceso, ')') 
								SEPARATOR ' \n '
						) AS DetalleRecesos,

						-- 2. Agrupación de NOVEDADES
						GROUP_CONCAT(DISTINCT 
								CONCAT(MNovedad.HoraNovedad, ': ', MNovedad.DescripcionNovedad) 
								SEPARATOR ' \n '
						) AS Novedades,

						-- 3. Agrupación de ELEMENTOS VERIFICADOS
						GROUP_CONCAT(DISTINCT 
								CONCAT(Ele.Elemento, ': Cant. ', MEle.CantidadVerificada, ' de ', MEle.CantidadReal, 
											IF(MEle.Verificado = 1, ' [OK]', ' [X]'), 
											IF(MEle.ObsVerifica <> '', CONCAT(' Obs: ', MEle.ObsVerifica), ''))
								SEPARATOR ' \n '
						) AS InventarioVerificado

				FROM " . $PrefBD . "solicitudes.vigilanciaminuta AS VM        
				JOIN " . $PrefBD . "solicitudes.vigilanciapuestosucursal AS PuestoSuc ON PuestoSuc.IDPuestoSucursal = VM.IDPuestoSucursal             
				JOIN " . $PrefBD . "solicitudes.vigilanciapuesto AS Puesto ON Puesto.IDPuesto = PuestoSuc.IDPuesto

				-- Joins de Empleados (Recursos)
				LEFT JOIN  " . $PrefBD . "recursos.emplea EElabora ON VM.Elabora = EElabora.Nit_CCE        
				LEFT JOIN " . $PrefBD . "recursos.emplea EVE ON VM.VigilanteEntrante = EVE.Nit_CCE        
				LEFT JOIN " . $PrefBD . "recursos.emplea EVS ON VM.VigilanteSaliente = EVS.Nit_CCE        

				-- Joins de Recesos
				LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaminutarecesos MRecesos ON VM.IDMinuta = MRecesos.IDMinuta AND MRecesos.Borrada = 0
				LEFT JOIN " . $PrefBD . "solicitudes.vigilanciareceso Recesos ON MRecesos.IDReceso = Recesos.IDReceso

				-- Joins de Novedades
				LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaminutanovedades MNovedad ON VM.IDMinuta = MNovedad.IDMinuta AND MNovedad.Borrada = 0

				-- Joins de Elementos / Inventario
				LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaminutaelemento MEle ON VM.IDMinuta = MEle.IDMinuta AND MEle.Borrada = 0
				LEFT JOIN " . $PrefBD . "solicitudes.vigilanciaelemento Ele ON MEle.IDElemento = Ele.IDElemento

				". $Filtrico ." AND VM.Borrada = 0
				GROUP BY VM.IDMinuta
				ORDER BY VM.Fecha DESC, VM.IDMinuta DESC;";

				$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));

        // Mapeo para el Excel (Mantengo los nombres de campos que espera tu Front-end)
        // Función para convertir HH:MM:SS a decimal de Excel y fechas correctamente
        $Campos = "rows = prez.map(row => {
            const horaADecimal = (h) => {
                if(!h || h == '00:00:00') return '';
                let partes = h.split(':');
                return (parseInt(partes[0]) / 24) + (parseInt(partes[1]) / (24 * 60)) + (parseInt(partes[2] || 0) / (24 * 60 * 60));
            };
            
            const formatoFecha = (f) => {
                if(!f || f == '') return '';
                return f;
            };

            return {
                IDMinuta: row.IDMinuta,
                ObsPuesto: row.ObsPuesto,
                Puesto: row.Puesto,
                Turno: row.Turno,
                NombreElabora: row.NomElabora,
                NombreElabora: row.NomElabora,
                Fecha: formatoFecha(row.Fecha),
                HoraInicio: row.HoraInicio,
                VigilanteEntrante: row.NomEVigilanteEn,
								FechaDeFirmaEntrante: formatoFecha(row.FFirmaEntrante),
                Observacion: row.ObsFirmaEntrante,
								VilganteSaliente: row.NomEVigilanteSAL,
								FechaDeFirmaSaliente: formatoFecha(row.FFirmaSaliente),
								ObservacionSaliente: row.ObsFirmaSaliente,
								RealizaRequisa: row.RealizaRequisa == 1 ? 'Si' : 'No',
								ObservacionRequisa: row.ObsRequisa,
								HoraFinalizaRecorrido: row.HoraFinalizaRecorrido,
								ObservacionMinuta: row.ObsMinuta,
								FechaFinalizaRegistro: formatoFecha(row.FinalizaRegistro),
								DetalleRecesos: row.DetalleRecesos,
								Novedades: row.Novedades,
								InventarioVerificado: row.InventarioVerificado,
            };
        });";

        $Formato = "
            formatColumn(worksheet, 1, '0');                    // IDMinuta
						formatColumn(worksheet, 6, 'mm/dd/yyyy');     // Fecha
						formatColumn(worksheet, 8, 'mm/dd/yyyy');     // FechaFirmaEntrante
						formatColumn(worksheet, 11, 'mm/dd/yyyy');    // FechaFirmaSaliente
						formatColumn(worksheet, 15, 'mm/dd/yyyy');    // FechaFinalizaRegistro
						formatColumn(worksheet, 9, '@');              // ObservacionFirmaEntrante
						formatColumn(worksheet, 12, '@');             // ObservacionFirmaSaliente
						formatColumn(worksheet, 14, '@');             // ObservacionMinuta
						formatColumn(worksheet, 3, '@');              // ObsPuesto
						formatColumn(worksheet, 4, '@');              // Puesto
						formatColumn(worksheet, 5, '@');              // Turno
						formatColumn(worksheet, 10, '@');             // Observacion
						formatColumn(worksheet, 13, '@');             // ObservacionRequisa
						formatColumn(worksheet, 16, '@');             // DetalleRecesos
						formatColumn(worksheet, 17, '@');             // Novedades
						formatColumn(worksheet, 18, '@');             // InventarioVerificado
        ";

        $mArrayCampos = array(
            'Codigo' => 'Campos',
            'Campos' => $Campos,
            'Formato' => $Formato
        );

        echo "[" . json_encode($mArrayCampos, JSON_UNESCAPED_UNICODE);

        while ($Row = $Result->fetch_assoc()) {
            $Array = array();
            foreach ($Row as $Clave => $Valor) {
                // Limpieza de datos y formato de fechas para el JSON
                $Array[$Clave] = trim(strip_tags($Valor));
            }
            echo "," . json_encode($Array, JSON_UNESCAPED_UNICODE);
        }
        echo "]";
    }

    mysqli_close($mysqli);
    exit;
}

	function NotificacionSolicitud($mysqli,$mIDSolicitud=0,$mTipo='', $sucursal, $puesto){

	// mensaje para el correo con diseño personalizado
	$destinario = "luisamp@colegiosminutodedios.edu.co";
	$Encabezado = "Notificación de Solicitud - ".$sucursal;
	$Servicio = "Departamento de Seguridad";
	$MensajeFinal = '<div style="background-color: #f4f7f9; padding: 20px; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif;">
				<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e1e8ed; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
						
						<!-- Encabezado con color corporativo -->
						<div style="background-color: #104379; padding: 20px; text-align: center;">
								<h2 style="color: #ffffff; margin: 0; font-size: 20px; letter-spacing: 1px;">
										Notificación de Solicitud
								</h2>
								<p style="color: #a5c3e4; margin: 5px 0 0 0; font-size: 14px;">Sucursal: '.$sucursal.'</p>
						</div>

						<!-- Cuerpo del correo -->
						<div style="padding: 30px; line-height: 1.6; color: #444;">
								<p style="margin-top: 0; font-size: 16px;">
										Hola, se ha generado una <strong>Nueva Solicitud</strong> en el sistema de seguridad <span style="font-weight: bold;">Modulo Minutas</span>. A continuación, los detalles principales:
								</p>

								<!-- Tabla de detalles para mejor lectura -->
								<div style="background-color: #f9fafb; border-radius: 6px; padding: 15px; margin: 20px 0; border-left: 4px solid #15b315;">
										<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
												<tr>
														<td style="padding: 5px 0; color: #777; width: 40%;"><strong>ID Solicitud:</strong></td>
														<td style="padding: 5px 0; color: #104379; font-weight: bold;">#'.$mIDSolicitud.'</td>
												</tr>
												<tr>
														<td style="padding: 5px 0; color: #777;"><strong>Puesto:</strong></td>
														<td style="padding: 5px 0; color: #333;">'.$puesto.'</td>
												</tr>
												<tr>
														<td style="padding: 5px 0; color: #777;"><strong>Tipo de Solicitud:</strong></td>
														<td style="padding: 5px 0; color: #333;">'.$mTipo.'</td>
												</tr>
												<tr>
														<td style="padding: 5px 0; color: #777;"><strong>Fecha y Hora:</strong></td>
														<td style="padding: 5px 0; color: #333;">'.date('d/m/Y H:i:s').'</td>
												</tr>
										</table>
								</div>

								<p style="font-size: 14px;">Por favor, ingrese al sistema para gestionar o revisar los detalles adicionales de este registro.</p>
								
								<!-- Botón de acción opcional -->
								<div style="text-align: center; margin-top: 25px;">
										<a href="https://intranet.cemid.org/Vigilancia/index.php?TipoModificar=66f60b50d5e1daffacbdf391e52a1963" style="background-color: #104379; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; display: inline-block;">
												Abrir Sistema de Seguridad
										</a>
								</div>
						</div>

						<!-- Pie de página -->
						<div style="background-color: #f9fafb; padding: 15px; text-align: center; border-top: 1px solid #eeeeee;">
								<p style="margin: 0; font-size: 12px; color: #999;">
										Este es un mensaje automático, por favor no responda a este correo.<br>
										<strong>Departamento de Seguridad</strong>
								</p>
						</div>
					</div>
			</div>';

    EnviarCorreo($mysqli, $destinario, $Encabezado, $MensajeFinal, $Servicio, $CC, $CCo, $mFile);
	}

?>
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


<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script src="../funciones.js"></script>
<style>
.colorBase{
	background-color: #104379;
}
</style>

<script type="text/javascript">
$(function(){//Para Fechas
	$("#VigilanteEntrante,#VigilanteSaliente,#VigilanteAsume1").autocomplete({
		source: "index.php?TipoModificar=<?php echo md5('Ajax1JorA6ListadoVigilantes'.date('d'));?>",
		minLength: 3,
		autoFocus: true,
		change: function (event, ui){
										if(ui.item == null || ui.item == undefined){
											$(this).val("");
									  	}
									}
	});
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
    $("#FechaInicial, #FechaFinal").datepicker({ dateFormat: 'dd-mm-yy' });
});

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

function DescargarReportes(){
	MostrarDivModales('Reportes');
}
function GenerarReporte(){
	$("#BotonesModalReportes").hide();
	mRetorno=true;
	ele=document.getElementById('FrmReporetes').TipoReporte;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	if(mRetorno){
		Frm=document.getElementById('FrmReporetes');
		Frm.TipoGrabar.value='<?php echo md5('JorA7GenerarReporte'.date('d'));?>';
		Frm.TipoModificar.value='<?php echo md5('ConsultaReporteJorA7'.date('d'));?>';
		let myData = $("#FrmReporetes").serialize();
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			dataType: 'json',
			data: myData
		}).done(function(response){
			if(response.length !== 0){
				
      /* 2. Separar Configuración de los Datos */
      const mCampos = response[0]; // Primer elemento: Metadatos
      response.shift();            // Eliminar configuración, quedan solo registros
                
      // Definimos 'prez' para que el eval() tenga donde trabajar
      const prez = response; 

      /* 3. Ejecutar el mapeo dinámico (Genera la variable 'rows') */
      // Esto ejecuta: rows = prez.map(row => ({ ... }));
      eval(mCampos['Campos']); 

      /* 4. Crear el libro de Excel */
      const worksheet = XLSX.utils.json_to_sheet(rows);

      /* 5. Aplicar formatos de columna (Fechas) */
      // Esto ejecuta los formatColumn definidos en PHP
      eval(mCampos['Formato']); 

      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Minutas");

      /* 6. Descargar archivo */
      XLSX.writeFile(workbook, "Reporte_Minutas.xlsx", {
        compression: true
      });

      Swal.close();

				Swal.fire({
					toast: true,
					position: "top-end",
					icon: "success",
					title: response.message || 'La descarga comenzará en breve.',
					showConfirmButton: false,
					timer: 3000
				});
				CerrarDivModales('Reportes');
				$("#BotonesModalReportes").show();

			}else{
				Swal.fire({
					toast: true,
					position: "top-end",
					icon: "error",
					title: `Error al generar el reporte: ${response.message}`,
					showConfirmButton: false,
					timer: 4000
				})
			}
		}).fail(function(jqXHR, textStatus, errorThrown){
			Swal.fire({
				toast: true,
				position: "top-end",
				icon: "error",
				title: `Error en la solicitud: ${textStatus}`,
				showConfirmButton: false,
				timer: 4000
			})
		});
	}else{
		Swal.fire({
			toast: true,
			position: "top-end",
			icon: "error",
			title: 'Hay inconsistencia en los datos, favor revisar.',
			showConfirmButton: false,
			timer: 3000
		});
		$("#BotonesModalReportes").show();
	}
}
function formatColumn(worksheet, col, fmt) {
    const range = XLSX.utils.decode_range(worksheet['!ref'])
    // note: range.s.r + 1 skips the header row
    for (let row = range.s.r + 1; row <= range.e.r; ++row) {
        const ref = XLSX.utils.encode_cell({
            c: col,
            r: row
        })
        if (worksheet[ref] && worksheet[ref].t === 'n') {
            worksheet[ref].z = fmt
        }
    }
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

	<main class="overflow-hidden">

		<div class="w-[95%] m-auto bg-green-500 text-white p-4 rounded-lg" id="DatoObser" style="z-index:1000000; display:<?php echo 'none';?>">
			<b>Success!</b> Indicates a successful or positive action.
		</div>

		<div class="myAlert-bottom alert alert-danger" id="DatoObserRed" style="z-index:1000000; display:<?php echo 'none';?>">
			<b>Danger!</b> This alert box could indicate a dangerous or potentially negative action.
		</div>

		<div class="relative h-screen  md:grid md:grid-cols-1 md:grid-cols-10 overflow-hidden">

			<nav class="overflow-auto w-[100vw] md:w-full md:h-full colorBase bottom-0 z-50 flex md:flex-col absolute md:static  md:col-span-2 navbar navbar-default navbar-fixed-top  text-white md:h-screen p-1 md:p-4 md:pt-10 transition-all duration-700">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="hidden md:block mb-2 pb-2 border-b border-white/20">
					<div class="flex flex-col items-center gap-y-4">
						<!-- Logo Container -->
						<div class="w-[80px] h-[80px] rounded-2xl bg-gradient-to-br from-white/20 to-white/10 backdrop-blur-sm flex items-center justify-center shadow-lg border border-white/30 hover:from-white/30 hover:to-white/20 transition-all duration-300">
							<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#ffffff" viewBox="0 0 24 24">
								<path d="m20.42 6.11-7.97-4c-.28-.14-.62-.14-.9 0l-7.97 4c-.31.15-.51.45-.55.79-.01.11-.96 10.76 8.55 15.01a.98.98 0 0 0 .82 0C21.91 17.66 20.97 7 20.95 6.9a.98.98 0 0 0-.55-.79ZM12 19.9C5.26 16.63 4.94 9.64 5 7.64l7-3.51 7 3.51c.04 1.99-.33 9.02-7 12.26"></path>
								<path d="m11 12.59-1.29-1.3-1.42 1.42 2.71 2.7 4.71-4.7-1.42-1.42z"></path>
							</svg>
						</div>

						<!-- Title Section -->
						<div class="text-center space-y-2">
							<h1 class="text-xl font-semibold text-white tracking-tight leading-tight">
								Departamento de <span class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">Seguridad</span>
							</h1>
							<!-- <div class="h-1 w-12 bg-gradient-to-r from-blue-300 to-white rounded-full mx-auto"></div> -->
							<!-- <p class="text-xs text-blue-100 font-medium uppercase tracking-widest">Vigilancia y Control</p> -->
						</div>
					</div>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="md:mt-2 flex md:flex-col p-2 gap-3 md:justify-between md:h-[calc(100vh-160px)]">
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
													</button></a></li>
													<li>
													<button onclick="DescargarReportes();" class="bg-white/5 md:bg-transparent w-full hover:bg-gray-50/10 p-1 md:px-4 md:py-3 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all flex items-center gap-x-2">
														<span>
															<svg  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 24 24" >
																<path d="m19.94 7.68-.03-.09a.8.8 0 0 0-.2-.29l-5-5a1 1 0 0 0-.3-.2l-.09-.03a.9.9 0 0 0-.27-.05c-.02 0-.04-.01-.05-.01H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-12s-.01-.04-.01-.06c0-.09-.02-.17-.05-.26ZM6 20V4h7v4c0 .55.45 1 1 1h4v11z"></path><path d="M8 12h2v6H8zm3-2h2v8h-2zm3 4h2v4h-2z"></path>
															</svg>
														</span>
														<span class="text-center hidden md:block">Reportes</span>
													</button></li><?php
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

	 <!-- MODAL REPORTES -->
    <div id="modal-Reportes" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
      <div id="modal-Reportes-backdrop" class="modal-backdrop absolute inset-0 bg-black/30" onclick="CerrarDivModales('Reportes')"></div>
        <div id="modal-Reportes-container" class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden">
					<div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">

                  <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                    <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  fill="#ffffff" viewBox="0 0 24 24" >
											<path d="m19.94 7.68-.03-.09a.8.8 0 0 0-.2-.29l-5-5a1 1 0 0 0-.3-.2l-.09-.03a.9.9 0 0 0-.27-.05c-.02 0-.04-.01-.05-.01H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-12s-.01-.04-.01-.06c0-.09-.02-.17-.05-.26ZM6 20V4h7v4c0 .55.45 1 1 1h4v11z"></path><path d="M8 12h2v6H8zm3-2h2v8h-2zm3 4h2v4h-2z"></path>
                    </svg>
              		</div>
             		<div>
                  <h3 id="ModalReportesTitle" class="text-xl font-bold">Descargar Reporte</h3>
                  <p class="text-blue-100 text-sm">¿Desea continuar con la descarga del reporte?</p>
              </div>
            </div>
            <button onclick="CerrarDivModales('Reportes')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
      	</div>
 
      	<div  class="p-6 space-y-4 overflow-y-auto">
          <form id="FrmReporetes" class="grid grid-cols-1 gap-4">
							<input name="TipoGrabar" type="hidden" id="TipoGrabar">
							<input name="TipoModificar" type="hidden" id="TipoModificar">

              <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
									

								<div class="">
									<div class="flex items-center gap-2 mb-3">
										<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
												<path d="M19 2H5c-.55 0-1 .45-1 1v4H2v2h2v2H2v2h2v2H2v2h2v4c0 .55.45 1 1 1h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2M6 4h8v16H6zm13 16h-3v-2.5h3zm0-4.5h-3V13h3zm0-4.5h-3V8.5h3zm0-4.5h-3V4h3z"></path>
											</svg>
										</div>
										<label for="TipoReporte" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Tipo De Reporte</label>
									</div>
									<select name="TipoReporte" id="TipoReporte"
										class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all">
										<option value='TodosLosRegistros' selected>Todos Los Acumulados</option>
									</select>
								</div>

								<div class="">
										<div class="flex items-center gap-2 mb-3">
											<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
													<path d="M21 4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v6H5c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h16c.55 0 1-.45 1-1zM5 12h6v8H5zm14 8h-6v-8c0-1.1-.9-2-2-2H9V4h10z"/><path d="M11 6h2v2h-2zm4 0h2v2h-2zm0 4.03h2V12h-2zM15 14h2v2h-2zm-8 0h2v2H7z"/>
												</svg>
											</div>
											<label for="SucursalFiltro" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Sucursal</label>
										</div>
										<select name="SucursalFiltro" id="SucursalFiltro"
											class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all"
											onChange="CambioSucursal(this.value);">
											<option value='' selected>— Seleccionar sucursal —</option>
												<?php
												$Queri = "SELECT DISTINCT Sucursal.Sucursal, Sucursal.NomSucursal
													FROM ".$PrefBD."novasoft.sucursal Sucursal
													JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal ON Sucursal.Sucursal=PuestoSucursal.Sucursal
													WHERE Sucursal.Sucursal<>'0'
													ORDER BY Sucursal.Sucursal";
													$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
													while($Row = $Result->fetch_assoc()){?>
												<option value='<?php echo $Row['Sucursal'];?>'><?php echo $Row['Sucursal'].' '.$Row['NomSucursal'];?></option>
											<?php }?>
										</select>
									</div>

								<div class="">
									<div class="flex items-center gap-2 mb-3">
										<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
												<path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8c.13 0 .26-.03.38-.08s.23-.12.33-.22l7-7c.09-.09.15-.19.2-.29l.03-.09c.03-.08.05-.17.05-.26 0-.02.01-.04.01-.06V6c0-1.1-.9-2-2-2m0 9h-6c-.55 0-1 .45-1 1v6H5V8h14z"/>
											</svg>
										</div>
										<label for="FechaInicial" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Fecha Inicial</label>
									</div>
									<input name="FechaInicial" type="text" id="FechaInicial"
										class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all"
									onBlur="ValidarFecha(this);" autocomplete="off" placeholder="Fecha Inicial" >
								</div>

								<div class="">
									<div class="flex items-center gap-2 mb-3">
										<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
												<path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8c.13 0 .26-.03.38-.08s.23-.12.33-.22l7-7c.09-.09.15-.19.2-.29l.03-.09c.03-.08.05-.17.05-.26 0-.02.01-.04.01-.06V6c0-1.1-.9-2-2-2m0 9h-6c-.55 0-1 .45-1 1v6H5V8h14z"/>
											</svg>
										</div>
										<label for="FechaFinal" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Fecha Final</label>
									</div>
									<input name="FechaFinal" type="text" id="FechaFinal"
										class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all"
									onBlur="ValidarFecha(this);" autocomplete="off" placeholder="Fecha Final" >
								</div>

								<!-- Puesto -->
								<div class="">
									<div class="flex items-center gap-2 mb-3">
										<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
												<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2M5 19V5h14v14z"></path><path d="m16.6 11.39-2.77-1.23-1.23-2.77a.68.68 0 0 0-.6-.4c-.27-.02-.5.15-.61.39l-1.23 2.67-2.78 1.34c-.23.11-.38.35-.38.61s.16.49.4.6l2.77 1.23 1.23 2.77a.663.663 0 0 0 1.22 0l1.23-2.77 2.77-1.23c.24-.11.4-.35.4-.61s-.16-.5-.4-.61Z"></path>
											</svg>
										</div>
										<label for="EstadoReporte" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Estado</label>
									</div>
									<select name="EstadoReporte" id="EstadoReporte"
										class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all"
										>
										<option value="">Estado</option>
										<option value="recibida">Recibida</option>
										<option value="en_proceso">En Proceso</option>
										<option value="finalizada">Finalizada</option>
									</select>
								</div>
								
              </div>

              <div class="flex gap-3 pt-4" id="BotonesModalReportes">
									<button type="button" onclick="CerrarDivModales('Reportes')"
										class=" flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
										Cerrar
									</button>
									<button  type="button" onclick="GenerarReporte();" 
										class=" flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Continuar</span>
									</button>
              </div>
          </form>
				</div>
      </div>
   </div>

	</main>
</body>
</html><?php
mysqli_close($mysqli);
?>