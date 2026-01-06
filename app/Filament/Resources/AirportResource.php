<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirportResource\Pages;
use App\Models\Airport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AirportResource extends Resource
{
    protected static ?string $model = Airport::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                FileUpload::make('image')
                    ->label('Upload Airport Image')
                    ->image()
                    ->directory('airport')
                    ->maxSize(10240) // Max size in KB
                    ->columnSpan(2)
                    ->validationMessages([
                        'maxSize' => 'The image must not be greater than 10 MB.',
                    ])
                    ->required(),

                TextInput::make('iata_code')
                    ->label('IATA Code')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->validationMessages([
                        'required' => 'The IATA Code field is required.',
                        'unique' => 'The IATA Code has already been taken.',

                    ])
                    ->maxLength(255),

                TextInput::make('name')
                    ->label('Airport Name')
                    ->required()
                    ->validationMessages([
                        'required' => 'The Airport Name field is required.',
                    ])
                    ->maxLength(255),

                TextInput::make('city')
                    ->label('City')
                    ->required()
                    ->validationMessages([
                        'required' => 'The City field is required.',
                    ])
                    ->maxLength(255),

                TextInput::make('country')
                    ->label('Country')
                    ->required()
                    ->validationMessages([
                        'required' => 'The Country field is required.',
                    ])
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('image')
                    ->label('Airport Image')
                    ->rounded()
                    ->size(50),

                Tables\Columns\TextColumn::make('iata_code')
                    ->label('IATA Code')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Airport Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
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
            'index' => Pages\ListAirports::route('/'),
            'create' => Pages\CreateAirport::route('/create'),
            'edit' => Pages\EditAirport::route('/{record}/edit'),
        ];
    }
}
