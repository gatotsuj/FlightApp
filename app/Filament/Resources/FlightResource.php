<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightResource\Pages;
use App\Models\Flight;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FlightResource extends Resource
{
    protected static ?string $model = Flight::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Flight Information')
                        ->schema([
                            TextInput::make('flight_number')
                                ->label('Flight Number')
                                ->unique(ignoreRecord: true)
                                ->required()
                                ->validationMessages([
                                    'required' => 'The Flight Number field is required.',
                                    'unique' => 'The Flight Number has already been taken.',
                                ])
                                ->maxLength(20),

                            Select::make('airline_id')
                                ->relationship('airline', 'name')
                                ->placeholder('Select Airline')
                                ->required()
                                ->validationMessages([
                                    'required' => 'The Airline field is required.',
                                ])
                                ->label('Airline'),
                        ]),

                    Wizard\Step::make('Flight Segments')
                        ->schema([
                            Repeater::make('flight_segments')
                                ->label('Flight Segments')
                                ->relationship('segments')
                                ->schema([
                                    TextInput::make('sequence')
                                        ->label('Sequence Number')
                                        ->required()
                                        ->numeric()
                                        ->validationMessages([
                                            'required' => 'The Sequence Number field is required.',
                                        ])
                                        ->maxLength(100),

                                    Select::make('airport_id')
                                        ->relationship('airport', 'name')
                                        ->placeholder('Select Airport')
                                        ->label('Airport')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'The Airport field is required.',
                                        ]),

                                    DateTimePicker::make('time')
                                        ->label('Time')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'The Departure Time field is required.',
                                        ]),
                                ]),
                        ]),

                    Wizard\Step::make('Flight Classes')
                        ->schema([
                            Repeater::make('flight_classes')
                                ->label('Flight Classes')
                                ->relationship('classes')
                                ->schema([
                                    Select::make('class_type')
                                        ->label('Class Type')
                                        ->options([
                                            'economy' => 'Economy',
                                            'business' => 'Business',
                                        ])
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'The Class Type field is required.',
                                        ]),

                                    TextInput::make('price')
                                        ->label('Price')
                                        ->numeric()
                                        ->prefix('IDR')
                                        ->required()
                                        ->minValue(0)
                                        ->validationMessages([
                                            'required' => 'The Price field is required.',
                                            'numeric' => 'The Price must be a number.',
                                        ]),

                                    TextInput::make('total_seats')
                                        ->label('Total Seats')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->validationMessages([
                                            'required' => 'The Seats Available field is required.',
                                            'numeric' => 'The Seats Available must be a number.',
                                        ]),

                                    Select::make('facilities')
                                        ->label('Facilities')
                                        ->relationship('facilities', 'name')
                                        ->multiple()
                                        ->searchable()
                                        ->placeholder('Select Facilities')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'The Facilities field is required.',
                                        ]),
                                ]),
                        ]),
                ])
                    ->columnSpanFull(), // Tambahkan ini untuk full width
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('flight_number')->label('Flight Number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('airline.name')->label('Airline')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('segments')
                    ->label('Route And Duration')
                    ->formatStateUsing(function ($state, $record) {
                        $firstSegment = $record->segments->sortBy('sequence')->first();
                        $lastSegment = $record->segments->sortBy('sequence')->last();
                        $route = $firstSegment->airport->iata_code.' - '.$lastSegment->airport->iata_code;
                        $duration = (new \DateTime($lastSegment->time))->format('d F Y H:i').' - '.(new \DateTime($firstSegment->time))->format('d F Y H:i');

                        return $route.' | '.$duration;
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlights::route('/'),
            'create' => Pages\CreateFlight::route('/create'),
            'edit' => Pages\EditFlight::route('/{record}/edit'),
        ];
    }
}
