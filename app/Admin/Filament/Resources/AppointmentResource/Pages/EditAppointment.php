<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\AppointmentResource\Pages;

use App\Application\Salon\Services\GiftCardService;
use App\Admin\Filament\Resources\AppointmentResource;
use Filament\Forms\Components\TextInput;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('payWithGiftCard')
                ->label(__('messages.filament.actions.pay_with_gift_card'))
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->modalWidth('2xl')
                ->modalContent(fn () => view('admin.filament.actions.gift-payment-scanner'))
                ->form([
                    TextInput::make('token')
                        ->label(__('messages.filament.gift_payment.token'))
                        ->placeholder(__('messages.filament.gift_payment.token_placeholder'))
                        ->required()
                        ->live(debounce: 400)
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $giftCard = app(GiftCardService::class)->findByQrToken((string) ($state ?? ''));

                            if ($giftCard === null) {
                                $set('balance_preview', null);
                                $set('status_preview', null);
                                return;
                            }

                            $set('balance_preview', number_format((float) $giftCard->balance, 0, '.', ' ').' '.$giftCard->currency);
                            $set('status_preview', __('messages.filament.gift_card_status.'.$giftCard->status->value));
                        }),
                    TextInput::make('amount')
                        ->label(__('messages.filament.gift_payment.amount'))
                        ->placeholder('1000')
                        ->required()
                        ->numeric()
                        ->minValue(0.01),
                    TextInput::make('balance_preview')
                        ->label(__('messages.filament.gift_payment.balance_preview'))
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('—'),
                    TextInput::make('status_preview')
                        ->label(__('messages.filament.gift_payment.status_preview'))
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('—'),
                ])
                ->action(function (array $data): void {
                    $giftCardService = app(GiftCardService::class);
                    $token = (string) ($data['token'] ?? '');
                    $amount = (float) ($data['amount'] ?? 0);

                    $giftCard = $giftCardService->findByQrToken($token);
                    if ($giftCard === null) {
                        Notification::make()
                            ->title(__('messages.filament.gift_payment.not_found'))
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $giftCardService->redeem(
                            giftCard: $giftCard,
                            amount: $amount,
                            performedBy: auth()->user(),
                            bookingOrderId: $this->record->booking_order_id ? (int) $this->record->booking_order_id : null,
                            appointmentId: (int) $this->record->id,
                            meta: ['source' => 'filament_appointment'],
                        );

                        $giftCard = $giftCard->fresh();

                        Notification::make()
                            ->title(__('messages.filament.gift_payment.success'))
                            ->body(__('messages.filament.gift_payment.balance_left', [
                                'amount' => number_format((float) ($giftCard?->balance ?? 0), 0, '.', ' '),
                                'currency' => (string) ($giftCard?->currency ?? 'AMD'),
                            ]))
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title(__('messages.filament.gift_payment.failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
