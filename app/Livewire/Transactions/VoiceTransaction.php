<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use App\Services\VoiceTransactionParser;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VoiceTransaction extends Component
{
    public string $originalTranscript = '';

    public string $transcript = '';

    public ?array $parsedDraft = null;

    public ?int $selectedAccountId = null;

    public function mount(): void
    {
        $this->selectedAccountId = auth()->user()->household
            ?->accounts()
            ?->where('is_active', true)
            ?->first()
            ?->id;
    }

    public function parse(VoiceTransactionParser $parser): void
    {
        $this->resetValidation();

        $this->validate([
            'transcript' => ['required', 'string', 'max:1000'],
        ], [
            'transcript.required' => 'Transkrip masih kosong.',
            'transcript.max' => 'Transkrip terlalu panjang.',
        ]);

        $household = auth()->user()->household;

        $this->parsedDraft = $parser->parse(
            $this->transcript,
            $household->categories()->get()
        );

        // Deteksi apakah nama akun disebutkan dalam transkrip
        $accounts = $household->accounts()->where('is_active', true)->get();
        foreach ($accounts as $account) {
            if (stripos($this->transcript, strtolower($account->name)) !== false) {
                $this->selectedAccountId = $account->id;
                break;
            }
        }

        if (! $this->selectedAccountId && $accounts->isNotEmpty()) {
            $this->selectedAccountId = $accounts->first()->id;
        }
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
            $this->addError('parsedDraft', $this->missingFieldsMessage());

            return null;
        }

        $type = $this->parsedDraft['type'] ?? Transaction::TYPE_EXPENSE;

        $household = auth()->user()->household;

        $category = $household
            ->categories()
            ->whereKey($this->parsedDraft['category_id'])
            ->where('type', $type)
            ->first();

        if (! $category) {
            $typeLabel = $type === Transaction::TYPE_INCOME ? 'pemasukan' : 'pengeluaran';
            $draftCategory = $this->parsedDraft['category_name'] ?? null;

            $this->addError('parsedDraft', $draftCategory
                ? "Kategori \"{$draftCategory}\" bukan kategori {$typeLabel}. Sebutkan kategori {$typeLabel}, atau ubah jenis transaksinya."
                : "Kategori untuk {$typeLabel} belum ditemukan. Sebutkan seperti 'kategori makanan'.");

            return null;
        }

        $amount = (float) $this->parsedDraft['amount'];
        $note = trim((string) $this->parsedDraft['note']);

        if ($amount <= 0 || $note === '') {
            $this->addError('parsedDraft', $this->missingFieldsMessage());

            return null;
        }

        $account = null;
        if ($this->selectedAccountId) {
            $account = $household->accounts()->where('is_active', true)->whereKey($this->selectedAccountId)->first();
        }
        if (! $account) {
            $account = $household->accounts()->where('is_active', true)->first();
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'household_id' => $household->id,
            'category_id' => $category->id,
            'account_id' => $account?->id,
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

    public function clearTranscript(): void
    {
        $this->originalTranscript = '';
        $this->transcript = '';
        $this->parsedDraft = null;
        $this->resetValidation();
    }

    public function resetToOriginal(): void
    {
        $this->transcript = $this->originalTranscript;
        $this->clearParsedDraft();
    }

    private function missingFieldsMessage(): string
    {
        if (! $this->parsedDraft) {
            return 'Proses transkrip dulu sebelum menyimpan.';
        }

        $missing = [];

        if (($this->parsedDraft['amount'] ?? 0) <= 0) {
            $missing[] = 'nominal';
        }

        if (empty($this->parsedDraft['category_id'])) {
            $missing[] = 'kategori';
        }

        if (trim((string) ($this->parsedDraft['note'] ?? '')) === '') {
            $missing[] = 'catatan';
        }

        if ($missing === []) {
            return 'Draf belum lengkap. Periksa lagi hasil proses transkrip.';
        }

        return 'Belum terbaca: '.implode(', ', $missing).'. Sebutkan ulang bagian itu di transkrip, lalu proses lagi.';
    }

    public function render(): View
    {
        $household = auth()->user()->household;

        return view('livewire.transactions.voice-transaction', [
            'hasCategories' => $household->categories()->exists(),
            'accounts' => $household->accounts()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
