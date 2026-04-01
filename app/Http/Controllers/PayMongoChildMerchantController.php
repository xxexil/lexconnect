<?php

namespace App\Http\Controllers;

use App\Services\PayMongoChildMerchantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PayMongoChildMerchantController extends Controller
{
    public function startForLawyer(PayMongoChildMerchantService $service): RedirectResponse
    {
        $profile = Auth::user()?->lawyerProfile;

        abort_unless($profile, 404);

        $service->ensureForOwner($profile);

        return back()->with('success', 'PayMongo child merchant onboarding has been prepared for your lawyer account. ' . $service->supportMessage());
    }

    public function startForLawFirm(PayMongoChildMerchantService $service): RedirectResponse
    {
        $profile = Auth::user()?->lawFirmProfile;

        abort_unless($profile, 404);

        $service->ensureForOwner($profile);

        return back()->with('success', 'PayMongo child merchant onboarding has been prepared for your law firm account. ' . $service->supportMessage());
    }
}
