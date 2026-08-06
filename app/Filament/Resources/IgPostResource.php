<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IgPostResource\Pages;
use App\Models\IgPost;
use App\Services\AuditService;
use App\Services\ImageOptimizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class IgPostResource extends Resource
{
    protected static ?string $model = IgPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Feed Instagram';

    protected static ?string $navigationGroup = 'Komunitas';

    protected static ?string $modelLabel = 'Postingan';

    protected static ?string $pluralModelLabel = 'Feed Instagram';

    protected static ?string $slug = 'feed-instagram';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('ig_id')->label('ID Instagram')->required(),
            Forms\Components\FileUpload::make('image_url')->label('Gambar')->image()->directory('ig')
                ->saveUploadedFileUsing(fn (UploadedFile $file): string => app(ImageOptimizer::class)->optimize($file, 'ig')),
            Forms\Components\TextInput::make('permalink')->label('Link Postingan')->url(),
            Forms\Components\Textarea::make('caption')->label('Caption')->rows(3),
            Forms\Components\DateTimePicker::make('posted_at')->label('Waktu Posting'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Foto')
                    ->square()
                    ->height(48),
                Tables\Columns\TextColumn::make('caption')
                    ->label('Caption')
                    ->limit(60)
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('posted_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import')
                    ->label('Import dari JSON')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File JSON hasil export Instagram')
                            ->acceptedFileTypes(['application/json', 'text/plain'])
                            ->storeFiles(false)
                            ->required(),
                    ])
                    ->action(function (array $data, AuditService $audit): void {
                        $file = $data['file'] ?? null;

                        if (! $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            Notification::make()->danger()->title('File tidak valid')->send();

                            return;
                        }

                        $json = json_decode($file->get(), true);

                        if (! is_array($json)) {
                            Notification::make()->danger()->title('File JSON tidak valid')->send();

                            return;
                        }

                        $imported = 0;
                        foreach ($json as $post) {
                            if (! isset($post['ig_id']) || IgPost::where('ig_id', $post['ig_id'])->exists()) {
                                continue;
                            }

                            IgPost::create([
                                'ig_id' => $post['ig_id'],
                                'image_url' => $post['image_url'] ?? null,
                                'permalink' => $post['permalink'] ?? null,
                                'caption' => $post['caption'] ?? null,
                                'posted_at' => $post['posted_at'] ?? null,
                            ]);

                            $imported++;
                        }

                        $audit->log('feed.imported', null, ['imported' => $imported]);

                        Notification::make()
                            ->success()
                            ->title('Import selesai')
                            ->body("{$imported} postingan baru ditambahkan.")
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Buka IG')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (IgPost $record) => $record->permalink)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn ($livewire) => (bool) data_get($livewire->getTableFilterState('trashed'), 'value')),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn ($livewire) => (bool) data_get($livewire->getTableFilterState('trashed'), 'value')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('posted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIgPosts::route('/'),
            'create' => Pages\CreateIgPost::route('/create'),
            'edit' => Pages\EditIgPost::route('/{record}/edit'),
        ];
    }
}
