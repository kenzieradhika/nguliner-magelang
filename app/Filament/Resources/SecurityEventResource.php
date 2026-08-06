<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityEventResource\Pages;
use App\Models\SecurityEvent;
use App\Services\SecurityEventService;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SecurityEventResource extends Resource
{
    protected static ?string $model = SecurityEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Keamanan';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'Insiden';

    protected static ?string $pluralModelLabel = 'Insiden Keamanan';

    protected static ?string $slug = 'keamanan';

    protected static ?int $navigationSort = 10;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => SecurityEvent::TYPES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'login_failed', 'csrf_mismatch' => 'warning',
                        'login_locked', 'session_hijack' => 'danger',
                        'login_success' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical', 'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(40)
                    ->tooltip(fn (SecurityEvent $record) => $record->user_agent),
                Tables\Columns\TextColumn::make('count')
                    ->label('Jumlah')
                    ->alignment('center')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Dibaca')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->options(SecurityEvent::SEVERITIES),
                Tables\Filters\SelectFilter::make('type')
                    ->options(SecurityEvent::TYPES),
                Tables\Filters\TernaryFilter::make('read_at')->label('Sudah dibaca'),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_read')
                    ->label(fn (SecurityEvent $record): string => $record->read_at ? 'Tandai belum dibaca' : 'Tandai dibaca')
                    ->icon(fn (SecurityEvent $record): string => $record->read_at ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(function (SecurityEvent $record): void {
                        $record->read_at ? $record->update(['read_at' => null]) : $record->markRead();
                        Notification::make()->success()->title('Diperbarui')->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_read')
                        ->label('Tandai dibaca')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (): void {
                            SecurityEvent::unread()->update(['read_at' => now()]);
                            Notification::make()->success()->title('Semua insiden ditandai dibaca')->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = app(SecurityEventService::class)->unreadCount();

        return $unread ? (string) $unread : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSecurityEvents::route('/'),
        ];
    }
}
