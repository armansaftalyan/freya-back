<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\GiftCardResource\Pages;
use App\Domain\Salon\Enums\GiftCardStatus;
use App\Domain\Salon\Models\GiftCard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.resources.gift_card.plural');
    }

    public static function getModelLabel(): string
    {
        return __('messages.filament.resources.gift_card.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.filament.resources.gift_card.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['owner', 'order']);
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
                Tables\Columns\TextColumn::make('code')
                    ->label(__('messages.filament.fields.code'))
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('messages.filament.fields.owner'))
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => $state ?: '—'),
                Tables\Columns\TextColumn::make('order.id')
                    ->label(__('messages.filament.fields.order'))
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => $state !== null ? '#'.$state : '—'),
                Tables\Columns\TextColumn::make('initial_amount')
                    ->label(__('messages.filament.fields.initial_amount'))
                    ->formatStateUsing(fn ($state, GiftCard $record): string => number_format((float) ($state ?? 0), 0, '.', ' ').' '.$record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('messages.filament.fields.balance'))
                    ->formatStateUsing(fn ($state, GiftCard $record): string => number_format((float) ($state ?? 0), 0, '.', ' ').' '.$record->currency)
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('messages.filament.fields.status'))
                    ->formatStateUsing(fn (string $state): string => __('messages.filament.gift_card_status.'.$state))
                    ->colors([
                        'warning' => GiftCardStatus::Pending->value,
                        'success' => GiftCardStatus::Active->value,
                        'gray' => GiftCardStatus::Redeemed->value,
                        'danger' => [GiftCardStatus::Expired->value, GiftCardStatus::Blocked->value, GiftCardStatus::Cancelled->value],
                    ]),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('messages.filament.fields.expires_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('last_used_at')
                    ->label(__('messages.filament.fields.last_used_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('messages.filament.fields.status'))
                    ->options([
                        GiftCardStatus::Pending->value => __('messages.filament.gift_card_status.pending'),
                        GiftCardStatus::Active->value => __('messages.filament.gift_card_status.active'),
                        GiftCardStatus::Redeemed->value => __('messages.filament.gift_card_status.redeemed'),
                        GiftCardStatus::Expired->value => __('messages.filament.gift_card_status.expired'),
                        GiftCardStatus::Blocked->value => __('messages.filament.gift_card_status.blocked'),
                        GiftCardStatus::Cancelled->value => __('messages.filament.gift_card_status.cancelled'),
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
            'index' => Pages\ListGiftCards::route('/'),
        ];
    }
}
