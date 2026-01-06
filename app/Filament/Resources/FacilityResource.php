<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityResource\Pages;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\FileUpload::make('image')
                    ->label('Upload Facility Image')
                    ->image()
                    ->directory('facility')
                    ->maxSize(10240) // Max size in KB
                    ->columnSpan(2)
                    ->validationMessages([
                        'maxSize' => 'The icon must not be greater than 10 MB.',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Facility Name')
                    ->required()
                    ->validationMessages([
                        'required' => 'The Facility Name field is required.',
                    ])
                    ->maxLength(50),

                Forms\Components\Textarea::make('description')
                    ->label('Facility Description')
                    ->required()
                    ->columnSpan(2)
                    ->validationMessages([
                        'required' => 'The Facility Description field is required.',
                    ])
                    ->maxLength(1000),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('image')
                    ->label('Facility Image')
                    ->rounded(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Facility Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Facility Description')
                    ->limit(50)
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
            'index' => Pages\ListFacilities::route('/'),
            'create' => Pages\CreateFacility::route('/create'),
            'edit' => Pages\EditFacility::route('/{record}/edit'),
        ];
    }
}
