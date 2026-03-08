<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\BookingOrderResource\Pages;
use App\Domain\Salon\Models\BookingOrder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingOrderResource extends Resource
{
    protected static ?string $model = BookingOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.resources.booking_order.plural');
    }

    public static function getModelLabel(): string
    {
        return __('messages.filament.resources.booking_order.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.filament.resources.booking_order.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['client', 'appointments'])
            ->withCount('appointments');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('messages.filament.fields.client'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.phone')
                    ->label(__('messages.filament.fields.client_phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('appointments_count')
                    ->label(__('messages.filament.fields.appointments_count'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointments')
                    ->label(__('messages.filament.fields.appointments'))
                    ->getStateUsing(function (BookingOrder $record): string {
                        if ($record->appointments->isEmpty()) {
                            return '—';
                        }

                        return $record->appointments
                            ->sortBy('start_at')
                            ->map(fn ($appointment): string => sprintf('#%d %s', $appointment->id, $appointment->start_at?->format('Y-m-d H:i') ?? ''))
                            ->implode("\n");
                    })
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('messages.filament.fields.price'))
                    ->formatStateUsing(fn ($state): string => number_format((float) ($state ?? 0), 0, '.', ' ').' ֏')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => __('messages.filament.status.'.$state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'primary' => 'done',
                        'gray' => ['no_show', 'mixed'],
                    ]),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('messages.filament.fields.source'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingOrders::route('/'),
        ];
    }
}
