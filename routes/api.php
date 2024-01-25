<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LineaController;
use App\Http\Controllers\Api\PuestoController;
use App\Http\Controllers\Api\AlergiaController;
use App\Http\Controllers\Api\ArchivoController;
use App\Http\Controllers\Api\EscuelaController;
use App\Http\Controllers\Api\EstatusController;
use App\Http\Controllers\Api\EstudioController;
use App\Http\Controllers\Api\EmpleadoController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\PlantillaController;
use App\Http\Controllers\Api\RequisitoController;
use App\Http\Controllers\Api\AntiguedadController;
use App\Http\Controllers\Api\AsignacionController;
use App\Http\Controllers\Api\EnfermedadController;
use App\Http\Controllers\Api\ExpedienteController;
use App\Http\Controllers\Api\EscolaridadController;
use App\Http\Controllers\Api\EstadoCivilController;
use App\Http\Controllers\Api\MedicamentoController;
use App\Http\Controllers\Api\ConstelacionController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\TipoDeSangreController;
use App\Http\Controllers\Api\DesvinculacionController;
use App\Http\Controllers\Api\EstadoDeEstudioController;
use App\Http\Controllers\Api\TipoDeAsignacionController;
use App\Http\Controllers\Api\DocumentoQueAvalaController;
use App\Http\Controllers\Api\ExperienciaLaboralController;
use App\Http\Controllers\Api\ReferenciaPersonalController;
use App\Http\Controllers\Api\TipoDeDesvinculacionController;
use App\Models\Expediente;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('alergia/all', [AlergiaController::class, 'all']);
Route::get('antiguedad/all', [AntiguedadController::class, 'all']);
Route::get('archivo/all', [ArchivoController::class, 'all']);
Route::get('asignacion/all', [AsignacionController::class, 'all']);
Route::get('constelacion/all', [ConstelacionController::class, 'all']);
Route::get('departamento/all', [DepartamentoController::class, 'all']);
Route::get('desvinculacion/all', [DesvinculacionController::class, 'all']);
Route::get('documento/all', [DocumentoController::class, 'all']);
Route::get('documentoQueAvala/all', [DocumentoQueAvalaController::class, 'all']);
Route::get('empleado/all', [EmpleadoController::class, 'all']);
Route::get('enfermedad/all', [EnfermedadController::class, 'all']);
Route::get('escolaridad/all', [EscolaridadController::class, 'all']);
Route::get('escuela/all', [EscuelaController::class, 'all']);
Route::get('estadoCivil/all', [EstadoCivilController::class, 'all']);
Route::get('estadoDeEstudio/all', [EstadoDeEstudioController::class, 'all']);
Route::get('Estatus/all', [EstatusController::class, 'all']);
Route::get('estudio/all', [EstudioController::class, 'all']);
Route::get('expediente/all', [ExpedienteController::class, 'all']);
Route::get('experienciaLaboral/all', [ExperienciaLaboralController::class, 'all']);
Route::get('linea/all', [LineaController::class, 'all']);
Route::get('medicamento/all', [MedicamentoController::class, 'all']);
Route::get('plantilla/all', [PlantillaController::class, 'all']);
Route::get('puesto/all', [PuestoController::class, 'all']);
Route::get('referenciaPersonal/all', [ReferenciaPersonalController::class, 'all']);
Route::get('requisito/all', [RequisitoController::class, 'all']);
Route::get('sucursal/all', [SucursalController::class, 'all']);
Route::get('tipoDeAsignacion/all', [TipoDeAsignacionController::class, 'all']);
Route::get('tipoDeDesvinculacion/all', [TipoDeDesvinculacionController::class, 'all']);
Route::get('tipoDeSangre/all', [TipoDeSangreController::class, 'all']);
Route::get('estatus/all', [EstatusController::class, 'all']);
Route::get('user/all', [UserController::class, 'all']);

Route::get('/buscar-expediente/{tipoModelo}/{idModelo}', [ExpedienteController::class, 'buscarExpedientePorArchivable']);

Route::resource('alergia', AlergiaController::class)->except("create", "edit");
Route::resource('antiguedad', AntiguedadController::class)->except("create", "edit");
Route::resource('archivo', ArchivoController::class)->except("create", "edit");
Route::resource('asignacion', AsignacionController::class)->except("create", "edit");
Route::resource('constelacion', ConstelacionController::class)->except("create", "edit");
Route::resource('departamento', DepartamentoController::class)->except("create", "edit");
Route::resource('desvinculacion', DesvinculacionController::class)->except("create", "edit");
Route::resource('documento', DocumentoController::class)->except("create", "edit");

Route::post('documento/uploadFile/{documento}', [DocumentoController::class, 'uploadFile']);

Route::resource('documentoQueAvala', DocumentoQueAvalaController::class)->except("create", "edit");
Route::resource('empleado', EmpleadoController::class)->except("create", "edit");
Route::resource('enfermedad', EnfermedadController::class)->except("create", "edit");
Route::resource('escolaridad', EscolaridadController::class)->except("create", "edit");
Route::resource('escuela', EscuelaController::class)->except("create", "edit");
Route::resource('estadoCivil', EstadoCivilController::class)->except("create", "edit");
Route::resource('estadoDeEstudio', EstadoDeEstudioController::class)->except("create", "edit");
Route::resource('Estatus', EstatusController::class)->except("create", "edit");
Route::resource('estudio', EstudioController::class)->except("create", "edit");
Route::resource('expediente', ExpedienteController::class)->except("create", "edit");
Route::resource('experienciaLaboral', ExperienciaLaboralController::class)->except("create", "edit");
Route::resource('linea', LineaController::class)->except("create", "edit");
Route::resource('medicamento', MedicamentoController::class)->except("create", "edit");
Route::resource('plantilla', PlantillaController::class)->except("create", "edit");
Route::resource('puesto', PuestoController::class)->except("create", "edit");
Route::resource('referenciaPersonal', ReferenciaPersonalController::class)->except("create", "edit");
Route::resource('requisito', RequisitoController::class)->except("create", "edit");
Route::resource('sucursal', SucursalController::class)->except("create", "edit");
Route::resource('tipoDeAsignacion', TipoDeAsignacionController::class)->except("create", "edit");
Route::resource('tipoDeDesvinculacion', TipoDeDesvinculacionController::class)->except("create", "edit");
Route::resource('tipoDeSangre', TipoDeSangreController::class)->except("create", "edit");
Route::resource('estatus', EstatusController::class)->except("create", "edit");
Route::resource('user', UserController::class)->except("create", "edit");

Route::group(['middleware' => 'auth:sanctum'], function () {

});

//usuarios
Route::post('auth/login', [UserController::class, 'login']);
Route::post('auth/logout', [UserController::class, 'logout']);
