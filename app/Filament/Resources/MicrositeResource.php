<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MicrositeResource\Pages;
use App\Models\Microsite;
use App\Services\ImageOptimizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;

class MicrositeResource extends Resource
{
    protected static ?string $model = Microsite::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Microsite';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Microsite';

    protected static ?string $pluralModelLabel = 'Microsite';

    protected static ?string $slug = 'microsite';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kuliner & Identitas')->schema([
                Forms\Components\Select::make('place_id')
                    ->label('Kuliner')
                    ->relationship('place', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('hero_title')
                    ->label('Judul Halaman')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('hero_image')
                    ->label('Gambar Header')
                    ->image()
                    ->directory('microsites')
                    ->imageEditor()
                    ->saveUploadedFileUsing(fn (UploadedFile $file): string => app(ImageOptimizer::class)->optimize($file, 'microsites')),
                Forms\Components\ColorPicker::make('accent_color')
                    ->label('Warna Aksen'),
                Forms\Components\TextInput::make('cta_text')
                    ->label('Teks Tombol CTA')
                    ->maxLength(60),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
            ])->columns(2),

            Forms\Components\Section::make('Konten')->schema([
                Forms\Components\Textarea::make('about')
                    ->label('Tentang')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('menu')
                    ->label('Menu')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nama Menu')->required()->maxLength(120),
                        Forms\Components\TextInput::make('price')->label('Harga')->maxLength(40),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('gallery')
                    ->label('Galeri (upload gambar)')
                    ->image()
                    ->multiple()
                    ->directory('microsites/gallery')
                    ->imageEditor()
                    ->reorderable()
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('socials')
                    ->label('Media Sosial (instagram, facebook, whatsapp)')
                    ->keyLabel('Jenis')
                    ->valueLabel('URL/Handle')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('map_embed')
                    ->label('Embed Peta (iframe HTML)')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),
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
                Tables\Columns\TextColumn::make('hero_title')
                    ->label('Judul')
                    ->searchable()
                    ->color('gray')
                    ->limit(40),
                Tables\Columns\ColorColumn::make('accent_color')
                    ->label('Aksen'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (Microsite $record): string => MicrositeResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn (Microsite $record): bool => ! ($record->is_active ?? false))
                    ->url(fn (Microsite $record): string => URL::signedRoute('admin.preview.microsite', ['microsite' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_site')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(fn (Microsite $record): bool => (bool) ($record->is_active ?? false))
                    ->url(fn (Microsite $record) => $record->place?->slug ? route('microsite.show', $record->place->slug) : null)
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Microsite $record): void {
                        $copy = $record->replicate();
                        $copy->title = $record->title.' (Salinan)';
                        $copy->is_active = false;
                        $copy->save();

                        Notification::make()->success()->title('Microsite disalin')->send();
                        redirect()->to(MicrositeResource::getUrl('edit', ['record' => $copy]));
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn ($livewire) => (bool) data_get($livewire->getTableFilterState('trashed'), 'value')),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn ($livewire) => (bool) data_get($livewire->getTableFilterState('trashed'), 'value')),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('place');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Ringkasan')->columns(2)->schema([
                Infolists\Components\ImageEntry::make('hero_image')->label('Gambar Header'),
                Infolists\Components\TextEntry::make('place.name')->label('Kuliner')->weight('bold'),
                Infolists\Components\TextEntry::make('hero_title')->label('Judul'),
                Infolists\Components\ColorEntry::make('accent_color')->label('Aksen'),
                Infolists\Components\IconEntry::make('is_active')->label('Aktif')->boolean(),
            ]),
            Infolists\Components\Section::make('Riwayat Audit')->schema([
                Infolists\Components\View::make('filament.infolists.components.audit-history'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMicrosites::route('/'),
            'view' => Pages\ViewMicrosite::route('/{record}'),
            'create' => Pages\CreateMicrosite::route('/create'),
            'edit' => Pages\EditMicrosite::route('/{record}/edit'),
        ];
    }
}