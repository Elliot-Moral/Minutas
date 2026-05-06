<style>
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
    left:2%;
    width: 96%;
}
.glyphicon {
    font-size: 18px;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("select,input,textarea").change(function(){
		document.getElementById('HuboCambio').value = 1;
	});
});
$(function(){//Para Fechas
	$("#VigilanteEntrante,#VigilanteSaliente").autocomplete({
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
    $( "#FiltroFecha1,#FiltroFecha2,#Fecha").datepicker({ dateFormat: 'dd-mm-yy' });
});
function documentHeight(){//Obtener el alto de la página
    return Math.max(
        document.documentElement.clientHeight,
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight
    );
}
function CrearMinuta(){
	//Verifico si el usuario tiene una Minuta previa que no haya finalizado
	$.ajax({
		type: "get",
		url: 'index.php',
		data: 'TipoModificar=<?php echo md5('Ajax3JorA6VerificarMinutaPrevia'.date('d'));?>',
		cache: false,
		dataType: 'json',
		success: function(data){ //Si se ejecuta correctamente
			if(data.Mensaje=="Error"){
				MostrarDatoObser("Se presentó un error");
			}else{
				if(data.IDMinuta>0){
					document.getElementById('ModalConfirmacionTitle').innerHTML = "<b>CONFIRMAR GENERAR MINUTA PENDIENTE</b>";
					document.getElementById('ModalConfirmacionBody').innerHTML = "Tiene pendiente una minuta por cerrar. <br>Sucursal <b>" + data.NomSucursal +
						"</b><br>Puesto <b>" + data.Puesto + 
						"</b><br>Fecha <b> " + data.Fecha +
						"</b><br>Vigilante Saliente <b> " + data.NomVigilanteSaliente +
						"</b><br>Vigilante Entrante <b> " + data.NomVigilanteEntrante +
						"</b>.<br><br>Desea retomar?<br><br>";
					jQuery('#ModalConfirmacion').unbind('hide.bs.modal');
					$('#ModalConfirmacion').on('hide.bs.modal', function (e){
						if(!(document.getElementById('BotAceptarModal').value>0)){
							MostrarDatoObser("No se realizó ninguna acción.");
						}
					});
					jQuery('#BotAceptarModal').unbind('click');
					$("#BotAceptarModal").click(function(){
						document.getElementById('BotAceptarModal').value=1;//Para que no muestre alerta de cambios
						$("#ModalConfirmacion").modal('hide');
						EditarMinuta(data.IDMinuta);
					});
					$("#ModalConfirmacion").modal('show');
				}else{
					EditarMinuta(0);
				}
			}
		},
		error: function(data){
			MostrarDatoObser("Se presentó un error");
			return false;
		}
	});
}
function EditarMinuta(mIDMinuta){
	$("#Frm0,#Frm1,#Frm2,#Frm3").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('Frm0').reset();
//	document.getElementById('Frm1').reset();
	document.getElementById('Frm2').reset();
	document.getElementById('Frm3').reset();
	$(document).find("div[id='IDMinuta']").each(function(){
		$(this).val(mIDMinuta);//Todos los campos ocultos quedan con el valor de la Minuta consultada
	});
	if(mIDMinuta>0){
		//Actualizo los forms con los datos grabados
		$.ajax({
			type: "get",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax4JorA6RetornarMinuta'.date('d'));?>&IDMinuta='+mIDMinuta,
			cache: false,
			dataType: 'json',
			success: function(data){ //Si se ejecuta correctamente
				if(data.Mensaje=="Error"){
					MostrarDatoObser("Se presentó un error");
					return false;
				}else{
					document.getElementById('Sucursal').value = data.Sucursal;
					CambioSucursal(data.Sucursal);
					document.getElementById('IDPuestoSucursal').value = data.IDPuestoSucursal;
					CambioPuestoSucursalElemento();
					document.getElementById('Turno').value = data.Turno;
					document.getElementById('Fecha').value = data.Fecha;
					document.getElementById('HoraInicio').value = data.HoraInicio;
					document.getElementById('VigilanteSaliente').value = data.NomVigilanteSaliente;
					document.getElementById('VigilanteEntrante').value = data.NomVigilanteEntrante;
					//Mostrar Pantallas para edición
					if(currentTab){//Esto es para evitar que se sobrepongan las pantallas cuando el usuario utiliza el botón cerrar sin haber terminado
						$(".tab").each(function(){
							$(this).hide();
						});
						currentTab=0
					}
					showTab(0);
					$("#ModalMinuta").modal({backdrop:'static',keyboard: false});
				}
			},
			error: function(data){
				MostrarDatoObser("Se presentó un error");
				return false;
			}
		});
	}else{
		if(currentTab){//Esto es para evitar que se sobrepongan las pantallas cuando el usuario utiliza el botón cerrar sin haber terminado
			$(".tab").each(function(){
				$(this).hide();
			});
			currentTab=0
		}
		showTab(0);
		$("#ModalMinuta").modal({backdrop:'static',keyboard: false});
	}
}
var currentTab = 0; // Current tab is set to be the first tab (0)
function showTab(n){
	// This function will display the specified tab of the form...
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
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
	// This function deals with validation of the form fields
	var x, y, i, mRetorno = true;
	if(currentTab==0){			//0  IDENTIFICACIÓN
		ele=document.getElementById('Sucursal');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('IDPuestoSucursal');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		/*
		ele=document.getElementById('Turno');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('Fecha');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('HoraInicio');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('VigilanteSaliente');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('VigilanteEntrante');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		*/
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm0');
			Frm.TipoGrabar.value='<?php echo md5('JorA6Tipo0'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Tipo0JorA6'.date('d'));?>';
			var myData = $("#Frm0").serialize();
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
	}else if(currentTab==1){	//1  LISTA DE CHEQUEO - Verificación de sede de acuerdo a puesto asumido
		/*
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm1');
			Frm.TipoGrabar.value='<?php echo md5('Tipo1'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor1'.date('d'));?>';
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
		*/
	}else if(currentTab==2){	//2	ACCIONES DE ATENCION INTEGRAL
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm2');
			Frm.TipoGrabar.value='<?php echo md5('Tipo2'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor2'.date('d'));?>';
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
	}else if(currentTab==3){	//3	EGRESADOS JARDIN
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm3');
			Frm.TipoGrabar.value='<?php echo md5('Tipo3'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor3'.date('d'));?>';
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
	}else if(currentTab==4){	//4	PARTICIPACIÓN DE LA FAMILIA
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm4');
			Frm.TipoGrabar.value='<?php echo md5('Tipo4'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor4'.date('d'));?>';
			var myData = $("#Frm4").serialize();
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
	}else if(currentTab==5){	//5	GESTIÓN INTERINSTITUCIONAL
		ele=document.getElementById('NumConvenios');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		for($i=1;$i<=document.getElementById('NumConvenios').value;$i++){
			ele=document.getElementById('Universidad' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Facultad' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('NumEstudiantes' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm5');
			Frm.TipoGrabar.value='<?php echo md5('Tipo5'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor5'.date('d'));?>';
			var myData = $("#Frm5").serialize();
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
	}else if(currentTab==6){	//6	REALIZACIÓN DE EVENTOS PROPIOS
		ele=document.getElementById('NumEventos');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		for($i=1;$i<=document.getElementById('NumEventos').value;$i++){
			ele=document.getElementById('Nombre' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Objetivo' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('NivelCumplimiento' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Nivel' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('NumInstituciones' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('NumParticipantes' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('TipoParticipantes' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Panelistas' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm6');
			Frm.TipoGrabar.value='<?php echo md5('Tipo6'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor6'.date('d'));?>';
			var myData = $("#Frm6").serialize();
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
	}else if(currentTab==7){	//7	ESPACIOS DE PARTICIPACIÓN
		ele=document.getElementById('NumEspaciosParticipacion');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		for($i=1;$i<=document.getElementById('NumEspaciosParticipacion').value;$i++){
			ele=document.getElementById('NomEspacio' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('Frecuencia' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm7');
			Frm.TipoGrabar.value='<?php echo md5('Tipo7'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor7'.date('d'));?>';
			var myData = $("#Frm7").serialize();
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
	}else if(currentTab==8){	//8	Confirmación y terminar
		ele=document.getElementById('CheckAceptoTerminar');
		if(ele.checked){
			$("#LblAceptoTerminar").removeClass("alert-danger");
		}else{
			$("#LblAceptoTerminar").addClass("alert-danger");
			mRetorno=false;
		}
		if(mRetorno){//Grabo los datos
			Frm=document.getElementById('Frm8');
			Frm.TipoGrabar.value='<?php echo md5('Tipo8'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Jor8'.date('d'));?>';
			var myData = $("#Frm8").serialize();
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
function CambioSucursal(mSucursal){
	mObIDPuestoSucursal=document.getElementById("IDPuestoSucursal");
	if(!mObIDPuestoSucursal){
		return
	}
	mValOld=mObIDPuestoSucursal.value;
	mObIDPuestoSucursal.length=0;
	mObIDPuestoSucursal.add(new Option('--Puesto--', '', true));
	<?php
	$Queri = "SELECT PuestoSucursal.IDPuestoSucursal, Puesto.Puesto, Sucursal.Sucursal, PuestoSucursal.ObsPuesto
				FROM ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal
				JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				JOIN ".$PrefBD."novasoft.sucursal Sucursal ON PuestoSucursal.Sucursal=Sucursal.Sucursal
				ORDER BY Puesto.Puesto";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
	while($Row = $Result->fetch_assoc()){?>
	if(mSucursal=='<?php echo $Row['Sucursal'];?>'){
		mObIDPuestoSucursal.add(new Option('<?php echo $Row['Puesto'].' '.$Row['ObsPuesto'];?>', '<?php echo $Row['IDPuestoSucursal'];?>', (mValOld=='<?php echo $Row['IDPuestoSucursal'];?>' ? true : null)));
	}
	<?php
	}//Ojo con los espacios (enter)
	?>
}
function CambioPuestoSucursalElemento(){
	mSucursal=document.getElementById('Sucursal').value;
	mIDPuestoSucursal=document.getElementById('IDPuestoSucursal').value;
	mIDMinuta =document.getElementById('Frm0').IDMinuta.value;
	/* Aquí se actualiza el form con los elementos para este puesto*/
	if(mSucursal && mIDPuestoSucursal){
		$('#DivListaChequeo').load("index.php?TipoModificar=<?php echo md5('Ajax2JorA6ElementosEditarMinuta'.date('d'));?>&Sucursal="+
												mSucursal+"&IDPuestoSucursal="+
												mIDPuestoSucursal+"&IDMinuta="+mIDMinuta);
	}else{
		document.getElementById('TBodyPuestoSucursalElemento').innerHTML="<tr><td>Nada</td></tr>";
	}
}
</script>
<div class="row sidenav"><?php
	if(!(isset($_GET['FiltroSede']))){//Esto quiere decir que es el primer ingreso y aún no se ha dado click en Filtrar
		$_GET['FiltroFecha1']=date('d-m-Y');
	}
	$Filtrico="";
	//Defino el Filtro por Sucursal (Sede)
	if($_GET['FiltroSede']){
		$Filtrico .= $Filtrico." AND FIND_IN_SET(Minuta.Sucursal,'".$_GET['FiltroSede']."')";
	}
	//Defino el Filtro por Puesto
	if($_GET['FiltroPuesto']){
		$Filtrico = $Filtrico." AND Puesto.Puesto LIKE '%".$_GET['FiltroPuesto']."%'";
	}
	//Defino el Filtro por Elabora, OJO NO TODOS DEBEN PODER VER TODO
	if(!$PuedeAdministrar){
		if($_GET['FiltroElabora']){
			$Filtrico .= $Filtrico." AND CONCAT(Elabora.Nom,' ',Elabora.Apellido1,' ',Elabora.Apellido2) LIKE '%".$_GET['FiltroElabora']."%'";
		}
	}else{
		$Filtrico .= $Filtrico." AND Minuta.Elabora='".$_SESSION['Usuario']."'";
	}
	//Por si acaso, optimizo las variables para el filtro por fecha
	if($_POST['FiltroFecha1'] and $_POST['FiltroFecha2']){
		//Nothing bhere
	}elseif($_POST['FiltroFecha1']){
		$_POST['FiltroFecha2']=$_POST['FiltroFecha1'];
	}elseif($_POST['FiltroFecha2']){
		$_POST['FiltroFecha1']=$_POST['FiltroFecha2'];
	}
	if($_POST['FiltroFecha1'] and $_POST['FiltroFecha2']){
		$Filtrico .= $Filtrico." AND LEFT(Minuta.Fecha,10) BETWEEN '".DarFechaSQL($_POST['FiltroFecha1'])."' AND '".DarFechaSQL($_POST['FiltroFecha2'])."'";
	}
	//Ahora si ejecutó la consulta
	$Queri = "SELECT Minuta.*, Puesto.Puesto, Sucursal.NomSucursal,
					CONCAT(Elabora.Nom,' ',Elabora.Apellido1) AS NomElabora,
					CONCAT(VEntrante.Nom,' ',VEntrante.Apellido1) AS NomVigilanteEntrante,
					CONCAT(VSaliente.Nom,' ',VSaliente.Apellido1) AS NomVigilanteSaliente
				FROM ".$PrefBD."solicitudes.vigilanciaminuta Minuta
				LEFT JOIN ".$PrefBD."recursos.emplea Elabora ON Minuta.Elabora=Elabora.Nit_CCE
				LEFT JOIN ".$PrefBD."recursos.emplea VEntrante ON Minuta.VigilanteEntrante=VEntrante.Nit_CCE
				LEFT JOIN ".$PrefBD."recursos.emplea VSaliente ON Minuta.VigilanteSaliente=VSaliente.Nit_CCE
				LEFT JOIN ".$PrefBD."novasoft.sucursal Sucursal ON Minuta.Sucursal=Sucursal.Sucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal ON Minuta.IDPuestoSucursal=PuestoSucursal.IDPuestoSucursal
				LEFT JOIN ".$PrefBD."solicitudes.vigilanciapuesto Puesto ON PuestoSucursal.IDPuesto=Puesto.IDPuesto
				WHERE Minuta.IDMinuta ".$Filtrico."
				ORDER BY Minuta.Fecha DESC";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));?>
	<div class="table-responsive">
	<table class="table table-striped table-bordered">
	<thead>
    <TR>
      <TH colspan="2">
      	<b style="font-size:20px">Administración Minutas</b>
        <a onClick="CrearMinuta();" style="cursor:pointer" title="Crear Minuta"><span class="glyphicon glyphicon-new-window"></span></a>
	  </TH>
      <TH colspan="4">
      <form action="index.php" method="get" name="FrmFiltroMinutas" id="FrmFiltroMinutas">
        <div class="col-sm-2">
        	<input type="hidden" name="TipoModificar" id="TipoModificar" value="<?php echo md5('JorA1'.date('d'));?>">
            <input name='FiltroSede' id="FiltroSede" class="form-control" value="<?php echo $_GET['FiltroSede'];?>" placeholder="Sede">
        </div>
        <div class="col-sm-2">
            <input name='FiltroPuesto' id="FiltroPuesto" class="form-control" value="<?php echo $_GET['FiltroPuesto'];?>" placeholder="Puesto">
        </div>
        <div class="col-sm-2">
            <input name='FiltroElabora' id="FiltroElabora" class="form-control" value="<?php echo $_GET['FiltroElabora'];?>" placeholder="Elabora">
        </div>
        <div class="col-sm-2">
            <input name='FiltroFecha1' id="FiltroFecha1" class="form-control" maxlength="10" value="<?php echo $_GET['FiltroFecha1'];?>" onBlur="ValidarFecha(this)" autocomplete="off" placeholder="Desde">
    	</div>
        <div class="col-sm-2">
            <input name='FiltroFecha2' id="FiltroFecha2" class="form-control" maxlength="10" value="<?php echo $_GET['FiltroFecha2'];?>" onBlur="ValidarFecha(this)" autocomplete="off" placeholder="Hasta">
    	</div>
        <div class="col-sm-1"><?php
	//Código Ingresado para Paginación
	$_GET['RegXPag']=100;//Número de registros por página
	if ($_GET['Pagina']=='Todas' or $Result->num_rows<=$_GET['RegXPag']){
		$Desde = 1;
		$Hasta = $Result->num_rows;
	}elseif(!$_GET['Pagina']){
		$_GET['Pagina'] = 1;
		$Desde = 1;
		$Hasta = $_GET['RegXPag'];
	}elseif(($_GET['Pagina']-1)*$_GET['RegXPag'] > $Result->num_rows){
		$_GET['Pagina'] = 1;
		$Desde = 1;
		$Hasta = $_GET['RegXPag'];
	}else{
		$Desde = (($_GET['Pagina']-1)*$_GET['RegXPag'])+1;
		if ($_GET['Pagina']*$_GET['RegXPag'] > $Result->num_rows){
			$Hasta = $Result->num_rows;
		}else{
			$Hasta = $_GET['Pagina']*$_GET['RegXPag'];
		}
	}
	//Paginación de a $RecorXPag si es necesario
	if ($Result->num_rows>$_GET['RegXPag']){?>
		<select name='Pagina' id='Pagina'><?php
		$i = ceil($Result->num_rows/$_GET['RegXPag']);
		for ($j = 1; $j <= $i; $j++){?>
			<option value='<?php echo $j;?>' <?php if ($j==$_GET['Pagina']){echo "selected";}?>>Pág. <?php echo $j;?></option><?php
		}?>
			<option value='Todas' <?php if ($_GET['Pagina']=='Todas'){echo "selected";}?>>Todas</option>
		</select><?php
	}?>
		</div>
        <div class="col-sm-1" align="right">
        <input type="submit" class="btn btn-primary" value=">>">
		</div>
    </form></Th>
    </TR>
	<TR>
      <TH width="10%">Fecha</TH>
      <TH width="18%">Sede</TH>
      <TH width="18%">Puesto</TH>
      <TH width="18%">Vigilante Entrante</TH>
      <TH width="18%">Vigilante Saliente</TH>
      <TH width="18%">Termina</TH>
    </TR>
    </thead>
	<?php
	$Switch=1;
	mysqli_data_seek($Result, $Desde-1);
	for ($j = $Desde; $j <= $Hasta; $j++){//Recorrido de la Consulta
		$Switch = $Switch * -1;
		$Row = $Result->fetch_assoc();//hay Registros de O/C
	?>
    <TR align=left>
      <TD><?php echo DarFechaHora($Row['Fecha'],3);?></TD>
      <TD align="center"><?php
		if($PuedeAdministrar){?>
			<a onClick="EditarMinuta(<?php echo $Row['IDMinuta'];?>);" style="cursor:pointer" title='Editar Minuta'><?php echo $Row['NomSucursal'];?></a><?php
		}else{
			echo $Row['NomSucursal'];
		}
		?></TD>
      <TD><?php echo $Row['Puesto'];?></TD>
      <TD><?php echo $Row['NomVigilanteEntrante'];?></TD>
      <TD><?php echo $Row['NomVigilanteSaliente'];?></TD>
      <TD><?php echo ($Row['FinalizaRegistro']>0 ? (DarFechaHora($Row['FinalizaRegistro'],3)) : 'Sin Finalizar');?></TD>
    </TR>
	<?php
	}//fin del for ?>
    </table>
	</div>
</div>
<div class="<?php echo "modal fade";?>" id="ModalMinuta" role="dialog">
    <input name="HuboCambio" type="hidden" id="HuboCambio">
    <div class="modal-dialog" style="width:95%;">
    	<!-- Modal content-->
		<div class="modal-content">
		<div class="modal-header">
           	<button type="button" class="close" data-dismiss="modal">&times;</button>
           	<h4 class="modal-title">MINUTA - PROCESO DE ENTREGA, EJECUCIÓN Y RECIBO DE PUESTO</h4>
        </div>
        <div class="modal-body" id="BodyMinuta">
			<!--0  IDENTIFICACIÓN -->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm0' id='Frm0'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>IDENTIFICACIÓN</H4>
					<div class="form-group col-sm-6">
                        <label for="Sucursal">Sucursal</label>
                        <select name="Sucursal" id="Sucursal" class="form-control" onChange="CambioSucursal(this.value);">
                            <option value= '' selected>--Sucursal--</option><?php
                        $Queri = "SELECT DISTINCT Sucursal.Sucursal, Sucursal.NomSucursal
                                    FROM ".$PrefBD."novasoft.sucursal Sucursal
									JOIN ".$PrefBD."solicitudes.vigilanciapuestosucursal PuestoSucursal ON Sucursal.Sucursal=PuestoSucursal.Sucursal
                                    WHERE Sucursal.Sucursal<>'0'
                                    ORDER BY Sucursal.Sucursal";
                        $Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
                        while($Row = $Result->fetch_assoc()){?>
                        <option value= '<?php echo $Row['Sucursal'];?>'><?php echo $Row['Sucursal'].' '.$Row['NomSucursal'];?></option><?php
                        }?>
                        </select>
					</div>
					<div class="form-group col-sm-6">
                        <label for="IDPuestoSucursal">Puesto</label>
                        <select name="IDPuestoSucursal" id="IDPuestoSucursal" class="form-control" onChange="CambioPuestoSucursalElemento();">
            			</select>
					</div>
					<div class="form-group col-sm-4">
                        <label for="Turno">Turno</label>
                        <select name="Turno" id="Turno" class="form-control">
                            <option value= '' selected>--Sucursal--</option><?php
							foreach($mTurno as $Var){?>
							<option value= '<?php echo $Var;?>'><?php echo $Var;?></option><?php
							}?>
            			</select>
					</div>
					<div class="form-group col-sm-4">
                        <label for="Fecha">Fecha</label>
						<input name="Fecha" type="text" class="form-control" id="Fecha" onBlur="ValidarFecha(this);" autocomplete="off" placeholder="Fecha">
					</div>
					<div class="form-group col-sm-4">
                        <label for="HoraInicio">Hora Inicio Turno</label>
						<input name="HoraInicio" type="text" class="form-control" id="HoraInicio" onBlur="ValidarHora(this);" autocomplete="off" placeholder="Hora Inicio Turno">
					</div>
					<div class="form-group col-sm-6">
                        <label for="VigilanteSaliente">Vigilante Saliente</label>
						<input name="VigilanteSaliente" type="text" class="form-control" id="VigilanteSaliente" autocomplete="off" placeholder="Vigilante Saliente">
					</div>
					<div class="form-group col-sm-6">
                        <label for="VigilanteEntrante">Vigilante Entrante</label>
						<input name="VigilanteEntrante" type="text" class="form-control" id="VigilanteEntrante" autocomplete="off" placeholder="Vigilante Entrante">
					</div>
                </form>
				<div class="clearfix"></div>
            </div>
			<div id="DivListaChequeo">
            </div>
			<!--2  ACCIONES DE ATENCION INTEGRAL -->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm2' id='Frm2'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>ACCIONES DE ATENCION INTEGRAL NIÑAS Y NIÑOS EN SEGUIMIENTO</H4>
    				<div class="table-responsive">
    				<table class="table table-striped table-bordered">
    					<thead>
                        <tr>
                        <th width="30%">HOGARES / CDI</th>
                        <th width="10%">NN Desplazados</th>
                        <th width="10%">NN Venezolanos</th>
                        <th width="10%">NN Discapacidad</th>
                        <th width="15%">NN Seguimiento por atención psicosocial</th>
                        <th width="15%">NN Seguimiento Nutricional</th>
                        <th width="10%">Total</th>
                        </tr>
                 		</thead>
                        <tbody><?php
				$AccionesAtencionIntegral = json_decode($Row['AccionesAtencionIntegral'],true);
				$TotSegDesplazados=0;
				$TotSegVenezolanos=0;
				$TotSegDiscapacitados=0;
				$TotSegPsicoSocial=0;
				$TotSegNutricional=0;
				foreach ($Hogar as $Clave => $Valor){
					$TotSegDesplazados+=$AccionesAtencionIntegral[$Clave]['SegDesplazados'];
					$TotSegVenezolanos+=$AccionesAtencionIntegral[$Clave]['SegVenezolanos'];
					$TotSegDiscapacitados+=$AccionesAtencionIntegral[$Clave]['SegDiscapacitados'];
					$TotSegPsicoSocial+=$AccionesAtencionIntegral[$Clave]['SegPsicoSocial'];
					$TotSegNutricional+=$AccionesAtencionIntegral[$Clave]['SegNutricional'];?>
                        <tr>
                        <td><?php echo $Valor;?></td>
                        <td><input name="SegDesplazados<?php echo $Clave;?>" type="text" class="form-control" id="SegDesplazados<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalAccionesAtencionIntegral();" value="<?php echo $AccionesAtencionIntegral[$Clave]['SegDesplazados'];?>"></td>
                        <td><input name="SegVenezolanos<?php echo $Clave;?>" type="text" class="form-control" id="SegVenezolanos<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalAccionesAtencionIntegral();" value="<?php echo $AccionesAtencionIntegral[$Clave]['SegVenezolanos'];?>"></td>
                        <td><input name="SegDiscapacitados<?php echo $Clave;?>" type="text" class="form-control" id="SegDiscapacitados<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalAccionesAtencionIntegral();" value="<?php echo $AccionesAtencionIntegral[$Clave]['SegDiscapacitados'];?>"></td>
                        <td><input name="SegPsicoSocial<?php echo $Clave;?>" type="text" class="form-control" id="SegPsicoSocial<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalAccionesAtencionIntegral();" value="<?php echo $AccionesAtencionIntegral[$Clave]['SegPsicoSocial'];?>"></td>
                        <td><input name="SegNutricional<?php echo $Clave;?>" type="text" class="form-control" id="SegNutricional<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalAccionesAtencionIntegral();" value="<?php echo $AccionesAtencionIntegral[$Clave]['SegNutricional'];?>"></td>
                        <td><input name="TotalSeg<?php echo $Clave;?>" type="text" class="form-control" id="TotalSeg<?php echo $Clave;?>" readonly value="<?php echo number_format($AccionesAtencionIntegral[$Clave]['SegDesplazados']+$AccionesAtencionIntegral[$Clave]['SegVenezolanos']+$AccionesAtencionIntegral[$Clave]['SegDiscapacitados']+$AccionesAtencionIntegral[$Clave]['SegPsicoSocial']+$AccionesAtencionIntegral[$Clave]['SegNutricional'],0,",",".");?>"></td>
                        </tr><?php
				}?>
                        <tr>
                        <th>Total</th>
                        <td><input name="TotSegDesplazados" type="text" class="form-control" id="TotSegDesplazados" readonly value="<?php echo number_format($TotSegDesplazados,0,",",".");?>"></td>
                        <td><input name="TotSegVenezolanos" type="text" class="form-control" id="TotSegVenezolanos" readonly value="<?php echo number_format($TotSegVenezolanos,0,",",".");?>"></td>
                        <td><input name="TotSegDiscapacitados" type="text" class="form-control" id="TotSegDiscapacitados" readonly value="<?php echo number_format($TotSegDiscapacitados,0,",",".");?>"></td>
                        <td><input name="TotSegPsicoSocial" type="text" class="form-control" id="TotSegPsicoSocial" readonly value="<?php echo number_format($TotSegPsicoSocial,0,",",".");?>"></td>
                        <td><input name="TotSegNutricional" type="text" class="form-control" id="TotSegNutricional" readonly value="<?php echo number_format($TotSegNutricional,0,",",".");?>"></td>
                        <td><input name="TotalSeg" type="text" class="form-control" id="TotalSeg" readonly value="<?php echo number_format($TotSegDesplazados+$TotSegVenezolanos+$TotSegDiscapacitados+$TotSegPsicoSocial+$TotSegNutricional,0,",",".");?>"></td>
                        </tr>
                        </tbody>
                	</table>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
			<!--3  EGRESADOS JARDIN -->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm3' id='Frm3'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>NIÑOS Y NIÑAS EGRESADOS DE NIVEL JARDÍN</H4>
    				<div class="table-responsive">
    				<table class="table table-striped table-bordered">
    					<thead>
                        <tr>
                        <th width="30%">HOGARES / CDI</th>
                        <th width="15%">Niños en Jardín</th>
                        <th width="15%">Colegio público</th>
                        <th width="15%">Colegios Minuto de Dios</th>
                        <th width="15%">Otros colegios privados</th>
                        <th width="10%">Total</th>
                        </tr>
                 		</thead>
                        <tbody><?php
				$EgresadosJardin = json_decode($Row['EgresadosJardin'],true);
				$TotEgreJardin=0;
				$TotEgrePublico=0;
				$TotEgreCEMID=0;
				$TotEgrePrivado=0;
				foreach ($Hogar as $Clave => $Valor){
					$TotEgreJardin+=$EgresadosJardin[$Clave]['EgreJardin'];
					$TotEgrePublico+=$EgresadosJardin[$Clave]['EgrePublico'];
					$TotEgreCEMID+=$EgresadosJardin[$Clave]['EgreCEMID'];
					$TotEgrePrivado+=$EgresadosJardin[$Clave]['EgrePrivado'];?>
                        <tr>
                        <td><?php echo $Valor;?></td>
                        <td><input name="EgreJardin<?php echo $Clave;?>" type="text" class="form-control" id="EgreJardin<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalEgresadosJardin();" value="<?php echo $EgresadosJardin[$Clave]['EgreJardin'];?>"></td>
                        <td><input name="EgrePublico<?php echo $Clave;?>" type="text" class="form-control" id="EgrePublico<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalEgresadosJardin();" value="<?php echo $EgresadosJardin[$Clave]['EgrePublico'];?>"></td>
                        <td><input name="EgreCEMID<?php echo $Clave;?>" type="text" class="form-control" id="EgreCEMID<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalEgresadosJardin();" value="<?php echo $EgresadosJardin[$Clave]['EgreCEMID'];?>"></td>
                        <td><input name="EgrePrivado<?php echo $Clave;?>" type="text" class="form-control" id="EgrePrivado<?php echo $Clave;?>" onBlur="ValidarNumeros(this);CalcularTotalEgresadosJardin();" value="<?php echo $EgresadosJardin[$Clave]['EgrePrivado'];?>"></td>
                        <td><input name="TotalEgre<?php echo $Clave;?>" type="text" class="form-control" id="TotalEgre<?php echo $Clave;?>" readonly value="<?php echo number_format($EgresadosJardin[$Clave]['EgreJardin']+$EgresadosJardin[$Clave]['EgrePublico']+$EgresadosJardin[$Clave]['EgreCEMID']+$EgresadosJardin[$Clave]['EgrePrivado'],0,",",".");?>"></td>
                        </tr><?php
				}?>
                        <tr>
                        <th>Total</th>
                        <td><input name="TotEgreJardin" type="text" class="form-control" id="TotEgreJardin" readonly value="<?php echo number_format($TotEgreJardin,0,",",".");?>"></td>
                        <td><input name="TotEgrePublico" type="text" class="form-control" id="TotEgrePublico" readonly value="<?php echo number_format($TotEgrePublico,0,",",".");?>"></td>
                        <td><input name="TotEgreCEMID" type="text" class="form-control" id="TotEgreCEMID" readonly value="<?php echo number_format($TotEgreCEMID,0,",",".");?>"></td>
                        <td><input name="TotEgrePrivado" type="text" class="form-control" id="TotEgrePrivado" readonly value="<?php echo number_format($TotEgrePrivado,0,",",".");?>"></td>
                        <td><input name="TotalEgre" type="text" class="form-control" id="TotalEgre" readonly value="<?php echo number_format($TotEgreJardin+$TotEgrePublico+$TotEgreCEMID+$TotEgrePrivado,0,",",".");?>"></td>
                        </tr>
                        </tbody>
                	</table>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
			<!--4  PARTICIPACIÓN DE LA FAMILIA-->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm4' id='Frm4'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>PARTICIPACIÓN DE LA FAMILIA</H4>
                    <div class="form-group col-sm-12">
                        <label>Se formaron
                        <input name="ComitesPadres" type="text" class="form-group" size="3" maxlength="3" onChange="ValidarNumeros(this)" id="ComitesPadres" value="<?php echo number_format($Row['ComitesPadres'],0,",",".");?>">
                        comités de padres líderes desarrollando actividades de apoyo a la gestión de los Hogares Infantiles Y CDI.
                        </label>
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Se realizaron
                        <input name="VisitasControl" type="text" class="form-group" size="3" maxlength="3" onChange="ValidarNumeros(this)" id="VisitasControl" value="<?php echo number_format($Row['VisitasControl'],0,",",".");?>">
                        visitas de control social y veeduría por parte de los padres de familia en todas las unidades de servicio.
                        </label>
                    </div>
                    <div class="clearfix"></div>
                </form><?php
				for($i=1; $i<=6; $i++){
					$mID=$_GET['IDMinuta'].'_Foto'.$i.'_HogaresComites';?>
				<div class="form-group col-sm-2">
					<img id="<?php echo $mID;?>" src="<?php echo "../file.php?Tipo=".md5("MinutaGestion".$mID.'.jpg').'&mFile='.$mID.".jpg";?>" width="100%" onDblClick="CambiarFoto(this.id)" title="Doble click para cambiar la foto.">
					<input type="file" id="File<?php echo $mID;?>" name="File<?php echo $mID;?>" onChange="AbrirImagen(this.id);" accept="image/*" style="display:none;">
				</div><?php
				}?>
            </div>
			<!--5  GESTIÓN INTERINSTITUCIONAL-->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm5' id='Frm5'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>GESTIÓN INTERINSTITUCIONAL</H4><?php
				$GestionInterInstitucional = json_decode($Row['GestionInterInstitucional'],true);?>
                    <div class="form-group col-sm-12">
                        <label>Número de convenios de práctica Vigentes
                        <input name="NumConvenios" type="text" class="form-group" size="2" maxlength="2" id="NumConvenios" onChange="ValidarNumeros(this);CambioNumConvenios();" value="<?php echo (count($GestionInterInstitucional) ? count($GestionInterInstitucional) : "");?>">
                        </label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-5">
                        <label>Universidad</label>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>Facultad</label>
                    </div>
                    <div class="form-group col-sm-2">
                        <label># de Estudiantes</label>
                    </div>
                    <div class="clearfix"></div><?php
				for($i=1;$i<=(count($GestionInterInstitucional) ? count($GestionInterInstitucional) : 1);$i++){?>
                <div class="row" id="DivConvenio<?php echo $i;?>" <?php if(!$GestionInterInstitucional[$i]) echo 'style="display:none"';?>>
                    <div class="form-group col-sm-5">
                        <input name="Universidad<?php echo $i;?>" type="text" class="form-control" id="Universidad<?php echo $i;?>" value="<?php echo $GestionInterInstitucional[$i]['Universidad'];?>" placeholder="Universidad">
                    </div>
                    <div class="form-group col-sm-5">
                        <input name="Facultad<?php echo $i;?>" type="text" class="form-control" id="Facultad<?php echo $i;?>" value="<?php echo $GestionInterInstitucional[$i]['Facultad'];?>" placeholder="Facultad">
                    </div>
                    <div class="form-group col-sm-2">
                        <input name="NumEstudiantes<?php echo $i;?>" type="text" class="form-control" id="NumEstudiantes<?php echo $i;?>" value="<?php echo $GestionInterInstitucional[$i]['NumEstudiantes'];?>" placeholder="# Estudiantes" maxlength="3" onChange="ValidarNumeros(this);">
                    </div>
                    <div class="clearfix"></div>
				</div><?php
				}?>
                </form>
            </div>
			<!--6  REALIZACIÓN DE EVENTOS PROPIOS-->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm6' id='Frm6'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4><?php
					$Eventos = json_decode($Row['Eventos'],true);//Ojo debe ir acá para que aparezca la Cantidad?>
                    REALIZACIÓN DE EVENTOS PROPIOS - Número de eventos
                    <input name="NumEventos" type="text" class="form-group" size="2" maxlength="2" id="NumEventos" onChange="ValidarNumeros(this);CambioNumEventos();" value="<?php echo (count($Eventos) ? count($Eventos) : "");?>">
                    </H4>
                    <div class="table-responsive">
    				<table class="table table-striped table-bordered">
    					<thead>
                        <tr>
                        <th width="18%">Nombre</th>
                        <th width="18%">Objetivo</th>
                        <th width="9%">Nivel Cumplimiento</th>
                        <th width="9%">Nivel</th>
                        <th width="18%"># Instituciones Asistentes</th>
                        <th width="5%"># Participantes</th>
                        <th width="5%">Tipo Participantes</th>
                        <th width="18%">Panelistas Asistentes destacados</th>
                        </tr>
                 		</thead>
                        <tbody id="TBodyEvento"><?php
				for($i=1;$i<=(count($Eventos) ? count($Eventos) : 1);$i++){?>
                        <tr id="TREvento<?php echo $i;?>" <?php if(!$Eventos[$i]) echo 'style="display:none"';?>>
                        <td><input name="Nombre<?php echo $i;?>" type="text" class="form-control" id="Nombre<?php echo $i;?>" value="<?php echo $Eventos[$i]['Nombre'];?>"></td>
                        <td><input name="Objetivo<?php echo $i;?>" type="text" class="form-control" id="Objetivo<?php echo $i;?>" value="<?php echo $Eventos[$i]['Objetivo'];?>"></td>
                        <td><select name="NivelCumplimiento<?php echo $i;?>" id="NivelCumplimiento<?php echo $i;?>" class="form-control">
                                <option value='' selected></option>
                                <option value='Alto'  <?php echo ($Eventos[$i]['NivelCumplimiento']=='Alto' ? 'selected' : '');?>>Alto</option>
                                <option value='Medio' <?php echo ($Eventos[$i]['NivelCumplimiento']=='Medio' ? 'selected' : '');?>>Medio</option>
                                <option value='Bajo'  <?php echo ($Eventos[$i]['NivelCumplimiento']=='Bajo' ? 'selected' : '');?>>Bajo</option>
                         	</select></td>
                        <td><select name="Nivel<?php echo $i;?>" id="Nivel<?php echo $i;?>" class="form-control">
                                <option value='' selected></option>
                                <option value='Internacional' <?php echo ($Eventos[$i]['Nivel']=='Internacional' ? 'selected' : '');?>>Internacional</option>
                                <option value='Nacional' <?php echo ($Eventos[$i]['Nivel']=='Nacional' ? 'selected' : '');?>>Nacional</option>
                                <option value='Regional' <?php echo ($Eventos[$i]['Nivel']=='Regional' ? 'selected' : '');?>>Regional</option>
                                <option value='Local' <?php echo ($Eventos[$i]['Nivel']=='Local' ? 'selected' : '');?>>Local</option>
                            </select></td>
                        <td><input name="NumInstituciones<?php echo $i;?>" type="text" class="form-control" id="NumInstituciones<?php echo $i;?>" onBlur="ValidarNumeros(this);" value="<?php echo $Eventos[$i]['NumInstituciones'];?>"></td>
                        <td><input name="NumParticipantes<?php echo $i;?>" type="text" class="form-control" id="NumParticipantes<?php echo $i;?>" onBlur="ValidarNumeros(this);" value="<?php echo $Eventos[$i]['NumParticipantes'];?>"></td>
                        <td><select name="TipoParticipantes<?php echo $i;?>" id="TipoParticipantes<?php echo $i;?>" class="form-control">
                                <option value='' selected></option>
                                <option value='Docente' <?php echo ($Eventos[$i]['TipoParticipantes']=='Docente' ? 'selected' : '');?>>Docente</option>
                                <option value='Directivo' <?php echo ($Eventos[$i]['TipoParticipantes']=='Directivo' ? 'selected' : '');?>>Directivo</option>
                                <option value='Estudiantes' <?php echo ($Eventos[$i]['TipoParticipantes']=='Estudiantes' ? 'selected' : '');?>>Estudiantes</option>
                                <option value='Psicosocial' <?php echo ($Eventos[$i]['TipoParticipantes']=='Psicosocial' ? 'selected' : '');?>>Psicosocial</option>
							</select></td>
                        <td><input name="Panelistas<?php echo $i;?>" type="text" class="form-control" id="Panelistas<?php echo $i;?>" value="<?php echo $Eventos[$i]['Panelistas'];?>"></td>
                        </tr><?php
				}?>
                        </tbody>
                	</table>
                    </div>
                </form>
                <div class="row" id="DivEventosFotos"><?php
				for($i=1;$i<=((count($Eventos)>6) ? count($Eventos) : 6);$i++){//El espacio para las fotos es mínimo 6
					$mID=$_GET['IDMinuta'].'_Foto'.$i.'_HogaresEventos';?>
				<div class="form-group col-sm-2" id="DivEventosFoto<?php echo $i;?>" <?php if(!$Eventos) echo 'style="display:none"';?>>
					<img id="<?php echo $mID;?>" src="<?php echo "../file.php?Tipo=".md5("MinutaGestion".$mID.'.jpg').'&mFile='.$mID.".jpg";?>" width="100%" onDblClick="CambiarFoto(this.id)" title="Doble click para cambiar la foto.">
					<input type="file" id="File<?php echo $mID;?>" name="File<?php echo $mID;?>" onChange="AbrirImagen(this.id);" accept="image/*" style="display:none;">
				</div><?php
				}?>
                </div>
            </div>
			<!--7  ESPACIOS DE PARTICIPACIÓN-->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm7' id='Frm7'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>ESPACIOS DE PARTICIPACIÓN</H4><?php
				$EspaciosParticipacion = json_decode($Row['EspaciosParticipacion'],true);?>
                    <div class="form-group col-sm-12">
                        <label>Número de espacios de participación:
                        <input name="NumEspaciosParticipacion" type="text" class="form-group" size="2" maxlength="2" id="NumEspaciosParticipacion" onChange="ValidarNumeros(this);CambioNumEspaciosParticipacion();" value="<?php echo (count($EspaciosParticipacion) ? count($EspaciosParticipacion) : "");?>">
                        </label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-9">
                        <label>Nombre Espacio</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Frecuencia</label>
                    </div>
                    <div class="clearfix"></div><?php
				for($i=1;$i<=(count($EspaciosParticipacion) ? count($EspaciosParticipacion) : 1);$i++){?>
                <div class="row" id="DivEspaciosParticipacion<?php echo $i;?>" <?php if(!$EspaciosParticipacion[$i]) echo 'style="display:none"';?>>
                    <div class="form-group col-sm-9">
                        <input name="NomEspacio<?php echo $i;?>" type="text" class="form-control" id="NomEspacio<?php echo $i;?>" value="<?php echo $EspaciosParticipacion[$i]['NomEspacio'];?>" placeholder="Nombre Espacio">
                    </div>
                    <div class="form-group col-sm-3">
                    	<select name="Frecuencia<?php echo $i;?>" id="Frecuencia<?php echo $i;?>" class="form-control">
                                <option value='' selected></option>
                                <option value='Mensual' <?php echo ($EspaciosParticipacion[$i]['Frecuencia']=='Mensual' ? 'selected' : '');?>>Mensual</option>
                                <option value='Semestral' <?php echo ($EspaciosParticipacion[$i]['Frecuencia']=='Semestral' ? 'selected' : '');?>>Semestral</option>
                                <option value='Anual' <?php echo ($EspaciosParticipacion[$i]['Frecuencia']=='Anual' ? 'selected' : '');?>>Anual</option>
							</select>
                    </div>
                    <div class="clearfix"></div>
				</div><?php
				}?>
                </form>
            </div>
			<!--8	Confirmación y terminar -->
			<div class="tab row">
				<form method=post enctype="multipart/form-data" name='Frm8' id='Frm8'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <div class="form-group col-md-12" align="justify">
                        <label id="LblAceptoTerminar">
						Al dar clic, se dará por terminada la Minuta.
						</label>
                        <div>
                        <input name="CheckAceptoTerminar" type="checkbox" id="CheckAceptoTerminar"><label for="CheckAceptoTerminar">Acepto</label>
                        </div>
                	</div>
				</form>
                <div class="clear"></div>
			</div>
		</div>
        <div class="modal-footer">
            <div style="overflow:auto;">
                <div style="float:right;">
                    <button type="button" id="prevBtn" onClick="nextPrev(-1)">Anterior</button>
                    <button type="button" id="nextBtn" onClick="nextPrev(1)">Siguiente</button>
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
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
            </div>
        </div>
		</div>
    </div>
</div>