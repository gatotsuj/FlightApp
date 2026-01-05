<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirlineResource\Pages;
use App\Models\Airline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AirlineResource extends Resource
{
    protected static ?string $model = Airline::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\FileUpload::make('logo')
                    ->label('Upload Airline Logo')
                    ->image()
                    ->directory('airline')
                    ->maxSize(10240) // Max size in KB
                    ->columnSpan(2)
                    ->validationMessages([
                        'maxSize' => 'The logo must not be greater than 10 MB.',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Airline Name')
                    ->required()
                    ->validationMessages([
                        'required' => 'The Airline Name field is required.',
                    ])
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->label('Airline Code')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->validationMessages([
                        'required' => 'The Airline Code field is required.',
                        'unique' => 'The Airline Code has already been taken.',
                    ])
                    ->maxLength(10),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Airline Logo')
                    ->rounded(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Airline Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Airline Code')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAirlines::route('/'),
            'create' => Pages\CreateAirline::route('/create'),
            'edit' => Pages\EditAirline::route('/{record}/edit'),
        ];
    }
}
