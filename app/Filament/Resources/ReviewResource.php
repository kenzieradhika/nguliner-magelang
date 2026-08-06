<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Review';

    protected static ?string $navigationGroup = 'Komunitas';

    protected static ?string $modelLabel = 'Review';

    protected static ?string $pluralModelLabel = 'Review';

    protected static ?string $slug = 'review';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('place_id')
                ->label('Kuliner')
                ->relationship('place', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255),
            Forms\Components\Select::make('rating')
                ->label('Rating')
                ->options([1 => '1 ★', 2 => '2 ★', 3 => '3 ★', 4 => '4 ★', 5 => '5 ★'])
                ->required(),
            Forms\Components\Textarea::make('comment')->label('Komentar')->rows(4)->required(),
            Forms\Components\Toggle::make('is_approved')->label('Disetujui (tampil di situs)')->inline(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('place.name')
                    ->label('Kuliner')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Pengulas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_repeat('★', (int) $state))
                    ->color(fn (string $state): string => (int) $state >= 4 ? 'success' : ((int) $state >= 3 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(60)
                    ->tooltip(fn (Review $record) => $record->comment),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Masuk')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (Review $record): string => ReviewResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('Status Persetujuan'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label("Ekspor")
                    ->exporter(App\Filament\Exports\ReviewExporter::class)
                    ->icon("heroicon-o-arrow-down-tray"),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn (Review $record): string => $record->is_approved ? 'Tarik' : 'Setujui')
                    ->icon(fn (Review $record): string => $record->is_approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Review $record): string => $record->is_approved ? 'gray' : 'success')
                    ->action(function (Review $record): void {
                        $record->update(['is_approved' => ! $record->is_approved]);
                        Notification::make()->success()->title('Status review diperbarui')->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Setujui terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records): void {
                            Review::whereIn('id', $records->pluck('id'))->update(['is_approved' => true]);
                            Notification::make()->success()->title('Review disetujui')->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('is_approved', false)->count();

        return $pending ? (string) $pending : null;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'comment', 'place.name'];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Ulasan')->columns(2)->schema([
                Infolists\Components\TextEntry::make('place.name')->label('Kuliner')->weight('bold'),
                Infolists\Components\TextEntry::make('name')->label('Pengulas'),
                Infolists\Components\TextEntry::make('rating')->label('Rating')->badge()
                    ->formatStateUsing(fn (string $state): string => str_repeat('★', (int) $state)),
                Infolists\Components\IconEntry::make('is_approved')->label('Disetujui')->boolean(),
                Infolists\Components\TextEntry::make('comment')->label('Komentar')->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('Riwayat Audit')->schema([
                Infolists\Components\View::make('filament.infolists.components.audit-history'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'view' => Pages\ViewReview::route('/{record}'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
