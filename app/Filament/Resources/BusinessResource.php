<?php

namespace App\Filament\Resources;

use App\Models\PostsajaBusiness;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class BusinessResource extends Resource
{
    protected static ?string $model = PostsajaBusiness::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('business_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('business_code')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('owner_wa')
                    ->label('Owner WhatsApp')
                    ->maxLength(20),
                Toggle::make('telegram_bot_enabled')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('business_code')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('users.name')
                    ->label('Owners')
                    ->searchable(),
                TextColumn::make('staff_count')
                    ->label('Staff')
                    ->counts('staff'),
                TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts'),
                Tables\Columns\IconColumn::make('telegram_bot_enabled')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('telegram_bot_enabled')
                    ->query(fn($q) => $q->where('telegram_bot_enabled', true)),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\BusinessResource\Pages\ListBusinesses::route('/'),
            'create' => \App\Filament\Resources\BusinessResource\Pages\CreateBusiness::route('/create'),
            'edit' => \App\Filament\Resources\BusinessResource\Pages\EditBusiness::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Businesses';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-building-storefront';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Business';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
