<?php

namespace App\Filament\Pages;

use App\Services\AuditService;
use App\Services\SiteSettingsService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?int $navigationSort = 15;

    protected static string $view = 'filament.pages.site-settings';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(app(SiteSettingsService::class)->all());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas')
                    ->description('Nama dan informasi dasar situs.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('site_name')->label('Nama Situs')->required()->maxLength(60),
                        TextInput::make('tagline')->label('Tagline')->maxLength(120),
                        TextInput::make('copyright')->label('Teks Hak Cipta')->maxLength(120),
                        TextInput::make('address')->label('Alamat')->maxLength(255),
                    ])->columns(2),

                Section::make('Kontak')
                    ->description('Informasi yang ditampilkan untuk kolaborasi dan komunikasi.')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextInput::make('whatsapp')->label('WhatsApp')->prefix('+62')->maxLength(30)
                            ->helperText('Tanpa kode negara. Contoh: 81234567890'),
                        TextInput::make('email')->label('Email')->email()->maxLength(120),
                        TextInput::make('instagram')->label('Handle Instagram')->maxLength(60),
                        TextInput::make('instagram_url')->label('URL Instagram')->url()->maxLength(255),
                    ])->columns(2),

                Section::make('SEO & Hero')
                    ->description('Default meta description dan teks hero beranda.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        TextInput::make('meta_description')->label('Meta Description (SEO)')->maxLength(300),
                        TextInput::make('hero_eyebrow')->label('Eyebrow Hero')->maxLength(120),
                        TextInput::make('hero_title')->label('Judul Hero')->maxLength(120),
                        TextInput::make('hero_title_italic')->label('Judul Hero (italic)')->maxLength(80),
                        TextInput::make('hero_subtitle')->label('Subjudul Hero')->maxLength(255),
                    ])->columns(2),

                Actions::make([
                    FormAction::make('save')
                        ->label('Simpan Pengaturan')
                        ->icon('heroicon-o-check-circle')
                        ->submit('save'),
                ]),
            ])
            ->statePath('data');
    }

    public function save(AuditService $audit): void
    {
        $data = $this->form->getState();

        app(SiteSettingsService::class)->update($data);
        $audit->log('settings.updated', null, ['fields' => array_keys($data)]);

        Notification::make()->success()->title('Pengaturan disimpan')->send();
    }
}