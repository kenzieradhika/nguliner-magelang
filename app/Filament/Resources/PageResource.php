<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Services\ImageOptimizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Halaman CMS';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Halaman';

    protected static ?string $pluralModelLabel = 'Halaman CMS';

    protected static ?string $slug = 'halaman';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Halaman')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Header')
                    ->image()
                    ->directory('pages')
                    ->imageEditor()
                    ->saveUploadedFileUsing(fn (UploadedFile $file): string => app(ImageOptimizer::class)->optimize($file, 'pages'))
                    ->helperText('Tampil sebagai thumbnail di daftar halaman.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta Title (SEO)')
                    ->maxLength(255)
                    ->live(onBlur: true),
                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta Description (SEO)')
                    ->rows(2)
                    ->maxLength(500)
                    ->live(onBlur: true),
                Forms\Components\View::make('filament.forms.components.seo-preview')
                    ->label('Preview Google')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')
                    ->label('Tayang')
                    ->default(true)
                    ->inline(false),
                Forms\Components\DateTimePicker::make('publish_at')
                    ->label('Jadwal Tayang')
                    ->helperText('Kosongkan jika langsung tayang. Konten otomatis tayang saat waktunya tiba.')
                    ->placeholder('30 menit lagi')
                    ->inline(false),
            ])->columns(2),

            Forms\Components\Section::make('Blok Section')
                ->description('heading, text, gambar, daftar, kutipan, CTA, atau embed HTML.')
                ->schema([
                    Forms\Components\Repeater::make('sections')
                        ->label('Blok Konten')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipe Blok')
                                ->options([
                                    'heading' => 'Heading',
                                    'text' => 'Text',
                                    'image' => 'Gambar',
                                    'list' => 'Daftar',
                                    'quote' => 'Kutipan',
                                    'cta' => 'Tombol CTA',
                                    'embed' => 'Embed (HTML)',
                                ])
                                ->default('text')
                                ->live()
                                ->required(),
                            Forms\Components\FileUpload::make('image')
                                ->label('Upload Gambar')
                                ->image()
                                ->directory('pages-blocks')
                                ->imageEditor()
                                ->saveUploadedFileUsing(fn (UploadedFile $file): string => app(ImageOptimizer::class)->optimize($file, 'pages-blocks'))
                                ->visible(fn (Forms\Get $get): bool => $get('type') === 'image')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('content')
                                ->label('Isi')
                                ->rows(3)
                                ->visible(fn (Forms\Get $get): bool => $get('type') !== 'image'),
                            Forms\Components\Textarea::make('items')
                                ->label('Item Daftar (satu per baris)')
                                ->rows(4)
                                ->visible(fn (Forms\Get $get) => $get('type') === 'list'),
                            Forms\Components\TextInput::make('url')
                                ->label('URL Tujuan')
                                ->visible(fn (Forms\Get $get): bool => $get('type') === 'cta'),
                            Forms\Components\TextInput::make('button')
                                ->label('Teks Tombol')
                                ->visible(fn (Forms\Get $get): bool => $get('type') === 'cta'),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderableWithButtons()
                        ->collapsible(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Page $record): ?string => $record->is_published ? null : 'Draft'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('sections')
                    ->label('Blok')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state).' blok' : '0 blok')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Tayang'),
                Tables\Columns\TextColumn::make('publish_at')
                    ->label('Terjadwal')
                    ->dateTime('d M Y H:i')
                    ->since()
                    ->badge()
                    ->color('warning')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->recordUrl(fn (Page $record): string => PageResource::getUrl('view', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn (Page $record): bool => ! $record->is_published)
                    ->url(fn (Page $record): string => URL::signedRoute('admin.preview.page', ['page' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_site')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(fn (Page $record): bool => $record->is_published)
                    ->url(fn (Page $record) => route('page.show', $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Page $record): void {
                        $copy = $record->replicate();
                        $copy->title = $record->title.' (Salinan)';
                        $copy->slug = $record->slug.'-'.Str::lower(Str::random(4));
                        $copy->is_published = false;
                        $copy->save();

                        Notification::make()->success()->title('Halaman disalin')->send();
                        redirect()->to(PageResource::getUrl('edit', ['record' => $copy]));
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'meta_title'];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informasi Halaman')->columns(2)->schema([
                Infolists\Components\ImageEntry::make('image')->label('Gambar')->circular(),
                Infolists\Components\TextEntry::make('title')->label('Judul')->weight('bold'),
                Infolists\Components\TextEntry::make('slug')->label('Slug'),
                Infolists\Components\TextEntry::make('meta_title')->label('Meta Title'),
                Infolists\Components\TextEntry::make('meta_description')->label('Meta Description'),
                Infolists\Components\IconEntry::make('is_published')->label('Tayang')->boolean(),
            ]),
            Infolists\Components\Section::make('Riwayat Audit')->schema([
                Infolists\Components\View::make('filament.infolists.components.audit-history'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'view' => Pages\ViewPage::route('/{record}'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
