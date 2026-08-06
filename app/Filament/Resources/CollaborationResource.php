<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollaborationResource\Pages;
use App\Models\Collaboration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CollaborationResource extends Resource
{
    protected static ?string $model = Collaboration::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationLabel = 'Kolaborasi';

    protected static ?string $navigationGroup = 'Komunitas';

    protected static ?string $modelLabel = 'Kolaborasi';

    protected static ?string $pluralModelLabel = 'Kolaborasi';

    protected static ?string $slug = 'kolaborasi';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
                Forms\Components\TextInput::make('whatsapp')->label('WhatsApp')->maxLength(30),
                Forms\Components\TextInput::make('business_name')->label('Nama Usaha')->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Jenis')
                    ->options(array_combine(Collaboration::TYPES, array_map('ucfirst', Collaboration::TYPES)))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(array_combine(Collaboration::STATUSES, array_map('ucfirst', Collaboration::STATUSES)))
                    ->required(),
                Forms\Components\Textarea::make('message')->label('Pesan')->rows(4)->columnSpanFull(),
            ])->columns(2),
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
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Usaha')
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'iklan' => 'primary',
                        'endorse' => 'info',
                        'review' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->label('Kontak')
                    ->copyable()
                    ->color('gray')
                    ->limit(25),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'contacted' => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Masuk')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (Collaboration $record): string => CollaborationResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(Collaboration::TYPES, array_map('ucfirst', Collaboration::TYPES))),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(Collaboration::STATUSES, array_map('ucfirst', Collaboration::STATUSES))),
                Tables\Filters\TrashedFilter::make()
                    ->label('Sampah (Recycle Bin)'),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label("Ekspor")
                    ->exporter(App\Filament\Exports\CollaborationExporter::class)
                    ->icon("heroicon-o-arrow-down-tray"),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label('Balas')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('template')
                            ->label('Template')
                            ->options([
                                'accepted' => 'Diterima — lanjut diskusi',
                                'rejected' => 'Ditolak dengan sopan',
                                'info' => 'Info tambahan',
                            ])
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\Textarea::make('message')
                            ->label('Pesan')
                            ->rows(5)
                            ->required()
                            ->helperText('Kamu bisa edit pesan sebelum dikirim.'),
                    ])
                    ->fillForm(fn (Collaboration $record): array => match (true) {
                        default => [
                            'template' => 'accepted',
                            'subject' => 'Respon Pengajuan Kolaborasi — '.($record->business_name ?: $record->name),
                            'message' => "Halo {$record->name}! Konsep kolaborasi kamu untuk ".($record->business_name ?: 'usaha kamu').' sudah kami terima. Team NGuliner akan segera menghubungi kamu untuk langkah berikutnya.',
                        ],
                    })
                    ->action(function (Collaboration $record, array $data): void {
                        \Illuminate\Support\Facades\Mail::to($record->email)->queue(new \App\Mail\CollaborationReplyMail(
                            $record->business_name ?: $record->name,
                            $data['subject'],
                            $data['message'],
                        ));

                        if ($record->status === 'new') {
                            $record->update(['status' => 'contacted']);
                        }

                        Notification::make()->success()->title('Balasan terkirim')->body('Email dikirim dan status diperbarui ke "contacted".')->send();
                    }),
                Tables\Actions\Action::make('wa')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn (Collaboration $record) => $record->whatsapp
                        ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $record->whatsapp)
                        : null)
                    ->openUrlInNewTab(),
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
            Infolists\Components\Section::make('Pengajuan')->columns(2)->schema([
                Infolists\Components\TextEntry::make('name')->label('Nama')->weight('bold'),
                Infolists\Components\TextEntry::make('business_name')->label('Usaha'),
                Infolists\Components\TextEntry::make('email')->label('Email'),
                Infolists\Components\TextEntry::make('whatsapp')->label('WhatsApp'),
                Infolists\Components\TextEntry::make('type')->label('Jenis')->badge(),
                Infolists\Components\TextEntry::make('status')->label('Status')->badge(),
                Infolists\Components\TextEntry::make('message')->label('Pesan')->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('Riwayat Audit')->schema([
                Infolists\Components\View::make('filament.infolists.components.audit-history'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollaborations::route('/'),
            'view' => Pages\ViewCollaboration::route('/{record}'),
            'create' => Pages\CreateCollaboration::route('/create'),
            'edit' => Pages\EditCollaboration::route('/{record}/edit'),
        ];
    }
}
