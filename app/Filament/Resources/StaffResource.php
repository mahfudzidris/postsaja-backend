<?php

namespace App\Filament\Resources;

use App\Models\PostsajaStaffTelegram;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class StaffResource extends Resource
{
    protected static ?string $model = PostsajaStaffTelegram::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('business_id')
                    ->relationship('business', 'business_name')
                    ->required()
                    ->searchable(),
                TextInput::make('telegram_chat_id')
                    ->required()
                    ->numeric(),
                TextInput::make('telegram_username')
                    ->maxLength(255),
                Toggle::make('active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business.business_name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('telegram_username')
                    ->label('Username')
                    ->searchable(),
                TextColumn::make('telegram_chat_id')
                    ->label('Chat ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->query(fn($q) => $q->where('active', true)),
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
            'index' => \App\Filament\Resources\StaffResource\Pages\ListStaff::route('/'),
            'create' => \App\Filament\Resources\StaffResource\Pages\CreateStaff::route('/create'),
            'edit' => \App\Filament\Resources\StaffResource\Pages\EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Business';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
