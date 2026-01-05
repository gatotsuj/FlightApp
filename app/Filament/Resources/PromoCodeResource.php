<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('code')
                    ->label('Promo Code')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->validationMessages([
                        'required' => 'The Promo Code field is required.',
                        'unique' => 'The Promo Code has already been taken.',
                    ])
                    ->maxLength(50),
                Select::make('discount_type')
                    ->label('Discount Type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'The Discount Type field is required.',
                    ]),

                TextInput::make('discount')
                    ->label('Discount Value')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(fn (Get $get) => $get('discount_type') === 'percentage' ? 100 : null
                    )
                    ->helperText(fn (Get $get) => $get('discount_type') === 'percentage'
                            ? 'Value must be between 0 and 100%'
                            : 'Enter fixed discount amount'
                    )
                    ->validationMessages([
                        'required' => 'The Discount Value field is required.',
                        'numeric' => 'The Discount Value must be a number.',
                        'max' => 'Percentage discount cannot exceed 100.',
                    ]),

                DateTimePicker::make('valid_until')
                    ->label('Valid Until')
                    ->date()
                    ->required()
                    ->validationMessages([
                        'required' => 'The Valid Until field is required.',
                        'date' => 'The Valid Until must be a valid date.',
                    ]),
                Toggle::make('is_used')
                    ->label('Is Used')

                    ->required()
                    ->validationMessages([
                        'required' => 'The Is Used field is required.',
                        'boolean' => 'The Is Used field must be true or false.',
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('code')->label('Promo Code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('discount_type')->label('Discount Type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('discount')->label('Discount Value')->searchable()->sortable()
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage' ? $state.' %' : 'Rp '.number_format($state, 2)),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Valid Until')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->color(fn ($state) => Carbon::parse($state)->isFuture()
                            ? 'success'   // hijau → belum lewat
                            : 'danger'    // merah → sudah lewat
                    ),
                Tables\Columns\BooleanColumn::make('is_used')->label('Is Used')->searchable()->sortable(),

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
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
