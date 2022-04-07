@extends('layout')

@section('title', 'Lista de Estudiantes')

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <br>
        <h1 class="lead"> Lista de Estudiantes </h1>
        <hr>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Master</th>
                    <th>Lugar de nacimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td> {{ $student->nombre }}</td>
                    <td> {{ $student->master }}</td>
                    <td> {{ $student->lugarnacimiento }}</td>
                    <td>
                        <a class="btn btn-info" href="{{ url('students/'.$student->numero) }}" target="_blank">
                            Crear pdf
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
@endsection
