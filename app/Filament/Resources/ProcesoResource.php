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

    public static function form(Form $form): Form
    {
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
    protected static function calcularValoresCurso(Set $set, Get $get, $porcentaje)
    {
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
    
    protected static function calcularValoresRenovacion(Set $set, Get $get, $renovacionId, $incluyeExamen, $incluyeLamina)
    {
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
    
    protected static function calcularValoresLicencia($set, $escuelaId)
    {
        if (!$escuelaId) {
            return;
        }

        $escuela = Escuela::find($escuelaId);
        $set('valor_carta_escuela', $escuela->valor_carta_escuela);

        // Recalcular total
        self::recalcularTotalLicencia($set, $escuelaId);
    }

    protected static function calcularValorExamenMedico(Set $set, Get $get, $estado)
    {
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

    protected static function calcularValorImpresion(Set $set, Get $get, $estado)
    {
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

    protected static function calcularValorSinCurso(Set $set, Get $get, $estado)
    {
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

    protected static function recalcularTotalLicencia(Set $set, Get $get)
    {
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

    protected static function calcularTotalTraspaso(Set $set, Get $get)
    {
        $derecho = $get('derecho_traspaso') ?? 0;
        $porcentaje = $get('porcentaje') ?? 0;
        $honorarios = $get('honorarios') ?? 0;
        $comision = $get('comision') ?? 0;
        $total = $derecho + $porcentaje + $honorarios + $comision;
        $set('total_recibir', $total);
    }

    protected static function calcularTotalRunt(Set $set, Get $get)
    {
        $comision = $get('comision') ?? 0;
        $pago = $get('pago') ?? 0;
        $honorarios = $get('honorarios') ?? 0;
        $total = $comision + $pago + $honorarios;
        $set('valor_recibir', $total);
    }

    protected static function calcularValorControversia(Set $set, Get $get, $categoriaId)
    {
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
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('total_general')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->color('success'),

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
                    Tables\Actions\Action::make('duplicar')
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function (Proceso $record) {
                            // Duplicar el proceso
                            $nuevoProceso = $record->replicate();
                            $nuevoProceso->created_at = now();
                            $nuevoProceso->save();
                            
                            // Duplicar los cursos si existen
                            if ($record->cursos()->exists()) {
                                foreach ($record->cursos as $curso) {
                                    $nuevoCurso = $curso->replicate();
                                    $nuevoCurso->proceso_id = $nuevoProceso->id;
                                    $nuevoCurso->save();
                                }
                            }
                            
                            // Duplicar las renovaciones si existen
                            if ($record->renovaciones()->exists()) {
                                foreach ($record->renovaciones as $renovacion) {
                                    $nuevaRenovacion = $renovacion->replicate();
                                    $nuevaRenovacion->proceso_id = $nuevoProceso->id;
                                    $nuevaRenovacion->save();
                                }
                            }
                            
                            // Duplicar licencias
                            if ($record->licencias()->exists()) {
                                foreach ($record->licencias as $licencia) {
                                    $nuevaLicencia = $licencia->replicate();
                                    $nuevaLicencia->proceso_id = $nuevoProceso->id;
                                    $nuevaLicencia->save();
                                }
                            }
                            
                            // Duplicar traspasos
                            if ($record->traspasos()->exists()) {
                                foreach ($record->traspasos as $traspaso) {
                                    $nuevoTraspaso = $traspaso->replicate();
                                    $nuevoTraspaso->proceso_id = $nuevoProceso->id;
                                    $nuevoTraspaso->save();
                                }
                            }
                            
                            // Duplicar RUNTS
                            if ($record->runts()->exists()) {
                                foreach ($record->runts as $runt) {
                                    $nuevoRunt = $runt->replicate();
                                    $nuevoRunt->proceso_id = $nuevoProceso->id;
                                    $nuevoRunt->save();
                                }
                            }
                            
                            // Duplicar controversias
                            if ($record->controversias()->exists()) {
                                foreach ($record->controversias as $controversia) {
                                    $nuevaControversia = $controversia->replicate();
                                    $nuevaControversia->proceso_id = $nuevoProceso->id;
                                    $nuevaControversia->save();
                                }
                            }
                            
                            // Duplicar estados de cuenta
                            if ($record->estadoCuentas()->exists()) {
                                foreach ($record->estadoCuentas as $estadoCuenta) {
                                    $nuevoEstadoCuenta = $estadoCuenta->replicate();
                                    $nuevoEstadoCuenta->proceso_id = $nuevoProceso->id;
                                    $nuevoEstadoCuenta->save();
                                }
                            }
                            
                            // Recalcular total
                            $nuevoProceso->calcularTotalGeneral();
                            
                            return redirect()->route('filament.admin.resources.procesos.edit', $nuevoProceso);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Duplicar Proceso')
                        ->modalDescription('¿Estás seguro de que quieres duplicar este proceso?')
                        ->modalSubmitActionLabel('Sí, duplicar'),
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
}
