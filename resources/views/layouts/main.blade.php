<!DOCTYPE>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>@yield('TITLE', 'Gestor de tareas')</title>
		<link rel="stylesheet" href="{{ asset('css/button_style.css') }}">
		<link rel="stylesheet" href="{{ asset('css/estructura.css') }}">
		<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">

	</head>
	
	<body>		
		<main>
			<h1>GESTOR DE TAREA</h1>
			<section class="formulario_tarea">
				<!-- Inputs ocultos tipo radio para simular el cambio de pestañas -->
				<input type="radio" id="tab1" name="tabs" {{ $tab == 1 ? 'checked' : '' }}>
				<input type="radio" id="tab2" name="tabs" {{ $tab == 2 ? 'checked' : '' }}>

				<div class="tabs-header">
					<label for="tab1">Nueva Tarea</label>
					<label for="tab2">Modificar Tarea</label>
				</div>

				<!-- Sección de Nueva Tarea (Formulario de Creación) -->
				<div class="nuevo">
					<form action="{{ route('tareas.store') }}" method="post">
						@csrf
						<section class="registrar_la_tarea">
							<h2> Crear nueva tarea </h2>
							<article class="contenido-columna">
								<label for="titulo_de_la_tarea">Titulo:</label>
								<input type="text" name="titulo" id="titulo_de_la_tarea" placeholder="Escribe el nombre de la tarea" required>
							</article>

							<article class="contenido-columna">
								<label for="descripcion_de_la_tarea">Descripción:</label>
								<textarea name="descripcion" id="descripcion_de_la_tarea" placeholder="Desarrolle el contenido" required></textarea>
							</article>

							<article class="contenido-columna">
								<label for="prioridad">Prioridad:</label>
								<select name="prioridad" id="prioridad">
									<option value="baja">Seleccione prioridad</option>
									@foreach($prioridades as $key => $value)
										<option value="{{ $key }}">{{ ucfirst($value) }}</option>
									@endforeach
								</select>
							</article>

							<article class="contenido-columna">
								<label for="fecha_limite">Fecha límite:</label>
								<input type="date" name="fecha_limite" id="fecha_limite" required>
							</article>

							<button type="submit" class="guardar">Guardar tarea</button>
						</section>
					</form>
				</div>

				<!-- Sección de Modificar Tarea (Formulario de Edición) -->
				<div class="modificar">
					@if($tarea)
						<form action="{{ route('tareas.update', $tarea->id) }}" method="post">
							@csrf 
							@method('PUT') 
							<section class="registrar_la_tarea">
								<h2> Modificar Tarea </h2> 
								
								<article class="contenido-columna">
									<label for="titulo_edit">Titulo:</label>
									<input type="text" name="titulo" id="titulo_edit" value="{{ $tarea->titulo }}" required>
								</article>

								<article class="contenido-columna">
									<label for="desc_edit">Descripción:</label>
									<textarea name="descripcion" id="desc_edit" required>{{ $tarea->descripcion }}</textarea>
								</article>

								<article class="contenido-columna">
									<label for="prioridad_edit">Prioridad:</label>
									<select name="prioridad" id="prioridad_edit">
										@foreach($prioridades as $key => $value)
											<option value="{{ $key }}" {{ $tarea->prioridad == $key ? 'selected' : '' }}>
												{{ ucfirst($value) }}
											</option>
										@endforeach
									</select>
								</article>

								<article class="contenido-columna">
									<label for="fecha_limite_edit">Fecha límite:</label>
									<input type="date" name="fecha_limite" id="fecha_limite_edit" value="{{ $tarea->fecha_limite }}" required>
								</article>

								<article class="contenido-columna">
									<label>¿Realizada?:</label>
									<input type="checkbox" name="estado" value="completada" {{ $tarea->estado == 'completada' ? 'checked' : '' }} style="width: 20px; height: 20px;">
								</article>

								<button type="submit" class="guardar">Actualizar Tarea</button>
							</section>
						</form>
					@else
						<p class="mensaje-vacio">Seleccione una tarea en la lista y haga clic en "Editar" para modificarla.</p>
					@endif
				</div>
			</section>

			<!-- Tabla de registros -->
			<section class="tarea_registrada">
				<article>
					<h2> Lista de tareas </h2>
					<div class="contenido-tabla">
						<table>
							<thead>
								<tr>
									<th>Id</th>
									<th>Titulo</th>
									<th>Descripción</th>
									<th>Prioridad</th>
									<th>Fecha Límite</th>
									<th>Estado actual</th>
									<th>Completada el</th>
									<th>Cambiar Estado</th>
									<th>Acción</th>
								</tr>
							</thead>
							<tbody>
								@forelse($tareas as $item)
									<tr> 
										<td>{{$item->id}}</td> 

										<!-- Añade una clase CSS para tachar el texto de las completadas -->
										<td class="{{ $item->estado == 'completada' ? 'completada' : '' }}">
											{{$item->titulo}}
										</td> 

										<td>{{$item->descripcion}}</td> 

										<td>{{ ucfirst($item->prioridad) }}</td>

										<td>{{ $item->fecha_limite ? date('d/m/Y', strtotime($item->fecha_limite)) : '-' }}</td>

										<td>{{ ucfirst(str_replace('_', ' ', $item->estado)) }}</td> 

										<td>{{ $item->completada_el ? date('d/m/Y', strtotime($item->completada_el)) : '-' }}</td>

										<!-- Selector interactivo para cambiar estado con un solo click -->
										<td>
											<form action="{{ route('tareas.cambiarEstado', $item->id) }}" method="post" style="display: inline;">
												@csrf
												@method('PATCH')
												<select name="estado" onchange="this.form.submit()" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer;">
													<option value="pendiente" {{ $item->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
													<option value="en_progreso" {{ $item->estado == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
													<option value="completada" {{ $item->estado == 'completada' ? 'selected' : '' }}>Completada</option>
													<option value="cancelada" {{ $item->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
												</select>
											</form>
										</td>

										<td class="botones_de_mi_tabla">
											<a href="{{ route('tareas.edit',$item->id) }}" class="btn btn-editar">Editar</a>

											<form action="{{ route('tareas.destroy', $item->id) }}" method="post" style="display: inline;">
												@csrf 
												@method('DELETE')
												<button class="btn btn-eliminar" onclick="return confirm('¿Estas seguro?')">Eliminar</button> 
											</form>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="9">No hay tareas pendientes</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</article>
			</section>
		</main>
	</body>
</html>