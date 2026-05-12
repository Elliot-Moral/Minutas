<script type="text/javascript">
function CambioSucursal(mSucursal){
	mObIDPuestoSucursal=document.getElementById("IDPuestoSucursal");
	if(!mObIDPuestoSucursal){
		return
	}
	mObIDPuestoSucursal.classList.remove("hidden");
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
	FiltrarPuestoSucursalElemento();
}
function FiltrarPuestoSucursalElemento(){
	const mSucursal=document.getElementById('Sucursal').value;
	const mIDPuestoSucursal=document.getElementById('IDPuestoSucursal').value;
	const mFiltroElemento=document.getElementById("FiltroElemento");

	if(mIDPuestoSucursal !=''){
		mFiltroElemento.classList.remove("hidden");
	}

	if(mSucursal && mIDPuestoSucursal){
		$('#TBodyPuestoSucursalElemento').load("index.php?TipoModificar=<?php echo md5('Ajax1JorA5PuestoSucursalElemento'.date('d'));?>&Sucursal="+mSucursal+"&IDPuestoSucursal="+mIDPuestoSucursal,
												function() {
													MostrarSoloActivos(0);
												});
	}else{
		document.getElementById('TBodyPuestoSucursalElemento').innerHTML = `
    <tr>
        <td colspan="100%" style="text-align: center; vertical-align: middle; padding: 20px; color: #6c757d;">
            <i class="bi bi-info-circle"></i> No se encontraron registros
        </td>
    </tr>`;
	}
}

function FiltrarElemento(){
	mSucursal=document.getElementById("Sucursal").value;
	console.log(mSucursal);
	if(!mSucursal){
		document.getElementById('TBodyPuestoSucursalElemento').innerHTML = `
    <tr>
        <td colspan="100%" style="text-align: center; vertical-align: middle; padding: 20px; color: #6c757d;">
            <i class="bi bi-info-circle"></i> No se encontraron registros
        </td>
    </tr>`;
	}

	const mFiltroElemento=document.getElementById("FiltroElemento").value.toLowerCase();
	$("#TBodyPuestoSucursalElemento").find("tr").each(function(){
		const mTextoElemento=$(this).find("td:eq(5)").text().toLowerCase();
		if(mTextoElemento.includes(mFiltroElemento)){
			$(this).show();
		}else{
			$(this).hide();
		}
	});
}

function MostrarSoloActivos(mCambiar){
	if(mCambiar){//Es porque dieron click para mostrar solo activos o todos, el texto del TH cambia
		if(document.getElementById('THSoloActivos').innerHTML=='Todos'){
			document.getElementById('THSoloActivos').innerHTML='Activos';
			document.getElementById('THSoloActivos').title='Click para Mostrar TODOS';
			$("#TBodyPuestoSucursalElemento").find("tr").each(function(){
				if($(this).find("input[type='checkbox']").is(":checked")){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
		}else{
			document.getElementById('THSoloActivos').innerHTML='Todos';
			document.getElementById('THSoloActivos').title='Click para Mostrar Solo Activos';
			$("#TBodyPuestoSucursalElemento").find("tr").show();
		}
	}else{//Se invoca desde otra función, por tanto el texto del TH no se cambia
		if(document.getElementById('THSoloActivos').innerHTML=='Todos'){
			$("#TBodyPuestoSucursalElemento").find("tr").show();
		}else{
			$("#TBodyPuestoSucursalElemento").find("tr").each(function(){
				if($(this).find("input[type='checkbox']").is(":checked")){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
		}
	}
	//RenumerarEstudiantes("TBodyMasEstudiantes");
}
function EnviarPuestoSucursalElemento(Obj,mIDElemento,mIDElementoPuestoSucursal){
	mIDPuestoSucursal=document.getElementById('IDPuestoSucursal').value;
	var myData = {};
	if(Obj.id.substr(0,7)=='Borrada'){//Pues es el Check
		if(Obj.checked){//Ojo que unque deje el nombre del campo como Borrada se refiere a que está activo o no, por tanto el concepto es contrario
			document.getElementById(Obj.name.replace("Borrada", "Cantidad")).disabled=false;
			myData.Borrada=0;
		}else{
			document.getElementById(Obj.name.replace("Borrada", "Cantidad")).disabled=true;
			myData.Borrada=1;
		}
	}else if(Obj.id.substr(0,8)=='Cantidad'){//Pues es la Cantidad
		if(isNaN(parseInt(Obj.value,10))){
			Obj.value="";
			myData.Cantidad=0;
		}else{
			myData.Cantidad=parseInt(Obj.value,10);
		}
	}
	if(mIDElementoPuestoSucursal || (mIDElemento && mIDPuestoSucursal)){
		myData.TipoGrabar = 'A5';
		myData.TipoModificar = '<?php echo md5('JorA5'.date('d'));?>';
		myData.IDElemento = mIDElemento;
		myData.IDPuestoSucursal = mIDPuestoSucursal;
		myData.IDElementoPuestoSucursal = mIDElementoPuestoSucursal;
		$.ajax({
			url:'index.php',
			type:'post',
			cache: false,
			data:myData
		}).done(function(html){
			if(html){
				MostrarDatoObser(html);
			}else{
				Swal.fire({
          toast: true,
          position: "top-end",
          icon: "success",
          title: 'Dato guardados',
          showConfirmButton: false,
          timer: 3000
				});
			}
		});
	}
}
</script>
<section class="h-screen col-span-9 md:col-span-8 bg-gray-50 p-4 lg:p-8 S max-h-screen">

	<h1 class="text-2xl font-bold mb-0 text-gray-600">Asignar elementos por puestos por sucursal</h1>
  <hr>

	<div class="flex  gap-2 mt-8">
		<div class="relative">
				<button id="" data-dropdown-toggle="dropdown" class="shrink-0 rounded-xl inline-flex items-center justify-center text-gray-500 bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-2 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-3 py-2 focus:outline-none" type="button">
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
			<select name="Sucursal" type="text" class="block w-full max-w-64 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all"  
				id="Sucursal" placeholder="Filtrar Elemento" onChange="CambioSucursal(this.value);">
				<option value= '' selected>--Sucursal--</option><?php
					$Queri = "SELECT Sucursal, NomSucursal
						FROM ".$PrefBD."novasoft.sucursal
						WHERE Sucursal<>'0'
						ORDER BY Sucursal";
						$Result = $mysqli->query($Queri) or die(mysqli_error($mysqli));
						while($Row = $Result->fetch_assoc()){?>
				<option value= '<?php echo $Row['Sucursal'];?>'><?php echo $Row['Sucursal'].' '.$Row['NomSucursal'];?></option><?php
				}?>
			</select>
		</div>
		<div>
			<select name="IDPuestoSucursal" id="IDPuestoSucursal" class="hidden block w-full max-w-98 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all"  
			onChange="FiltrarPuestoSucursalElemento();">
			</select>
		</div>
		<div>
			<input name="FiltroElemento" type="text" class="hidden block w-full max-w-64 ps-3 pe-3 py-2 text-gray-500 rounded-lg border border-default-medium text-heading text-sm shadow-xs placeholder:text-body outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all"  
			id="FiltroElemento" placeholder="Filtrar Elemento" onChange="FiltrarElemento();">
		</div>
		<div>
				<button onClick="FiltrarElemento();"
					class="cursor-pointer bg-gradient-to-br from-blue-700 to-blue-400 px-4 py-2 rounded-lg text-white hover:shadow-lg active:scale-95 transition-all font-semibold flex items-center gap-x-2">
					<svg  xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="#ffffff" viewBox="0 0 24 24" >
						<path d="M10.5 19c1.98 0 3.81-.69 5.25-1.83L20 21.42l1.41-1.41-4.25-4.25a8.47 8.47 0 0 0 1.83-5.25c0-4.69-3.81-8.5-8.5-8.5S2 5.81 2 10.5 5.81 19 10.5 19m0-15c3.58 0 6.5 2.92 6.5 6.5S14.08 17 10.5 17 4 14.08 4 10.5 6.92 4 10.5 4"></path>
					</svg>
				</button>
		</div>
	</div>

	<div class="mt-4 rounded-t-xl border border-gray-200 text-sm text-gray-600 overflow-x-auto h-[74vh] md:h-[72vh]">
		<table class="table table-striped table-bordered">
			<thead class="bg-gray-100  rounded-t-lg shadow-xs sticky top-0 z-10">
				<tr>
					<th width="4%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">N°</th>
					<th width="4%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">ID</th>
					<th width="18%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Sucursal</th>
					<th width="20%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Puesto</th>
					<th width="10%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Grupo</th>
					<th width="31%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Elemento</th>
					<th width="8%" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Cantidad</th>
					<th width="5%" id="THSoloActivos" onclick="MostrarSoloActivos(1);" title="Click para Mostrar Solo Activos" class="p-3 text-left text-sm font-bold text-gray-700 tracking-wider text-center">Todos</th>
				</tr>
			</thead>
    	<tbody id="TBodyPuestoSucursalElemento">
				<tr class="bg-white border-t border-gray-200" align=left>
					<td class="p-2"></td>
					<td class="p-2"></td>
					<td class="p-2"></td>
					<td class="p-2"></td>
					<td class="p-2"></td>
					<td class="p-2"></td>
					<td class="p-2"></td>
					<td class="p-2"></td>
				</tr>
			</tbody>
    </table>
	</div>
</section>