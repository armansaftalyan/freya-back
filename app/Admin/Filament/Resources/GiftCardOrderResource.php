<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\GiftCardOrderResource\Pages;
use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Models\GiftCardOrder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftCardOrderResource extends Resource
{
    protected static ?string $model = GiftCardOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?int $navigationSort = 29;

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.resources.gift_card_order.plural');
    }

    public static function getModelLabel(): string
    {
        return __('messages.filament.resources.gift_card_order.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.filament.resources.gift_card_order.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['buyer', 'giftCard']);
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
                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(__('messages.filament.fields.recipient_name'))
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('recipient_phone')
                    ->label(__('messages.filament.fields.recipient_phone'))
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('recipient_email')
                    ->label(__('messages.filament.fields.recipient_email'))
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('payment_provider')
                    ->label(__('messages.filament.fields.payment_provider'))
                    ->formatStateUsing(fn (?string $state): string => __('messages.filament.payment_provider.'.($state ?: 'manual')))
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('messages.filament.fields.status'))
                    ->formatStateUsing(function ($state): string {
                        $value = $state instanceof GiftCardOrderStatus ? $state->value : (string) $state;
                        return __('messages.filament.gift_card_order_status.'.$value);
                    })
                    ->colors([
                        'warning' => fn ($state): bool => ($state instanceof GiftCardOrderStatus ? $state->value : (string) $state) === GiftCardOrderStatus::Pending->value,
                        'success' => fn ($state): bool => ($state instanceof GiftCardOrderStatus ? $state->value : (string) $state) === GiftCardOrderStatus::Paid->value,
                        'danger' => fn ($state): bool => ($state instanceof GiftCardOrderStatus ? $state->value : (string) $state) === GiftCardOrderStatus::Failed->value,
                        'gray' => fn ($state): bool => ($state instanceof GiftCardOrderStatus ? $state->value : (string) $state) === GiftCardOrderStatus::Refunded->value,
                    ]),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('messages.filament.fields.amount'))
                    ->formatStateUsing(fn ($state, GiftCardOrder $record): string => number_format((float) ($state ?? 0), 0, '.', ' ').' '.$record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('giftCard.code')
                    ->label(__('messages.filament.fields.gift_card'))
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')->dateTime('Y-m-d H:i')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('messages.filament.fields.status'))
                    ->options([
                        GiftCardOrderStatus::Pending->value => __('messages.filament.gift_card_order_status.pending'),
                        GiftCardOrderStatus::Paid->value => __('messages.filament.gift_card_order_status.paid'),
                        GiftCardOrderStatus::Failed->value => __('messages.filament.gift_card_order_status.failed'),
                        GiftCardOrderStatus::Refunded->value => __('messages.filament.gift_card_order_status.refunded'),
                    ]),
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
            'index' => Pages\ListGiftCardOrders::route('/'),
        ];
    }
}
