<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceSuggestionResource\Pages;
use App\Models\Category;
use App\Models\Place;
use App\Models\PlaceSuggestion;
use App\Services\AuditService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlaceSuggestionResource extends Resource
{
    protected static ?string $model = PlaceSuggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Saran Tempat';

    protected static ?string $navigationGroup = 'Komunitas';

    protected static ?string $modelLabel = 'Saran Tempat';

    protected static ?string $pluralModelLabel = 'Saran Tempat';

    protected static ?string $slug = 'saran-tempat';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255),
            Forms\Components\TextInput::make('category')->label('Kategori')->maxLength(60),
            Forms\Components\Textarea::make('address')->label('Alamat')->rows(2),
            Forms\Components\TextInput::make('contact')->label('Kontak')->maxLength(60),
            Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(4),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(array_combine(PlaceSuggestion::STATUSES, array_map('ucfirst', PlaceSuggestion::STATUSES)))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->tooltip(fn (PlaceSuggestion $record) => $record->address),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Kontak')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'reviewed' => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Masuk')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (PlaceSuggestion $record): string => PlaceSuggestionResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(PlaceSuggestion::STATUSES, array_map('ucfirst', PlaceSuggestion::STATUSES))),
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label("Ekspor")
                    ->exporter(App\Filament\Exports\PlaceSuggestionExporter::class)
                    ->icon("heroicon-o-arrow-down-tray"),
            ])
            ->actions([
                Tables\Actions\Action::make('convert')
                    ->label('Konversi ke Kuliner')
                    ->icon('heroicon-o-arrow-up-right')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->options(Category::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (PlaceSuggestion $record, array $data, AuditService $audit): void {
                        $place = Place::create([
                            'category_id' => $data['category_id'],
                            'name' => $record->name,
                            'slug' => $record->name.'-'.Str::lower(Str::random(4)),
                            'address' => $record->address,
                            'description' => $record->description,
                            'is_published' => true,
                        ]);

                        $record->update(['status' => 'imported']);
                        $audit->log('place.created', $place, ['source' => 'suggestion']);

                        Notification::make()
                            ->success()
                            ->title('Kuliner dibuat')
                            ->body("{$place->name} sudah masuk daftar. Lengkapi detailnya.")
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $new = static::getModel()::where('status', 'new')->count();

        return $new ? (string) $new : null;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Saran')->columns(2)->schema([
                Infolists\Components\TextEntry::make('name')->label('Nama')->weight('bold'),
                Infolists\Components\TextEntry::make('category')->label('Kategori')->badge(),
                Infolists\Components\TextEntry::make('contact')->label('Kontak'),
                Infolists\Components\TextEntry::make('status')->label('Status')->badge(),
                Infolists\Components\TextEntry::make('address')->label('Alamat'),
                Infolists\Components\TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('Riwayat Audit')->schema([
                Infolists\Components\View::make('filament.infolists.components.audit-history'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaceSuggestions::route('/'),
            'view' => Pages\ViewPlaceSuggestion::route('/{record}'),
            'create' => Pages\CreatePlaceSuggestion::route('/create'),
            'edit' => Pages\EditPlaceSuggestion::route('/{record}/edit'),
        ];
    }
}
