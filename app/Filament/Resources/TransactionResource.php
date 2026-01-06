<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Informasi Umum')
                    ->schema([
                        //
                        TextInput::make('code')
                            ->label('Code Transaction')
                            ->required()
                            ->maxLength(255),

                        Select::make('flight_id')
                            ->relationship('flight', 'flight_number')
                            ->placeholder('Select Flight')
                            ->required()
                            ->label('Flight Number'),

                        Select::make('flight_class_id')
                            ->relationship('flightClass', 'class_type')
                            ->placeholder('Select Flight Class')
                            ->required()
                            ->label('Flight Class'),
                    ]),

                Section::make('Informasi Penumpang')
                    ->schema([
                        //
                        TextInput::make('name')
                            ->label('Passenger Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->required()
                            ->maxLength(20),
                        Section::make('Detail Penumpang')
                            ->schema([
                                Repeater::make('daftar_penumpang')
                                    ->label('Daftar Penumpang')
                                    ->relationship('passengers')
                                    ->schema([
                                        TextInput::make('seat.name')
                                            ->label('Flight Seat ID')
                                            ->required(),
                                        TextInput::make('name')
                                            ->label('Passenger Name')
                                            ->required()
                                            ->maxLength(255),
                                        DatePicker::make('date_of_birth')
                                            ->label('Date of Birth')
                                            ->required()
                                            ->maxDate(now()->subYears(0)),
                                        TextInput::make('nationality')
                                            ->label('Nationality')
                                            ->required()
                                            ->maxLength(100),
                                        TextInput::make('number_of_passengers')
                                            ->label('Number of Passengers')
                                            ->required()
                                            ->numeric(),

                                    ])
                                    ->minItems(1)
                                    ->required(),
                            ]),

                        Section::make('Informasi Pembayaran')
                            ->schema([
                                //
                                Select::make('promo_id')
                                    ->relationship('promo', 'code')
                                    ->placeholder('Select Promo')
                                    ->label('Promo Code'),

                                TextInput::make('payment_status')
                                    ->label('Payment Status')
                                    ->required()
                                    ->maxLength(50),

                                TextInput::make('sub_total')
                                    ->label('Sub Total')
                                    ->required()
                                    ->numeric(),

                                TextInput::make('grandtotal')
                                    ->label('Grand Total')
                                    ->required()
                                    ->numeric(),
                            ]),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('code')->label('Code Transaction')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('flight.flight_number')->label('Flight Number')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Passenger Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('number_of_passengers')->label('Number of Passengers')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('promo.code')->label('Promo Code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('payment_status')->label('Payment Status')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sub_total')->label('Sub Total')->money('rp', true)->sortable()->searchable(),
                Tables\Columns\TextColumn::make('grandtotal')->label('Grand Total')->money('rp', true)->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
