<?php

declare(strict_types=1);

namespace App\Admin\Filament\Pages;

use App\Application\Salon\Services\GiftCardService;
use App\Domain\Salon\Models\GiftCard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GiftCardScanner extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?int $navigationSort = 30;

    protected static string $view = 'admin.filament.pages.gift-card-scanner';

    public ?array $data = [];

    public ?GiftCard $giftCard = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasAnyRole(['admin', 'manager']);
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.filament.gift_card_scanner.navigation');
    }

    public function getTitle(): string
    {
        return __('messages.filament.gift_card_scanner.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'token' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('token')
                    ->label(__('messages.filament.gift_card_scanner.token'))
                    ->placeholder(__('messages.filament.gift_card_scanner.token_placeholder'))
                    ->required(),
            ])
            ->statePath('data');
    }

    public function findCard(GiftCardService $giftCardService): void
    {
        $token = trim((string) ($this->data['token'] ?? ''));

        if ($token === '') {
            Notification::make()
                ->title(__('messages.filament.gift_card_scanner.token_required'))
                ->danger()
                ->send();

            return;
        }

        $giftCard = $giftCardService->findByQrToken($token);

        if ($giftCard === null) {
            $this->giftCard = null;

            Notification::make()
                ->title(__('messages.filament.gift_card_scanner.not_found'))
                ->danger()
                ->send();

            return;
        }

        $this->giftCard = $giftCard->loadMissing(['owner', 'order']);

        Notification::make()
            ->title(__('messages.filament.gift_card_scanner.loaded'))
            ->success()
            ->send();
    }
}
