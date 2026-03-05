<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources;

use App\Admin\Filament\Resources\BranchResource\Pages;
use App\Domain\Salon\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.resources.branch.plural');
    }

    public static function getModelLabel(): string
    {
        return __('messages.filament.resources.branch.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.filament.resources.branch.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('address'),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\TextInput::make('geo_lat')->numeric(),
            Forms\Components\TextInput::make('geo_lng')->numeric(),
            Forms\Components\KeyValue::make('working_hours')->addActionLabel(__('messages.filament.actions.add_day_rule')),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('address')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
