<?php

namespace App\Livewire\Transactions;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\VoiceTransactionParser;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VoiceTransaction extends Component
{
    public string $originalTranscript = '';

    public string $transcript = '';

    public ?array $parsedDraft = null;

    public function parse(VoiceTransactionParser $parser): void
    {
        $this->resetValidation();

        $this->validate([
            'transcript' => ['required', 'string', 'max:1000'],
        ], [
            'transcript.required' => 'Transkrip masih kosong.',
            'transcript.max' => 'Transkrip terlalu panjang.',
        ]);

        $this->parsedDraft = $parser->parse(
            $this->transcript,
            auth()->user()->categories()->get()
        );
    }


    public function getCanSaveProperty(): bool
    {
        return $this->parsedDraft !== null
            && ($this->parsedDraft['amount'] ?? 0) > 0
            && ! empty($this->parsedDraft['category_id'])
            && trim((string) ($this->parsedDraft['note'] ?? '')) !== '';
    }

    public function save()
    {
        $this->resetValidation();

        $this->validate([
            'transcript' => ['required', 'string', 'max:1000'],
        ], [
            'transcript.required' => 'Transkrip masih kosong.',
            'transcript.max' => 'Transkrip terlalu panjang.',
        ]);

        if (! $this->parsedDraft || ! $this->canSave) {
            $this->addError('parsedDraft', 'Lengkapi nominal, kategori, dan catatan sebelum menyimpan.');
            return null;
        }

        $type = $this->parsedDraft['type'] ?? Transaction::TYPE_EXPENSE;

        $category = auth()->user()
            ->categories()
            ->whereKey($this->parsedDraft['category_id'])
            ->where('type', $type)
            ->first();

        if (! $category) {
            $this->addError('parsedDraft', 'Kategori tidak valid untuk jenis transaksi ini.');
            return null;
        }

        $amount = (float) $this->parsedDraft['amount'];
        $note = trim((string) $this->parsedDraft['note']);

        if ($amount <= 0 || $note === '') {
            $this->addError('parsedDraft', 'Lengkapi nominal, kategori, dan catatan sebelum menyimpan.');
            return null;
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'type' => $type,
            'amount' => $amount,
            'transaction_date' => $this->parsedDraft['transaction_date'],
            'note' => $note,
            'source' => Transaction::SOURCE_VOICE,
            'raw_voice_text' => $this->transcript,
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('transaction_status', 'Transaksi suara berhasil disimpan.');
    }

    public function clearParsedDraft(): void
    {
        $this->parsedDraft = null;
    }

    public function render(): View
    {
        return view('livewire.transactions.voice-transaction');
    }
}
