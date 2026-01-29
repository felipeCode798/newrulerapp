<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcesoResource\Pages;
use App\Models\Proceso;
use App\Models\Cliente;
use App\Models\Tramitador;
use App\Models\Curso;
use App\Models\Renovacion;
use App\Models\Escuela;
use App\Models\PinEscuela;
use App\Models\CategoriaLicencia;
use App\Models\CategoriaControversia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProcesoResource extends Resource
{
    protected static ?string $model = Proceso::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Procesos';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Selección de Usuario')
                    ->schema([
                        Forms\Components\Select::make('tipo_usuario')
                            ->label('Tipo de Usuario')
                            ->options([
                                'cliente' => 'Cliente',
                                'tramitador' => 'Tramitador',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('cliente_id', null);
                                $set('tramitador_id', null);
                            }),

                        // Sección para Cliente
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('cliente_id')
                                ->label('Cliente')
                                ->options(Cliente::where('activo', true)->pluck('nombre', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    if ($state) {
                                        $cliente = Cliente::find($state);
                                        $set('cliente_nombre_display', $cliente->nombre);
                                        $set('cliente_email_display', $cliente->email);
                                        $set('cliente_telefono_display', $cliente->telefono);
                                        $set('cliente_cedula_base', $cliente->cedula);
                                    }
                                })
                                ->suffixAction(
                                    Forms\Components\Actions\Action::make('crear_cliente')
                                        ->icon('heroicon-o-plus')
                                        ->form([
                                            Forms\Components\TextInput::make('nombre')
                                                ->required(),
                                            Forms\Components\TextInput::make('cedula')
                                                ->required()
                                                ->unique('clientes', 'cedula'),
                                            Forms\Components\TextInput::make('email')
                                                ->email()
                                                ->required()
                                                ->unique('clientes', 'email'),
                                            Forms\Components\TextInput::make('telefono')
                                                ->required(),
                                        ])
                                        ->action(function (array $data, Set $set) {
                                            $user = \App\Models\User::create([
                                                'name' => $data['nombre'],
                                                'email' => $data['email'],
                                                'password' => \Illuminate\Support\Facades\Hash::make($data['cedula']),
                                            ]);
                                            $user->assignRole('cliente');

                                            $cliente = Cliente::create([
                                                'user_id' => $user->id,
                                                'nombre' => $data['nombre'],
                                                'cedula' => $data['cedula'],
                                                'email' => $data['email'],
                                                'telefono' => $data['telefono'],
                                            ]);

                                            $set('cliente_id', $cliente->id);
                                            $set('cliente_nombre_display', $cliente->nombre);
                                            $set('cliente_email_display', $cliente->email);
                                            $set('cliente_telefono_display', $cliente->telefono);
                                            $set('cliente_cedula_base', $cliente->cedula);
                                        })
                                ),

                            Forms\Components\TextInput::make('cliente_nombre_display')
                                ->label('Nombre')
                                ->disabled()
                                ->dehydrated(false),

                            Forms\Components\TextInput::make('cliente_email_display')
                                ->label('Email')
                                ->disabled()
                                ->dehydrated(false),

                            Forms\Components\TextInput::make('cliente_telefono_display')
                                ->label('Teléfono')
                                ->disabled()
                                ->dehydrated(false),

                            Forms\Components\Hidden::make('cliente_cedula_base')
                                ->dehydrated(false),
                        ])
                            ->columns(2)
                            ->visible(fn (Get $get) => $get('tipo_usuario') === 'cliente'),

                        // Sección para Tramitador
                        Forms\Components\Select::make('tramitador_id')
                            ->label('Tramitador')
                            ->options(Tramitador::where('activo', true)->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Get $get) => $get('tipo_usuario') === 'tramitador'),
                    ])
                    ->columns(1),

                // CURSOS
                Forms\Components\Section::make('Cursos')
                    ->schema([
                        Forms\Components\Repeater::make('cursos')
                            ->relationship('cursos')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre del Cliente')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\Select::make('curso_id')
                                    ->label('Curso')
                                    ->options(Curso::where('activo', true)->get()->pluck('categoria', 'id'))
                                    ->required()
                                    ->live()
                                    ->searchable(),

                                Forms\Components\TextInput::make('numero_comparendo')
                                    ->label('Número de Comparendo'),

                                Forms\Components\Select::make('cia_id')
                                    ->label('CIA')
                                    ->options(function () {
                                        return \App\Models\Cia::pluck('nombre', 'id')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->required(),
                                        Forms\Components\TextInput::make('direccion'),
                                        Forms\Components\TextInput::make('telefono'),
                                    ]),

                                Forms\Components\TextInput::make('cedula')
                                    ->label('Cédula')
                                    ->required()
                                    ->default(function (Get $get) {
                                        $tipoUsuario = $get('../../tipo_usuario');
                                        if ($tipoUsuario === 'cliente') {
                                            return $get('../../cliente_cedula_base');
                                        }
                                        return '';
                                    }),

                                Forms\Components\Select::make('porcentaje')
                                    ->label('Porcentaje')
                                    ->options([
                                        '50' => '50%',
                                        '20' => '20%',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        self::calcularValoresCurso($set, $get, $state);
                                    }),

                                Forms\Components\TextInput::make('valor_transito')
                                    ->label('Valor Tránsito')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->reactive()
                                    ->disabled(),

                                Forms\Components\TextInput::make('valor_recibir')
                                    ->label('Valor a Recibir')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->reactive()
                                    ->disabled(),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'enviado' => 'Enviado',
                                        'en_proceso' => 'En Proceso',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->default('pendiente')
                                    ->required(),

                                Forms\Components\Textarea::make('descripcion_general')
                                    ->label('Descripción General')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Curso')
                            ->collapsible(),
                    ])
                    ->collapsible(),

                // RENOVACIONES
                Forms\Components\Section::make('Renovaciones')
                ->schema([
                    Forms\Components\Repeater::make('renovaciones')
                        ->relationship('renovaciones')
                        ->schema([
                            Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre del Cliente')
                                    ->required()
                                    ->columnSpan(2),

                            Forms\Components\TextInput::make('cedula')
                                ->label('Cédula')
                                ->required()
                                ->default(function (Get $get) {
                                    $tipoUsuario = $get('../../tipo_usuario');
                                    if ($tipoUsuario === 'cliente') {
                                        return $get('../../cliente_cedula_base');
                                    }
                                    return '';
                                }),

                            Forms\Components\Select::make('renovacion_id')
                                ->label('Renovación')
                                ->options(Renovacion::where('activo', true)->pluck('nombre', 'id'))
                                ->required()
                                ->live()
                                ->searchable()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $incluyeExamen = $get('incluye_examen') ?? true;
                                    $incluyeLamina = $get('incluye_lamina') ?? true;
                                    self::calcularValoresRenovacion($set, $get, $state, $incluyeExamen, $incluyeLamina);
                                }),

                            Forms\Components\Checkbox::make('incluye_examen')
                                ->label('Incluye Examen')
                                ->default(true)
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $renovacionId = $get('renovacion_id');
                                    $incluyeLamina = $get('incluye_lamina') ?? true;
                                    self::calcularValoresRenovacion($set, $get, $renovacionId, $state, $incluyeLamina);
                                }),

                            Forms\Components\Checkbox::make('incluye_lamina')
                                ->label('Incluye Lámina')
                                ->default(true)
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $renovacionId = $get('renovacion_id');
                                    $incluyeExamen = $get('incluye_examen') ?? true;
                                    self::calcularValoresRenovacion($set, $get, $renovacionId, $incluyeExamen, $state);
                                }),

                            Forms\Components\TextInput::make('valor_total')
                                ->label('Valor Total')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->disabled(),

                            Forms\Components\Select::make('estado')
                                ->label('Estado')
                                ->options([
                                    'pendiente' => 'Pendiente',
                                    'enviado' => 'Enviado',
                                    'en_proceso' => 'En Proceso',
                                    'finalizado' => 'Finalizado',
                                ])
                                ->default('pendiente')
                                ->required(),

                            Forms\Components\Textarea::make('descripcion_general')
                                ->label('Descripción General')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel('Agregar Renovación')
                        ->collapsible(),
                ])
                ->collapsible(),

                // LICENCIAS
                Forms\Components\Section::make('Licencias')
                    ->schema([
                        Forms\Components\Repeater::make('licencias')
                            ->relationship('licencias')
                            ->schema([
                                Forms\Components\TextInput::make('cedula')
                                    ->label('Cédula')
                                    ->required()
                                    ->default(function (Get $get) {
                                        $tipoUsuario = $get('../../tipo_usuario');
                                        if ($tipoUsuario === 'cliente') {
                                            return $get('../../cliente_cedula_base');
                                        }
                                        return '';
                                    })
                                    ->columnSpan(2),

                                Forms\Components\CheckboxList::make('categorias_seleccionadas')
                                    ->label('Categorías de Licencia')
                                    ->options(CategoriaLicencia::where('activo', true)->pluck('nombre', 'id'))
                                    ->required()
                                    ->columns(3)
                                    ->columnSpan(3),

                                Forms\Components\Select::make('escuela_id')
                                    ->label('Escuela')
                                    ->options(Escuela::where('activo', true)->pluck('nombre', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if ($state) {
                                            $escuela = Escuela::find($state);
                                            $set('valor_carta_escuela', $escuela->valor_carta_escuela);
                                        }
                                        self::calcularValoresLicencia($set, $state);
                                    }),

                                Forms\Components\Select::make('enrolamiento')
                                    ->label('Enrolamiento')
                                    ->options([
                                        'cruce_pin' => 'Cruce de PIN',
                                        'guardado' => 'Guardado',
                                        'pagado' => 'Pagado',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($state !== 'cruce_pin') {
                                            $set('pin_escuela_id', null);
                                        }
                                    }),

                                Forms\Components\TextInput::make('valor_enrolamiento')
                                    ->label('Valor Enrolamiento')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required(fn ($get) => $get('enrolamiento') === 'pagado')
                                    ->visible(fn ($get) => $get('enrolamiento') === 'pagado'),

                                Forms\Components\Select::make('pin_escuela_id')
                                    ->label('PIN de Escuela')
                                    ->options(function (Get $get) {
                                        $escuelaId = $get('escuela_id');
                                        if (!$escuelaId) {
                                            return [];
                                        }
                                        return PinEscuela::where('escuela_id', $escuelaId)
                                            ->where('estado', 'asignado')
                                            ->whereNotNull('user_id')
                                            ->get()
                                            ->mapWithKeys(function ($pin) {
                                                return [$pin->id => $pin->pin . ' - ' . $pin->user->name];
                                            });
                                    })
                                    ->searchable()
                                    ->visible(fn (Get $get) => $get('enrolamiento') === 'cruce_pin'),

                                Forms\Components\TextInput::make('valor_carta_escuela')
                                    ->label('Valor Carta Escuela')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->disabled(),

                                Forms\Components\Select::make('examen_medico')
                                    ->label('Examen Médico')
                                    ->options([
                                        'no_aplica' => 'No Aplica',
                                        'pendiente' => 'Pendiente',
                                        'finalizado' => 'Finalizado',
                                        'devuelto' => 'Devuelto',
                                    ])
                                    ->default('no_aplica')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        self::calcularValorExamenMedico($set, $get, $state);
                                    }),

                                Forms\Components\TextInput::make('valor_examen_medico')
                                    ->label('Valor Examen Médico')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->default(0),

                                Forms\Components\Select::make('impresion')
                                    ->label('Impresión')
                                    ->options([
                                        'no_aplica' => 'No Aplica',
                                        'pendiente' => 'Pendiente',
                                        'finalizado' => 'Finalizado',
                                        'devuelto' => 'Devuelto',
                                    ])
                                    ->default('no_aplica')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        self::calcularValorImpresion($set, $get, $state);
                                    }),

                                Forms\Components\TextInput::make('valor_impresion')
                                    ->label('Valor Impresión')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->default(0),

                                Forms\Components\Select::make('sin_curso')
                                    ->label('Sin Curso')
                                    ->options([
                                        'no_aplica' => 'No Aplica',
                                        'pendiente' => 'Pendiente',
                                        'finalizado' => 'Finalizado',
                                        'devuelto' => 'Devuelto',
                                    ])
                                    ->default('no_aplica')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        self::calcularValorSinCurso($set, $get, $state);
                                    }),

                                Forms\Components\TextInput::make('valor_sin_curso')
                                    ->label('Valor Sin Curso')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->default(0),

                                Forms\Components\TextInput::make('valor_total_licencia')
                                    ->label('Valor Total Licencia')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->disabled()
                                    ->columnSpan(3),
                                    Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'enviado' => 'Enviado',
                                        'en_proceso' => 'En Proceso',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->default('pendiente')
                                    ->required(),

                                Forms\Components\Textarea::make('descripcion_general')
                                    ->label('Descripción General')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Licencia')
                            ->collapsible(),
                    ])
                    ->collapsible(),

                // TRASPASOS
                Forms\Components\Section::make('Traspasos')
                    ->schema([
                        Forms\Components\Repeater::make('traspasos')
                            ->relationship('traspasos')
                            ->schema([
                                Forms\Components\TextInput::make('cedula')
                                    ->label('Cédula')
                                    ->required()
                                    ->default(function (Get $get) {
                                        $tipoUsuario = $get('../../tipo_usuario');
                                        if ($tipoUsuario === 'cliente') {
                                            return $get('../../cliente_cedula_base');
                                        }
                                        return '';
                                    }),

                                Forms\Components\TextInput::make('nombre_propietario')
                                    ->label('Nombre Propietario')
                                    ->required(),

                                Forms\Components\TextInput::make('nombre_comprador')
                                    ->label('Nombre Comprador')
                                    ->required(),

                                Forms\Components\TextInput::make('cedula_comprador')
                                    ->label('Cédula Comprador')
                                    ->required(),

                                Forms\Components\TextInput::make('derecho_traspaso')
                                    ->label('Derecho de Traspaso')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalTraspaso($set, $get);
                                    }),

                                Forms\Components\TextInput::make('porcentaje')
                                    ->label('Porcentaje')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalTraspaso($set, $get);
                                    }),

                                Forms\Components\TextInput::make('honorarios')
                                    ->label('Honorarios')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalTraspaso($set, $get);
                                    }),

                                Forms\Components\TextInput::make('comision')
                                    ->label('Comisión')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalTraspaso($set, $get);
                                    }),

                                Forms\Components\TextInput::make('total_recibir')
                                    ->label('Total a Recibir')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->disabled(),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'enviado' => 'Enviado',
                                        'en_proceso' => 'En Proceso',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->default('pendiente')
                                    ->required(),

                                Forms\Components\Textarea::make('descripcion_general')
                                    ->label('Descripción General')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Traspaso')
                            ->collapsible(),
                    ])
                    ->collapsible(),

                // RUNT
                Forms\Components\Section::make('RUNT')
                    ->schema([
                        Forms\Components\Repeater::make('runts')
                            ->relationship('runts')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required(),

                                Forms\Components\TextInput::make('cedula')
                                    ->label('Cédula')
                                    ->required()
                                    ->default(function (Get $get) {
                                        $tipoUsuario = $get('../../tipo_usuario');
                                        if ($tipoUsuario === 'cliente') {
                                            return $get('../../cliente_cedula_base');
                                        }
                                        return '';
                                    }),

                                Forms\Components\TextInput::make('numero')
                                    ->label('Número')
                                    ->required(),

                                Forms\Components\TextInput::make('comision')
                                    ->label('Comisión')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalRunt($set, $get);
                                    }),

                                Forms\Components\TextInput::make('pago')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalRunt($set, $get);
                                    }),

                                Forms\Components\TextInput::make('honorarios')
                                    ->label('Honorarios')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calcularTotalRunt($set, $get);
                                    }),

                                Forms\Components\TextInput::make('valor_recibir')
                                    ->label('Valor a Recibir')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->disabled(),
                                
                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'enviado' => 'Enviado',
                                        'en_proceso' => 'En Proceso',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->default('pendiente')
                                    ->required(),

                                Forms\Components\Textarea::make('descripcion_general')
                                    ->label('Descripción General')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar RUNT')
                            ->collapsible(),
                    ])
                    ->collapsible(),

                // CONTROVERSIAS
                Forms\Components\Section::make('Controversias')
                    ->schema([
                        Forms\Components\Repeater::make('controversias')
                            ->relationship('controversias')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre del Cliente')
                                    ->required(),

                                Forms\Components\TextInput::make('comparendo')
                                    ->label('Número de Comparendo'),

                                Forms\Components\TextInput::make('cedula')
                                    ->label('Cédula')
                                    ->required()
                                    ->default(function (Get $get) {
                                        $tipoUsuario = $get('../../tipo_usuario');
                                        if ($tipoUsuario === 'cliente') {
                                            return $get('../../cliente_cedula_base');
                                        }
                                        return '';
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('celular')
                                    ->label('Celular'),

                                Forms\Components\Select::make('categoria_controversia_id')
                                    ->label('Categoría')
                                    ->options(CategoriaControversia::where('activo', true)->pluck('nombre', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        self::calcularValorControversia($set, $get, $state);
                                    })
                                    ->columnSpan(2),

                                Forms\Components\Select::make('cia_id')
                                    ->label('CIA')
                                    ->options(function () {
                                        return \App\Models\Cia::pluck('nombre', 'id')->toArray();
                                    })
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('precio_cia')
                                    ->label('Precio CIA')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),

                                Forms\Components\TextInput::make('valor_controversia')
                                    ->label('Valor Controversia')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->disabled()
                                    ->columnSpan(2),

                                Forms\Components\DateTimePicker::make('fecha_hora_cita')
                                    ->label('Fecha y Hora de Cita')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('codigo_controversia')
                                    ->label('Código Controversia')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('venta_controversia')
                                    ->label('Venta Controversia')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\FileUpload::make('documento_identidad')
                                    ->label('Documento de Identidad')
                                    ->directory('controversias/documentos')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->columnSpan(2),

                                Forms\Components\FileUpload::make('poder')
                                    ->label('Poder')
                                    ->directory('controversias/poderes')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->columnSpan(2),

                                Forms\Components\Toggle::make('debe')
                                    ->label('¿Debe?')
                                    ->default(false),

                                    Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'enviado' => 'Enviado',
                                        'en_proceso' => 'En Proceso',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->default('pendiente')
                                    ->required(),

                                Forms\Components\Textarea::make('descripcion_general')
                                    ->label('Descripción General')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Controversia')
                            ->collapsible(),
                    ])
                    ->collapsible(),

                // ESTADOS DE CUENTA
                Forms\Components\Section::make('Estados de Cuenta')
                    ->schema([
                        Forms\Components\FileUpload::make('estados_cuenta')
                            ->label('Archivos de Estado de Cuenta')
                            ->multiple()
                            ->directory('estados-cuenta')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->helperText('Puede subir varios archivos de estados de cuenta'),
                    ])
                    ->collapsible(),
            ]);
    }

    // Métodos auxiliares para cálculos
    protected static function calcularValoresCurso(Set $set, Get $get, $porcentaje) {
        $cursoId = $get('curso_id');
        $tipoUsuario = $get('../../tipo_usuario');
    
        if (!$cursoId || !$porcentaje) {
            return;
        }
    
        $curso = Curso::find($cursoId);
    
        if ($tipoUsuario === 'cliente') {
            if ($porcentaje === '50') {
                $set('valor_transito', $curso->precio_cliente_50_transito);
                $set('valor_recibir', $curso->precio_cliente_50_recibir);
            } else {
                $set('valor_transito', $curso->precio_cliente_20_transito);
                $set('valor_recibir', $curso->precio_cliente_20_recibir);
            }
        } else {
            $tramitadorId = $get('../../tramitador_id');
            if ($tramitadorId) {
                $tramitador = Tramitador::find($tramitadorId);
                $cursoPivot = $tramitador->cursos()
                    ->where('curso_id', $cursoId)
                    ->first();
                
                if ($cursoPivot) {
                    if ($porcentaje === '50') {
                        $set('valor_transito', $cursoPivot->pivot->precio_50_transito);
                        $set('valor_recibir', $cursoPivot->pivot->precio_50_recibir);
                    } else {
                        $set('valor_transito', $cursoPivot->pivot->precio_20_transito);
                        $set('valor_recibir', $cursoPivot->pivot->precio_20_recibir);
                    }
                } else {
                    // Si no tiene precio configurado, usar los predeterminados del tramitador
                    if ($porcentaje === '50') {
                        $set('valor_transito', $tramitador->curso_50_transito);
                        $set('valor_recibir', $tramitador->curso_50_recibir);
                    } else {
                        $set('valor_transito', $tramitador->curso_20_transito);
                        $set('valor_recibir', $tramitador->curso_20_recibir);
                    }
                }
            }
        }
    }
    
    protected static function calcularValoresRenovacion(Set $set, Get $get, $renovacionId, $incluyeExamen, $incluyeLamina) {
        if (!$renovacionId) {
            $set('valor_total', 0);
            return;
        }
    
        $tipoUsuario = $get('../../tipo_usuario');
        $total = 0;
    
        $renovacion = Renovacion::find($renovacionId);
    
        if ($tipoUsuario === 'cliente') {
            // Para cliente
            $total += $renovacion->precio_renovacion;
            if ($incluyeExamen) {
                $total += $renovacion->precio_examen;
            }
            if ($incluyeLamina) {
                $total += $renovacion->precio_lamina;
            }
        } else {
            // Para tramitador
            $tramitadorId = $get('../../tramitador_id');
            if ($tramitadorId) {
                $tramitador = Tramitador::find($tramitadorId);
                $precioPivot = $tramitador->renovaciones()
                    ->where('renovacion_id', $renovacionId)
                    ->first();
                
                if ($precioPivot) {
                    // Usar precios específicos del tramitador
                    $total += $precioPivot->pivot->precio_renovacion; // Corregido
                    if ($incluyeExamen) {
                        $total += $precioPivot->pivot->precio_examen; // Corregido
                    }
                    if ($incluyeLamina) {
                        $total += $precioPivot->pivot->precio_lamina; // Corregido
                    }
                } else {
                    // Si no tiene precios configurados, usar los predeterminados de la renovación
                    $total += $renovacion->precio_renovacion;
                    if ($incluyeExamen) {
                        $total += $renovacion->precio_examen;
                    }
                    if ($incluyeLamina) {
                        $total += $renovacion->precio_lamina;
                    }
                }
            }
        }
    
        $set('valor_total', $total);
    }
    
    protected static function calcularValoresLicencia($set, $escuelaId) {
        if (!$escuelaId) {
            return;
        }

        $escuela = Escuela::find($escuelaId);
        $set('valor_carta_escuela', $escuela->valor_carta_escuela);

        // Recalcular total
        self::recalcularTotalLicencia($set, $escuelaId);
    }

    protected static function calcularValorExamenMedico(Set $set, Get $get, $estado) {
        if ($estado === 'no_aplica') {
            $set('valor_examen_medico', 0);
        } else {
            $categorias = $get('categorias_seleccionadas') ?? [];
            $totalExamen = 0;
            if (!empty($categorias)) {
                foreach ($categorias as $categoriaId) {
                    $categoria = CategoriaLicencia::find($categoriaId);
                    if ($categoria) {
                        $totalExamen += $categoria->examen_medico;
                    }
                }
            }
            $set('valor_examen_medico', $totalExamen);
        }
        self::recalcularTotalLicencia($set, $get);
    }

    protected static function calcularValorImpresion(Set $set, Get $get, $estado) {
        if ($estado === 'no_aplica') {
            $set('valor_impresion', 0);
        } else {
            $categorias = $get('categorias_seleccionadas') ?? [];
            $totalImpresion = 0;
            if (!empty($categorias)) {
                foreach ($categorias as $categoriaId) {
                    $categoria = CategoriaLicencia::find($categoriaId);
                    if ($categoria) {
                        $totalImpresion += $categoria->lamina;
                    }
                }
            }
            $set('valor_impresion', $totalImpresion);
        }
        self::recalcularTotalLicencia($set, $get);
    }

    protected static function calcularValorSinCurso(Set $set, Get $get, $estado) {
        if ($estado === 'no_aplica') {
            $set('valor_sin_curso', 0);
        } else {
            $categorias = $get('categorias_seleccionadas') ?? [];
            $totalSinCurso = 0;
            if (!empty($categorias)) {
                foreach ($categorias as $categoriaId) {
                    $categoria = CategoriaLicencia::find($categoriaId);
                    if ($categoria) {
                        $totalSinCurso += $categoria->sin_curso;
                    }
                }
            }
            $set('valor_sin_curso', $totalSinCurso);
        }
        self::recalcularTotalLicencia($set, $get);
    }

    protected static function recalcularTotalLicencia(Set $set, Get $get) {
        $valorCarta = $get('valor_carta_escuela') ?? 0;
        $valorExamen = $get('valor_examen_medico') ?? 0;
        $valorImpresion = $get('valor_impresion') ?? 0;
        $valorSinCurso = $get('valor_sin_curso') ?? 0;
        
        // Sumar también los honorarios de la categoría
        $categorias = $get('categorias_seleccionadas') ?? [];
        $totalHonorarios = 0;
        
        if (!empty($categorias)) {
            foreach ($categorias as $categoriaId) {
                $categoria = CategoriaLicencia::find($categoriaId);
                if ($categoria) {
                    $totalHonorarios += $categoria->honorarios;
                }
            }
        }
    
        $total = $valorCarta + $valorExamen + $valorImpresion + $valorSinCurso + $totalHonorarios;
        $set('valor_total_licencia', $total);
    }

    protected static function calcularTotalTraspaso(Set $set, Get $get) {
        $derecho = $get('derecho_traspaso') ?? 0;
        $porcentaje = $get('porcentaje') ?? 0;
        $honorarios = $get('honorarios') ?? 0;
        $comision = $get('comision') ?? 0;
        $total = $derecho + $porcentaje + $honorarios + $comision;
        $set('total_recibir', $total);
    }

    protected static function calcularTotalRunt(Set $set, Get $get) {
        $comision = $get('comision') ?? 0;
        $pago = $get('pago') ?? 0;
        $honorarios = $get('honorarios') ?? 0;
        $total = $comision + $pago + $honorarios;
        $set('valor_recibir', $total);
    }

    protected static function calcularValorControversia(Set $set, Get $get, $categoriaId) {
        if (!$categoriaId) {
        return;
        }
        $tipoUsuario = $get('../../tipo_usuario');
        $categoria = CategoriaControversia::find($categoriaId);

        if ($tipoUsuario === 'cliente') {
            $set('valor_controversia', $categoria->precio_cliente);
        } else {
            $tramitadorId = $get('../../tramitador_id');
            if ($tramitadorId) {
                $tramitador = Tramitador::find($tramitadorId);
                $precio = $tramitador->controversias()
                    ->where('categoria_controversia_id', $categoriaId)
                    ->first();
                if ($precio) {
                    $set('valor_controversia', $precio->pivot->precio_tramitador);
                } else {
                    $set('valor_controversia', 0);
                }
            }
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('tipo_usuario')
                    ->label('Tipo Usuario')
                    ->formatStateUsing(fn ($state) => $state === 'cliente' ? 'Cliente' : 'Tramitador')
                    ->badge()
                    ->color(fn ($state) => $state === 'cliente' ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Cliente/Tramitador')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cedula_completa')
                    ->label('Cédula')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_servicio')
                    ->label('Tipo Servicio')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'curso' => 'Curso',
                        'renovacion' => 'Renovación',
                        'licencia' => 'Licencia',
                        'traspaso' => 'Traspaso',
                        'runt' => 'RUNT',
                        'controversia' => 'Controversia',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'curso' => 'info',
                        'renovacion' => 'primary',
                        'licencia' => 'success',
                        'traspaso' => 'warning',
                        'runt' => 'danger',
                        'controversia' => 'gray',
                        default => 'secondary',
                    }),


                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->getStateUsing(function ($record) {
                        if ($record->cursos()->count() > 0) {
                            return $record->cursos->first()->estado ?? 'pendiente';
                        }
                        if ($record->renovaciones()->count() > 0) {
                            return $record->renovaciones->first()->estado ?? 'pendiente';
                        }
                        if ($record->licencias()->count() > 0) {
                            return $record->licencias->first()->estado ?? 'pendiente';
                        }
                        if ($record->controversias()->count() > 0) {
                            return $record->controversias->first()->estado ?? 'pendiente';
                        }
                        return 'pendiente';
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pendiente' => 'gray',
                        'enviado' => 'info',
                        'en_proceso' => 'warning',
                        'finalizado' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->sortable()
                    ->action(
                        Tables\Actions\Action::make('cambiarEstado')
                            ->form([
                                Forms\Components\Select::make('estado')
                                    ->label('Nuevo Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'enviado' => 'Enviado',
                                        'en_proceso' => 'En Proceso',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->required(),
                            ])
                            ->action(function ($record, array $data) {
                                // Actualizar todos los servicios
                                $record->cursos()->update(['estado' => $data['estado']]);
                                $record->renovaciones()->update(['estado' => $data['estado']]);
                                $record->licencias()->update(['estado' => $data['estado']]);
                                $record->traspasos()->update(['estado' => $data['estado']]);
                                $record->runts()->update(['estado' => $data['estado']]);
                                $record->controversias()->update(['estado' => $data['estado']]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Estado actualizado')
                                    ->body("Estado cambiado a: " . ucfirst(str_replace('_', ' ', $data['estado'])))
                                    ->success()
                                    ->send();
                            })
                    ),
                    
                Tables\Columns\TextColumn::make('total_general')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('estado_pago')
                    ->label('Estado Pago')
                    ->getStateUsing(function ($record) {
                        $pagos = \App\Models\Pago::calcularSaldoProceso($record);
                        
                        if ($record->total_general <= 0) {
                            return 'sin_cargo';
                        }
                        
                        if ($pagos['completamente_pagado']) {
                            return 'pagado';
                        } elseif ($pagos['total_pagado'] > 0) {
                            return 'parcial';
                        } else {
                            return 'pendiente';
                        }
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pagado' => 'success',
                        'parcial' => 'warning',
                        'pendiente' => 'danger',
                        'sin_cargo' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pagado' => 'Pagado',
                        'parcial' => 'Pago Parcial',
                        'pendiente' => 'Pendiente',
                        'sin_cargo' => 'Sin Cargo',
                        default => $state,
                    })
                    ->tooltip(function ($record) {
                        $pagos = \App\Models\Pago::calcularSaldoProceso($record);
                        return "Total: $" . number_format($record->total_general, 2) . 
                               "\nPagado: $" . number_format($pagos['total_pagado'], 2) . 
                               "\nSaldo: $" . number_format($pagos['saldo_pendiente'], 2);
                    }),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_usuario')
                    ->label('Tipo de Usuario')
                    ->options([
                        'cliente' => 'Cliente',
                        'tramitador' => 'Tramitador',
                    ]),

                Tables\Filters\SelectFilter::make('tipo_servicio')
                    ->label('Tipo de Servicio')
                    ->options([
                        'curso' => 'Curso',
                        'renovacion' => 'Renovación',
                        'licencia' => 'Licencia',
                        'traspaso' => 'Traspaso',
                        'runt' => 'RUNT',
                        'controversia' => 'Controversia',
                    ]),

                Tables\Filters\SelectFilter::make('estado_cursos')
                    ->label('Estado de Cursos')
                    ->query(function ($query, $state) {
                        if ($state['value']) {
                            $query->whereHas('cursos', function ($q) use ($state) {
                                $q->where('estado', $state['value']);
                            });
                        }
                    })
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'en_proceso' => 'En Proceso',
                        'finalizado' => 'Finalizado',
                    ]),

                Tables\Filters\SelectFilter::make('estado_renovaciones')
                    ->label('Estado de Renovaciones')
                    ->query(function ($query, $state) {
                        if ($state['value']) {
                            $query->whereHas('renovaciones', function ($q) use ($state) {
                                $q->where('estado', $state['value']);
                            });
                        }
                    })
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'en_proceso' => 'En Proceso',
                        'finalizado' => 'Finalizado',
                    ]),

                Tables\Filters\SelectFilter::make('estado_licencias')
                    ->label('Estado de Licencias')
                    ->query(function ($query, $state) {
                        if ($state['value']) {
                            $query->whereHas('licencias', function ($q) use ($state) {
                                $q->where('estado', $state['value']);
                            });
                        }
                    })
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'en_proceso' => 'En Proceso',
                        'finalizado' => 'Finalizado',
                    ]),

                Tables\Filters\SelectFilter::make('estado_controversias')
                    ->label('Estado de Controversias')
                    ->query(function ($query, $state) {
                        if ($state['value']) {
                            $query->whereHas('controversias', function ($q) use ($state) {
                                $q->where('estado', $state['value']);
                            });
                        }
                    })
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'en_proceso' => 'En Proceso',
                        'finalizado' => 'Finalizado',
                    ]),

                Tables\Filters\Filter::make('fecha_creacion')
                    ->label('Fecha de Creación')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                Tables\Filters\Filter::make('total_general')
                    ->label('Total General')
                    ->form([
                        Forms\Components\TextInput::make('min')
                            ->label('Mínimo')
                            ->numeric(),
                        Forms\Components\TextInput::make('max')
                            ->label('Máximo')
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min'], fn ($q, $amount) => $q->where('total_general', '>=', $amount))
                            ->when($data['max'], fn ($q, $amount) => $q->where('total_general', '<=', $amount));
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                    Tables\Actions\Action::make('enviar_factura_individual')
                        ->label('Enviar Factura')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->form(function (Proceso $record) {
                            // Verificar si tiene email
                            $tieneEmail = $record->tipo_usuario === 'cliente' && 
                                         !empty($record->cliente->email) &&
                                         filter_var($record->cliente->email, FILTER_VALIDATE_EMAIL);
                            
                            // Verificar si tiene teléfono
                            $tieneTelefono = $record->cliente && !empty($record->cliente->telefono);
                            
                            // Crear opciones dinámicamente
                            $opciones = ['descargar' => '📥 Descargar localmente'];
                            
                            if ($tieneEmail) {
                                $opciones['email'] = '📧 Enviar por Email';
                            } else {
                                $opciones['email'] = '📧 Enviar por Email (no disponible)';
                            }
                            
                            if ($tieneTelefono) {
                                $opciones['whatsapp'] = '💬 Enviar por WhatsApp';
                            } else {
                                $opciones['whatsapp'] = '💬 Enviar por WhatsApp (no disponible)';
                            }
                            
                            $formFields = [
                                Forms\Components\Select::make('metodo')
                                    ->label('Método de envío')
                                    ->options($opciones)
                                    ->required()
                                    ->live()
                                    ->default('descargar'),
                            ];
                            
                            if ($tieneEmail) {
                                $formFields[] = Forms\Components\TextInput::make('email')
                                    ->label('Email destino')
                                    ->email()
                                    ->default($record->cliente->email)
                                    ->required(fn ($get) => $get('metodo') === 'email')
                                    ->visible(fn ($get) => $get('metodo') === 'email');
                            }
                            
                            if ($tieneTelefono) {
                                $formFields[] = Forms\Components\TextInput::make('telefono')
                                    ->label('Número WhatsApp')
                                    ->default($record->cliente->telefono)
                                    ->required(fn ($get) => $get('metodo') === 'whatsapp')
                                    ->visible(fn ($get) => $get('metodo') === 'whatsapp');
                            }
                            
                            $formFields[] = Forms\Components\Textarea::make('mensaje')
                                ->label('Mensaje personalizado')
                                ->rows(3)
                                ->default(fn () => "Hola,\n\nAdjunto factura del proceso #{$record->id}.\n\nTotal: $" . 
                                    number_format($record->total_general, 2) . 
                                    "\n\nSaludos,\nSistema Jurídico de Tránsito");
                            
                            return $formFields;
                        })
                        ->action(function (Proceso $record, array $data) {
                            try {
                                // Validar si el método está disponible
                                $tieneEmail = $record->tipo_usuario === 'cliente' && 
                                             !empty($record->cliente->email) &&
                                             filter_var($record->cliente->email, FILTER_VALIDATE_EMAIL);
                                
                                $tieneTelefono = $record->cliente && !empty($record->cliente->telefono);
                                
                                if ($data['metodo'] === 'email' && !$tieneEmail) {
                                    throw new \Exception('Este proceso no tiene un email válido registrado.');
                                }
                                
                                if ($data['metodo'] === 'whatsapp' && !$tieneTelefono) {
                                    throw new \Exception('Este proceso no tiene un teléfono registrado.');
                                }
                                
                                // Generar factura
                                $facturaPath = self::generarPdfFactura($record);
                                $urlDescarga = self::obtenerUrlDescarga($facturaPath, $record);
                                
                                // Preparar mensaje
                                $mensajeBase = $data['mensaje'] ?? '';
                                $mensajeCompleto = $mensajeBase . "\n\n🔗 Enlace para descargar factura: " . $urlDescarga;
                                
                                switch ($data['metodo']) {
                                    case 'email':
                                        if (empty($data['email'])) {
                                            throw new \Exception('No se proporcionó un email válido');
                                        }
                                        
                                        // Enviar email (versión simple)
                                        self::enviarEmailSimple($data['email'], $mensajeCompleto, $facturaPath, $record);
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('Email programado')
                                            ->body("Factura programada para enviar a: " . $data['email'])
                                            ->success()
                                            ->send();
                                        break;
                                        
                                    case 'whatsapp':
                                        if (empty($data['telefono'])) {
                                            throw new \Exception('No se proporcionó un número de teléfono');
                                        }
                                        
                                        // Para WhatsApp, generar el enlace
                                        $telefonoLimpio = preg_replace('/[^0-9]/', '', $data['telefono']);
                                        $mensajeCodificado = urlencode($mensajeCompleto);
                                        $whatsappUrl = "https://web.whatsapp.com/send?phone={$telefonoLimpio}&text={$mensajeCodificado}";
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('WhatsApp listo')
                                            ->body('Haz clic en el botón para abrir WhatsApp con el mensaje preparado')
                                            ->actions([
                                                \Filament\Notifications\Actions\Action::make('abrir_whatsapp')
                                                    ->label('Abrir WhatsApp')
                                                    ->url($whatsappUrl, shouldOpenInNewTab: true)
                                                    ->button(),
                                            ])
                                            ->success()
                                            ->send();
                                        break;
                                        
                                    case 'descargar':
                                    default:
                                        // Solo descargar
                                        \Filament\Notifications\Notification::make()
                                            ->title('Factura generada')
                                            ->body("Descargar factura desde: " . $urlDescarga)
                                            ->actions([
                                                \Filament\Notifications\Actions\Action::make('descargar')
                                                    ->label('Descargar Factura')
                                                    ->url($urlDescarga, shouldOpenInNewTab: true)
                                                    ->button(),
                                                \Filament\Notifications\Actions\Action::make('copiar_enlace')
                                                    ->label('Copiar Enlace')
                                                    ->action(function () use ($urlDescarga) {
                                                        // Esto necesita JavaScript
                                                        echo "<script>
                                                            navigator.clipboard.writeText('{$urlDescarga}');
                                                            alert('Enlace copiado al portapapeles');
                                                        </script>";
                                                    })
                                                    ->icon('heroicon-o-clipboard'),
                                            ])
                                            ->success()
                                            ->send();
                                        break;
                                }
                                
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalSubmitActionLabel('Enviar')
                        ->modalCancelActionLabel('Cancelar'),
                    Tables\Actions\Action::make('abrir_whatsapp')
                        ->label('Enviar por WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->visible(fn ($record) => $record->tipo_servicio === 'curso')
                        ->form([
                            Forms\Components\Textarea::make('mensaje_personalizado')
                                ->label('Mensaje adicional (opcional)')
                                ->rows(2)
                                ->placeholder('Ej: Buenas tardes, curso listo...'),
                            
                            Forms\Components\Toggle::make('marcar_como_enviado')
                                ->label('Marcar como "Enviado"')
                                ->default(true),
                        ])
                        ->action(function (Proceso $record, array $data) {
                            $cursosData = [];
                            
                            foreach ($record->cursos as $curso) {
                                $nombre = $curso->nombre ?? ($record->tipo_usuario === 'cliente' 
                                    ? $record->cliente->nombre 
                                    : ($record->tramitador->nombre ?? 'N/A'));
                                
                                $cedula = $curso->cedula ?? ($record->tipo_usuario === 'cliente' 
                                    ? $record->cliente->cedula 
                                    : ($record->tramitador->cedula ?? 'N/A'));
                                
                                $cursosData[] = [
                                    'nombre' => $nombre,
                                    'cedula' => $cedula,
                                    'numero_comparendo' => $curso->numero_comparendo ?? 'N/A',
                                    'porcentaje' => $curso->porcentaje ?? '50',
                                    'curso_id' => $curso->id,
                                ];
                            }
                            
                            // Usar el método estático
                            $mensajeFormateado = self::formatearMensajeWhatsApp(
                                $cursosData, 
                                $data['mensaje_personalizado'] ?? ''
                            );
                            
                            // Codificar para URL
                            $mensajeCodificado = urlencode($mensajeFormateado);
                            
                            // Marcar como enviado si se seleccionó
                            if ($data['marcar_como_enviado']) {
                                foreach ($record->cursos as $curso) {
                                    $curso->update(['estado' => 'enviado']);
                                }
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Cursos marcados como enviados')
                                    ->success()
                                    ->send();
                            }
                            
                            // Abrir WhatsApp Web
                            return redirect()->away("https://web.whatsapp.com/send?text={$mensajeCodificado}");
                        })
                        ->tooltip('Abrir WhatsApp con los cursos'),

                    Tables\Actions\Action::make('registrar_pago')
                        ->label('Registrar Pago')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->form(function (Proceso $record) {
                            $saldo = \App\Models\Pago::calcularSaldoProceso($record);
                            
                            return [
                                Forms\Components\Section::make('Información del Proceso')
                                    ->schema([
                                        Forms\Components\Placeholder::make('total_proceso')
                                            ->label('Total del Proceso')
                                            ->content('$ ' . number_format($record->total_general, 2))
                                            ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
                                        
                                        Forms\Components\Placeholder::make('total_pagado')
                                            ->label('Total Pagado')
                                            ->content('$ ' . number_format($saldo['total_pagado'], 2))
                                            ->extraAttributes(['class' => 'text-lg font-bold text-blue-600']),
                                        
                                        Forms\Components\Placeholder::make('saldo_pendiente')
                                            ->label('Saldo Pendiente')
                                            ->content('$ ' . number_format($saldo['saldo_pendiente'], 2))
                                            ->extraAttributes([
                                                'class' => $saldo['saldo_pendiente'] > 0 
                                                    ? 'text-lg font-bold text-red-600' 
                                                    : 'text-lg font-bold text-green-600'
                                            ]),
                                        
                                        Forms\Components\Placeholder::make('porcentaje_pagado')
                                            ->label('Porcentaje Pagado')
                                            ->content(number_format($saldo['porcentaje_pagado'], 1) . '%')
                                            ->extraAttributes([
                                                'class' => match(true) {
                                                    $saldo['porcentaje_pagado'] >= 100 => 'text-green-600 font-bold',
                                                    $saldo['porcentaje_pagado'] >= 50 => 'text-yellow-600 font-bold',
                                                    default => 'text-red-600 font-bold',
                                                }
                                            ]),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Nuevo Pago')
                                    ->schema([
                                        Forms\Components\TextInput::make('valor')
                                            ->label('Valor del Pago')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(fn () => $saldo['saldo_pendiente'])
                                            ->helperText(fn () => 'Máximo: $' . number_format($saldo['saldo_pendiente'], 2))
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) use ($saldo) {
                                                $metodo = $get('metodo');
                                                $set('observaciones', self::generarObservacion($state, $metodo, $saldo['saldo_pendiente']));
                                            }),
                                        
                                        Forms\Components\Select::make('metodo')
                                            ->label('Método de Pago')
                                            ->options([
                                                'efectivo' => 'Efectivo',
                                                'transferencia' => 'Transferencia',
                                                'tarjeta_credito' => 'Tarjeta Crédito',
                                                'tarjeta_debito' => 'Tarjeta Débito',
                                                'nequi' => 'Nequi',
                                                'daviplata' => 'Daviplata',
                                                'pse' => 'PSE',
                                                'otro' => 'Otro',
                                            ])
                                            ->default('efectivo')
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) use ($saldo) {
                                                $valor = $get('valor');
                                                $set('observaciones', self::generarObservacion($valor, $state, $saldo['saldo_pendiente']));
                                            }),
                                        
                                        Forms\Components\TextInput::make('referencia')
                                            ->label('Referencia/Número')
                                            ->placeholder('Ej: 123456, comprobante #001')
                                            ->helperText('Opcional: número de transacción, referencia bancaria, etc.')
                                            ->maxLength(50),
                                        
                                        Forms\Components\DatePicker::make('fecha_pago')
                                            ->label('Fecha del Pago')
                                            ->default(now())
                                            ->required()
                                            ->displayFormat('d/m/Y'),
                                        
                                        Forms\Components\Textarea::make('observaciones')
                                            ->label('Observaciones')
                                            ->placeholder('Pago parcial, abono inicial, etc.')
                                            ->default(function (Get $get) use ($saldo) {
                                                return self::generarObservacion(
                                                    $get('valor') ?? 0,
                                                    $get('metodo') ?? 'efectivo',
                                                    $saldo['saldo_pendiente']
                                                );
                                            }),
                                        
                                    ])
                                    ->columns(2),
                            ];
                        })
                        ->action(function (Proceso $record, array $data) {
                            try {
                                // Crear el pago
                                $pago = \App\Models\Pago::create([
                                    'proceso_id' => $record->id,
                                    'valor' => $data['valor'],
                                    'metodo' => $data['metodo'],
                                    'referencia' => $data['referencia'],
                                    'fecha_pago' => $data['fecha_pago'],
                                    'observaciones' => $data['observaciones'],
                                    'registrado_por' => auth()->id(),
                                    'estado' => 'confirmado',
                                ]);
                                
                                // Calcular nuevo saldo
                                $nuevoSaldo = \App\Models\Pago::calcularSaldoProceso($record);
                                
                                // Preparar mensaje para WhatsApp si está activado
                                if ($data['enviar_recibo']) {
                                    // Lógica de enviar recibo por WhatsApp
                                    try {
                                        $telefono = null;
                                        if ($record->tipo_usuario === 'cliente' && $record->cliente && $record->cliente->telefono) {
                                            $telefono = $record->cliente->telefono;
                                        } elseif ($record->tipo_usuario === 'tramitador' && $record->tramitador && $record->tramitador->telefono) {
                                            $telefono = $record->tramitador->telefono;
                                        }
                                        
                                        if ($telefono) {
                                            // Preparar mensaje
                                            $nombreDestinatario = $record->tipo_usuario === 'cliente' 
                                                ? ($record->cliente->nombre ?? 'Cliente')
                                                : ($record->tramitador->nombre ?? 'Tramitador');
                                            
                                            $mensaje = "✅ *COMPROBANTE DE PAGO* ✅\n\n" .
                                                "📋 *Proceso:* #{$record->id}\n" .
                                                "👤 *Destinatario:* {$nombreDestinatario}\n" .
                                                "💰 *Monto Pagado:* $" . number_format($pago->valor, 2) . "\n" .
                                                "💳 *Método:* " . ucfirst($pago->metodo) . "\n" .
                                                ($pago->referencia ? "🔢 *Referencia:* {$pago->referencia}\n" : "") .
                                                "📅 *Fecha:* " . $pago->fecha_pago->format('d/m/Y') . "\n" .
                                                "---\n" .
                                                "📊 *RESUMEN FINANCIERO*\n" .
                                                "• Total Proceso: $" . number_format($record->total_general, 2) . "\n" .
                                                "• Total Pagado: $" . number_format($nuevoSaldo['total_pagado'], 2) . "\n" .
                                                "• Saldo Pendiente: $" . number_format($nuevoSaldo['saldo_pendiente'], 2) . "\n" .
                                                "• Porcentaje Pagado: " . number_format($nuevoSaldo['porcentaje_pagado'], 1) . "%\n" .
                                                "---\n" .
                                                "📝 *Observaciones:*\n" .
                                                ($pago->observaciones ?: "Pago registrado correctamente") . "\n\n" .
                                                "Gracias por su pago. 🎉";
                                            
                                            $mensajeCodificado = urlencode($mensaje);
                                            $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
                                            $whatsappUrl = "https://web.whatsapp.com/send?phone={$telefonoLimpio}&text={$mensajeCodificado}";
                                            
                                            // Agregar script para abrir WhatsApp
                                            echo "<script>
                                                window.open('{$whatsappUrl}', '_blank');
                                            </script>";
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('Error al enviar recibo por WhatsApp: ' . $e->getMessage());
                                    }
                                }
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('✅ Pago registrado exitosamente')
                                    ->body("Se registró un pago de $" . number_format($data['valor'], 2) . 
                                        "\nSaldo pendiente: $" . number_format($nuevoSaldo['saldo_pendiente'], 2))
                                    ->success()
                                    ->send();
                                    
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('❌ Error al registrar pago')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalHeading('Registrar Pago - Proceso #')
                        ->modalSubmitActionLabel('Registrar Pago')
                        ->modalCancelActionLabel('Cancelar')
                        ->visible(fn ($record) => $record->total_general > 0),
                    
                        
                ])
                ->label('Acciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('exportar_excel')
                    ->label('Exportar a Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Collection $records) {
                        return (new \App\Exports\ProcesosExportManual($records))
                            ->download('procesos_' . date('Y-m-d_H-i-s'), 'xlsx');
                    }),
                Tables\Actions\BulkAction::make('exportar_pdf')
                    ->label('Exportar a PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->action(function (Collection $records) {
                        return (new \App\Exports\ProcesoFacturaManual($records))
                            ->download('procesos_' . date('Y-m-d_H-i-s'));
                    }),
                    Tables\Actions\BulkAction::make('abrir_whatsapp_cursos')
                    ->label('Abrir WhatsApp con Cursos')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->modalHeading('Preparar mensaje para WhatsApp')
                    ->modalSubmitActionLabel('Abrir WhatsApp')
                    ->modalCancelActionLabel('Cancelar')
                    ->form([
                        Forms\Components\Textarea::make('mensaje_personalizado')
                            ->label('Mensaje adicional (opcional)')
                            ->rows(2)
                            ->placeholder('Ej: Buenas tardes, adjunto lista de cursos para hoy...')
                            ->helperText('Se agregará antes de la tabla'),
                        
                        Forms\Components\Toggle::make('incluir_encabezado')
                            ->label('Incluir tabla con bordes')
                            ->default(true)
                            ->helperText('Tabla con formato especial para WhatsApp'),
                        
                        Forms\Components\Toggle::make('marcar_como_enviado')
                            ->label('Marcar cursos como "Enviado"')
                            ->default(true)
                            ->helperText('Cambiará el estado de los cursos después de abrir WhatsApp'),
                    ])
                    ->action(function (Collection $records, array $data) {
                        // Filtrar solo procesos de tipo curso
                        $procesosCursos = $records->filter(function ($proceso) {
                            return $proceso->tipo_servicio === 'curso';
                        });
                        
                        if ($procesosCursos->isEmpty()) {
                            throw new \Exception('No hay procesos de curso seleccionados.');
                        }
                        
                        // Obtener todos los cursos
                        $cursosData = [];
                        foreach ($procesosCursos as $proceso) {
                            foreach ($proceso->cursos as $curso) {
                                // Obtener nombre
                                $nombre = $curso->nombre ?? ($proceso->tipo_usuario === 'cliente' 
                                    ? $proceso->cliente->nombre 
                                    : ($proceso->tramitador->nombre ?? 'N/A'));
                                
                                // Obtener cédula
                                $cedula = $curso->cedula ?? ($proceso->tipo_usuario === 'cliente' 
                                    ? $proceso->cliente->cedula 
                                    : ($proceso->tramitador->cedula ?? 'N/A'));
                                
                                $cursosData[] = [
                                    'nombre' => $nombre,
                                    'cedula' => $cedula,
                                    'numero_comparendo' => $curso->numero_comparendo ?? 'N/A',
                                    'porcentaje' => $curso->porcentaje ?? '50',
                                    'proceso_id' => $proceso->id,
                                    'curso_id' => $curso->id,
                                ];
                            }
                        }
                        
                        if (empty($cursosData)) {
                            throw new \Exception('No hay cursos para enviar.');
                        }
                        
                        // Formatear mensaje usando el método estático
                        $mensajeFormateado = self::formatearMensajeWhatsApp(
                            $cursosData, 
                            $data['mensaje_personalizado'] ?? '',
                            $data['incluir_encabezado'] ?? true
                        );
                        
                        // Codificar el mensaje para URL
                        $mensajeCodificado = urlencode($mensajeFormateado);
                        
                        // Crear URL de WhatsApp
                        $urlWhatsApp = "https://web.whatsapp.com/send?text={$mensajeCodificado}";
                        
                        // Si se marca como enviado, guardar en sesión para procesar después
                        if ($data['marcar_como_enviado'] ?? true) {
                            \App\Models\ProcesoCurso::whereIn('id', array_column($cursosData, 'curso_id'))
                                ->update(['estado' => 'enviado']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Cursos marcados como enviados')
                                ->body(count($cursosData) . ' cursos han sido marcados como "Enviado"')
                                ->success()
                                ->send();
                        }
                        // Redirigir a WhatsApp Web
                        return redirect()->away($urlWhatsApp);
                    })
                    ->modalDescription('Se abrirá WhatsApp Web con el mensaje listo para enviar. Solo selecciona el grupo y envía.')
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    protected function afterSave(): void
    {
        // Marcar cursos como enviados si se abrió WhatsApp
        if (session()->has('cursos_a_marcar')) {
            $cursoIds = session('cursos_a_marcar');
            
            \App\Models\ProcesoCurso::whereIn('id', $cursoIds)
                ->update(['estado' => 'enviado']);
                
            session()->forget('cursos_a_marcar');
            
            \Filament\Notifications\Notification::make()
                ->title('Cursos marcados como enviados')
                ->success()
                ->send();
        }
    }

    private static function formatearMensajeWhatsApp(
        array $cursosData, 
        string $mensajePersonalizado = '', 
        bool $incluirEncabezado = true
    ): string {
        $mensaje = '';
        
        // Mensaje personalizado
        if (!empty($mensajePersonalizado)) {
            $mensaje .= trim($mensajePersonalizado) . "\n\n";
        }
        
        // Agrupar por porcentaje
        $cursos50 = array_filter($cursosData, fn($c) => $c['porcentaje'] == '50');
        $cursos20 = array_filter($cursosData, fn($c) => $c['porcentaje'] == '20');
        
        if (!empty($cursos50)) {
            $mensaje .= "📋 *CURSOS 50%*\n";
            $mensaje .= self::generarTablaWhatsApp($cursos50, $incluirEncabezado);
            $mensaje .= "\n";
        }
        
        if (!empty($cursos20)) {
            if (!empty($cursos50)) {
                $mensaje .= "\n";
            }
            $mensaje .= "📋 *CURSOS 20%*\n";
            $mensaje .= self::generarTablaWhatsApp($cursos20, $incluirEncabezado);
        }
        
        // Total
        $totalCursos = count($cursosData);
        $mensaje .= "\n" . str_repeat('═', 40) . "\n";
        $mensaje .= "📊 *TOTAL: {$totalCursos} cursos*\n";
        
        // Fecha actual
        $mensaje .= "📅 " . now()->format('d/m/Y H:i') . "\n";
        
        return trim($mensaje);
    }
    
    private static function generarTablaWhatsApp(array $cursosData, bool $incluirEncabezado): string
    {
        $tabla = '';
        
        if ($incluirEncabezado) {
            $tabla .= "┌───────────────────────────────────────────────────────────┐\n";
            $tabla .= "│ Nombre             - Cédula     - # Comparendo - Descuento│\n";
            $tabla .= "├───────────────────────────────────────────────────────────┤\n";
        }
        
        foreach ($cursosData as $curso) {
            $nombre = self::mb_str_pad(mb_substr($curso['nombre'], 0, 18), 18);
            $cedula = str_pad(substr($curso['cedula'], 0, 10), 10);
            $comparendo = str_pad(substr($curso['numero_comparendo'], 0, 12), 12);
            $porcentaje = str_pad($curso['porcentaje'] . '%', 8);
            
            $tabla .= "│ * {$nombre} - {$cedula} - {$comparendo} - {$porcentaje} │\n";
        }
        
        if ($incluirEncabezado) {
            $tabla .= "└───────────────────────────────────────────────────────────┘\n";
        }
        
        return $tabla;
    }

    private static function mb_str_pad($string, $length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = null)
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        
        $pad_length = $length - mb_strlen($string, $encoding);
        
        if ($pad_length <= 0) {
            return $string;
        }
        
        switch ($pad_type) {
            case STR_PAD_LEFT:
                $left_pad = str_repeat($pad_string, ceil($pad_length / mb_strlen($pad_string, $encoding)));
                return mb_substr($left_pad, 0, $pad_length, $encoding) . $string;
            case STR_PAD_RIGHT:
                $right_pad = str_repeat($pad_string, ceil($pad_length / mb_strlen($pad_string, $encoding)));
                return $string . mb_substr($right_pad, 0, $pad_length, $encoding);
            case STR_PAD_BOTH:
                $pad_length_left = floor($pad_length / 2);
                $pad_length_right = $pad_length - $pad_length_left;
                $left_pad = str_repeat($pad_string, ceil($pad_length_left / mb_strlen($pad_string, $encoding)));
                $right_pad = str_repeat($pad_string, ceil($pad_length_right / mb_strlen($pad_string, $encoding)));
                return mb_substr($left_pad, 0, $pad_length_left, $encoding) . 
                    $string . 
                    mb_substr($right_pad, 0, $pad_length_right, $encoding);
        }
        
        return $string;
    }
    

    private static function generarPdfFactura(Proceso $proceso): string
    {
        // Generar HTML de la factura usando tu método existente
        $html = self::generateSingleInvoice($proceso);
        
        // Crear nombre único para el archivo
        $filename = "factura_proceso_{$proceso->id}_" . time() . ".html";
        $tempPath = storage_path('app/public/facturas/' . $filename);
        
        // Asegurar que existe el directorio
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        file_put_contents($tempPath, $html);
        
        return $tempPath;
    }

    private static function obtenerUrlDescarga(string $filePath, Proceso $proceso): string
    {
        $filename = basename($filePath);
        return url("/storage/facturas/{$filename}");
    }

    private static function abrirWhatsApp(string $telefono, string $mensaje): void
    {
        // Limpiar número de teléfono
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
        
        // Codificar mensaje para URL
        $mensajeCodificado = urlencode($mensaje);
        
        // Crear URL de WhatsApp Web
        $whatsappUrl = "https://web.whatsapp.com/send?phone={$telefonoLimpio}&text={$mensajeCodificado}";
        
        // Esto se manejará desde JavaScript en el frontend
        echo "<script>window.open('{$whatsappUrl}', '_blank');</script>";
    }

    private static function enviarEmailSimple(string $email, string $mensaje, string $filePath, Proceso $proceso): void
    {
        try {
            // Configuración básica de email
            $subject = "Factura Proceso #{$proceso->id} - Sistema Jurídico de Tránsito";
            
            // Aquí iría la lógica para enviar email
            // Por ahora solo registramos en la base de datos
            \App\Models\EnvioFactura::create([
                'proceso_id' => $proceso->id,
                'email_destino' => $email,
                'mensaje' => $mensaje,
                'ruta_archivo' => $filePath,
                'enviado_por' => auth()->id(),
                'fecha_envio' => now(),
                'estado' => 'pendiente', // Cambiar a 'enviado' cuando implementes SMTP
            ]);
            
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar email: " . $e->getMessage());
        }
    }

    private function enviarWhatsApp(string $numero, string $mensaje, string $pdfPath)
    {
        // Para WhatsApp Web: crear un HTML simple con el mensaje y enlace al PDF
        $urlPdf = url('/factura/download/' . basename($pdfPath));
        $mensajeCompleto = $mensaje . "\n\nDescargar factura: " . $urlPdf;
        $mensajeCodificado = urlencode($mensajeCompleto);
        
        // Formatear número (remover espacios, guiones, etc.)
        $numeroFormateado = preg_replace('/[^0-9]/', '', $numero);
        
        // Crear URL de WhatsApp
        $urlWhatsApp = "https://web.whatsapp.com/send?phone={$numeroFormateado}&text={$mensajeCodificado}";
        
        // Esto abrirá WhatsApp Web
        return redirect()->away($urlWhatsApp);
    }

    private function enviarEmail(string $email, string $mensaje, string $pdfPath, Proceso $proceso)
    {
        try {
            // Usar el sistema de correo de Laravel
            \Mail::raw($mensaje, function($message) use ($email, $proceso, $pdfPath) {
                $message->to($email)
                    ->subject("Factura Proceso #{$proceso->id} - Sistema Jurídico de Tránsito")
                    ->attach($pdfPath, [
                        'as' => "factura_proceso_{$proceso->id}.html",
                        'mime' => 'text/html',
                    ]);
            });
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar email: " . $e->getMessage());
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcesos::route('/'),
            'create' => Pages\CreateProceso::route('/create'),
            'edit' => Pages\EditProceso::route('/{record}/edit'),
            'view' => Pages\ViewProceso::route('/{record}'),
        ];
    }

    // En ProcesoResource.php, añade este método después del método form() o antes de table()
    private static function generateSingleInvoice(Proceso $proceso): string
    {
        // Obtener datos del proceso
        $totalCursos = $proceso->cursos()->sum('valor_recibir');
        $totalRenovaciones = $proceso->renovaciones()->sum('valor_total');
        $totalLicencias = $proceso->licencias()->sum('valor_total_licencia');
        $totalTraspasos = $proceso->traspasos()->sum('total_recibir');
        $totalRunts = $proceso->runts()->sum('valor_recibir');
        $totalControversias = $proceso->controversias()->sum('valor_controversia');
        
        // Obtener información del cliente/tramitador
        $clienteNombre = $proceso->tipo_usuario == 'cliente' ? ($proceso->cliente->nombre ?? '-') : '-';
        $clienteCedula = $proceso->tipo_usuario == 'cliente' ? ($proceso->cliente->cedula ?? '-') : '-';
        $tramitadorNombre = $proceso->tipo_usuario == 'tramitador' ? ($proceso->tramitador->nombre ?? '-') : '-';
        $tramitadorCedula = $proceso->tipo_usuario == 'tramitador' ? ($proceso->tramitador->cedula ?? '-') : '-';
        
        // Usar el método del modelo para obtener cédula completa
        $cedulaBeneficiario = $proceso->cedula_completa ?? '-';

        // Generar HTML de la factura
        $html = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Factura Proceso #' . $proceso->id . '</title>
            <style>
                @media print {
                    @page {
                        margin: 15mm;
                        size: A4;
                    }
                    
                    body {
                        margin: 0;
                        padding: 0;
                        font-size: 11pt;
                    }
                    
                    .no-print { display: none !important; }
                }
                
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                
                .invoice-container {
                    max-width: 210mm;
                    margin: 0 auto;
                    background: white;
                    padding: 25px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    border-radius: 5px;
                }
                
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #2c3e50;
                }
                
                .company-info {
                    flex: 1;
                }
                
                .invoice-info {
                    text-align: right;
                }
                
                .company-name {
                    font-size: 24px;
                    font-weight: bold;
                    color: #2c3e50;
                    margin: 0 0 10px 0;
                }
                
                .invoice-title {
                    font-size: 20px;
                    color: #2c3e50;
                    margin: 0 0 10px 0;
                }
                
                .invoice-number {
                    font-size: 16px;
                    color: #666;
                    margin: 5px 0;
                }
                
                .client-info {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 25px;
                    border-left: 4px solid #2c3e50;
                }
                
                .client-header {
                    font-size: 14px;
                    font-weight: bold;
                    color: #2c3e50;
                    margin-bottom: 10px;
                }
                
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                }
                
                .info-item {
                    padding: 5px 0;
                }
                
                .badge {
                    display: inline-block;
                    padding: 3px 8px;
                    border-radius: 12px;
                    font-size: 10px;
                    font-weight: bold;
                    color: white;
                }
                
                .badge-cliente {
                    background-color: #28a745;
                }
                
                .badge-tramitador {
                    background-color: #17a2b8;
                }
                
                .section-title {
                    font-size: 16px;
                    color: #2c3e50;
                    margin: 25px 0 15px;
                    padding-bottom: 5px;
                    border-bottom: 1px solid #ddd;
                }
                
                .services-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                    font-size: 11px;
                }
                
                .services-table th {
                    background-color: #2c3e50;
                    color: white;
                    padding: 8px 10px;
                    text-align: left;
                    border: 1px solid #ddd;
                }
                
                .services-table td {
                    padding: 6px 8px;
                    border: 1px solid #ddd;
                }
                
                .services-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                
                .amount {
                    text-align: right;
                }
                
                .total-section {
                    margin-top: 30px;
                    text-align: right;
                }
                
                .total-amount {
                    font-size: 18px;
                    font-weight: bold;
                    color: #2c3e50;
                    padding: 10px;
                    background: #e8f5e8;
                    border-radius: 5px;
                    display: inline-block;
                }
                
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    border-top: 1px solid #ddd;
                    padding-top: 15px;
                    color: #666;
                    font-size: 10px;
                }
                
                .print-btn {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    margin: 20px 0;
                }
                
                .print-btn:hover {
                    background: #0056b3;
                }
                
                .instructions {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                    border-left: 4px solid #007bff;
                }
            </style>
        </head>
        <body>
            <div class="invoice-container">
                <div class="instructions no-print">
                    <strong>📋 Instrucciones:</strong><br>
                    1. Haz clic en "Imprimir"<br>
                    2. Selecciona "Guardar como PDF"<br>
                    3. Configura márgenes a 15mm<br>
                    4. Haz clic en "Guardar"<br>
                    <button class="print-btn" onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>
                </div>
                
                <div class="header">
                    <div class="company-info">
                        <h1 class="company-name">SISTEMA JURÍDICO DE TRÁNSITO</h1>
                        <div style="color: #666;">
                            <div>Nit: 900.000.000-1</div>
                            <div>Fecha: ' . $proceso->created_at->format('d/m/Y') . '</div>
                        </div>
                    </div>
                    <div class="invoice-info">
                        <h2 class="invoice-title">FACTURA</h2>
                        <div class="invoice-number">No. ' . str_pad($proceso->id, 6, '0', STR_PAD_LEFT) . '</div>
                        <div>Hora: ' . $proceso->created_at->format('H:i:s') . '</div>
                    </div>
                </div>
                
                <div class="client-info">
                    <div class="client-header">INFORMACIÓN DEL ' . ($proceso->tipo_usuario == 'cliente' ? 'CLIENTE' : 'TRAMITADOR') . '</div>
                    <div class="info-grid">';
            
            if ($proceso->tipo_usuario == 'cliente') {
                $html .= '
                        <div class="info-item">
                            <strong>Nombre Cliente:</strong> ' . $clienteNombre . '
                        </div>
                        <div class="info-item">
                            <strong>Cédula Cliente:</strong> ' . $clienteCedula . '
                        </div>';
            } else {
                $html .= '
                        <div class="info-item">
                            <strong>Nombre Tramitador:</strong> ' . $tramitadorNombre . '
                        </div>
                        <div class="info-item">
                            <strong>Cédula Tramitador:</strong> ' . $tramitadorCedula . '
                        </div>';
            }
            
            $html .= '
                        <div class="info-item">
                            <strong>Cédula Beneficiario:</strong> ' . $cedulaBeneficiario . '
                        </div>
                        <div class="info-item">
                            <strong>Tipo:</strong> ';
            
            if ($proceso->tipo_usuario == 'cliente') {
                $html .= '<span class="badge badge-cliente">Cliente</span>';
            } else {
                $html .= '<span class="badge badge-tramitador">Tramitador</span>';
            }
            
            $html .= '
                        </div>
                    </div>
                </div>';
            
            // Mostrar secciones solo si tienen datos
            $hasServices = false;
            
            // CURSOS
            if ($proceso->cursos()->count() > 0) {
                $hasServices = true;
                $html .= '
                <div class="section-title">📚 CURSOS</div>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Cédula</th>
                            <th>Porcentaje</th>
                            <th>Valor a Recibir</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                foreach ($proceso->cursos as $curso) {
                    $html .= '
                        <tr>
                            <td>' . ($curso->curso->categoria ?? 'N/A') . '</td>
                            <td>' . ($curso->cedula ?? '-') . '</td>
                            <td>' . ($curso->porcentaje ?? '0') . '%</td>
                            <td class="amount">$ ' . number_format($curso->valor_recibir, 2, ',', '.') . '</td>
                        </tr>';
                }
                
                $html .= '
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f0f8ff;">
                            <td colspan="3" class="amount"><strong>Subtotal Cursos:</strong></td>
                            <td class="amount"><strong>$ ' . number_format($totalCursos, 2, ',', '.') . '</strong></td>
                        </tr>
                    </tfoot>
                </table>';
            }
            
            // RENOVACIONES
            if ($proceso->renovaciones()->count() > 0) {
                $hasServices = true;
                $html .= '
                <div class="section-title">🔄 RENOVACIONES</div>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Renovación</th>
                            <th>Cédula</th>
                            <th>Examen</th>
                            <th>Lámina</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                foreach ($proceso->renovaciones as $renovacion) {
                    $html .= '
                        <tr>
                            <td>' . ($renovacion->renovacion->nombre ?? 'N/A') . '</td>
                            <td>' . ($renovacion->cedula ?? '-') . '</td>
                            <td>' . ($renovacion->incluye_examen ? 'Sí' : 'No') . '</td>
                            <td>' . ($renovacion->incluye_lamina ? 'Sí' : 'No') . '</td>
                            <td class="amount">$ ' . number_format($renovacion->valor_total, 2, ',', '.') . '</td>
                        </tr>';
                }
                
                $html .= '
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f0f8ff;">
                            <td colspan="4" class="amount"><strong>Subtotal Renovaciones:</strong></td>
                            <td class="amount"><strong>$ ' . number_format($totalRenovaciones, 2, ',', '.') . '</strong></td>
                        </tr>
                    </tfoot>
                </table>';
            }
            
            // LICENCIAS
            if ($proceso->licencias()->count() > 0) {
                $hasServices = true;
                $html .= '
                <div class="section-title">📄 LICENCIAS</div>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Categorías</th>
                            <th>Escuela</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                foreach ($proceso->licencias as $licencia) {
                    $categorias = '-';
                    if (is_array($licencia->categorias_seleccionadas) && !empty($licencia->categorias_seleccionadas)) {
                        $categoriasArray = [];
                        foreach ($licencia->categorias_seleccionadas as $categoriaId) {
                            // Suponiendo que tienes un modelo CategoriaLicencia
                            $categoria = \App\Models\CategoriaLicencia::find($categoriaId);
                            if ($categoria) {
                                $categoriasArray[] = $categoria->nombre;
                            }
                        }
                        $categorias = implode(', ', $categoriasArray);
                    }
                    
                    $html .= '
                        <tr>
                            <td>' . ($licencia->cedula ?? '-') . '</td>
                            <td>' . $categorias . '</td>
                            <td>' . ($licencia->escuela->nombre ?? 'N/A') . '</td>
                            <td class="amount">$ ' . number_format($licencia->valor_total_licencia, 2, ',', '.') . '</td>
                        </tr>';
                }
                
                $html .= '
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f0f8ff;">
                            <td colspan="3" class="amount"><strong>Subtotal Licencias:</strong></td>
                            <td class="amount"><strong>$ ' . number_format($totalLicencias, 2, ',', '.') . '</strong></td>
                        </tr>
                    </tfoot>
                </table>';
            }
            
            // Si no hay servicios, mostrar mensaje
            if (!$hasServices) {
                $html .= '
                <div style="text-align: center; padding: 40px; color: #666;">
                    <h3>⚠️ No hay servicios registrados en este proceso</h3>
                    <p>Este proceso no tiene cursos, renovaciones, licencias u otros servicios.</p>
                </div>';
            }
            
            // TOTAL GENERAL
            $html .= '
                <div class="total-section">
                    <div class="total-amount">
                        💰 TOTAL GENERAL: $ ' . number_format($proceso->total_general, 2, ',', '.') . '
                    </div>
                    <div style="margin-top: 10px; color: #666; font-size: 11px;">
                        Pesos Colombianos (COP)
                    </div>
                </div>
                
                <div class="footer">
                    <p>Sistema Jurídico de Tránsito • Factura No. ' . str_pad($proceso->id, 6, '0', STR_PAD_LEFT) . '</p>
                    <p>Generado automáticamente el ' . now()->format('d/m/Y H:i:s') . '</p>
                </div>
                
                <script>
                    // Auto-imprimir después de 1 segundo (opcional)
                    setTimeout(function() {
                        window.print();
                    }, 1000);
                </script>
            </div>
        </body>
        </html>';
            
        return $html;
    }

    private static function generarObservacion($valor, $metodo, $saldoPendiente): string
    {
        $observaciones = [];
        
        if ($valor > 0) {
            $observaciones[] = "Pago de $" . number_format($valor, 2) . " por " . self::getMetodoTexto($metodo);
            
            if ($valor < $saldoPendiente) {
                $observaciones[] = "Abono parcial";
            } elseif ($valor == $saldoPendiente) {
                $observaciones[] = "Pago completo del saldo";
            } elseif ($valor > $saldoPendiente) {
                $observaciones[] = "Pago excedente";
            }
        }
        
        return implode('. ', $observaciones);
    }

    private static function getMetodoTexto($metodo): string
    {
        return match($metodo) {
            'efectivo' => 'efectivo',
            'transferencia' => 'transferencia bancaria',
            'tarjeta_credito' => 'tarjeta de crédito',
            'tarjeta_debito' => 'tarjeta de débito',
            'nequi' => 'Nequi',
            'daviplata' => 'Daviplata',
            'pse' => 'PSE',
            'otro' => 'otro medio',
            default => $metodo,
        };
    }

    private function enviarReciboWhatsApp(Proceso $proceso, Pago $pago, array $saldo): void
    {
        try {
            // Determinar destinatario
            $telefono = null;
            if ($proceso->tipo_usuario === 'cliente' && $proceso->cliente && $proceso->cliente->telefono) {
                $telefono = $proceso->cliente->telefono;
            } elseif ($proceso->tipo_usuario === 'tramitador' && $proceso->tramitador && $proceso->tramitador->telefono) {
                $telefono = $proceso->tramitador->telefono;
            }
            
            if (!$telefono) {
                return; // No hay teléfono para enviar
            }
            
            // Preparar mensaje
            $mensaje = $this->generarMensajeRecibo($proceso, $pago, $saldo);
            $mensajeCodificado = urlencode($mensaje);
            
            // Limpiar número de teléfono
            $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
            
            // Crear URL de WhatsApp
            $whatsappUrl = "https://web.whatsapp.com/send?phone={$telefonoLimpio}&text={$mensajeCodificado}";
            
            // Abrir WhatsApp en nueva pestaña (esto se ejecutará en el frontend)
            echo "<script>
                window.open('{$whatsappUrl}', '_blank');
            </script>";
            
        } catch (\Exception $e) {
            // Silenciar errores de WhatsApp, no interrumpir el registro del pago
            \Log::error('Error al enviar recibo por WhatsApp: ' . $e->getMessage());
        }
    }

    private function generarMensajeRecibo(Proceso $proceso, Pago $pago, array $saldo): string
    {
        $nombreDestinatario = $proceso->tipo_usuario === 'cliente' 
            ? ($proceso->cliente->nombre ?? 'Cliente')
            : ($proceso->tramitador->nombre ?? 'Tramitador');
        
        return "✅ *COMPROBANTE DE PAGO* ✅\n\n" .
            "📋 *Proceso:* #{$proceso->id}\n" .
            "👤 *Destinatario:* {$nombreDestinatario}\n" .
            "💰 *Monto Pagado:* $" . number_format($pago->valor, 2) . "\n" .
            "💳 *Método:* " . ucfirst($pago->metodo) . "\n" .
            ($pago->referencia ? "🔢 *Referencia:* {$pago->referencia}\n" : "") .
            "📅 *Fecha:* " . $pago->fecha_pago->format('d/m/Y') . "\n" .
            "---\n" .
            "📊 *RESUMEN FINANCIERO*\n" .
            "• Total Proceso: $" . number_format($proceso->total_general, 2) . "\n" .
            "• Total Pagado: $" . number_format($saldo['total_pagado'], 2) . "\n" .
            "• Saldo Pendiente: $" . number_format($saldo['saldo_pendiente'], 2) . "\n" .
            "• Porcentaje Pagado: " . number_format($saldo['porcentaje_pagado'], 1) . "%\n" .
            "---\n" .
            "📝 *Observaciones:*\n" .
            ($pago->observaciones ?: "Pago registrado correctamente") . "\n\n" .
            "Gracias por su pago. 🎉";
    }

}
