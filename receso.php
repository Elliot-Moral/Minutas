<style>
#DivReceso{
	border: 3px solid;
	border-color:#888;
	left:25%;
	width: 50%;
	height: auto;
	z-index:1;
	background:#fff;
	overflow:auto;
	border-radius:14px;
	padding:8px;
}
</style>
<script type="text/javascript">

let estilos = {
	requeired: ["border-red-500", "bg-red-100", "text-red-700", "focus:ring-red-500", "focus:border-red-500", "placeholder-red-700"]
}

$(function(){
	$('#DivReceso').css({position : 'absolute'});
});
function FiltrarReceso(){
	mFiltro = document.getElementById('FiltroReceso').value.toUpperCase();
	if(mFiltro){
		$("#TBodyReceso").find("tr").each(function(){
			if((mFiltro ? ($(this).find("td:eq(1):contains('"+mFiltro+"')").length) : true)){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	}else{
		$("#TBodyReceso").find("tr").show();
	}
}
function MostrarDivReceso(mIDReceso){
	$("#FrmReceso").find("input:text,select,textarea").each(function(){
		this.classList.remove(...estilos.requeired);
	});
	document.getElementById('IDReceso').value = mIDReceso;
	if(mIDReceso=='Nuevo'){
		document.getElementById('Receso').value='';
		document.getElementById('Borrada').value=0;
	}else{
		$.ajax({
			type: "GET",
			url: 'index.php',
			data: 'TipoModificar=<?php echo md5('Ajax1JorA6Receso'.date('d'));?>&IDReceso='+mIDReceso,
			cache: false,
			dataType: 'json',
			success: function(data){ //Si se ejecuta correctamente
				if(data.Mensaje=="Error"){
					Swal.fire({
              toast: true,
              position: "top-end",
              icon: "error",
              title: 'Se presento un error al cargar los datos',
              showConfirmButton: false,
              timer: 3000
					});
				}else{
					document.getElementById('Receso').value=data.Receso;
					document.getElementById('Borrada').value=(data.Borrada==1 ? 1 : 0);
				}
			},
			error: function(data){
				Swal.fire({
              toast: true,
              position: "top-end",
              icon: "error",
              title: 'Se presento un error',
              showConfirmButton: false,
              timer: 3000
				});
				return false;
			}
		});
	}
	MostrarDivModales('Receso');
}
function EnviarReceso(){//Enviar los datos del pago en un ajax
	mRetorno=true;
	ele=document.getElementById('Receso');if(ele.value){ele.classList.remove(...estilos.requeired);}else{ele.classList.add(...estilos.requeired);mRetorno=false;}
	if(mRetorno){
		$("#BotsReceso").hide();
		document.getElementById('FrmReceso').TipoGrabar.value='A6';
		document.getElementById('FrmReceso').TipoModificar.value='<?php echo md5('JorA6'.date('d'));?>';
		var myData = $("#FrmReceso").serialize();
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data:myData
		}).done(function(html){
			if(html=='Hecho'){
				Swal.fire({
              toast: true,
              position: "top-end",
              icon: "success",
              title: 'Los datos se grabaron correctamente.',
              showConfirmButton: false,
              timer: 3000
				});
				setTimeout("location.reload()", 500);
			}else{
				Swal.fire({
					title: 'Error',
					text: html,
					icon: 'error',
					confirmButtonText: 'Aceptar',
					confirmButtonColor: '#0e69ca'
				})
			}
		});
	}else{
		$("#BotsReceso").show();
		Swal.fire({
        toast: true,
        position: "top-end",
        icon: "info",
        title: 'Los campos resaltados son obligatorios, o presentan algún inconveniente.',
        showConfirmButton: false,
        timer: 3000
			});
	}
}
</script>


<section class="h-screen md:col-span-8 bg-gray-50 p-4 lg:p-8 S max-h-screen"><?php
	$Queri = "SELECT *
				FROM ".$PrefBD."solicitudes.vigilanciareceso
				ORDER BY IDReceso";
	$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));?>

	<h1 class="text-2xl font-bold mb-0 text-gray-600">Administrar Tipos de Recesos</h1>
  <hr>

	<div class="grid grid-cols-2 md:flex gap-2 mt-8">
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
		<div>
			<input name="FiltroReceso" type="text" class="block w-full md:max-w-64 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all"  
			id="FiltroReceso" placeholder="Filtrar Receso" onChange="FiltrarReceso();">
		</div>
		<div>
				<button onClick="FiltrarReceso();"
					class="w-full md:w-fit cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-2 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
					<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
						<path d="M10.5 19c1.98 0 3.81-.69 5.25-1.83L20 21.42l1.41-1.41-4.25-4.25a8.47 8.47 0 0 0 1.83-5.25c0-4.69-3.81-8.5-8.5-8.5S2 5.81 2 10.5 5.81 19 10.5 19m0-15c3.58 0 6.5 2.92 6.5 6.5S14.08 17 10.5 17 4 14.08 4 10.5 6.92 4 10.5 4"></path>
					</svg>
				</button>
		</div>
		<div>
				<div onClick="MostrarDivReceso('Nuevo');"
					class="w-full md:w-fit cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-2 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
					<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
						<path d="M3 13h8v8h2v-8h8v-2h-8V3h-2v8H3z"></path>
					</svg>
				</div>
		</div>
	</div>

	<div class="mt-4 rounded-t-xl border border-gray-200 text-sm text-gray-600 overflow-x-auto h-[72vh] md:h-[72vh]">
		<table class="table table-striped table-bordered">
			<thead class="bg-gray-100  rounded-t-lg shadow-xs sticky top-0 z-10">
				<tr>
					<th width="10%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">ID</th>
					<th width="80%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Tipo Receso</th>
					<th width="10%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Estado</th>
					<th width="10%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Acciones</th>
				</tr>
			</thead>
			<tbody id="TBodyReceso">
				<?php
					while($Row = $Result->fetch_assoc()){?>
					<tr class="bg-white border-t border-gray-200">
						<td class="p-2 text-center" left>
							<div class="inline-flex items-center gap-3 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg group hover:bg-white transition-colors">
							<div class="flex flex-col">
								<span class="text-[12px] font-mono font-bold text-slate-700 leading-none tracking-tight">
									<?php echo $Row['IDReceso'];?>
								</span>
							</div>
						</div>
						</td>
						<td class="p-2 text-center"><?php echo $Row['Receso'];?></td>
						<td class="p-2 text-center" align="center">						
							<?php echo ($Row['Borrada']==1 ? ('<span class="text-red-700 bg-red-100 border border-red-200 rounded-lg px-2 ">Inactivo</span>') : ('<span class="text-green-700 bg-green-100 border border-green-200 rounded-lg px-2 ">Activo</span>'));?>
						</td>
						<td class="p-2 flex justify-center items-center" align="center">
							<?php
								if($PuedeAdministrar){?>
									<span onClick="MostrarDivReceso(<?php echo $Row['IDReceso'];?>);" class="cursor-pointer group" title="Editar registro">
											<svg xmlns="http://www.w3.org/2000/svg" 
													width="20" 
													height="20" 
													viewBox="0 0 24 24" 
													class=" text-blue-400 hover:text-blue-500 transition-colors duration-200 fill-current">
												<path d="M5 21h14c1.1 0 2-.9 2-2v-7h-2v7H5V5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2"></path><path d="M7 13v3c0 .55.45 1 1 1h3c.27 0 .52-.11.71-.29l9-9a.996.996 0 0 0 0-1.41l-3-3a.996.996 0 0 0-1.41 0l-9.01 8.99A1 1 0 0 0 7 13m10-7.59L18.59 7 17.5 8.09 15.91 6.5zm-8 8 5.5-5.5 1.59 1.59-5.5 5.5H9z"></path>
											</svg>
									</span><?php
							}?>
						</td>
					</tr><?php
				}//fin del while?>
			</tbody>
		</table>
	</div>
</section>


<!-- MODAL RECESO -->
 <div id="modal-Receso" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 font-normal">
    <div id="modal-Receso-backdrop" class="modal-backdrop absolute inset-0 bg-black/30 " onclick="CerrarDivModales('Receso')"></div>
    <div id="modal-Receso-container" class="relative bg-white rounded-2xl shadow-2xl w-full md:max-w-[40%] overflow-hidden">
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
                        <h3 id="modal-Receso-title" class="text-xl">Editar | Crear Receso</h3>
                        <p class="text-blue-100 text-sm"></p>
                    </div>
                </div>

                <button onclick="CerrarDivModales('Receso')" class="hover:bg-white/20 p-2 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

				<div class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
            <div class="grid grid-cols-1 gap-4">

							<!-- Formulario de crear/editar Receso -->
							<div class="">
									<form method="post" enctype="multipart/form-data" name="FrmReceso" id="FrmReceso" >
										<!-- Inputs Ocultos -->
									<input name="TipoGrabar" type="hidden" id="TipoGrabar">
									<input name="TipoModificar" type="hidden" id="TipoModificar">
									<input name="IDReceso" type="hidden" id="IDReceso">

										<div class="flex flex-col gap-4">

											<div class="md:col-span-4 flex flex-col gap-1.5 text-left">
												<label for="Receso" class="">Descripción del Tipo de Receso</label>
												<input name="Receso" type="text" id="Receso"
													class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
											</div>

											<div>
												<label for="Borrada">Activo:</label>
													<select name="Borrada" id="Borrada" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
														<option value= '1' selected>No</option><!--Ojo que como el texto dice Activo?, el criterio es al contrario-->
														<option value= '0' selected>Si</option>
													</select>
											</div>

										</div>
								</form>
							</div>
						</div>

						<div class="flex gap-3 pt-4" id="BotsReceso">
								<button type="button" onclick="CerrarDivModales('Receso')"
									class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all active:scale-95">
									Cerrar
								</button>
								<button  type="button" id="BotAceptarReceso" onClick="javascript:return EnviarReceso();"
										class="bottonConfirmacion flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-400 hover:shadow-lg text-white font-semibold rounded-lg transition-all active:scale-95">
										<span>Guardar</span>
									</button>
						</div>
        </div>
    </div>
</div>