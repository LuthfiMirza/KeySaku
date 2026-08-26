<?php

namespace App\Livewire\Settings;

use App\Actions\ProvisionHouseholdForUser;
use App\Models\HouseholdMember;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ManageHousehold extends Component
{
    public function removeMember(int $memberId): void
    {
        abort_unless($this->isOwner(), 403);

        $household = auth()->user()->household;
        $target = $household->members()->whereKey($memberId)->first();

        abort_if(! $target, 404);

        if ($target->user_id === auth()->id()) {
            $this->addError('member', 'Tidak bisa mengeluarkan diri sendiri. Pakai tombol "Keluar dari Keluarga".');
            return;
        }

        $removedUser = $target->user;
        $target->delete();

        (new ProvisionHouseholdForUser())->handle($removedUser, null, 0);

        session()->flash('household_status', $removedUser->name.' telah dikeluarkan dari keluarga.');
    }

    public function leaveHousehold()
    {
        $user = auth()->user();
        $household = $user->household;
        $membership = $user->householdMembership;

        if ($this->isOwner() && $household->members()->count() > 1) {
            $this->addError('leave', 'Kamu pemilik keluarga ini dan masih ada anggota lain. Keluarkan semua anggota dulu sebelum keluar.');
            return null;
        }

        $membership->delete();
        (new ProvisionHouseholdForUser())->handle($user, null, 0);

        if ($household->members()->count() === 0) {
            $household->delete();
        }

        return redirect()
            ->route('settings')
            ->with('settings_status', 'Kamu sudah keluar dan mendapat keluarga baru untuk dirimu sendiri.');
    }

    public function render(): View
    {
        $household = auth()->user()->household;

        return view('livewire.settings.manage-household', [
            'household' => $household,
            'members' => $household->members()->with('user')->orderByDesc('role')->get(),
            'isOwner' => $this->isOwner(),
        ]);
    }

    private function isOwner(): bool
    {
        return auth()->user()->householdMembership->role === HouseholdMember::ROLE_OWNER;
    }
}
