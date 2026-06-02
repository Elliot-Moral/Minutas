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
.ui-datepicker {
    z-index: 150 !important;
}
</style>
<!--Para la firma-->
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
	$("#VigilanteAsume").autocomplete({
		source: "index.php?TipoModificar=<?php echo md5('Ajax2JorA7ListadoNomina'.date('d'));?>",
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
    $( "#FiltroFecha1,#FiltroFecha2, #FechaInicial, #FechaFinal").datepicker({ dateFormat: 'dd-mm-yy' });


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
				Swal.fire({
					title: 'Error',
					text: 'Se presentó un error al verificar si hay minutas previas.',
					icon: 'error',
					confirmButtonText: 'Aceptar',
					confirmButtonColor: '#0e69ca'
				});
			}else{
				if(data.IDMinuta>0){
					document.getElementById('ModalConfirmacionTitle').innerHTML = "<p>Confirmar generar minuta pendiente</p>";
					document.getElementById('ModalConfirmacionBody').innerHTML = `
						<div class="p-1">
								<!-- Encabezado de Aviso -->
								<div class="flex items-center gap-3 mb-4 bg-amber-50 p-3 rounded-xl border border-amber-200">
										<div class="bg-amber-500 p-2 rounded-full shadow-sm">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
												</svg>
										</div>
										<div>
												<h4 class="text-amber-800 font-bold text-sm uppercase tracking-tight">Registro Pendiente</h4>
												<p class="text-amber-700 text-xs">Existe una minuta sin cerrar en el sistema.</p>
										</div>
								</div>

								<!-- Cuerpo de Datos -->
								<div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
										<div class="p-4 space-y-3">
												<div class="flex flex-col">
														<span class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Ubicación</span>
														<span class="text-gray-800 font-semibold text-sm">${data.NomSucursal} — <span class="text-blue-600">${data.Puesto}</span></span>
												</div>

												<div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-200/60">
														<div class="flex flex-col">
																<span class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Fecha Registro</span>
																<span class="text-gray-700 font-medium text-sm">${data.Fecha}</span>
														</div>
												</div>

												<div class="pt-2 border-t border-gray-200/60">
														<div class="flex items-center gap-2 mb-2">
																<div class="w-2 h-2 bg-red-400 rounded-full"></div>
																<span class="text-[10px] uppercase font-black text-gray-400">Personal Involucrado</span>
														</div>
														
														<div class="space-y-1 pl-4 border-l-2 border-gray-200">
																<div class="flex flex-col">
																		<span class="text-[9px] text-gray-400 font-bold">SALIENTE</span>
																		<span class="text-gray-700 text-xs font-semibold uppercase">${data.NomVigilanteSaliente}</span>
																</div>
																<div class="flex flex-col pt-1">
																		<span class="text-[9px] text-gray-400 font-bold">ENTRANTE</span>
																		<span class="text-gray-700 text-xs font-semibold uppercase">${data.NomVigilanteEntrante}</span>
																</div>
														</div>
												</div>
										</div>
								</div>

								<!-- Pregunta de Acción -->
								<div class="mt-5 text-center">
										<p class="text-gray-600 font-bold text-base">¿Desea retomar el proceso ahora?</p>
										<p class="text-gray-400 text-[10px] italic">Al aceptar, se cargarán los datos automáticamente.</p>
								</div>
						</div>
				`;
				
				let aceptoContinuar = false;
				const botonesModal = document.querySelectorAll('.bottonConfirmacion');
				botonesModal.forEach((boton, index) => {
							if (index === 0) {
									// Botón Cancelar (Índice 0)
									boton.addEventListener('click', function() {
											aceptoContinuar = false; 
											Swal.fire({
													title: 'Proceso Cancelado',
													text: 'Se canceló la creación de la nueva minuta.',
													icon: 'info',
													confirmButtonText: 'Aceptar',
													confirmButtonColor: '#0e69ca'
											});
									});
							} else if (index === 1) {
									// Botón Continuar/Aceptar (Índice 1)
									boton.addEventListener('click', function() {
											CerrarDivModales('Confirmacion');
											EditarMinuta(data.IDMinuta);
									});
							}
					});

					MostrarDivModales('Confirmacion');
				}else{
					EditarMinuta(0, true);
					HbilitarMismoVigilante();
					document.getElementById('CheckSameVigilante').disabled=false;
				}
			}
		},
		error: function(data){
			MostrarDatoObser("Se presentó un error");
			return false;
		}
	});
}
function EditarMinuta(mIDMinuta, Editando = true) {
	['Frm0', 'Frm1', 'Frm2'].forEach(id => document.getElementById(id).reset());
	['Frm0', 'Frm1', 'Frm2'].forEach(id => {
		const input = document.getElementById(id).querySelector("input[id='IDMinuta']");
		if(input) input.value = mIDMinuta;
	});

	['Frm0', 'Frm1', 'Frm2'].forEach(id => {
		document.getElementById(id).querySelectorAll("input[type='text'],select,textarea").forEach(elem => {
			elem.classList.remove(...estilos.requeired);
		});
	});


	
	if(mIDMinuta > 0) {
		$('#Sucursal,#IDPuestoSucursal').attr('disabled', true);
		$.ajax({
			type: "get",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax4JorA6RetornarMinuta'.date('d'));?>&IDMinuta=' + mIDMinuta,
			cache: false,
			dataType: 'json',
			success: function(data) {
				if(data.Mensaje == "Error") {
					Swal.fire({
						title: 'Error',
						text: 'Se presentó un error al cargar la minuta.',
						icon: 'error',
						confirmButtonText: 'Aceptar',
						confirmButtonColor: '#0e69ca'
					});
					return false;
				} else {
					document.getElementById('Turno').value = data.Turno;
					document.getElementById('Fecha').value = data.Fecha;
					document.getElementById('HoraInicio').value = data.HoraInicio;
					document.getElementById('VigilanteSaliente').value = data.NomVigilanteSaliente;

					let checkbox=document.getElementById('CheckSameVigilante');
					if(data.NomVigilanteSaliente == data.NomVigilanteEntrante){
						checkbox.checked = true;
						HbilitarMismoVigilante();
					}else{
						checkbox.checked = false;
						HbilitarMismoVigilante();
						document.getElementById('VigilanteEntrante').value = data.NomVigilanteEntrante;

					}

					document.getElementById('CheckAceptoTerminar').checked = true;

					document.getElementById('ObsInfraestructura').value = data.ObsInfraestructura;
					document.getElementById('RealizaRequisa').value = data.RealizaRequisa;
					document.getElementById('ObsRequisa').value = data.ObsRequisa;
					document.getElementById('HoraFinalizaRecorrido').value = data.HoraFinalizaRecorrido;
					document.getElementById('Sucursal').value = data.Sucursal;
					CambioSucursal(data.Sucursal);
					document.getElementById('IDPuestoSucursal').value = data.IDPuestoSucursal;
					CambioPuestoSucursalElemento();
					if(Editando) {
						// Modo edición - solo mostrar tabs de edición
						$('#DivMostrarMinuta').find(".tab").hide();
						$('#DivMostrarMinuta').find("select,:checkbox").attr("disabled", false);
						$('#DivMostrarMinuta').find("input:text,textarea").attr("readonly", false);
						document.getElementById('CheckAceptoTerminar').checked = false;

						
						$("select,input,textarea").change(function() {
							document.getElementById('HuboCambio').value = 1;
						});

						currentTab = 0;
						showTab(0);
						MostrarDivModales('verDetalles');
						document.getElementById("nextBtn").style.display = "block";
					} else {
						// Modo consulta - mostrar todos los tabs pero deshabilitados
						$('#DivMostrarMinuta').find(".tab").show();
						$('#DivMostrarMinuta').find("select,:checkbox").attr("disabled", true);
						$('#DivMostrarMinuta').find("input:text,textarea").attr("readonly", true);
						InhabilitarInput();
						MostrarDivModales('verDetalles');
						document.getElementById("nextBtn").style.display = "none";
							
					}
				}
			},
			sync: true,
			error: function(data) {
				Swal.fire({
						title: 'Error',
						text: 'Se presentó un error al cargar la minuta.',
						icon: 'error',
						confirmButtonText: 'Aceptar',
						confirmButtonColor: '#0e69ca'
					});
				return false;
			}
		});
	} else {
		// Minuta nueva - solo mostrar primer tab

		$('#Sucursal,#IDPuestoSucursal,#VigilanteEntrante,#VigilanteSaliente,#Turno, #ObsInfraestructura').attr('disabled', false);
		$('#Sucursal,#IDPuestoSucursal,#VigilanteEntrante,#VigilanteSaliente,#Turno, #ObsInfraestructura').attr('readonly', false);

		document.getElementById('Fecha').value = '<?php echo date('d-m-Y');?>';
		document.getElementById('HoraInicio').value = '<?php echo date('H:i');?>';

		document.getElementById('CheckAceptoTerminar').checked = false;
		
		$("select,input,textarea").change(function() {
			document.getElementById('HuboCambio').value = 1;
		});

		// Ocultar todos los tabs
		$(".tab").each(function() {
			$(this).hide();
		});
		
		// Mostrar solo el primer tab
		currentTab = 0;
		showTab(0);
		MostrarDivModales('verDetalles');
		document.getElementById("nextBtn").style.display = "block";
	}
}

function InhabilitarInput(){
	setTimeout(() => {
		const cuerpoTabla = document.getElementById("TBodyListaChequeo");
		const inputs = cuerpoTabla.querySelectorAll("input, select, textarea");
		inputs.forEach(input => {
				input.disabled = true;
		});
	}, 1000);
}

var currentTab = 0;

function showTab(n) {
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
	
	if(currentTab == 0) {
		document.getElementById("prevBtn").style.display = "none";
	} else {
		document.getElementById("prevBtn").style.display = "inline";
	}
	
	if(n == (x.length - 1)) {
		document.getElementById("nextBtn").innerHTML = "Terminar";
	} else {
		document.getElementById("nextBtn").innerHTML = "Grabar y Siguiente";
	}
}

function nextPrev(n) {
	var x = document.getElementsByClassName("tab");
	
	if(n == 1 && !validateForm()) return false;
	
	x[currentTab].style.display = "none";
	currentTab = currentTab + n;
	
	if(currentTab >= x.length) {
		return;
	}
	
	showTab(currentTab);
}

// aca va los estilos para marcar los campos que no se han diligenciado, son tipo tailwind
let estilos = {
	requeired: ["border-red-500", "bg-red-100", "text-red-700", "focus:ring-red-500", "focus:border-red-500", "placeholder-red-700"]
}

function validateForm(){
	// This function deals with validation of the form fields
	var x, y, i, mRetorno = true;
	if(currentTab==0){			//0  IDENTIFICACIÓN
		ele=document.getElementById('Sucursal');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('IDPuestoSucursal');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('Turno');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('Fecha');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('HoraInicio');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		
		let checkbox=document.getElementById('CheckSameVigilante');
		ele=document.getElementById('VigilanteSaliente');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		
		if(checkbox.checked){
			ele=document.getElementById('VigilanteEntrante').value=document.getElementById('VigilanteSaliente').value;
		}else{
			ele=document.getElementById('VigilanteEntrante');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		}

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
					Swal.fire({
              toast: true,
              position: "top-end",
              icon: "success",
              title: 'Datos guardados',
              showConfirmButton: false,
              timer: 3000
					});
					$('#Frm0,#Frm1,#Frm2').find("input[id='IDMinuta']").each(function(){
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
			let tr=document.getElementsByName("Elemento_" + mID)[0];
			mCantidadReal=parseInt(document.getElementById('CantidadReal' + mID).value,10);
			
			const esFilaVacia = tr.classList.contains('elementovacio');
			
      if(!esFilaVacia){
				ele=document.getElementById('CantidadVerificada' + mID);if(ele.value.length>0){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}

					if(isNaN(mCantidadReal)){
						mCantidadReal=0;
					}
					CantidadVerificada=parseInt(document.getElementById('CantidadVerificada' + mID).value,10);
					if(isNaN(CantidadVerificada)){
						CantidadVerificada=0;
					}
					if(mCantidadReal!=CantidadVerificada && document.getElementById('CantidadVerificada' + mID).value.length>0){
						ele=document.getElementById('ObsVerifica' + mID);if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
					}
			}
				
		});
		//Los datos no se graban acá, para este form se graban campo a campo, por cambio
	}else if(currentTab==2){	//2	REQUISA A VIGILANTE SALIENTE  - Finalizar Crear Minuta por Puesto y Turno
		ele=document.getElementById('RealizaRequisa');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('ObsInfraestructura');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('HoraFinalizaRecorrido');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('ObsRequisa');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
		ele=document.getElementById('CheckAceptoTerminar');
		if(ele.checked){
			document.getElementById("DivAceptoTerminarMinuta").classList.remove(...estilos.requeired);
		}else{
			document.getElementById("DivAceptoTerminarMinuta").classList.add(...estilos.requeired);
			mRetorno=false;
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
					Swal.fire({
						title: '¡Registro Exitoso!',
						text: 'La información se ha guardado correctamente en el sistema.',
						icon: 'success',
						confirmButtonText: 'Continuar',
						confirmButtonColor: '#0e69ca',
						allowOutsideClick: false // Evita que recargue si cierran haciendo clic fuera
					}).then((result) => {
						if (result.isConfirmed) {
							location.reload();
						}
					});
				}else{
					Swal.fire({
						toast: true,
						position: "top-end",
						icon: "error",
						title: html,
						showConfirmButton: false,
						timer: 3000
					});
				}
			});
		}
	}
	x = document.getElementsByClassName("tab");
	y = x[currentTab].getElementsByTagName("input");
	// If the mRetorno status is true, mark the step as finished and mRetorno:
	if(mRetorno){
		// document.getElementsByClassName("step")[currentTab].className += " finish";
	}else{
		Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: 'Datos incompletos',
      showConfirmButton: false,
      timer: 3000
		});
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
					Swal.fire({
						toast: true,
						position: "top-end",
						icon: "error",
						title: html,
						showConfirmButton: false,
						timer: 3000
					});
				}else{
					Swal.fire({
						toast: true,
						position: "top-end",
						icon: "success",
						title: "Dato grabado.",
						showConfirmButton: false,
						timer: 3000
					});
				}
			});
		}
	}
}

function MonstrarElementosVacios() {
	let trVacias = document.querySelectorAll('.elementovacio');
	trVacias.forEach(fila => {
		if(fila.classList.contains('hidden')) {
			fila.classList.remove('hidden');
				Swal.fire({
						toast: true,
						position: "top-end",
						icon: "success",
						title: "Elementos vacíos mostrados.",
						showConfirmButton: false,
						timer: 3000
					});
			return;
		}else{
			fila.classList.add('hidden');
				Swal.fire({
						toast: true,
						position: "top-end",
						icon: "success",
						title: "Elementos vacíos ocultos.",
						showConfirmButton: false,
						timer: 3000
					});
		}
	});
	
}
/******************************************
RECESOS
*******************************************/
function MostrarModalListaRecesos(mIDMinuta){
	document.getElementById('FrmReceso').IDMinuta.value=mIDMinuta;
	$('#DivRecesos').load("index.php?TipoModificar=<?php echo md5('Ajax5JorA6Recesos'.date('d'));?>&IDMinuta="+mIDMinuta,function(){
		MostrarDivModales('Rotaciones');
	});
}
function EditarReceso(mIDMinutaReceso){
	mIDMinuta=document.getElementById('FrmReceso').IDMinuta.value;//Para no perder la referencia a la Minuta
	$("#FrmReceso").each(function(){
   		$(this).trigger("reset");
		$(this).find("input:text,select,textarea").removeClass( "alert-danger");//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	});
	document.getElementById('FrmReceso').IDMinuta.value=mIDMinuta;
	document.getElementById('FrmReceso').IDMinutaReceso.value=mIDMinutaReceso;
	if(mIDMinutaReceso!='Nuevo'){
		$.ajax({
			type: "get",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax6JorA6TraerReceso'.date('d'));?>&IDMinutaReceso='+mIDMinutaReceso,
			cache: false,
			dataType: 'json',
			success: function(data){ //Si se ejecuta correctamente
				if(data.Mensaje=="Error"){
					Swal.fire({
						title: 'Error',
						text: 'Se presentó un error al cargar el receso.',
						icon: 'error',
						confirmButtonText: 'Aceptar',
						confirmButtonColor: '#0e69ca'
					});
					return false;
				}else{
					document.getElementById('FrmReceso').HoraInicioReceso.value = data.HoraInicioReceso;
					document.getElementById('FrmReceso').IDReceso.value = data.IDReceso;
					document.getElementById('FrmReceso').VigilanteAsume.value = data.VigilanteAsume;
					document.getElementById('FrmReceso').HoraFinReceso.value = data.HoraFinReceso;
					document.getElementById('FrmReceso').ObsReceso.value = data.ObsReceso;
				}
			},
			error: function(data){
				Swal.fire({
					title: 'Error',
					text: 'Se presentó un error en la conexión con el servidor.',
					icon: 'error',
					confirmButtonText: 'Aceptar',
					confirmButtonColor: '#0e69ca'
				});
				return false;
			}
		});
	}

	MostrarDivModales('GuardarRotaciones');

}
function GrabarReceso(){
	mRetorno=true;
	ele=document.getElementById('FrmReceso').HoraInicioReceso;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('FrmReceso').IDReceso;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('FrmReceso').VigilanteAsume;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('FrmReceso').HoraFinReceso;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('FrmReceso').ObsReceso;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	if(mRetorno){
		$("#BotsReceso").hide();
		Frm=document.getElementById('FrmReceso');
		Frm.TipoGrabar.value='<?php echo md5('JorA6GrabarReceso'.date('d'));?>';
		Frm.TipoModificar.value='<?php echo md5('GrabarRecesoJorA6'.date('d'));?>';
		var myData = $("#FrmReceso").serialize();
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data: myData
		}).done(function(html){
			$("#BotsReceso").show();
			CerrarDivModales('GuardarRotaciones');
			mIDMinuta=document.getElementById('FrmReceso').IDMinuta.value;
			MostrarModalListaRecesos(mIDMinuta);
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
		$("#BotsReceso").show();
	}
}
function BorrarReceso(mIDMinutaReceso){
		Swal.fire({
		title: '<span class="text-lg font-bold text-gray-700">Confirmar Borrado</span>',
		html: `<p class="text-sm text-gray-500">Desea borrar este registro?.</p>`,
		icon: 'warning',
		iconColor: '#f87171', // Un rojo suave para advertencia
		showDenyButton: true,
		confirmButtonText: 'Sí, Borrar',
		denyButtonText: 'No, cancelar',
		// Personalización de colores y botones con Tailwind
		customClass: {
			confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg mr-2 focus:ring-2 focus:ring-red-500 outline-none transition-all',
			denyButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg focus:ring-2 focus:ring-gray-300 outline-none transition-all',
			popup: 'rounded-2xl shadow-xl border border-gray-100'
		},
		buttonsStyling: false 
	}).then((result) => {
		if (result.isConfirmed) {

			$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data:{
					TipoGrabar:'<?php echo md5('JorA6BorrarReceso'.date('d'));?>',
					TipoModificar:'<?php echo md5('BorrarRecesoJorA6'.date('d'));?>',
					IDMinutaReceso: mIDMinutaReceso
				}
			}).done(function(html){
				mIDMinuta=document.getElementById('FrmReceso').IDMinuta.value;
				MostrarModalListaRecesos(mIDMinuta);
			});
				
		}
		return false;
	});
}
/******************************************
NOVEDADES
*******************************************/
function MostrarModalListaNovedades(mIDMinuta){
	document.getElementById('FrmNovedad').IDMinuta.value=mIDMinuta;
	$('#DivNovedades').load("index.php?TipoModificar=<?php echo md5('Ajax7JorA6Novedades'.date('d'));?>&IDMinuta="+mIDMinuta,function(){
		MostrarDivModales('Novedades');
	});
}
function EditarNovedad(mIDMinutaNovedad){
	mIDMinuta=document.getElementById('FrmNovedad').IDMinuta.value;//Para no perder la referencia a la Minuta
	$("#FrmNovedad").each(function(){
   		$(this).trigger("reset");
		$(this).find("input:text,select,textarea").removeClass(...estilos.requeired);//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	});
	document.getElementById('FrmNovedad').IDMinuta.value=mIDMinuta;
	document.getElementById('FrmNovedad').IDMinutaNovedad.value=mIDMinutaNovedad;
	if(mIDMinutaNovedad!='Nuevo'){
		$.ajax({
			type: "get",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax7JorA6TraerNovedad'.date('d'));?>&IDMinutaNovedad='+mIDMinutaNovedad,
			cache: false,
			dataType: 'json',
			success: function(data){ //Si se ejecuta correctamente
				if(data.Mensaje=="Error"){
					Swal.fire({
						toast: true,
						position: "top-end",
						icon: "error",
						title: 'Se presentó un error al cargar la novedad.',
						showConfirmButton: false,
						timer: 3000
					});
					return false;
				}else{
					document.getElementById('FrmNovedad').HoraNovedad.value = data.HoraNovedad;
					document.getElementById('FrmNovedad').DescripcionNovedad.value = data.DescripcionNovedad;
					document.getElementById('FrmNovedad').ComunicadorNovedad.value = data.ComunicadorNovedad;
					document.getElementById('FrmNovedad').CargoComunicador.value = data.CargoComunicador;
				}
			},
			error: function(data){
				Swal.fire({
						toast: true,
						position: "top-end",
						icon: "error",
						title: 'Se presentó un error linea 763.',
						showConfirmButton: false,
						timer: 3000
					});
				return false;
			}
		});
	}
	MostrarDivModales('GuardarNovedades');
}
function GrabarNovedad(){
	$("#BotsNovedad").hide();
	mRetorno=true;
	ele=document.getElementById('FrmNovedad').HoraNovedad;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('FrmNovedad').DescripcionNovedad;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	// ele=document.getElementById('FrmNovedad').ComunicadorNovedad;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	// ele=document.getElementById('FrmNovedad').CargoComunicador;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	if(mRetorno){
		Frm=document.getElementById('FrmNovedad');
		Frm.TipoGrabar.value='<?php echo md5('JorA6GrabarNovedad'.date('d'));?>';
		Frm.TipoModificar.value='<?php echo md5('GrabarNovedadJorA6'.date('d'));?>';
		let myData = $("#FrmNovedad").serialize();
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data: myData
		}).done(function(html){
			Swal.fire({
				toast: true,
				position: "top-end",
				icon: "success",
				title: 'La novedad se grabó satisfactoriamente.',
				showConfirmButton: false,
				timer: 3000
			});
			CerrarDivModales('GuardarNovedades');
			mIDMinuta=document.getElementById('FrmNovedad').IDMinuta.value;
			MostrarModalListaNovedades(mIDMinuta);
			$("#BotsNovedad").show();
			if(html){
				Swal.fire({
					toast: true,
					position: "top-end",
					icon: "error",
					title: `Error al grabar la novedad: ${html}`,
					showConfirmButton: false,
					timer: 4000
				})
			}
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
		$("#BotsNovedad").show();
	}
}
function BorrarNovedad(mIDMinutaNovedad){
	Swal.fire({
		title: '<span class="text-lg font-bold text-gray-700">Confirmar Borrado</span>',
		html: `<p class="text-sm text-gray-500">Desea borrar este registro?.</p>`,
		icon: 'warning',
		iconColor: '#f87171', // Un rojo suave para advertencia
		showDenyButton: true,
		confirmButtonText: 'Sí, Borrar',
		denyButtonText: 'No, cancelar',
		// Personalización de colores y botones con Tailwind
		customClass: {
			confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg mr-2 focus:ring-2 focus:ring-red-500 outline-none transition-all',
			denyButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg focus:ring-2 focus:ring-gray-300 outline-none transition-all',
			popup: 'rounded-2xl shadow-xl border border-gray-100'
		},
		buttonsStyling: false 
	}).then((result) => {
		if (result.isConfirmed) {

			$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data: {
					TipoGrabar:'<?php echo md5('JorA6BorrarNovedad'.date('d'));?>',
					TipoModificar:'<?php echo md5('BorrarNovedadJorA6'.date('d'));?>',
					IDMinutaNovedad: mIDMinutaNovedad
				}
		}).done(function(html){
			mIDMinuta=document.getElementById('FrmNovedad').IDMinuta.value;
			MostrarModalListaNovedades(mIDMinuta);
		});
				
		}
		return false;
	});
}
/******************************************
CERRAR MINUTA
*******************************************/
function MostrarModalCerrarMinuta(mIDMinuta){
	document.getElementById('FrmCerrarMinuta').IDMinuta.value=mIDMinuta;
	// $("#ModalCerrarMinuta").modal('show');
	MostrarDivModales('Finalizar');
}
function GrabarCerrarMinuta(){
	mRetorno=true;
	let ele=document.getElementById('FrmCerrarMinuta').ObsMinuta;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('CheckAceptoCerrarMinuta');
	let divBoton = document.getElementById('fondoToogle');
	if(ele.checked){
		divBoton.classList.remove("bg-red-300");
	}else{
		divBoton.classList.add("bg-red-300");
		mRetorno=false;
	}
	if(mRetorno){
		$("#BotsCerrarMinuta").hide();
		Frm=document.getElementById('FrmCerrarMinuta');
		Frm.TipoGrabar.value='<?php echo md5('JorA6GrabarCerrarNovedad'.date('d'));?>';
		Frm.TipoModificar.value='<?php echo md5('GrabarCerrarNovedadJorA6'.date('d'));?>';
		var myData = $("#FrmCerrarMinuta").serialize();
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data: myData
		}).done(function(html){
			if(html==''){
				Swal.fire({
						title: '¡Registro Exitoso!',
						text: 'La información se ha guardado correctamente en el sistema.',
						icon: 'success',
						confirmButtonText: 'Continuar',
						confirmButtonColor: '#0e69ca',
						allowOutsideClick: false // Evita que recargue si cierran haciendo clic fuera
					}).then((result) => {
						if (result.isConfirmed) {
							location.reload();
						}
					});
			}else{
				Swal.fire({
						title: '¡Registro Exitoso!',
						text: html,
						icon: 'success',
						confirmButtonText: 'Continuar',
						confirmButtonColor: '#0e69ca',
						allowOutsideClick: false // Evita que recargue si cierran haciendo clic fuera
					});
			}
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
		$("#BotsCerrarMinuta").show();
	}
}


function FirmarMinuta(mIDMinuta=0,mTipoFirma=''){
	$("#FrmFirma").find("input:text,select,textarea").removeClass(...estilos.requeired);//Ojo, solo se deben incluir en input los text, porque se pierden los efectos de los botones
	document.getElementById('FrmFirma').IDMinuta.value=mIDMinuta;
	document.getElementById('FrmFirma').TipoFirma.value=mTipoFirma;
	MostrarDivModales('Firmar');
}

function deshacerFirma(){
	const canvas = document.getElementById('CanvasFirma');
	const context = canvas.getContext('2d');
	context.clearRect(0, 0, canvas.width, canvas.height);
}

function EnviarFirma(){
	mRetorno=true;
	ele=document.getElementById('FrmFirma').ClaveFirma;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	ele=document.getElementById('FrmFirma').ObsFirma;if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	if(mRetorno){
		$("#BotsFirma").hide();
		Frm=document.getElementById('FrmFirma');
		Frm.TipoGrabar.value='<?php echo md5('JorA6Firma'.date('d'));?>';
		Frm.TipoModificar.value='<?php echo md5('FirmaJorA6'.date('d'));?>';
		var myData = $("#FrmFirma").serialize();
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data: myData
		}).done(function(html){
			console.log(html);
			if(html==''){
				Swal.fire({
						title: '¡Registro Exitoso!',
						text: 'El registro se grabó satisfactoriamente',
						icon: 'success',
						confirmButtonText: 'Continuar',
						confirmButtonColor: '#0e69ca',
						allowOutsideClick: false
				}).then((result) => {
					if (result.isConfirmed) {
						location.reload();
					}
				});
			}else{
				MostrarDatoObser(html);
				$("#BotsFirma").show();
			}
		});
	}else{
		MostrarDatoObser("<b>Alerta</b> Hay inconsistencia en los datos, favor revisar.");
		$("#BotsFirma").show();
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


function HbilitarMismoVigilante(elemento){
	let checkbox=document.getElementById('CheckSameVigilante');
	let DivVigilanteSaliente=document.getElementById('DivVigilanteSaliente');
	let DivVigilanteEntrante=document.getElementById('DivVigilanteEntrante');
	let parrafo1 = document.getElementById('ParrafoVigilanteSaliente');
	let parrafo2 = document.getElementById('ParrafoEntregaPuesto');
	let inputVigilanteEntrante = document.getElementById('VigilanteEntrante');
	if(checkbox.checked){
		gsap.to(DivVigilanteEntrante, { opacity: 0, height: 0, duration: 0.3, ease: 'power2.in', onComplete: () => {
			DivVigilanteEntrante.classList.add('hidden');
		}});
		parrafo1.innerText="Vigilante Entrante y saliente";
		parrafo2.innerText="Entrega el puesto a sí mismo";
	}else{
		DivVigilanteEntrante.classList.remove('hidden');
		gsap.fromTo(DivVigilanteEntrante, { opacity: 0, height: 0 }, { opacity: 1, height: 'auto', duration: 0.5, ease: 'back.out(1.5)', overwrite: false });
		parrafo1.innerText="Vigilante saliente";
		parrafo2.innerText="Entrega el puesto";
		inputVigilanteEntrante.value="";
	}
}
</script>

<section class="h-screen col-span-9 md:col-span-8 bg-gray-50 p-4 lg:p-8 S max-h-screen"><?php

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
	if($PuedeAdministrar){
		if($_GET['FiltroElabora']){
			$Filtrico .= $Filtrico." AND CONCAT(Elabora.Nom,' ',Elabora.Apellido1,' ',Elabora.Apellido2) LIKE '%".$_GET['FiltroElabora']."%'";
		}
	}else{
		$Filtrico .= $Filtrico." AND (Minuta.Elabora='".$_SESSION['Usuario']."' OR Minuta.VigilanteEntrante='".$_SESSION['Usuario']."' OR Minuta.VigilanteSaliente='".$_SESSION['Usuario']."')";
	}
	//Por si acaso, optimizo las variables para el filtro por fecha
	
	if($_GET['FiltroFecha1'] and $_GET['FiltroFecha2']){
		$Filtrico .= $Filtrico." AND LEFT(Minuta.Fecha,10) BETWEEN '".DarFechaSQL($_GET['FiltroFecha1'])."' AND '".DarFechaSQL($_GET['FiltroFecha2'])."'";
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
				ORDER BY Minuta.Fecha DESC, Minuta.IDMinuta DESC";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));

	$MismoVigilante=false;
	if($Result->num_rows>0){
		while($row = $Result->fetch_assoc()){
			if($row['VigilanteEntrante']==$row['VigilanteSaliente']){
				$MismoVigilante=true;
			}
		}
	}

	// echo '<pre style="background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; font-family: monospace; font-size: 12px;">'.htmlspecialchars($Queri).'</pre>';
	?>

	<h1 class="text-2xl font-bold mb-0 text-gray-600">Administrar Minutas</h1>
  <hr>

	<form action="index.php" method="get" name="FrmFiltroMinutas" id="FrmFiltroMinutas" class="flex justify-between items-end gap-4">
		<input type="hidden" name="TipoModificar" id="TipoModificar" value="<?php echo md5('JorA1'.date('d'));?>">
		<div class="grid grid-cols-2 md:flex  gap-2 mt-8">
			<!-- filtros y temas de control -->
			<div class="relative">
				<button id="" data-dropdown-toggle="dropdown" class="w-full md:w-auto shrink-0 rounded-xl inline-flex items-center justify-center text-gray-500 bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-2 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-3 py-2 focus:outline-none" type="button">
					<svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
						<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z"/>
					</svg>
					Filtros
				<!-- Dropdown menu -->
				<div id="dropdown" class="z-10 hidden bg-gray-50 border border-default-medium rounded-lg shadow-lg w-32 mt-2 absolute">
					<ul class="p-2 text-sm text-body font-medium" aria-labelledby="dropdownDefaultButton">
						<li>
							<a href="#" class="inline-flex items-center w-full p-2 hover:bg-gray-200 hover:text-heading rounded">Nombre</a>
						</li>
						<li>
							<a href="#" class="inline-flex items-center w-full p-2 hover:bg-gray-200 hover:text-heading rounded">Codigo</a>
						</li>
						<li>
							<a href="#" class="inline-flex items-center w-full p-2 hover:bg-gray-200 hover:text-heading rounded">Colegio</a>
						</li>
						<li>
							<a href="#" class="inline-flex items-center w-full p-2 hover:bg-gray-200 hover:text-heading rounded">Estado</a>
						</li>
					</ul>
				</div>
			</div>

			<!-- imputs -->
			<div>
				<input type="text" name='FiltroSede' id="FiltroSede" class="block w-full max-w-48 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
				placeholder="Sede"  value="<?php echo $_GET['FiltroSede'];?>">
			</div>

			<div>
				<input type="text" name='FiltroPuesto' id="FiltroPuesto" class="block w-full max-w-48 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
				placeholder="Puesto"  value="<?php echo $_GET['FiltroPuesto'];?>">
			</div>

			<div>
				<input type="text" name='FiltroElabora' id="FiltroElabora" class="block w-full max-w-48 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
				placeholder="Elabora"  value="<?php echo $_GET['FiltroElabora'];?>">
			</div>

			<div class="relative">
				<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
					<svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9c9c9c" viewBox="0 0 24 24" >
						<path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2M5 20V8h14V6v14z"></path><path d="M12 13h5v5h-5z"></path>
					</svg>
				</div>
				<input type="text" name='FiltroFecha1' id="FiltroFecha1" class="block w-full max-w-32 ps-9 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
				placeholder="Desde" value="<?php echo $_GET['FiltroFecha1'];?>">
			</div>

			<div class="relative">
				<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
					<svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9c9c9c" viewBox="0 0 24 24" >
						<path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2M5 20V8h14V6v14z"></path><path d="M12 13h5v5h-5z"></path>
					</svg>
				</div>
				<input type="text" name='FiltroFecha2' id="FiltroFecha2" class="block w-full max-w-32 ps-9 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" 
				placeholder="Hasta" value="<?php echo $_GET['FiltroFecha2'];?>">
			</div>

			<!-- buttons -->
			<div>
				<button
					class="w-full md:w-fit cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-2 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
					<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
						<path d="M10.5 19c1.98 0 3.81-.69 5.25-1.83L20 21.42l1.41-1.41-4.25-4.25a8.47 8.47 0 0 0 1.83-5.25c0-4.69-3.81-8.5-8.5-8.5S2 5.81 2 10.5 5.81 19 10.5 19m0-15c3.58 0 6.5 2.92 6.5 6.5S14.08 17 10.5 17 4 14.08 4 10.5 6.92 4 10.5 4"></path>
					</svg>
				</button>
			</div>

			<div>
				<div onclick="CrearMinuta()"
					class="w-full md:w-fit cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-2 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
					<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
						<path d="M3 13h8v8h2v-8h8v-2h-8V3h-2v8H3z"></path>
					</svg>
				</div>
			</div>
		</div>
		<!-- boton paginacion -->
		<div>
			<?php
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

				<button type="submit"
					class="bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-2 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
					<svg  xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="#ffffff" viewBox="0 0 24 24" >
						<path d="m9.71 17.71 5.7-5.71-5.7-5.71-1.42 1.42 4.3 4.29-4.3 4.29z"></path>
					</svg>
				</button>	
		</div>
	</form>

	<div class="mt-4 rounded-t-xl border border-gray-200 text-sm text-gray-600 overflow-x-auto  h-[62vh] md:h-[72vh]">
		<table class="table table-striped table-bordered">
			<thead class="bg-gray-100  rounded-t-lg shadow-xs sticky top-0 z-10">
					<TR>
						<!-- <TH width="1%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">ID</TH> -->
						<TH width="8%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Fecha</TH>
						<TH width="16%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Sede</TH>
						<TH width="17%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Puesto</TH>
						<TH width="15%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Personal</TH>
						<TH width="6%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Fin.Recorrido</TH>
						<TH width="10%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Acciones</TH>
					</TR>
			</thead>
				<?php
					$Switch=1;
					mysqli_data_seek($Result, $Desde-1);
					for ($j = $Desde; $j <= $Hasta; $j++){//Recorrido de la Consulta
						$Switch = $Switch * -1;
						$Row = $Result->fetch_assoc();//hay Registros de O/C
					?>
			<tbody>
				<TR class="bg-white border-t border-gray-200" align=center>
					<!-- ID -->
					<!-- <TD class="p-2" align="center">
						<div class="inline-flex items-center justify-center px-3 py-1 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-full group hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 shadow-sm">
							<span class="text-[11px] font-bold text-blue-700 tracking-tight font-mono"><?php echo $Row['IDMinuta'];?></span>
						</div>
					</TD> -->
					<!-- fecha -->
					<TD class="p-2">
						<div class="inline-flex items-center gap-3 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg group hover:bg-white transition-colors">
								<div class="flex flex-col">
										<span class="text-[10px] uppercase font-bold text-slate-400 tracking-tighter leading-none mb-1">Registro</span>
										<span class="text-[12px] font-mono font-bold text-slate-700 leading-none tracking-tight">
												<?php echo DarFechaHora($Row['FElabora'], 3); ?>
										</span>
								</div>
						</div>
					</TD>
					<!-- sede -->
					<TD class="p-2" align="center"><?php echo $Row['NomSucursal'];?></TD>
					<!-- puesto -->
					<TD class="p-2"><?php echo $Row['Puesto'];?></TD>
					<!-- vigilante entrante -->
					<TD class="p-2">
						<?php 
						$esMismoVigilante = ($Row['VigilanteEntrante'] == $Row['VigilanteSaliente']);
						?>
						<div class="bg-white p-2 rounded-lg text-center shadow-sm border border-gray-100" >
							<?php if($esMismoVigilante){ ?>
								<!-- Mismo vigilante: mostrar solo Entrante -->
								<div class="text-[12px] flex justify-between gap-2" title="Vigilante Entrante y Saliente">
									<?php
									echo $Row['NomVigilanteEntrante'];
									if($Row['HoraFinalizaRecorrido']>0){
										if($Row['FFirmaEntrante']>0){?>
											<span class="cursor-pointer group" title="<?php echo DarFechaHora($Row['FFirmaEntrante'],3).' '.$Row['ObsFirmaEntrante'];?>">
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="16" 
														height="16" 
														viewBox="0 0 24 24" 
														class="text-green-300 hover:text-green-500 transition-colors duration-200 fill-current">
														<path d="M13.29 7.29 7 13.58l-2.29-2.29L3.3 12.7l3 3c.2.2.45.29.71.29s.51-.1.71-.29l7-7-1.41-1.41Zm-.29 6.3-.79-.79-1.41 1.41 1.5 1.5c.2.2.45.29.71.29s.51-.1.71-.29l7-7-1.41-1.41-6.29 6.29Z"></path>
											</svg>
										</span><?php
										}else{?>
											<span onClick="FirmarMinuta(<?php echo $Row['IDMinuta'];?>,'Ambos')" class="cursor-pointer group" title="Firmar Minuta">
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="16" 
														height="16" 
														viewBox="0 0 24 24" 
														class="text-blue-300 hover:text-blue-500 transition-colors duration-200 fill-current">
														<path d="M17 17.76v-5.35l2.91-2.91L21 8.41c.38-.38.58-.88.58-1.42s-.21-1.04-.59-1.41L18.4 3c-.38-.38-.88-.58-1.41-.58s-1.04.21-1.41.59L13.8 4.8l-2.21 2.21H6.24l-3.35 12.3 1.82 1.82 12.3-3.35Zm0-13.35 2.59 2.58-1.09 1.09-2.59-2.59 1.08-1.08ZM7.77 9h4.65l2.09-2.09L17.1 9.5l-2.09 2.09v4.65L7 18.42l3.43-3.43h.08c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5v.08L5.58 17l2.18-8.01Z"></path>
													</svg>
										</span><?php
										}
									}?>
								</div>
							<?php } else { ?>
								<!-- Vigilantes diferentes: mostrar ambos -->
								<div class="text-[12px] flex justify-between gap-2" title="Vigilante Entrante">
									<?php
									echo $Row['NomVigilanteEntrante'];
									if($Row['HoraFinalizaRecorrido']>0){
										if($Row['FFirmaEntrante']>0){?>
											<span class="cursor-pointer group" title="<?php echo DarFechaHora($Row['FFirmaEntrante'],3).' '.$Row['ObsFirmaEntrante'];?>">
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="16" 
														height="16" 
														viewBox="0 0 24 24" 
														class="text-green-300 hover:text-green-500 transition-colors duration-200 fill-current">
														<path d="M13.29 7.29 7 13.58l-2.29-2.29L3.3 12.7l3 3c.2.2.45.29.71.29s.51-.1.71-.29l7-7-1.41-1.41Zm-.29 6.3-.79-.79-1.41 1.41 1.5 1.5c.2.2.45.29.71.29s.51-.1.71-.29l7-7-1.41-1.41-6.29 6.29Z"></path>
											</svg>
										</span><?php
										}else{?>
											<span onClick="FirmarMinuta(<?php echo $Row['IDMinuta'];?>,'Entrante')" class="cursor-pointer group" title="Firmar Minuta">
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="16" 
														height="16" 
														viewBox="0 0 24 24" 
														class="text-blue-300 hover:text-blue-500 transition-colors duration-200 fill-current">
														<path d="M17 17.76v-5.35l2.91-2.91L21 8.41c.38-.38.58-.88.58-1.42s-.21-1.04-.59-1.41L18.4 3c-.38-.38-.88-.58-1.41-.58s-1.04.21-1.41.59L13.8 4.8l-2.21 2.21H6.24l-3.35 12.3 1.82 1.82 12.3-3.35Zm0-13.35 2.59 2.58-1.09 1.09-2.59-2.59 1.08-1.08ZM7.77 9h4.65l2.09-2.09L17.1 9.5l-2.09 2.09v4.65L7 18.42l3.43-3.43h.08c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5v.08L5.58 17l2.18-8.01Z"></path>
													</svg>
										</span><?php
										}
									}?>
								</div>
								<hr>
								<div class="text-[12px] flex justify-between gap-2" title="Vigilante Saliente">
									<?php 
										echo $Row['NomVigilanteSaliente'];
										if($Row['HoraFinalizaRecorrido']>0){
											if($Row['FFirmaSaliente']>0){?>
												<span class="cursor-pointer group" title="<?php echo DarFechaHora($Row['FFirmaSaliente'],3).' '.$Row['ObsFirmaSaliente'];?>" >
													<svg xmlns="http://www.w3.org/2000/svg" 
															width="16" 
															height="16" 
															viewBox="0 0 24 24" 
															class="text-green-300 hover:text-green-500 transition-colors duration-200 fill-current">
														<path d="M13.29 7.29 7 13.58l-2.29-2.29L3.3 12.7l3 3c.2.2.45.29.71.29s.51-.1.71-.29l7-7-1.41-1.41Zm-.29 6.3-.79-.79-1.41 1.41 1.5 1.5c.2.2.45.29.71.29s.51-.1.71-.29l7-7-1.41-1.41-6.29 6.29Z"></path>
													</svg>
												</span><?php
											}else{?>
												<span onClick="FirmarMinuta(<?php echo $Row['IDMinuta'];?>,'Saliente')" class="cursor-pointer group" title="Firmar Minuta">
													<svg xmlns="http://www.w3.org/2000/svg" 
															width="16" 
															height="16" 
															viewBox="0 0 24 24" 
															class="text-blue-300 hover:text-blue-500 transition-colors duration-200 fill-current">
														<path d="M17 17.76v-5.35l2.91-2.91L21 8.41c.38-.38.58-.88.58-1.42s-.21-1.04-.59-1.41L18.4 3c-.38-.38-.88-.58-1.41-.58s-1.04.21-1.41.59L13.8 4.8l-2.21 2.21H6.24l-3.35 12.3 1.82 1.82 12.3-3.35Zm0-13.35 2.59 2.58-1.09 1.09-2.59-2.59 1.08-1.08ZM7.77 9h4.65l2.09-2.09L17.1 9.5l-2.09 2.09v4.65L7 18.42l3.43-3.43h.08c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5v.08L5.58 17l2.18-8.01Z"></path>
													</svg>
												</span><?php
											}
										}?>
								</div>
							<?php } ?>
						</div>
					</TD>
			
					<!-- fin recorrido -->
					<TD class="p-2"><?php
						if($Row['HoraFinalizaRecorrido']>0){?>
						<div class="flex gap-2 items-center justify-center">
							<span class="bg-green-100 text-green-700  border border-green-200 rounded-lg px-2 "><?php echo $Row['HoraFinalizaRecorrido']; ?></span>
						</div>
						<?php
						}else{
							if($PuedeAdministrar or $Row['Elabora']==$_SESSION['Usuario']){?>
								<div class="flex gap-2 items-center justify-center">
									<span class="bg-red-100 text-red-600  border border-red-600 rounded-lg px-2 ">Sin Finalizar</span>
								</div>
								<?php
							}else{?>
								<div class="flex gap-2 items-center justify-center">
									<span class="text-red-200 text-red-800">Por Finalizar Recorrido</span>
								</div><?php
							}
						}?>
					</TD>
					<!-- Acciones y finalización -->
					<TD class="p-2">
						<div class="flex gap-2 flex-col justify-center items-center"><?php

							if($Row['HoraFinalizaRecorrido']>0){
								// muestra la hora
							}else{
									if($PuedeAdministrar or $Row['Elabora']==$_SESSION['Usuario']){?>
										<span  onClick="EditarMinuta(<?php echo $Row['IDMinuta'];?>);" class="cursor-pointer group" title='Editar Minuta | Finalizar Recorrido'>
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="25" 
														height="25" 
														viewBox="0 0 24 24" 
														class="text-violet-300 hover:text-violet-500 transition-colors duration-200 fill-current">
														<path d="M5 21h14c1.1 0 2-.9 2-2v-7h-2v7H5V5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2"></path><path d="M7 13v3c0 .55.45 1 1 1h3c.27 0 .52-.11.71-.29l9-9a.996.996 0 0 0 0-1.41l-3-3a.996.996 0 0 0-1.41 0l-9.01 8.99A1 1 0 0 0 7 13m10-7.59L18.59 7 17.5 8.09 15.91 6.5zm-8 8 5.5-5.5 1.59 1.59-5.5 5.5H9z"></path>
												</svg>
											</span>
										<?php
									}else{?>
										<span>
												Por Finalizar Recorrido
										</span><?php
									}
								}?>
								<?php
								if($Row['HoraFinalizaRecorrido']>0){?>
									<div class="inline-flex gap-2 items-center">
										<div class="flex gap-2 items-center justify-center">
											<span onClick="EditarMinuta(<?php echo $Row['IDMinuta'];?>,false)"  class="cursor-pointer group" title="Ver Detalles de la Minuta">
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="25" 
														height="25" 
														viewBox="0 0 24 24" 
														class="text-blue-400 hover:text-blue-600 transition-colors duration-200 fill-current">
													<path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6"></path><path d="M12 19c7.63 0 9.93-6.62 9.95-6.68.07-.21.07-.43 0-.63-.02-.07-2.32-6.68-9.95-6.68s-9.93 6.61-9.95 6.67c-.07.21-.07.43 0 .63.02.07 2.32 6.68 9.95 6.68Zm0-12c5.35 0 7.42 3.85 7.93 5-.5 1.16-2.58 5-7.93 5s-7.42-3.84-7.93-5c.5-1.16 2.58-5 7.93-5"></path>
												</svg>
											</span>
											<span onClick="MostrarModalListaRecesos(<?php echo $Row['IDMinuta'];?>);" class="cursor-pointer group" title="Listar las Rotaciones">
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="24" 
														height="24" 
														viewBox="0 0 24 24" 
														class="text-red-300 hover:text-red-500 transition-colors duration-200 fill-current">
														<path d="M14 7 9 3v3H8c-3.31 0-6 2.69-6 6s2.69 6 6 6v-2c-2.21 0-4-1.79-4-4s1.79-4 4-4h1v3zm2-1v2c2.21 0 4 1.79 4 4s-1.79 4-4 4h-1v-3l-5 4 5 4v-3h1c3.31 0 6-2.69 6-6s-2.69-6-6-6"></path>
												</svg>
											</span>
											<span onClick="MostrarModalListaNovedades(<?php echo $Row['IDMinuta'];?>);" class="cursor-pointer group" title='Listar las Novedades'>
												<svg xmlns="http://www.w3.org/2000/svg" 
														width="20" 
														height="20" 
														viewBox="0 0 24 24" 
														class="text-orange-300 hover:text-orange-500 transition-colors duration-200 fill-current">
														<path d="M11 7h2v6h-2zm0 8h2v2h-2z"></path><path d="M12 22c5.51 0 10-4.49 10-10S17.51 2 12 2 2 6.49 2 12s4.49 10 10 10m0-18c4.41 0 8 3.59 8 8s-3.59 8-8 8-8-3.59-8-8 3.59-8 8-8"></path>
												</svg>
											</span>
										</div>
									</div>
										<?php
									if($Row['FinalizaRegistro']>0){?>
										<div class="flex flex-col items-start gap-1">
											<!-- Etiqueta de estado -->
											<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 uppercase tracking-wider">
													<span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
													Finalizada
											</span>
											<!-- Fecha y Hora -->
											<span class="text-[11px] font-medium text-gray-500 ml-1">
													<i class="far fa-clock mr-1"></i> <!-- Opcional: Icono de reloj -->
													<?php echo DarFechaHora($Row['FinalizaRegistro'], 3); ?>
											</span>
									</div>
									<?php
									}elseif($PuedeAdministrar or $Row['Elabora']==$_SESSION['Usuario']){?>
									<div class="inline-flex gap-2 items-center">
										<span onClick="MostrarModalCerrarMinuta(<?php echo $Row['IDMinuta'];?>);" class="cursor-pointer group " title='Cierre de la minuta'>
											<svg xmlns="http://www.w3.org/2000/svg" 
													width="20" 
													height="20" 
													viewBox="0 0 24 24" 
													class="text-blue-500 hover:text-blue-700 transition-colors duration-200 fill-current">
													<path d="M19.17 9h-4.02c-.32 0-.61-.14-.8-.4a.99.99 0 0 1-.16-.88l.54-1.9c.26-.91.08-1.87-.49-2.63S12.8 2 11.84 2h-1.35c-.45 0-.84.3-.96.73L8.38 6.74a3.99 3.99 0 0 1-3.13 2.84l-2.44.44c-.48.09-.82.5-.82.98v9c0 .55.45 1 1 1h2.42c.94 0 1.87.15 2.77.45 1.1.37 2.24.55 3.4.55h5.59c1.56 0 2.83-1.27 2.83-2.83 0-.46-.11-.9-.31-1.28a2.83 2.83 0 0 0 1-3.67c.79-.5 1.31-1.39 1.31-2.39C22 10.27 20.73 9 19.17 9m0 3.67H18c-.55 0-1 .45-1 1s.45 1 1 1h.17c.46 0 .83.37.83.83s-.37.83-.83.83H17c-.55 0-1 .45-1 1s.45 1 1 1h.17c.46 0 .83.37.83.83s-.37.83-.83.83h-5.59c-.94 0-1.87-.15-2.77-.45-1.1-.37-2.24-.55-3.4-.55H3.99v-7.17l1.62-.29a5.98 5.98 0 0 0 4.7-4.25l.94-3.29h.59c.32 0 .61.14.8.4.19.25.25.57.16.88l-.54 1.9c-.26.91-.08 1.87.49 2.63s1.44 1.19 2.4 1.19h4.02c.46 0 .83.37.83.83s-.37.83-.83.83Z"></path>
											</svg>
										</span>
									</div>
										<?php
									}else{
										echo 'Minuta por Cerrar';
									}
								}else{
									echo '<span class="text-[12px]">Por Finalizar Recorrido</span>';
								}?>
						</div>
					</TD>
				</TR>
			</tbody>
			<?php
			}//fin del for ?>
    </table>
	</div>
</section>


<!--VENTANA MODAL PARA LIST ROTACIONES EN LA MINUTA-->
<div id="modal-Rotaciones" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-Rotaciones-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('Rotaciones')"></div>
    <div id="modal-Rotaciones-container" class="relative bg-white rounded-2xl shadow-2xl  w-full  md:max-w-[70%] overflow-hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-x-3">
                    <div class="hidden md:block bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
													fill="#ffffff" viewBox="0 0 24 24" >
													<path d="M14 7 9 3v3H8c-3.31 0-6 2.69-6 6s2.69 6 6 6v-2c-2.21 0-4-1.79-4-4s1.79-4 4-4h1v3zm2-1v2c2.21 0 4 1.79 4 4s-1.79 4-4 4h-1v-3l-5 4 5 4v-3h1c3.31 0 6-2.69 6-6s-2.69-6-6-6"></path>
												</svg>
                    </div>
                    <div>
                        <h3 id="modal-rotaciones-title" class="text-xl">Registro de rotaciones (recesos)</h3>
                        <p class="text-blue-100 text-sm">Lista de rotaciones en la minuta</p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('Rotaciones')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<div class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">
							<!-- boton agregar -->
							<div class="flex justify-end mb-4">
								<div class="w-[130px] h-[16px]">
									<div id="BotEditarReceso" onClick="EditarReceso('Nuevo');"
										class="cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-1 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
										<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
											<path d="M3 13h8v8h2v-8h8v-2h-8V3h-2v8H3z"></path>
										</svg>
										<p>Agregar</p>
									</div>
								</div>
							</div>
							<!-- cuerpo de los datos -->
							<div class="overflow-y-auto w-full" id="DivRecesos">
							</div>
						</div>

						<div class="flex gap-3 pt-4">
								<button type="button" onclick="CerrarDivModales('Rotaciones')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
							</div>
        </div>
    </div>
</div>
<!--VENTANA MODAL 2 PARA GUARDAR ROTACIONES EN LA MINUTA-->
<div id="modal-GuardarRotaciones" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-GuardarRotaciones-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('GuardarRotaciones')"></div>
    <div id="modal-GuardarRotaciones-container" class="relative bg-white rounded-2xl shadow-2xl  w-full  md:max-w-[70%] overflow-hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-x-3">
                    <div class="hidden md:block bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
													fill="#ffffff" viewBox="0 0 24 24" >
													<path d="M14 7 9 3v3H8c-3.31 0-6 2.69-6 6s2.69 6 6 6v-2c-2.21 0-4-1.79-4-4s1.79-4 4-4h1v3zm2-1v2c2.21 0 4 1.79 4 4s-1.79 4-4 4h-1v-3l-5 4 5 4v-3h1c3.31 0 6-2.69 6-6s-2.69-6-6-6"></path>
												</svg>
                    </div>
                    <div>
                        <h3 id="modal-GuardarRotaciones-title" class="text-xl">Guardar Rotaciones</h3>
                        <p class="text-blue-100 text-sm">Asignado a puesto</p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('GuardarRotaciones')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<form id="FrmReceso" class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">
	
							<!-- Formulario de crear/editar rotaciones -->
							<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
									<div action="" class="space-y-6">
											<!-- Hidden Inputs -->
											<input name="TipoGrabar" type="hidden" id="TipoGrabar">
											<input name="TipoModificar" type="hidden" id="TipoModificar">
											<input name="IDMinuta" type="hidden" id="IDMinuta">
											<input name="IDMinutaReceso" type="hidden" id="IDMinutaReceso">

											<!-- Grid Principal del Formulario -->
											<div class="grid grid-cols-1 md:grid-cols-12 gap-5">
													
													<!-- Hora Inicio -->
													<div class="md:col-span-2 flex flex-col gap-1.5">
															<label for="HoraInicioReceso" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Hora Inicio</label>
															<input name="HoraInicioReceso" type="text" id="HoraInicioReceso" maxlength="5" onBlur="ValidarHora(this);" autocomplete="off" placeholder="00:00"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div>

													<!-- Actividad Autorizada -->
													<div class="md:col-span-4 flex flex-col gap-1.5">
															<label for="IDReceso" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Actividad Autorizada</label>
															<select name="IDReceso" id="IDReceso" class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none appearance-none cursor-pointer">
																	<option value='' selected>Seleccione actividad...</option>
																	<?php
																	$QueriR = "SELECT DISTINCT Receso.IDReceso, Receso.Receso 
																							FROM ".$PrefBD."solicitudes.vigilanciareceso Receso 
																							WHERE Receso.Borrada=0";
																	$ResultR = $mysqli->query($QueriR) or die(mysqli_error($mysqli));
																	while($RowR = $ResultR->fetch_assoc()){ ?>
																			<option value='<?php echo $RowR['IDReceso'];?>'><?php echo $RowR['Receso'];?></option>
																	<?php } ?>
															</select>
													</div>

													<!-- Vigilante Asume -->
													<div class="md:col-span-4 flex flex-col gap-1.5">
															<label for="VigilanteAsume" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Vigilante / Persona Autorizada</label>
															<input name="VigilanteAsume" type="text" id="VigilanteAsume" autocomplete="off" placeholder="Nombre de quien asume"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div>

													<!-- Hora Fin -->
													<div class="md:col-span-2 flex flex-col gap-1.5">
															<label for="HoraFinReceso" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Hora Fin</label>
															<input name="HoraFinReceso" type="text" id="HoraFinReceso" maxlength="5" onBlur="ValidarHora(this);" autocomplete="off" placeholder="00:00"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div>

													<!-- Observación (Ancho completo o mayor) -->
													<div class="md:col-span-12 flex flex-col gap-1.5">
															<label for="ObsReceso" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Observación del Receso</label>
															<input name="ObsReceso" type="text" id="ObsReceso" maxlength="200" placeholder="Escriba detalles o motivos aquí..."
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div>
											</div>
									</div>
							</div>
						</div>

						<div class="flex gap-3 pt-4" id="BotsReceso">
								<button type="button" onclick="CerrarDivModales('GuardarRotaciones')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
								<button  type="button" onClick="GrabarReceso();"
									class="flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Guardar Rotacion</span>
								</button>
							</div>
        </form>
    </div>
</div>

<!--VENTANA MODAL PARA LISTAR NOVEDADES EN LA MINUTA-->
<div id="modal-Novedades" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-Novedades-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('Novedades')"></div>
    <div id="modal-Novedades-container" class="relative bg-white rounded-2xl shadow-2xl w-full md:max-w-[70%] overflow-hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-x-3">
                    <div class="hidden md:block bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
													fill="#ffffff" viewBox="0 0 24 24" >
													<path d="M20 3H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h3v2c0 .36.19.69.51.87a1 1 0 0 0 1-.01L13.27 19h6.72c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2Zm0 14h-7c-.18 0-.36.05-.51.14L9 19.23V18c0-.55-.45-1-1-1H4V5h16z"></path><path d="M11 7h2v4.5h-2zm0 6h2v2h-2z"></path>
												</svg>
                    </div>
                    <div>
                        <h3 id="modal-Novedades-title" class="text-xl">Sucesos durante el turno</h3>
                        <p class="text-blue-100 text-sm">Registro de sucesos comunicados verbalmente o fuera del alcance de los registros</p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('Novedades')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<div class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">
							<!-- boton agregar -->
							<div class="flex justify-end mb-4">
								<div class="w-[130px] h-[16px]">
									<div id="BotEditarNovedad" onClick="EditarNovedad('Nuevo');"
										class="cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-1 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
										<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
											<path d="M3 13h8v8h2v-8h8v-2h-8V3h-2v8H3z"></path>
										</svg>
										<p>Agregar</p>
									</div>
								</div>
							</div>
							<!-- cuerpo de los datos -->
							<div class="overflow-y-auto w-full" id="DivNovedades">
							</div>

						</div>

						<div class="flex gap-3 pt-4">
								<button type="button" onclick="CerrarDivModales('Novedades')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
							</div>
        </div>
    </div>
</div>

<!--VENTANA MODAL PARA GUARDAR NOVEDADES EN LA MINUTA-->
<div id="modal-GuardarNovedades" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-GuardarNovedades-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('GuardarNovedades')"></div>
    <div id="modal-GuardarNovedades-container" class="relative bg-white rounded-2xl shadow-2xl w-full md:max-w-[70%] overflow-hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-x-3">
                    <div class="hidden md:block bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
													fill="#ffffff" viewBox="0 0 24 24" >
													<path d="M20 3H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h3v2c0 .36.19.69.51.87a1 1 0 0 0 1-.01L13.27 19h6.72c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2Zm0 14h-7c-.18 0-.36.05-.51.14L9 19.23V18c0-.55-.45-1-1-1H4V5h16z"></path><path d="M11 7h2v4.5h-2zm0 6h2v2h-2z"></path>
												</svg>
                    </div>
                    <div>
                        <h3 id="modal-GuardarNovedades-title" class="text-xl">Guardar Suceso</h3>
                        <p class="text-blue-100 text-sm">Registra los sucesos u hechos correspondientes al turno actual.</p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('GuardarNovedades')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<!-- Formulario de crear/editar novedades -->
				<form id="FrmNovedad" class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">

							<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6" >
									<div class="space-y-6">
											<!-- Hidden Inputs -->
											<input name="TipoGrabar" type="hidden" id="TipoGrabar">
											<input name="TipoModificar" type="hidden" id="TipoModificar">
											<input name="IDMinuta" type="hidden" id="IDMinuta">
											<input name="IDMinutaNovedad" type="hidden" id="IDMinutaNovedad">

											<!-- Grid Principal del Formulario -->
											<div class="grid grid-cols-1 md:grid-cols-12 gap-5">
													
													<!-- Hora Inicio -->
													<div class="md:col-span-3 flex flex-col gap-1.5">
															<label for="HoraNovedad" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Hora</label>
															<input name="HoraNovedad" type="text" id="HoraNovedad" maxlength="5" onBlur="ValidarHora(this);" autocomplete="off" placeholder="00:00"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div>

													<!-- Comunicador -->
													<!-- <div class="md:col-span-5 flex flex-col gap-1.5">
															<label for="ComunicadorNovedad" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Comunicador Novedad</label>
															<input name="ComunicadorNovedad" type="text" maxlength="60" autocomplete="off" placeholder="Comunicador Novedad"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div> -->

													<!-- Cargo Comunicador -->
													<!-- <div class="md:col-span-4 flex flex-col gap-1.5">
															<label for="CargoComunicador" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Cargo Comunicador</label>
															<input name="CargoComunicador" type="text" id="CargoComunicador" maxlength="50" autocomplete="off" placeholder="Cargo Comunicador"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div> -->

													<!-- Observación (Ancho completo o mayor) -->
													<div class="md:col-span-9 flex flex-col gap-1.5">
															<label for="DescripcionNovedad" class="text-[11px] font-black text-gray-500 uppercase tracking-wider ml-1">Descripción Novedad</label>
															<input name="DescripcionNovedad" type="text" id="DescripcionNovedad" maxlength="200" autocomplete="off" placeholder="Descripción Novedad"
																	class="text-sm block w-full px-4 py-2 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
													</div>
											</div>
										
									</div>
							</div>
						</div>

						<div class="flex gap-3 pt-4" id="BotsNovedad">
								<button type="button" onclick="CerrarDivModales('GuardarNovedades')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
								<button  type="button" onClick="GrabarNovedad();"
									class="flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Guardar Suceso</span>
								</button>
							</div>
        </form>
    </div>
</div>

<!--VENTANA MODAL PARA 4  Finalización Registro de Minuta por Puesto y Turno-->
<div id="modal-Finalizar" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-Finalizar-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('Finalizar')"></div>
    <div id="modal-Finalizar-container" class="relative bg-white rounded-2xl shadow-2xl w-full md:max-w-[70%] overflow-hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-x-3">
                    <div class="hidden md:block bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
													fill="#ffffff" viewBox="0 0 24 24" >
												<path d="M19.67 2.61c-.81-.81-2.14-.81-2.95 0L3.38 15.95c-.13.13-.22.29-.26.46l-1.09 4.34c-.08.34.01.7.26.95.19.19.45.29.71.29.08 0 .16 0 .24-.03l4.34-1.09c.18-.04.34-.13.46-.26L21.38 7.27c.81-.81.81-2.14 0-2.95L19.66 2.6ZM6.83 19.01l-2.46.61.61-2.46 9.96-9.94 1.84 1.84zM19.98 5.86 18.2 7.64 16.36 5.8l1.78-1.78s.09-.03.12 0l1.72 1.72s.03.09 0 .12"></path>
												</svg>
                    </div>
                    <div>
                        <h3 id="modal-Finalizar-title" class="text-xl">Finalización Registro de Minuta</h3>
                        <p class="text-blue-100 text-sm">Por puesto y turno</p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('Finalizar')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<div class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">

							<!-- Formulario de crear/editar Finalizar -->
							<div class="">
									<form method="post" enctype="multipart/form-data" name="FrmCerrarMinuta" id="FrmCerrarMinuta" >
										<!-- Inputs Ocultos -->
									<input name="TipoGrabar" type="hidden" id="TipoGrabar">
									<input name="TipoModificar" type="hidden" id="TipoModificar">
									<input name="IDMinuta" type="hidden" id="IDMinuta">

										<div class="modal-dialog modal-dialog-scrollable mx-auto" role="document" id="DivContieneFirma">
												<div id="signature-pad" class="signature-pad bg-white rounded-2xl overflow-hidden">

														<!-- Inputs de Seguridad y Observaciones -->
														<div class="p-6 space-y-5 border-t border-gray-100">
																		<!-- Clave de Firma -->
																		<div class="md:col-span-4 flex flex-col gap-1.5 text-left">
																				<label for="ObsMinuta" class="">Observaciones al firmar</label>
																				<input name="ObsMinuta" type="text" id="ObsMinuta"
																								class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
																		</div>

																		<!-- Contenedor del Toggle -->
																		<label for="CheckAceptoCerrarMinuta" class="flex items-center cursor-pointer group">
																			<!-- Input oculto -->
																			<div class="relative">
																				<input type="checkbox" name="CheckAceptoCerrarMinuta" id="CheckAceptoCerrarMinuta" class="sr-only peer" />
																				
																				<!-- Pista del interruptor (Track) -->
																				<div id="fondoToogle" class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-500/20 peer-checked:bg-blue-600 transition-all duration-300"></div>
																				
																				<!-- Círculo del interruptor (Thumb) -->
																				<div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all duration-300 peer-checked:translate-x-full peer-checked:border-white border border-gray-300"></div>
																			</div>
																			
																			<!-- Texto descriptivo -->
																			<span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">
																				Al dar clic, acepta cerrar la minuta, y ya no se podrán crear novedades en ella.
																			</span>
																		</label>
														</div>
												</div>

										</div>
								</form>
							</div>
						</div>

						<div class="flex gap-3 pt-4" id="BotsCerrarMinuta">
								<button type="button" onclick="CerrarDivModales('Finalizar')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
								<button  type="button" id="BotAceptarCerrarMinuta" onclick="GrabarCerrarMinuta();"
										class="flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Finalizar</span>
									</button>
						</div>
        </div>
    </div>
</div>

<!--VENTANA MODAL PARA FIRMAR-->
<div id="modal-Firmar" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-Firmar-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('Firmar')"></div>
    <div id="modal-Firmar-container" class="relative bg-white rounded-2xl shadow-2xl w-full md:max-w-[70%] w-full overflow-hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-x-3">
                    <div class="hidden md:block bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
													fill="#ffffff" viewBox="0 0 24 24" >
												<path d="M19.67 2.61c-.81-.81-2.14-.81-2.95 0L3.38 15.95c-.13.13-.22.29-.26.46l-1.09 4.34c-.08.34.01.7.26.95.19.19.45.29.71.29.08 0 .16 0 .24-.03l4.34-1.09c.18-.04.34-.13.46-.26L21.38 7.27c.81-.81.81-2.14 0-2.95L19.66 2.6ZM6.83 19.01l-2.46.61.61-2.46 9.96-9.94 1.84 1.84zM19.98 5.86 18.2 7.64 16.36 5.8l1.78-1.78s.09-.03.12 0l1.72 1.72s.03.09 0 .12"></path>
												</svg>
                    </div>
                    <div>
                        <h3 id="modal-Firmar-title" class="text-xl">Registro de Firmar (recesos)</h3>
                        <p class="text-blue-100 text-sm">Asignado a puesto</p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('Firmar')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<div class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">

							<!-- Formulario de crear/editar Firmar -->
							<div class="">
									<form method="post" enctype="multipart/form-data" name="FrmFirma" id="FrmFirma" >
										<!-- Inputs Ocultos -->
										<input name="TipoGrabar" type="hidden" id="TipoGrabar">
										<input name="TipoModificar" type="hidden" id="TipoModificar">
										<input name="IDMinuta" type="hidden" id="IDMinuta">
										<input name="TipoFirma" type="hidden" id="TipoFirma">

										<div class="modal-dialog modal-dialog-scrollable mx-auto" role="document" id="DivContieneFirma">
														
														<!-- Encabezado del Área de Firma -->
														<div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
																<span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Panel de Firma Digital</span>
														</div>

														<!-- Inputs de Seguridad y Observaciones -->
														<div class="p-6 space-y-5 border-t border-gray-100">
																<div class="grid grid-cols-1 md:grid-cols-12 gap-5">
																		<!-- Codigo de Seguridad -->
																		<div class="md:col-span-4 flex flex-col gap-1.5 text-left">
																				<label for="ClaveFirma" class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Codigo de Seguridad</label>
																				<div class="relative">
																						<input name="ClaveFirma" type="password" id="ClaveFirma" placeholder="••••••"
																								class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
																				</div>
																		</div>

																		<!-- Observaciones -->
																		<div class="md:col-span-8 flex flex-col gap-1.5 text-left">
																				<label for="ObsFirma" class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Observaciones</label>
																				<textarea name="ObsFirma" id="ObsFirma" rows="1" maxlength="250" placeholder="Notas adicionales sobre la firma..."
																						class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none resize-none"></textarea>
																		</div>
																</div>
														</div>
										</div>
								</form>
							</div>
						</div>

						<div class="flex gap-3 pt-4" id="BotsFirma">
								<button type="button" onclick="CerrarDivModales('Firmar')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
								<button  type="button" onclick="EnviarFirma();"
										class="flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Firmar</span>
									</button>
						</div>
        </div>
    </div>
</div>

<!-- MODAL Detalles Minuta -->
	<div id="modal-verDetalles" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-verDetalles-backdrop" class="modal-backdrop absolute inset-0 bg-black/30" onclick="CerrarDivModales('verDetalles')"></div>
    	<div id="modal-verDetalles-container" class="relative bg-white rounded-2xl shadow-2xl max-w-5xl w-full overflow-hidden">
					<div class="bg-gradient-to-br from-blue-600 to-blue-400 p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                  <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                    <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  fill="#ffffff" viewBox="0 0 24 24" >
											<path d="M19.5 3h-5c-.83 0-1.5.67-1.5 1.5v5c0 .83.67 1.5 1.5 1.5h5c.83 0 1.5-.67 1.5-1.5v-5c0-.83-.67-1.5-1.5-1.5M19 9h-4V5h4zM9.5 3h-5C3.67 3 3 3.67 3 4.5v15c0 .83.67 1.5 1.5 1.5h5c.83 0 1.5-.67 1.5-1.5v-15c0-.83-.67-1.5-1.5-1.5M9 19H5V5h4zm10.5-6h-5c-.83 0-1.5.67-1.5 1.5v5c0 .83.67 1.5 1.5 1.5h5c.83 0 1.5-.67 1.5-1.5v-5c0-.83-.67-1.5-1.5-1.5m-.5 6h-4v-4h4z"></path>
                    </svg>
              			</div>
             				<div>
										<h3 id="ModalMostrarMinutaTitle" class="text-xl font-bold">Minuta - proceso de entrega</h3>
										<p class="text-blue-100 text-sm">Ejecución y recibo de puesto</p>
									</div>
									</div>
								<button onclick="CerrarDivModales('verDetalles')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
									<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</button>
							</div>
      			</div>
 
      	<div  class="p-6 pt-0 space-y-4 overflow-y-auto max-h-[82vh]">
    			<input name="HuboCambio" type="hidden" id="HuboCambio">
          <div class="grid grid-cols-1 gap-4">
            	<div>
								<!-- Ver - Crear y editar -->
              	<div id="DivMostrarMinuta" class=" text-gray-700">

									<!-- header -->
									<div class="tab">
											<form name="Frm0" id="Frm0" method="post" enctype="multipart/form-data">
													<input name="TipoGrabar" type="hidden" id="TipoGrabar">
													<input name="TipoModificar" type="hidden" id="TipoModificar">
													<input name="IDMinuta" type="hidden" id="IDMinuta">

													<div class="grid grid-cols-1 md:grid-cols-10 gap-4">

															<!-- Columna izquierda: Sucursal, Puesto, Turno+Fecha -->
															<div class="col-span-6 grid grid-cols-6 gap-4">

																	<!-- Sucursal -->
																	<div class="col-span-6 bg-white rounded-xl border border-slate-200 p-4">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
																							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
																									<path d="M21 4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v6H5c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h16c.55 0 1-.45 1-1zM5 12h6v8H5zm14 8h-6v-8c0-1.1-.9-2-2-2H9V4h10z"/><path d="M11 6h2v2h-2zm4 0h2v2h-2zm0 4.03h2V12h-2zM15 14h2v2h-2zm-8 0h2v2H7z"/>
																							</svg>
																					</div>
																					<label for="Sucursal" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Sucursal</label>
																			</div>
																			<select name="Sucursal" id="Sucursal"
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

																	<!-- Puesto -->
																	<div class="col-span-6 bg-white rounded-xl border border-slate-200 p-4">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
																							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
																									<path d="M6 8.44c-.02 5.1 5.17 9.18 5.39 9.35.18.14.4.21.61.21s.43-.07.61-.21c.22-.17 5.41-4.25 5.39-9.35C18 4.89 15.31 2 12 2S6 4.89 6 8.44m10 0c.01 3.19-2.74 6.08-4 7.24-1.26-1.15-4.01-4.04-4-7.24C8 5.99 9.79 4 12 4s4 1.99 4 4.44"/><path d="M12 6a2 2 0 1 0 0 4 2 2 0 1 0 0-4m6.02 8.73c-.4.64-.84 1.23-1.27 1.76C18.88 16.97 20 17.68 20 18c0 .51-2.75 2-8 2s-8-1.49-8-2c0-.32 1.12-1.03 3.25-1.51-.43-.53-.86-1.12-1.27-1.76C3.66 15.37 2 16.44 2 18c0 2.75 5.18 4 10 4s10-1.25 10-4c0-1.56-1.67-2.63-3.98-3.27"/>
																							</svg>
																					</div>
																					<label for="IDPuestoSucursal" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Puesto</label>
																			</div>
																			<select name="IDPuestoSucursal" id="IDPuestoSucursal"
																					class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all"
																					onChange="CambioPuestoSucursalElemento();">
																					<option value="">— Seleccionar puesto —</option>
																			</select>
																	</div>

																	<!-- Turno + Fecha -->
																	<div class="col-span-2 bg-white rounded-xl border border-slate-200 p-4">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
																							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
																									<path d="M19 3c-1.65 0-3 1.35-3 3 0 .5.14.97.35 1.38l-1.12 1.3c-.64-.43-1.41-.69-2.24-.69s-1.53.24-2.15.64l-2.2-1.65c.22-.45.35-.96.35-1.49 0-1.93-1.57-3.5-3.5-3.5s-3.5 1.57-3.5 3.5 1.57 3.5 3.5 3.5c.66 0 1.28-.2 1.81-.52l2.18 1.64c-.3.56-.49 1.2-.49 1.88 0 1 .38 1.9.99 2.6l-1.69 1.69.03.03c-.4-.2-.84-.32-1.32-.32-1.65 0-3 1.35-3 3s1.35 3 3 3 3-1.35 3-3c0-.48-.12-.92-.32-1.32l.03.03 1.95-1.95c.42.15.87.25 1.34.25 2.21 0 4-1.79 4-4 0-.64-.17-1.24-.44-1.78l1.25-1.46c.36.16.76.25 1.19.25 1.65 0 3-1.35 3-3s-1.35-3-3-3Z"/>
																							</svg>
																					</div>
																					<label for="Turno" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Turno</label>
																			</div>
																			<select name="Turno" id="Turno"
																					class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all">
																					<option value='' selected>— Turno —</option>
																					<?php foreach($mTurno as $Var){?>
																						<option value='<?php echo $Var;?>'><?php echo $Var;?></option>
																					<?php }?>
																			</select>
																	</div>

																	<div class="col-span-4 bg-white rounded-xl border border-slate-200 p-4">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="bg-[#EEF4FF] p-2 rounded-lg flex-shrink-0">
																							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#2563eb" viewBox="0 0 24 24">
																									<path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8c.13 0 .26-.03.38-.08s.23-.12.33-.22l7-7c.09-.09.15-.19.2-.29l.03-.09c.03-.08.05-.17.05-.26 0-.02.01-.04.01-.06V6c0-1.1-.9-2-2-2m0 9h-6c-.55 0-1 .45-1 1v6H5V8h14z"/>
																							</svg>
																					</div>
																					<label for="Fecha" class="text-xs font-600 text-slate-500 uppercase tracking-wide">Fecha</label>
																			</div>
																			<input name="Fecha" type="text" id="Fecha"
																					class="text-sm w-full px-3 py-2 bg-slate-50 text-slate-700 rounded-lg border border-slate-200 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all"
																					onBlur="ValidarFecha(this);" autocomplete="off" placeholder="Fecha" readonly>
																	</div>

															</div>

															<!-- Columna derecha: Vigilantes + Hora -->
															<div class="col-span-4 flex flex-col gap-4">

																 	<!-- Toggle para un mismo vigilante -->
																	<div class="flex items-center gap-x-3 p-3 rounded-lg border border-emerald-200 bg-white">
																			<div class="flex items-center gap-3">
																					<label class="relative inline-flex items-center cursor-pointer" id="LblSameVigilante" for="CheckSameVigilante">
																							<!-- Input oculto pero funcional -->
																							<input type="checkbox" id="CheckSameVigilante" onclick="HbilitarMismoVigilante(this)" name="CheckSameVigilante" class="sr-only peer">
																							<!-- El Riel del Toggle -->
																							<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
																							<!-- Texto del Label -->
																					</label>
																			</div>
																			<div>
																					<p class="font-medium text-sm text-slate-700">Habilitar un mismo vigilante quien recibe y entrega el puesto</p>
																					<p class="text-[12px] text-gray-400">Al activar esta opción, el mismo vigilante será asignado en ambas posiciones.</p>
																			</div>
																	</div>

																	<!-- Vigilante Saliente -->
																	<div class="bg-white rounded-xl border border-slate-200 border-l-[3px] border-l-[#10b981] p-4" id="DivVigilanteSaliente">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="w-8 h-8 bg-[#d1fae5] rounded-full flex items-center justify-center flex-shrink-0">
																							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#047857" viewBox="0 0 24 24">
																									<path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5m0-8c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3M4 22h16c.55 0 1-.45 1-1v-1c0-3.86-3.14-7-7-7h-4c-3.86 0-7 3.14-7 7v1c0 .55.45 1 1 1m6-7h4c2.76 0 5 2.24 5 5H5c0-2.76 2.24-5 5-5"/>
																							</svg>
																					</div>
																					<div>
																							<p class="text-xs font-600 text-[#047857] uppercase tracking-wide leading-none mb-0.5" id="ParrafoVigilanteSaliente">Vigilante saliente</p>
																							<p class="text-xs text-[#6ee7b7] leading-none" id="ParrafoEntregaPuesto">Entrega el puesto</p>
																					</div>
																			</div>
																			<input name="VigilanteSaliente" type="text" id="VigilanteSaliente"
																					class="text-sm w-full px-3 py-2 bg-[#f0fdf4] text-slate-700 rounded-lg border border-[#a7f3d0] outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition-all"
																					autocomplete="off" placeholder="Nombre del vigilante saliente">
																	</div>

																	<!-- Vigilante Entrante -->
																	<div class="bg-white rounded-xl border border-slate-200 border-l-[3px] border-l-[#f59e0b] p-4" id="DivVigilanteEntrante">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="w-8 h-8 bg-[#fef3c7] rounded-full flex items-center justify-content flex-shrink-0 flex items-center justify-center">
																							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#92400e" viewBox="0 0 24 24">
																									<path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5m0-8c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3M4 22h16c.55 0 1-.45 1-1v-1c0-3.86-3.14-7-7-7h-4c-3.86 0-7 3.14-7 7v1c0 .55.45 1 1 1m6-7h4c2.76 0 5 2.24 5 5H5c0-2.76 2.24-5 5-5"/>
																							</svg>
																					</div>
																					<div>
																							<p class="text-xs font-600 text-[#92400e] uppercase tracking-wide leading-none mb-0.5">Vigilante entrante</p>
																							<p class="text-xs text-[#fcd34d] leading-none">Recibe el puesto</p>
																					</div>
																			</div>
																			<input name="VigilanteEntrante" type="text" id="VigilanteEntrante"
																					class="text-sm w-full px-3 py-2 bg-[#fffbeb] text-slate-700 rounded-lg border border-[#fde68a] outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-all"
																					autocomplete="off" placeholder="Nombre del vigilante entrante">
																	</div>

																	<!-- Hora Inicio Turno -->
																	<div class="bg-white rounded-xl border border-slate-200 border-l-[3px] border-l-[#8b5cf6] p-4">
																			<div class="flex items-center gap-2 mb-3">
																					<div class="w-8 h-8 bg-[#ede9fe] rounded-full flex items-center justify-center flex-shrink-0">
																							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#5b21b6" viewBox="0 0 24 24">
																									<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2M12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8m.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
																							</svg>
																					</div>
																					<div>
																							<p class="text-xs font-600 text-[#5b21b6] uppercase tracking-wide leading-none mb-0.5">Hora inicio turno</p>
																							<p class="text-xs text-[#c4b5fd] leading-none">Hora de inicio registrada</p>
																					</div>
																			</div>
																			<input name="HoraInicio" type="text" id="HoraInicio"
																					class="text-sm w-full px-3 py-2 bg-[#f5f3ff] text-slate-700 rounded-lg border border-[#ddd6fe] outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all"
																					autocomplete="off" placeholder="Hora inicio turno">
																	</div>

															</div>
													</div>

											</form>
									</div>

									 <!-- tabla de lista -->
										<div class="tab mt-8">
											
											<div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100 shadow-sm">
												<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
													<!-- Título con icono -->
													<div class="flex items-center gap-3">
														<div class="bg-blue-500 p-3 rounded-lg shadow-md">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ffffff" viewBox="0 0 24 24">
																<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
															</svg>
														</div>
														<div>
															<h3 class="font-bold text-gray-800 text-lg">Lista de Chequeo</h3>
															<p class="text-sm text-gray-500">Verificación de sede según puesto asumido</p>
														</div>
													</div>
													<!-- Botón mejorado -->
													<button onclick="MonstrarElementosVacios()" class="w-full md:w-auto cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 hover:from-blue-700 hover:to-blue-800 px-6 py-2.5 rounded-lg text-white font-semibold flex items-center justify-center gap-2 shadow-lg hover:shadow-xl active:scale-95 transition-all duration-200">
														<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
															<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
														</svg>
														<span>Todos Los Elementos</span>
													</button>
												</div>
											</div>

											<form method=post enctype="multipart/form-data" name='Frm1' id='Frm1'>
												<input name="TipoGrabar" type="hidden" id="TipoGrabar">
                    		<input name="TipoModificar" type="hidden" id="TipoModificar">
                    		<input name="IDMinuta" type="hidden" id="IDMinuta">
												<div class="mt-4 rounded-t-xl border border-gray-200 text-sm text-gray-600">
													<table class="table table-striped table-bordered">
														<thead class="bg-gray-50 border-b border-gray-200 rounded-lg shadow-xs">
																<tr>
																	<th width="8%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Item</th>
																	<th width="16%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center" title="Espacios y/o elementos expuestos (Visuales)">Descripción espacios</th>
																	<th width="6%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Cantidad Existente</th>
																	<th width="6%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Cantidad Verificada</th>
																	<th width="36%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center" title="Observaciones sobre la verificación de puesto">Observación</th>
																</tr>
															</thead>
															<tbody id="TBodyListaChequeo">
															</tbody>
													</table>
												</div>
											</form>
										</div>

										<!-- Requisa a vigilante saliente e infraestructura -->
										<div class="tab shadow-md rounded-lg p-6 bg-white mt-6 border border-gray-200 space-y-8">
											<form method="post" enctype="multipart/form-data" name="Frm2" id="Frm2">
												<input name="TipoGrabar" type="hidden" id="TipoGrabar">
												<input name="TipoModificar" type="hidden" id="TipoModificar">
												<input name="IDMinuta" type="hidden" id="IDMinuta">

												<!-- Observaciones a infraestructura -->
												<div class="space-y-3">
													<h3 class="text-lg font-semibold text-blue-800 flex items-center gap-2">
														<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.5 1.5H3.75A2.25 2.25 0 001.5 3.75v12.5A2.25 2.25 0 003.75 18.5h12.5a2.25 2.25 0 002.25-2.25V9.5M6.5 6.5h7M6.5 10h7M6.5 13.5h3"/></svg>
														Observaciones a Infraestructura
													</h3>
													<div class="col-span-10">
														<label for="ObsInfraestructura" class="block text-sm font-medium text-gray-700 mb-2">Relacione cualquier novedad con respecto a la infraestructura.</label>
														<textarea name="ObsInfraestructura" id="ObsInfraestructura" rows="3" class="block w-full px-3 py-2 text-gray-700 rounded-lg border border-gray-300 shadow-sm placeholder:text-gray-400 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" placeholder="Describa las observaciones de infraestructura..." maxlength="250"></textarea>
													</div>
												</div>

												<!-- Requisa a vigilante saliente -->
												<div class="space-y-4 pt-6 border-t border-gray-200">
													<h3 class="text-lg font-semibold text-blue-800 flex items-center gap-2">
														<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
														Requisa a Vigilante Saliente
													</h3>

													<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-10 gap-4">
														<!-- Requisa realizada -->
														<div class="col-span-1 md:col-span-3 lg:col-span-3">
															<label for="RealizaRequisa" class="block text-sm font-medium text-gray-700 mb-2">¿Requisa realizada?</label>
															<select name="RealizaRequisa" id="RealizaRequisa" class="block w-full px-3 py-2 text-gray-700 rounded-lg border border-gray-300 shadow-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all">
																<option value="1">✓ Sí</option>
																<option value="-1">✗ No</option>
															</select>
														</div>

														<!-- Observaciones de requisa -->
														<div class="col-span-1 md:col-span-3 lg:col-span-5">
															<label for="ObsRequisa" class="block text-sm font-medium text-gray-700 mb-2">Observaciones de la requisa</label>
															<input name="ObsRequisa" type="text" id="ObsRequisa" class="block w-full px-3 py-2 text-gray-700 rounded-lg border border-gray-300 shadow-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" placeholder="Ingrese observaciones...">
														</div>

														<!-- Hora Finaliza Recorrido -->
														<div class="col-span-1 md:col-span-1 lg:col-span-2">
															<label for="HoraFinalizaRecorrido" class="block text-sm font-medium text-gray-700 mb-2">Hora de Finalización</label>
															<input name="HoraFinalizaRecorrido" type="text" id="HoraFinalizaRecorrido" onBlur="ValidarHora(this);" maxlength="5" autocomplete="off" class="block w-full px-3 py-2 text-gray-700 rounded-lg border border-gray-300 shadow-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all" placeholder="HH:MM">
														</div>
													</div>

													<!-- Checkbox Aceptar Terminar Minuta -->
													<div class="flex items-start gap-4 mt-6 p-4 rounded-lg bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 border-l-4 border-l-blue-600" id="DivAceptoTerminarMinuta">
														<label class="relative inline-flex items-center cursor-pointer" id="LblAceptoTerminar" for="CheckAceptoTerminar">
																	<!-- Input oculto pero funcional -->
																	<input type="checkbox" id="CheckAceptoTerminar" name="CheckAceptoTerminar" class="sr-only peer">
																	<!-- El Riel del Toggle -->
																	<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
																	<!-- Texto del Label -->
																</label>
														<div class="flex-1">
															<p class="font-semibold text-gray-800">Aceptar Creación de Minuta</p>
															<p class="text-sm text-gray-600 mt-1">Al activar esta opción, la minuta quedará lista para agregar rotaciones y novedades.</p>
														</div>
													</div>
												</div>
											</form>
										</div>

								</div>

                <div class="flex gap-3 pt-4">
                  <button type="button" id="prevBtn" onClick="nextPrev(-1)"
                    class="hidden flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
                    Anterior
                  </button>
									<button  type="button" id="nextBtn" onClick="nextPrev(1)"
										class="flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span id="btnEditarMinuta">Grabar Y Siguiente</span>
									</button>
                </div>

            	</div>
          </div>
        </div>

      </div>
  </div>
