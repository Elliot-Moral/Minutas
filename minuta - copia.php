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
<!--Para la firma-->
<link rel="stylesheet" href="../librerias/firma/css/signature-pad-jorge.css">
<script type="text/javascript">
$(document).ready(function(){
	$("select,input,textarea").change(function(){
		document.getElementById('HuboCambio').value = 1;
	});
});
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
    $( "#FiltroFecha1,#FiltroFecha2").datepicker({ dateFormat: 'dd-mm-yy' });
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
function EditarMinuta(mIDMinuta,Editando=true){
	$("#DivMostrarMinuta").html("");
	$('#Frm0,#Frm1,#Frm2,#Frm3,#Frm4').trigger("reset");
	$('#Frm0,#Frm1,#Frm2,#Frm3,#Frm4').find("input[id='IDMinuta']").each(function(){
		$(this).val(mIDMinuta);//Todos los campos ocultos quedan con el valor de la Minuta consultada
	});
	$('#DivRecesos').load("index.php?TipoModificar=<?php echo md5('Ajax5JorA6Recesos'.date('d'));?>&IDMinuta="+mIDMinuta);
	$('#DivNovedades').load("index.php?TipoModificar=<?php echo md5('Ajax6JorA6Novedades'.date('d'));?>&IDMinuta="+mIDMinuta);
	$("#Frm0,#Frm1,#Frm2,#Frm3,#Frm4").find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	if(mIDMinuta>0){
		$('#Sucursal,#IDPuestoSucursal').attr('disabled', true);
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
					document.getElementById('Turno').value = data.Turno;
					document.getElementById('Fecha').value = data.Fecha;
					document.getElementById('HoraInicio').value = data.HoraInicio;
					document.getElementById('VigilanteSaliente').value = data.NomVigilanteSaliente;
					document.getElementById('VigilanteEntrante').value = data.NomVigilanteEntrante;
					document.getElementById('RealizaRequisa').value = data.RealizaRequisa;
					document.getElementById('ObsRequisa').value = data.ObsRequisa;
					document.getElementById('HoraFinalizaEntrega').value = data.HoraFinalizaEntrega;
					document.getElementById('ObsMinuta').value = data.ObsMinuta;
					document.getElementById('Sucursal').value = data.Sucursal;
					CambioSucursal(data.Sucursal);
					document.getElementById('IDPuestoSucursal').value = data.IDPuestoSucursal;
					CambioPuestoSucursalElemento();
					$('.modal').css('overflow-y', 'auto');//Esto es para que no se pierda el scroll luego de invocar el modal de confirmación.
					if(Editando){
						//Mostrar Pantallas para edición
						if(currentTab){//Esto es para evitar que se sobrepongan las pantallas cuando el usuario utiliza el botón cerrar sin haber terminado
							//Para que se active el cambio cada vez que haya un cambio en cualquier campo
							$("select,input,textarea").change(function(){
								document.getElementById('HuboCambio').value = 1;
							});
							$(".tab").each(function(){
								$(this).hide();
							});
							currentTab=0
						}
						showTab(0);
						$("#ModalMinuta").modal({backdrop:'static',keyboard: false});
					}else{
						html=$("#BodyMinuta").html();//Por alguna razón, al clonar el div, se pierde el html de BodyMinuta
						$('#Frm0,#Frm1,#Frm2,#Frm3,#Frm4').clone().end().appendTo('#DivMostrarMinuta');
						$('#DivMostrarMinuta').find("form,input,select,div,.tab").each(function(){
							$(this).attr({
								'id': '',
								'name': ''
							});
						});
						$('#DivMostrarMinuta').find(".tab").show();
						$('#DivMostrarMinuta').find("select,:checkbox").attr("disabled", true);
						$('#DivMostrarMinuta').find("input:text,textarea").attr("readonly", true);
						$('#DivMostrarMinuta').find(":button").hide();
						$("#ModalMostrarMinuta").modal('show');
						$("#BodyMinuta").html(html);
					}
				}
			},
			sync:false,
			error: function(data){
				MostrarDatoObser("Se presentó un error");
				return false;
			}
		});
	}else{
		$('#Sucursal,#IDPuestoSucursal').attr('disabled', false);
		document.getElementById('Fecha').value = '<?php echo date('d-m-Y');?>';
		document.getElementById('HoraInicio').value = '<?php echo date('H:i');?>';
		//Para que se active el cambio cada vez que haya un cambio en cualquier campo
		$("select,input,textarea").change(function(){
			document.getElementById('HuboCambio').value = 1;
		});
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
		document.getElementById("nextBtn").innerHTML = "Grabar y Siguiente";
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
		ele=document.getElementById('Turno');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('Fecha');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('HoraInicio');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('VigilanteSaliente');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('VigilanteEntrante');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
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
				mIDMinuta=parseInt(html,10);
				if(mIDMinuta>0){
					document.getElementById('HuboCambio').value=0;
					MostrarDatoObser('Datos Grabados',true);
					$('#Frm0,#Frm1,#Frm2,#Frm3,#Frm4').find("input[id='IDMinuta']").each(function(){
						$(this).val(mIDMinuta);//Todos los campos ocultos quedan con el valor de la Minuta consultada
					});
				}else if(html){
					MostrarDatoObser(html);
				}
			});
		}
	}else if(currentTab==1){	//1  LISTA DE CHEQUEO - Verificación de sede de acuerdo a puesto asumido
		$('#TBodyListaChequeo').find("input[id^='CantidadReal']").each(function(){
			mID=$(this).attr('id');
			mID=mID.replace("CantidadReal", "");
			ele=document.getElementById('CantidadVerificada' + mID);if(ele.value.length>0){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			mCantidadReal=parseInt(document.getElementById('CantidadReal' + mID).value,10);
			if(isNaN(mCantidadReal)){
				mCantidadReal=0;
			}
			CantidadVerificada=parseInt(document.getElementById('CantidadVerificada' + mID).value,10);
			if(isNaN(CantidadVerificada)){
				CantidadVerificada=0;
			}
			if(mCantidadReal!=CantidadVerificada && document.getElementById('CantidadVerificada' + mID).value.length>0){
				ele=document.getElementById('ObsVerifica' + mID);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			}
		});
		//Los datos no se graban acá, para este form se graban campo a campo, por cambio
	}else if(currentTab==2){	//2	REQUISA A VIGILANTE SALIENTE  - REGISTRO DE ROTACION DE ASIGNADO A PUESTO
		ele=document.getElementById('RealizaRequisa');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		ele=document.getElementById('HoraFinalizaEntrega');if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		for($i=1;$i<=document.getElementById('NumRecesos').value;$i++){
			ele=document.getElementById('HoraInicioReceso' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('IDReceso' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('VigilanteAsume' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('HoraFinReceso' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('ObsReceso' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm2');
			Frm.TipoGrabar.value='<?php echo md5('JorA6Tipo2'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Tipo2JorA6'.date('d'));?>';
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
	}else if(currentTab==3){	//3  NOVEDADES DURANTE EL TURNO
		for($i=1;$i<=document.getElementById('NumNovedades').value;$i++){
			ele=document.getElementById('HoraNovedad' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('DescripcionNovedad' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('ComunicadorNovedad' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
			ele=document.getElementById('CargoComunicador' + $i);if(ele.value){ele.classList.remove("alert-danger");}else{ele.classList.add("alert-danger");mRetorno=false;}
		}
		if(mRetorno && document.getElementById('HuboCambio').value==1){//Grabo los datos
			Frm=document.getElementById('Frm3');
			Frm.TipoGrabar.value='<?php echo md5('JorA6Tipo3'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Tipo3JorA6'.date('d'));?>';
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
	}else if(currentTab==4){	//4  Finalización Registro de Minuta por Puesto y Turno
		ele=document.getElementById('CheckAceptoTerminar');
		if(ele.checked){
			$("#LblAceptoTerminar").removeClass("alert-danger");
		}else{
			$("#LblAceptoTerminar").addClass("alert-danger");
			mRetorno=false;
		}
		if(mRetorno){//Grabo los datos
			Frm=document.getElementById('Frm4');
			Frm.TipoGrabar.value='<?php echo md5('JorA6Tipo4'.date('d'));?>';
			Frm.TipoModificar.value='<?php echo md5('Tipo4JorA6'.date('d'));?>';
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
		$('#TBodyListaChequeo').load("index.php?TipoModificar=<?php echo md5('Ajax2JorA6ElementosEditarMinuta'.date('d'));?>&Sucursal="+
												mSucursal+"&IDPuestoSucursal="+
												mIDPuestoSucursal+"&IDMinuta="+mIDMinuta
												);
	}else{
		document.getElementById('TBodyPuestoSucursalElemento').innerHTML="<tr><td>Nada</td></tr>";
	}
}
function EnviarMinutaElemento(Obj){
	mCampo="";
	mIDMinuta=document.getElementById('Frm1').IDMinuta.value;
	if(mIDMinuta>0){
		if(Obj.id.substr(0,18)=='CantidadVerificada'){
			mCampo='CantidadVerificada';
		}else if(Obj.id.substr(0,10)=='Verificado'){
			mCampo='Verificado';
		}else if(Obj.id.substr(0,11)=='ObsVerifica'){
			mCampo='ObsVerifica';
		}
	}
	if(mCampo){
		mIDElemento=parseInt(Obj.id.replace(mCampo, ""),10);
		mCantidadReal=document.getElementById('CantidadReal'+mIDElemento).value;
		var myData = {};
		if(mIDMinuta && mIDElemento){
			myData.TipoGrabar = '<?php echo md5('Tipo1JorA6'.date('d'));?>';
			myData.TipoModificar = '<?php echo md5('JorA6Tipo1'.date('d'));?>';
			myData.IDMinuta=mIDMinuta;
			myData.IDElemento=mIDElemento;
			myData.CantidadReal=mCantidadReal;
			myData.Campo=mCampo;
			myData.Valor=Obj.value;
			$.ajax({
				url:'index.php',
				type:'post',
				cache: false,
				data:myData
			}).done(function(html){
				if(html){
					MostrarDatoObser(html);
				}else{
					MostrarDatoObser("Dato grabado.",true);
				}
			});
		}
	}
}
function AgregarReceso(){
	document.getElementById('NumRecesos').value++;
	ActualizarRecesos(document.getElementById('NumRecesos').value);
}
function ActualizarRecesos(mNumRecesos){
	for(mI=1; mI<=mNumRecesos; mI++){
		if(document.getElementById('DivReceso'+mI)){
			$('#DivReceso'+mI).show();
		}else{
			mRegNew=mI;
			$('#DivReceso1').clone().find("input,select,a,td").each(function(){
				$(this).attr({
					'id': function(_, id) { return (id ? id.replace(1, mRegNew) : null)},
					'name': function(_, name) { return (name ? name.replace(1, mRegNew) : null)},
					'checked': false
				});
				$(this).removeClass("alert-danger");
				$(this).val(($(this).is(':checkbox') ? 1 : ''));
			}).end().appendTo('#DivRecesos');
			$("#DivRecesos div[id^='DivReceso']:last").attr('id','DivReceso'+mRegNew);
			
			$("#VigilanteAsume"+mRegNew).autocomplete({
				source: "index.php?TipoModificar=<?php echo md5('Ajax1JorA6ListadoVigilantes'.date('d'));?>",
				minLength: 3,
				autoFocus: true,
				change: function (event, ui){
												if(ui.item == null || ui.item == undefined){
													$(this).val("");
												}
											}
			});
		}
	}
	$("#DivRecesos").find("div[id^='DivReceso']").each(function(){
		mID = $(this).attr('id');
		mID = parseFloat(mID.replace("DivReceso", ""),10);
		if(mID>mNumRecesos){
			$(this).hide();
		}
	});
}
function AgregarNovedad(){
	document.getElementById('NumNovedades').value++;
	ActualizarNovedades(document.getElementById('NumNovedades').value);
}
function ActualizarNovedades(mNumNovedades){
	for(mI=1; mI<=mNumNovedades; mI++){
		if(document.getElementById('DivNovedad'+mI)){
			$('#DivNovedad'+mI).show();
		}else{
			mRegNew=mI;
			$('#DivNovedad1').clone().find("input,select,a,td").each(function(){
				$(this).attr({
					'id': function(_, id) { return (id ? id.replace(1, mRegNew) : null)},
					'name': function(_, name) { return (name ? name.replace(1, mRegNew) : null)},
					'checked': false
				});
				$(this).removeClass("alert-danger");
				$(this).val(($(this).is(':checkbox') ? 1 : ($(this).attr('id')=='NumNovedad'+mRegNew ? mRegNew : '')));	//Para que conserve el consecutivo
			}).end().appendTo('#DivNovedades');
			$("#DivNovedades div[id^='DivNovedad']:last").attr('id','DivNovedad'+mRegNew);
			
			$("#VigilanteAsume"+mRegNew).autocomplete({
				source: "index.php?TipoModificar=<?php echo md5('Ajax1JorA6ListadoVigilantes'.date('d'));?>",
				minLength: 3,
				autoFocus: true,
				change: function (event, ui){
												if(ui.item == null || ui.item == undefined){
													$(this).val("");
												}
											}
			});
		}
	}
	$("#DivNovedades").find("div[id^='DivNovedad']").each(function(){
		mID = $(this).attr('id');
		mID = parseFloat(mID.replace("DivNovedad", ""),10);
		if(mID>mNumNovedades){
			$(this).hide();
		}
	});
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
      <TH width="18%">Finalizada</TH>
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
      <TD><a onClick="EditarMinuta(<?php echo $Row['IDMinuta'];?>,false)" style="cursor:pointer" title="Mostrar Minuta"><?php echo DarFecha($Row['Fecha']);?></a></TD>
      <TD align="center"><?php echo $Row['NomSucursal'];?></TD>
      <TD><?php echo $Row['Puesto'];?></TD>
      <TD><?php echo $Row['NomVigilanteEntrante'];?></TD>
      <TD><?php echo $Row['NomVigilanteSaliente'];?></TD>
      <TD><?php
		if($Row['FinalizaRegistro']>0){
			 echo DarFechaHora($Row['FinalizaRegistro'],3);?>
			 <a onClick="CapturarFirma(<?php echo $Row['IDMinuta'];?>,false)" style="cursor:pointer" title="Firmar Minuta"><span class="glyphicon glyphicon-calendar"></span></a><?php
		}else{
			if($PuedeAdministrar){?>
				<a onClick="EditarMinuta(<?php echo $Row['IDMinuta'];?>);" style="cursor:pointer" title='Editar Minuta'>Sin Finalizar</a><?php
			}else{
				echo 'Sin Finalizar';
			}
		}?></TD>
    </TR>
	<?php
	}//fin del for ?>
    </table>
	</div>
</div>
<div class="<?php echo "modal fade";?>" id="ModalMinuta" role="dialog">
    <input name="HuboCambio" type="hidden" id="HuboCambio">
    <div class="modal-dialog modal-dialog-scrollable" style="width:98%;" role="document">
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
                            <option value= '' selected>--Turno--</option><?php
							foreach($mTurno as $Var){?>
							<option value= '<?php echo $Var;?>'><?php echo $Var;?></option><?php
							}?>
            			</select>
					</div>
					<div class="form-group col-sm-4">
                        <label for="Fecha">Fecha</label>
						<input name="Fecha" type="text" class="form-control" id="Fecha" onBlur="ValidarFecha(this);" autocomplete="off" placeholder="Fecha" readonly>
					</div>
					<div class="form-group col-sm-4">
                        <label for="HoraInicio">Hora Inicio Turno</label>
						<input name="HoraInicio" type="text" class="form-control" id="HoraInicio" onBlur="ValidarHora(this);" autocomplete="off" placeholder="Hora Inicio Turno" readonly>
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
			<!--1 LISTA DE CHEQUEO - Verificación de sede de acuerdo a puesto asumido -->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm1' id='Frm1'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>1. LISTA DE CHEQUEO - Verificación de sede de acuerdo a puesto asumido</H4>
    				<div class="table-responsive">
    				<table class="table table-striped table-bordered">
    					<thead>
                        <tr>
                        <th width="2%">Ítem</th>
                        <th width="30%">Descripción espacios y/o elementos expuestos (Visuales)</th>
                        <th width="5%">Cantidad Existente</th>
                        <th width="5%">Cantidad Verificada</th>
                        <th width="58%">Observación en la verificación de puesto</th>
                        </tr>
                 		</thead>
                        <tbody id="TBodyListaChequeo">
                        </tbody>
                	</table>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
			<!--2	REQUISA A VIGILANTE SALIENTE  - REGISTRO DE ROTACION DE ASIGNADO A PUESTO -->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm2' id='Frm2'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>REQUISA A VIGILANTE SALIENTE</H4>
					<div class="form-group col-sm-2">
                        <label for="RealizaRequisa">Requisa realizada?</label>
                        <select name="RealizaRequisa" id="RealizaRequisa" class="form-control">
                            <option value= ''></option>
                            <option value= 1>Si</option>
                            <option value= -1>No</option>
                        </select>
					</div>
					<div class="form-group col-sm-8">
                        <label for="ObsRequisa">Observaciones de la requisa:</label>
                        <input name="ObsRequisa" type="text" class="form-control" id="ObsRequisa" placeholder="Observaciones de la requisa">
					</div>
					<div class="form-group col-sm-2">
                        <label for="HoraFinalizaEntrega">Hora Finaliza Entrega</label>
						<input name="HoraFinalizaEntrega" type="text" class="form-control" id="HoraFinalizaEntrega" onBlur="ValidarHora(this);" autocomplete="off" placeholder="Hora Finaliza Entrega">
					</div>
					<div class="clearfix"></div>
                    <div class="input-group col-sm-6">
                    	<h4>3. REGISTRO DE ROTACION (RECESO) DE ASIGNADO A PUESTO</h4>
                    	<input name="NumRecesos" type="hidden" id="NumRecesos" value=0>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" onClick="AgregarReceso();" title="Agregar registro de Receso/Rotación">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
					<div class="clearfix"></div>
              <div class="col-sm-12" id="DivRecesos">
              <!-- vía ajax se actualizan los recesos previos-->
              </div>
                </form>
				<div class="clearfix"></div>
            </div>
			<!--3  NOVEDADES DURANTE EL TURNO -->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm3' id='Frm3'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <div class="input-group col-sm-12">
                        <H4>NOVEDADES DURANTE EL TURNO - Registro de novedades comunicadas verbalmente o fuera del alcance de los registros</H4>
                        <input name="NumNovedades" type="hidden" id="NumNovedades" value=0>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" onClick="AgregarNovedad();" title="Agregar registro de Novedad">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
					<div class="clearfix"></div>
              <div class="col-sm-12" id="DivNovedades">
              <!-- vía ajax se actualizan las Novedades previas-->
              </div>
                </form>
				<div class="clearfix"></div>
            </div>
			<!--4  Finalización Registro de Minuta por Puesto y Turno-->
            <div class="tab row">
                <form method=post enctype="multipart/form-data" name='Frm4' id='Frm4'>
                    <input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    <input name="TipoModificar" type="hidden" id="TipoModificar">
                    <input name="IDMinuta" type="hidden" id="IDMinuta">
                    <H4>Finalización Registro de Minuta por Puesto y Turno</H4>
					<div class="form-group col-sm-12">
                        <label for="ObsMinuta">Observaciones o Novedades Adicionales:</label>
                        <textarea name="ObsMinuta" id="ObsMinuta" rows="2" class="form-control" maxlength="250" placeholder="Observaciones o Novedades Adicionales"></textarea>
					</div>
                    <div class="form-group col-md-12" align="justify" id="DivAceptoTerminar">
                        <label id="LblAceptoTerminar" for="CheckAceptoTerminar">
                        Al dar clic, se dará por terminada la Minuta.
                        </label>
                        <div>
                        <input name="CheckAceptoTerminar" type="checkbox" id="CheckAceptoTerminar"><label for="CheckAceptoTerminar">Acepto</label>
                        </div>
                    </div>
            	</form>
            </div>
		</div>
        <div class="modal-footer">
            <div style="overflow:auto;">
                <div style="float:right;">
                    <button type="button" id="prevBtn" onClick="nextPrev(-1)">Anterior</button>
                    <button type="button" id="nextBtn" onClick="nextPrev(1)">Grabar y Siguiente</button>
                </div>
            </div>
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
<div class="modal fade" id="ModalMostrarMinuta" tabindex="-1" role="dialog" aria-labelledby="ModalMostrarMinutaTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document" style="width:98%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalMostrarMinutaTitle">Minuta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="DivMostrarMinuta">
            Contenido
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ModalFirma" tabindex="-1" role="dialog" aria-labelledby="ModalFirmaTitle" aria-hidden="true" onselectstart="return false">
    <div class="modal-dialog modal-dialog-scrollable" role="document" id="DivContieneFirma">
        <div id="signature-pad" class="signature-pad">
            <div class="signature-pad--body" id="CanvasFirma">
            <canvas></canvas>
            </div>
            <div class="signature-pad--footer">
                <div class="description">Firma</div>
                <div class="signature-pad--actions">
                    <div>
                    <button type="button" class="btn btn-default" data-action="clear" id="BotLimpiarFirma">Limpiar</button>
                    <button type="button" class="btn btn-default" data-action="undo">Deshacer</button>
                    </div>
                    <div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-default">Grabar</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="../librerias/firma/js/signature_pad.umd-jorge.js"></script>
        <script src="../librerias/firma/js/app-jorge.js"></script>
        <script>
		function CapturarFirma(){
			jQuery('#ModalFirma').unbind('shown.bs.modal');
			$('#ModalFirma').on('shown.bs.modal', function (e){
				resizeCanvas();//Esto se hace así y dentro de esta función, para habilitar la edición en el canvas
			});
			$("#ModalFirma").modal('show');
		}
		</script>
	</div>
</div>
