<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\AppointmentResource\Pages;
use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Branch;
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['client', 'master', 'branch', 'service']);
        $user = auth()->user();

        if ($user?->hasRole('master')) {
            return $query->whereHas('master', fn (Builder $q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')->label('Client')->options(User::role('client')->pluck('email', 'id'))->required()->searchable(),
            Forms\Components\Select::make('master_id')->label('Master')->options(Master::query()->pluck('name', 'id'))->required()->searchable(),
            Forms\Components\Select::make('branch_id')->label('Branch')->options(Branch::query()->pluck('name', 'id'))->required()->searchable(),
            Forms\Components\Select::make('service_id')->label('Service')->options(Service::query()->pluck('name', 'id'))->required()->searchable(),
            Forms\Components\DateTimePicker::make('start_at')->required(),
            Forms\Components\DateTimePicker::make('end_at')->required(),
            Forms\Components\Select::make('status')->options([
                AppointmentStatus::Pending->value => 'Pending',
                AppointmentStatus::Confirmed->value => 'Confirmed',
                AppointmentStatus::Cancelled->value => 'Cancelled',
                AppointmentStatus::Done->value => 'Done',
                AppointmentStatus::NoShow->value => 'No show',
            ])->required(),
            Forms\Components\Textarea::make('comment'),
            Forms\Components\Select::make('source')->options([
                'site' => 'Site',
                'phone' => 'Phone',
                'instagram' => 'Instagram',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('start_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('client.email')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('master.name')->label('Master')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Service')->searchable(),
                Tables\Columns\TextColumn::make('branch.name')->label('Branch')->searchable(),
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
                    AppointmentStatus::Pending->value => 'Pending',
                    AppointmentStatus::Confirmed->value => 'Confirmed',
                    AppointmentStatus::Cancelled->value => 'Cancelled',
                    AppointmentStatus::Done->value => 'Done',
                    AppointmentStatus::NoShow->value => 'No show',
                ]),
                Tables\Filters\SelectFilter::make('master_id')->options(Master::query()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('branch_id')->options(Branch::query()->pluck('name', 'id')),
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
                    ->label('Confirm selected')
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
                    ->label('Cancel selected')
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
}
