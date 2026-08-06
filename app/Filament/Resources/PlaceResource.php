<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PlaceExporter;
use App\Filament\Resources\PlaceResource\Pages;
use App\Models\Place;
use App\Services\AuditService;
use App\Services\ImageOptimizer;
use App\Services\PlaceImportService;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Kuliner';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Kuliner';

    protected static ?string $pluralModelLabel = 'Kuliner';

    protected static ?string $slug = 'kuliner';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatis dari nama.'),
                Forms\Components\TextInput::make('tagline')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(5)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Lokasi & Kontak')->schema([
                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->rows(2),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->minValue(-90)
                    ->maxValue(90)
                    ->helperText('-90 s/d 90'),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->minValue(-180)
                    ->maxValue(180)
                    ->helperText('-180 s/d 180'),
                Forms\Components\TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->prefix('+62')
                    ->maxLength(20),
            ])->columns(3),

            Forms\Components\Section::make('Jam Buka & Info')->schema([
                Forms\Components\TextInput::make('open_days')
                    ->label('Hari Buka')
                    ->helperText('Format: Mon,Tue,Wed,Thu,Fri,Sat,Sun'),
                Forms\Components\TextInput::make('open_time')
                    ->label('Jam Buka')
                    ->placeholder('10:00')
                    ->maxLength(10),
                Forms\Components\TextInput::make('close_time')
                    ->label('Jam Tutup')
                    ->placeholder('17:30 / kosong = sampai habis')
                    ->maxLength(10),
                Forms\Components\TextInput::make('price_range')
                    ->label('Kisaran Harga')
                    ->placeholder('Rp 10.000 – Rp 25.000'),
                Forms\Components\TextInput::make('since_year')
                    ->label('Sejak Tahun')
                    ->numeric()
                    ->placeholder('2001'),
                Forms\Components\Textarea::make('tips')
                    ->rows(2)
                    ->maxLength(500),
            ])->columns(3),

            Forms\Components\Section::make('Media & Status')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Foto')
                    ->image()
                    ->directory('places')
                    ->imageEditor()
                    ->saveUploadedFileUsing(fn (UploadedFile $file): string => app(ImageOptimizer::class)->optimize($file, 'places'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_legendary')
                    ->label('Legendaris (lencana)')
                    ->inline(false),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Unggulan (featured)')
                    ->inline(false),
                Forms\Components\Toggle::make('is_published')
                    ->label('Tayang di situs')
                    ->default(true)
                    ->inline(false),
                Forms\Components\DateTimePicker::make('publish_at')
                    ->label('Jadwal Tayang')
                    ->helperText('Kosongkan jika langsung tayang. Konten otomatis tayang saat waktunya tiba.')
                    ->placeholder('19:30 hari ini')
                    ->inline(false),
                Forms\Components\TextInput::make('views')
                    ->label('Jumlah Views')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_legendary')
                    ->label('Legendaris')
                    ->boolean()
                    ->trueIcon('heroicon-o-trophy')
                    ->falseIcon('heroicon-o-minus'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Review')
                    ->counts('reviews')
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Tayang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('publish_at')
                    ->label('Terjadwal')
                    ->dateTime('d M Y H:i')
                    ->since()
                    ->badge()
                    ->color('warning')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->recordUrl(fn (Place $record): string => PlaceResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Tayang'),
                Tables\Filters\TernaryFilter::make('is_legendary')
                    ->label('Legendaris'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Unggulan'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Ekspor')
                    ->exporter(PlaceExporter::class)
                    ->icon('heroicon-o-arrow-down-tray'),
                Tables\Actions\Action::make('import')
                    ->label('Import JSON')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File JSON hasil scraper')
                            ->acceptedFileTypes(['application/json', 'text/plain'])
                            ->storeFiles(false)
                            ->helperText('Data yang cocok dengan slug akan diperbarui, sisanya ditambahkan.')
                            ->required(),
                    ])
                    ->action(function (array $data, AuditService $audit): void {
                        $file = $data['file'] ?? null;

                        if (! $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            Notification::make()->danger()->title('File tidak valid')->send();

                            return;
                        }

                        $items = json_decode($file->get(), true);

                        if (! is_array($items)) {
                            Notification::make()->danger()->title('File JSON tidak valid')->send();

                            return;
                        }

                        $result = app(PlaceImportService::class)->import($items);
                        $audit->log('place.imported', null, $result);

                        Notification::make()
                            ->success()
                            ->title('Import selesai')
                            ->body("{$result['imported']} ditambahkan · {$result['updated']} diperbarui · {$result['skipped']} dilewati.")
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn (Place $record): bool => ! $record->is_published)
                    ->url(fn (Place $record): string => URL::signedRoute('admin.preview.place', ['place' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_site')
                    ->label('Lihat Situs')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(fn (Place $record): bool => $record->is_published)
                    ->url(fn (Place $record) => route('place.show', $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Place $record): void {
                        $copy = $record->replicate();
                        $copy->name = $record->name.' (Salinan)';
                        $copy->slug = $record->slug.'-'.Str::lower(Str::random(4));
                        $copy->is_published = false;
                        $copy->views = 0;
                        $copy->save();

                        Notification::make()->success()->title('Kuliner disalin')->send();
                        redirect()->to(PlaceResource::getUrl('edit', ['record' => $copy]));
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\Action::make('bulk_publish')
                        ->label('Tayangkan')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records, AuditService $audit): void {
                            $count = $records->where('is_published', false)->each->update(['is_published' => true, 'publish_at' => null])->count();
                            $audit->log('place.bulk_published', null, ['count' => $count]);
                            Notification::make()->success()->title("{$count} kuliner ditayangkan")->send();
                        }),
                    Tables\Actions\Action::make('bulk_unpublish')
                        ->label('Sembunyikan')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records, AuditService $audit): void {
                            $count = $records->where('is_published', true)->each->update(['is_published' => false])->count();
                            $audit->log('place.bulk_unpublished', null, ['count' => $count]);
                            Notification::make()->success()->title("{$count} kuliner disembunyikan")->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $drafts = static::getModel()::where('is_published', false)->count();

        return $drafts ? (string) $drafts : null;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'tagline', 'address', 'category.name'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('reviews');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Ringkasan')->columns(3)->schema([
                Infolists\Components\ImageEntry::make('image')
                    ->label('Foto')
                    ->circular()
                    ->columnSpan(1),
                Infolists\Components\TextEntry::make('name')->label('Nama')->weight('bold')->size(TextEntry\TextEntrySize::Large),
                Infolists\Components\TextEntry::make('tagline')->label('Tagline'),
                Infolists\Components\TextEntry::make('category.name')->label('Kategori')->badge()->color('primary'),
                Infolists\Components\TextEntry::make('views')->label('Views')->numeric(),
                Infolists\Components\TextEntry::make('rating')
                    ->label('Rating')
                    ->state(fn (Place $record): string => $record->averageRating() > 0 ? number_format($record->averageRating(), 1).' ★' : 'Belum ada review'),
                Infolists\Components\IconEntry::make('is_legendary')->label('Legendaris')->boolean(),
                Infolists\Components\IconEntry::make('is_featured')->label('Unggulan')->boolean(),
                Infolists\Components\IconEntry::make('is_published')->label('Tayang')->boolean(),
            ]),
            Infolists\Components\Section::make('Lokasi & Kontak')->columns(2)->schema([
                Infolists\Components\TextEntry::make('address')->label('Alamat'),
                Infolists\Components\TextEntry::make('whatsapp')->label('WhatsApp'),
                Infolists\Components\TextEntry::make('latitude')->label('Latitude'),
                Infolists\Components\TextEntry::make('longitude')->label('Longitude'),
            ]),
            Infolists\Components\Section::make('Jam Buka & Info')->columns(3)->schema([
                Infolists\Components\TextEntry::make('open_days')->label('Hari Buka'),
                Infolists\Components\TextEntry::make('open_time')->label('Jam Buka'),
                Infolists\Components\TextEntry::make('close_time')->label('Jam Tutup'),
                Infolists\Components\TextEntry::make('price_range')->label('Kisaran Harga'),
                Infolists\Components\TextEntry::make('since_year')->label('Sejak Tahun'),
                Infolists\Components\TextEntry::make('tips')->label('Tips'),
            ]),
            Infolists\Components\Section::make('Deskripsi')->schema([
                Infolists\Components\TextEntry::make('description')->label('Deskripsi'),
            ]),
            Infolists\Components\Section::make('Riwayat Audit')->schema([
                Infolists\Components\View::make('filament.infolists.components.audit-history'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaces::route('/'),
            'view' => Pages\ViewPlace::route('/{record}'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }
}
