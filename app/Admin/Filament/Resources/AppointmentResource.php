<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\AppointmentResource\Pages;
use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Domain\Users\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.resources.appointment.plural');
    }

    public static function getModelLabel(): string
    {
        return __('messages.filament.resources.appointment.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.filament.resources.appointment.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['client', 'master', 'service', 'services']);
        $user = auth()->user();

        if ($user?->hasRole('master')) {
            return $query->whereHas('master', fn (Builder $q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')
                ->label(__('messages.filament.fields.client'))
                ->options(
                    User::role('client')
                        ->orderBy('name')
                        ->get(['id', 'name', 'phone', 'email'])
                        ->mapWithKeys(fn (User $user): array => [$user->id => self::formatClientLabel($user)])
                        ->all()
                )
                ->required()
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    $clientId = (int) ($state ?? 0);
                    $set('client_phone_display', $clientId > 0 ? (string) (User::query()->find($clientId)?->phone ?? '') : '');
                }),
            Forms\Components\TextInput::make('client_phone_display')
                ->label(__('messages.filament.fields.client_phone'))
                ->readOnly()
                ->dehydrated(false)
                ->afterStateHydrated(function (callable $set, callable $get, ?Appointment $record): void {
                    if ($record?->client?->phone) {
                        $set('client_phone_display', (string) $record->client->phone);
                        return;
                    }

                    $clientId = (int) ($get('client_id') ?? 0);
                    if ($clientId <= 0) {
                        $set('client_phone_display', '');
                        return;
                    }

                    $set('client_phone_display', (string) (User::query()->find($clientId)?->phone ?? ''));
                }),
            Forms\Components\TextInput::make('booking_order_id')
                ->label(__('messages.filament.fields.booking_order'))
                ->readOnly()
                ->dehydrated(false),
            Forms\Components\Select::make('master_id')
                ->label(__('messages.filament.fields.master'))
                ->options(Master::query()->pluck('name', 'id'))
                ->searchable(),
            Forms\Components\Select::make('service_id')
                ->label(__('messages.filament.fields.service'))
                ->options(Service::query()->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    $serviceId = (int) ($state ?? 0);
                    $priceFrom = $serviceId > 0 ? Service::query()->find($serviceId)?->price_from : null;
                    $set('total_price_display', $priceFrom !== null ? number_format((float) $priceFrom, 0, '.', ' ') : '');
                }),
            Forms\Components\TextInput::make('total_price_display')
                ->label(__('messages.filament.fields.price'))
                ->prefix('֏')
                ->readOnly()
                ->dehydrated(false)
                ->afterStateHydrated(function (callable $set, callable $get, ?Appointment $record): void {
                    if ($record !== null) {
                        $set('total_price_display', number_format($record->total_price, 0, '.', ' '));
                        return;
                    }

                    $serviceId = (int) ($get('service_id') ?? 0);
                    if ($serviceId <= 0) {
                        $set('total_price_display', '');
                        return;
                    }

                    $priceFrom = Service::query()->find($serviceId)?->price_from;
                    if ($priceFrom === null) {
                        $set('total_price_display', '');
                        return;
                    }

                    $set('total_price_display', number_format((float) $priceFrom, 0, '.', ' '));
                }),
            Forms\Components\DateTimePicker::make('start_at')->required(),
            Forms\Components\DateTimePicker::make('end_at')->required(),
            Forms\Components\Select::make('status')->options([
                AppointmentStatus::Pending->value => __('messages.filament.status.pending'),
                AppointmentStatus::Confirmed->value => __('messages.filament.status.confirmed'),
                AppointmentStatus::Cancelled->value => __('messages.filament.status.cancelled'),
                AppointmentStatus::Done->value => __('messages.filament.status.done'),
                AppointmentStatus::NoShow->value => __('messages.filament.status.no_show'),
            ])->required(),
            Forms\Components\Textarea::make('comment'),
            Forms\Components\Select::make('source')->options([
                'site' => __('messages.filament.source.site'),
                'phone' => __('messages.filament.source.phone'),
                'instagram' => __('messages.filament.source.instagram'),
                'yandex_maps' => __('messages.filament.source.yandex_maps'),
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('booking_order_id')
                    ->label(__('messages.filament.fields.booking_order'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('messages.filament.fields.client'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.phone')->label(__('messages.filament.fields.client_phone'))->searchable(),
                Tables\Columns\TextColumn::make('master.name')
                    ->label(__('messages.filament.fields.master'))
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => $state ?: '—'),
                Tables\Columns\TextColumn::make('service.name')->label(__('messages.filament.fields.service'))->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('messages.filament.fields.price'))
                    ->formatStateUsing(fn ($state): string => number_format((float) ($state ?? 0), 0, '.', ' ').' ֏')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => AppointmentStatus::Pending->value,
                        'success' => AppointmentStatus::Confirmed->value,
                        'danger' => AppointmentStatus::Cancelled->value,
                        'primary' => AppointmentStatus::Done->value,
                        'gray' => AppointmentStatus::NoShow->value,
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    AppointmentStatus::Pending->value => __('messages.filament.status.pending'),
                    AppointmentStatus::Confirmed->value => __('messages.filament.status.confirmed'),
                    AppointmentStatus::Cancelled->value => __('messages.filament.status.cancelled'),
                    AppointmentStatus::Done->value => __('messages.filament.status.done'),
                    AppointmentStatus::NoShow->value => __('messages.filament.status.no_show'),
                ]),
                Tables\Filters\SelectFilter::make('master_id')->options(Master::query()->pluck('name', 'id')),
                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from'),
                        Forms\Components\DatePicker::make('date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('start_at', '>=', $date))
                            ->when($data['date_to'] ?? null, fn (Builder $q, $date) => $q->whereDate('start_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('confirm')
                    ->label(__('messages.filament.actions.confirm_selected'))
                    ->color('success')
                    ->action(function ($records): void {
                        foreach ($records as $record) {
                            if (AppointmentStatus::canTransition($record->status->value, AppointmentStatus::Confirmed->value)) {
                                $record->status = AppointmentStatus::Confirmed;
                                $record->save();
                            }
                        }
                    }),
                Tables\Actions\BulkAction::make('cancel')
                    ->label(__('messages.filament.actions.cancel_selected'))
                    ->color('danger')
                    ->action(function ($records): void {
                        foreach ($records as $record) {
                            if (AppointmentStatus::canTransition($record->status->value, AppointmentStatus::Cancelled->value)) {
                                $record->status = AppointmentStatus::Cancelled;
                                $record->save();
                            }
                        }
                    }),
            ])
            ->defaultSort('start_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    private static function formatClientLabel(User $user): string
    {
        $name = trim((string) $user->name);
        $phone = trim((string) ($user->phone ?? ''));
        $email = trim((string) ($user->email ?? ''));

        $main = $name !== '' ? $name : ('#'.$user->id);
        $contacts = array_values(array_filter([$phone, $email], fn (string $value): bool => $value !== ''));
        if ($contacts === []) {
            return $main;
        }

        return sprintf('%s (%s)', $main, implode(', ', $contacts));
    }
}
