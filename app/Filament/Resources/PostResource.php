<?php

namespace App\Filament\Resources;

use App\Models\PostsajaPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists;

class PostResource extends Resource
{
    protected static ?string $model = PostsajaPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('business_id')
                    ->relationship('business', 'business_name')
                    ->required()
                    ->searchable(),
                TextInput::make('staff_chat_id')
                    ->label('Staff Telegram Chat ID')
                    ->numeric(),
                TextInput::make('image_url')
                    ->label('Image URL')
                    ->maxLength(2048)
                    ->columnSpanFull(),
                Textarea::make('ai_caption')
                    ->label('AI Caption')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'processing' => 'Processing',
                        'posted' => 'Posted',
                        'failed' => 'Failed',
                    ])
                    ->required()
                    ->default('processing'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular()
                    ->size(48),
                TextColumn::make('business.business_name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ai_caption')
                    ->label('Caption')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'processing' => 'Processing',
                        'posted' => 'Posted',
                        'failed' => 'Failed',
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'processing' => 'Processing',
                        'posted' => 'Posted',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('business')
                    ->relationship('business', 'business_name'),
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
            'index' => \App\Filament\Resources\PostResource\Pages\ListPosts::route('/'),
            'create' => \App\Filament\Resources\PostResource\Pages\CreatePost::route('/create'),
            'edit' => \App\Filament\Resources\PostResource\Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
