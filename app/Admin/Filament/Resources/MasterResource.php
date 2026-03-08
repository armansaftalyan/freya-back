<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\MasterResource\Pages;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Domain\Users\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MasterResource extends Resource
{
    protected static ?string $model = Master::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.resources.master.plural');
    }

    public static function getModelLabel(): string
    {
        return __('messages.filament.resources.master.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.filament.resources.master.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->hasRole('master')) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->options(User::query()->pluck('email', 'id'))->searchable(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Textarea::make('bio')->rows(3),
            Forms\Components\TextInput::make('avatar')->maxLength(255),
            Forms\Components\Section::make(__('messages.filament.fields.schedule_rules'))
                ->description(__('messages.filament.fields.schedule_rules_hint'))
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        self::dayScheduleRepeater('monday', __('messages.filament.days.monday')),
                        self::dayScheduleRepeater('tuesday', __('messages.filament.days.tuesday')),
                        self::dayScheduleRepeater('wednesday', __('messages.filament.days.wednesday')),
                        self::dayScheduleRepeater('thursday', __('messages.filament.days.thursday')),
                        self::dayScheduleRepeater('friday', __('messages.filament.days.friday')),
                        self::dayScheduleRepeater('saturday', __('messages.filament.days.saturday')),
                        self::dayScheduleRepeater('sunday', __('messages.filament.days.sunday')),
                    ]),
                ]),
            Forms\Components\Repeater::make('masterServices')
                ->label(__('messages.filament.fields.services_with_prices'))
                ->relationship('masterServices')
                ->addActionLabel(__('messages.filament.actions.add_service_price'))
                ->defaultItems(0)
                ->schema([
                    Forms\Components\Select::make('service_id')
                        ->label(__('messages.filament.fields.service'))
                        ->options(Service::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->afterStateUpdated(function (callable $set, $state): void {
                            $service = Service::query()->find((int) $state);
                            if ($service === null) {
                                return;
                            }

                            $set('duration_minutes', (int) $service->duration_minutes);
                            $set('price', $service->price_from !== null ? (float) $service->price_from : null);
                        }),
                    Forms\Components\TextInput::make('duration_minutes')
                        ->label(__('messages.filament.fields.duration'))
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    Forms\Components\TextInput::make('price')
                        ->label(__('messages.filament.fields.price'))
                        ->numeric()
                        ->prefix('֏')
                        ->minValue(0)
                        ->required(),
                ])
                ->columns(3)
                ->collapsible(),
            Forms\Components\TextInput::make('sort')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label(__('messages.filament.fields.linked_user')),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sort');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'manager']) ?? false;
    }

    protected static function dayScheduleRepeater(string $dayKey, string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make("schedule_rules.{$dayKey}")
            ->label($label)
            ->defaultItems(1)
            ->addActionLabel(__('messages.filament.actions.add_time_range'))
            ->columns(2)
            ->schema([
                Forms\Components\TimePicker::make('start')
                    ->label(__('messages.filament.fields.start_time'))
                    ->seconds(false)
                    ->required(),
                Forms\Components\TimePicker::make('end')
                    ->label(__('messages.filament.fields.end_time'))
                    ->seconds(false)
                    ->required(),
            ])
            ->collapsible()
            ->collapsed();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMasters::route('/'),
            'create' => Pages\CreateMaster::route('/create'),
            'edit' => Pages\EditMaster::route('/{record}/edit'),
        ];
    }
}
