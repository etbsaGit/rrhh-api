<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Linea;
use App\Models\Puesto;
use App\Models\Estatus;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\Plantilla;
use App\Models\Escolaridad;
use App\Models\EstadoCivil;
use App\Models\Departamento;
use App\Models\TipoDeSangre;
use Illuminate\Http\Request;
use App\Traits\UploadableFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Empleado\PicRequest;
use App\Http\Requests\Empleado\PutRequest;
use App\Http\Requests\Empleado\StoreRequest;
use App\Models\Termination;

class EmpleadoController extends ApiController
{
    use UploadableFile;

    public function index()
    {
        return $this->respond(Empleado::paginate(5));
    }

    public function all()
    {
        return response()->json(Empleado::with(['archivable', 'archivable.requisito', 'escolaridad', 'departamento', 'estado_civil', 'jefe_directo', 'linea', 'puesto', 'sucursal', 'tipo_de_sangre', 'user', 'estatus', 'termination.estatus'])->get());
    }

    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return tap(
                Empleado::create($request->validated()),
                function (Empleado $empleado) use ($request) {
                    $plantilla = Plantilla::find(1);
                    $ids = $plantilla->requisito->pluck('id');

                    // Crea el expediente asociado al empleado
                    $expediente = $empleado->archivable()->create(['nombre' => $empleado->rfc . ' expediente']);

                    // Adjunta requisitos a través de la relación
                    $expediente->requisito()->syncWithPivotValues($ids, ['comentaio' => 'com 1']);

                    // Se le crea un usuario
                    $correo = $empleado->correo_institucional;
                    if ($correo) {
                        $usuario = User::firstOrCreate(
                            ['email' => $correo],
                            ['password' => Hash::make('password123'), 'name' => $empleado->nombre]
                        );
                        if (!$empleado->user) {
                            $empleado->user()->associate($usuario);
                            $empleado->save();
                            $empleado->user->syncRoles('Empleado');
                        }
                    }

                    // Verifica si el request contiene 'desvinculacion'
                    if ($request->has('desvinculacion')) {
                        $desvinculacionData = $request->input('desvinculacion');
                        Termination::create([
                            'reason_id' => $desvinculacionData['reason_id'],
                            'estatus_id' => $desvinculacionData['estatus_id'],
                            'date' => $desvinculacionData['date'],
                            'comments' => $desvinculacionData['comments'] ?? null,
                            'empleado_id' => $empleado->id
                        ]);
                    }
                }
            );
        });
    }


    public function show(Empleado $empleado)
    {
        return response()->json($empleado->load('archivable', 'archivable.requisito', 'escolaridad', 'departamento', 'estado_civil', 'jefe_directo', 'linea', 'puesto', 'sucursal', 'tipo_de_sangre', 'user', 'estatus'));
    }

    public function update(PutRequest $request, Empleado $empleado)
    {
        $empleado->update($request->only([
            'nombre',
            'segundo_nombre',
            'apellido_paterno',
            'apellido_materno',
            'telefono',
            'telefono_institucional',
            'fecha_de_nacimiento',
            'curp',
            'rfc',
            'ine',
            'licencia_de_manejo',
            'nss',
            'fecha_de_ingreso',
            'hijos',
            'dependientes_economicos',
            'cedula_profesional',
            'matriz',
            'sueldo_base',
            'comision',
            'numero_exterior',
            'numero_interior',
            'calle',
            'colonia',
            'codigo_postal',
            'ciudad',
            'estado',
            'cuenta_bancaria',
            'correo_institucional',
            'escolaridad_id',
            'puesto_id',
            'sucursal_id',
            'linea_id',
            'departamento_id',
            'estado_civil_id',
            'tipo_de_sangre_id',
            'jefe_directo_id',
            'estatus_id',

            'descripcion_puesto',
            'carrera',
        ]));

        $correo = $request->correo_institucional;
        if ($correo) {
            $usuario = User::firstOrCreate(['email' => $correo], ['password' => Hash::make("password123"), 'name' => $empleado->nombre]);
            if (!$empleado->user) {
                $empleado->user()->associate($usuario);
                $empleado->save();
                $empleado->user->syncRoles('Empleado');
            }
        }

        // Verifica si el request contiene información de desvinculación
        if ($request->has('desvinculacion')) {
            // Extrae los datos de desvinculación de la solicitud
            $desvinculacionData = $request->input('desvinculacion');

            // Busca una instancia existente de Termination asociada al empleado
            $termination = Termination::where('empleado_id', $empleado->id)->first();

            // Si se encuentra una instancia, actualiza sus datos con los datos de la solicitud
            // Si no se encuentra ninguna instancia, crea una nueva instancia de Termination
            if ($termination) {
                $termination->update([
                    'reason_id' => $desvinculacionData['reason_id'],
                    'estatus_id' => $desvinculacionData['estatus_id'],
                    'date' => $desvinculacionData['date'],
                    'comments' => $desvinculacionData['comments'] ?? null,
                    'empleado_id' => $empleado->id
                ]);
            } else {
                Termination::create([
                    'reason_id' => $desvinculacionData['reason_id'],
                    'estatus_id' => $desvinculacionData['estatus_id'],
                    'date' => $desvinculacionData['date'],
                    'comments' => $desvinculacionData['comments'] ?? null,
                    'empleado_id' => $empleado->id
                ]);
            }
        }

        return response()->json($empleado);
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return response()->json("ok");
    }

    public function uploadPicture(PicRequest $request, Empleado $empleado)
    {
        if (!is_null($request['base64'])) {
            if ($empleado->fotografia) {
                Storage::disk('s3')->delete($empleado->fotografia);
            }
            $relativePath  = $this->saveImage($request['base64'], $empleado->default_path_folder);
            $request['base64'] = $relativePath;
            $updateData = ['fotografia' => $relativePath];
            $empleado->update($updateData);
        }
    }

    public function findEmpleadoByRFCandINE($rfc, $ine)
    {
        $empleado = Empleado::where('rfc', $rfc)
            ->where('ine', $ine)
            ->first();

        if ($empleado) {
            return response()->json($empleado->load('archivable', 'archivable.requisito', 'escolaridad', 'departamento', 'estado_civil', 'jefe_directo', 'linea', 'puesto', 'sucursal', 'tipo_de_sangre', 'user'));
        } else {
            return response()->json(['error' => 'No se encontro un empleado con esos datos.'], 400);
        }
    }

    private function getAllSubordinates($employee)
    {
        $subordinates = $employee->empleado()
            ->with([
                'archivable',
                'archivable.requisito',
                'escolaridad',
                'departamento',
                'estado_civil',
                'jefe_directo',
                'linea',
                'puesto',
                'sucursal',
                'tipo_de_sangre',
                'user',
                'estatus'
            ])
            ->get();

        $allSubordinates = collect();
        foreach ($subordinates as $subordinate) {
            $allSubordinates->push($subordinate);
            $allSubordinates = $allSubordinates->merge($this->getAllSubordinates($subordinate));
        }
        return $allSubordinates;
    }

    public function filtertwo(Request $request)
    {
        $filters = $request->all();

        $filteredEmployees = Empleado::filtertwo($filters)->get();

        return response()->json($filteredEmployees);
    }

    public function modeloNegocio(Request $request)
    {
        $filters = $request->all();
        $user = Auth::user();
        $empleado = $user->empleado;

        if ($user->hasRole('RRHH')) {
            $empleados = Empleado::filter($filters)->with(['archivable', 'archivable.requisito', 'escolaridad', 'departamento', 'estado_civil', 'jefe_directo', 'linea', 'puesto', 'sucursal', 'tipo_de_sangre', 'user', 'estatus', 'termination.estatus', 'termination.reason'])->get();
        } else {
            $empleados = $this->getAllSubordinates($empleado);
        }

        $data = [
            'empleados' => $empleados,
            'sucursales' => Sucursal::all(),
            'departamentos' => Departamento::all(),
            'lineas' => Linea::all(),
            'puestos' => Puesto::all(),
            'estatus' => Estatus::where('tipo_estatus', 'empleado')->get()
        ];

        return $this->respond($data);
    }

    public function personal()
    {
        $data = [
            'escolaridades' => Escolaridad::all(),
            'estados_civiles' => EstadoCivil::all(),
            'tipos_de_sangre' => TipoDeSangre::all(),
        ];

        return $this->respond($data);
    }

    public function getEmployeesTerminations($anio = null, $mes = null)
    {
        $query = Empleado::with(['archivable', 'archivable.requisito', 'escolaridad', 'departamento', 'estado_civil', 'jefe_directo', 'linea', 'puesto', 'sucursal', 'tipo_de_sangre', 'user', 'estatus', 'termination.estatus', 'termination.reason']); // Cargar las relaciones sucursal y termination

        // Agregar filtros para la fecha en la relación termination
        if (!is_null($anio)) {
            $query->whereHas('termination', function ($q) use ($anio) {
                $q->whereYear('date', $anio);
            });
        }

        if (!is_null($mes)) {
            $query->whereHas('termination', function ($q) use ($mes) {
                $q->whereMonth('date', $mes);
            });
        }

        // Si tanto $anio como $mes son nulos, buscar empleados con estatus_id igual a 6
        if (is_null($anio) && is_null($mes)) {
            $query->where('estatus_id', 6);
        }

        $employees = $query->get();

        return response()->json($employees);
    }
}
