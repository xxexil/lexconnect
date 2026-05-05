    <?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\FirmApplication;
use App\Models\LawFirmProfile;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SensitiveDocumentController extends Controller
{
    public function consultationDocument(Request $request, Consultation $consultation)
    {
        $consultation->loadMissing('lawyer.lawyerProfile');

        $user = $request->user();
        $firmProfileIds = $this->firmProfileIdsFor($user);
        $isParticipant = (int) $consultation->client_id === (int) $user->id
            || (int) $consultation->lawyer_id === (int) $user->id;
        $consultationFirmId = optional(optional($consultation->lawyer)->lawyerProfile)->law_firm_id;
        $isFirmOwner = $firmProfileIds->contains((int) $consultationFirmId);

        abort_unless($user->role === 'admin' || $isParticipant || $isFirmOwner, 403);

        return $this->serve($request, $consultation->case_document);
    }

    public function lawyerDocument(Request $request, LawyerProfile $lawyerProfile, string $document)
    {
        $path = $lawyerProfile->documentPath($document);
        abort_unless($path, 404);

        $user = $request->user();
        $firmProfileIds = $this->firmProfileIdsFor($user);
        $isOwner = (int) $lawyerProfile->user_id === (int) $user->id;
        $isFirmOwner = $firmProfileIds->contains((int) $lawyerProfile->law_firm_id);
        $hasFirmApplication = $firmProfileIds->isNotEmpty()
            && FirmApplication::whereIn('law_firm_id', $firmProfileIds)
                ->where('lawyer_id', $lawyerProfile->user_id)
                ->exists();

        abort_unless($user->role === 'admin' || $isOwner || $isFirmOwner || $hasFirmApplication, 403);

        return $this->serve($request, $path);
    }

    public function lawFirmDocument(Request $request, LawFirmProfile $lawFirmProfile, string $document)
    {
        $path = $lawFirmProfile->documentPath($document);
        abort_unless($path, 404);

        $user = $request->user();
        $isOwner = $this->firmProfileIdsFor($user)->contains((int) $lawFirmProfile->id);

        abort_unless($user->role === 'admin' || $isOwner, 403);

        return $this->serve($request, $path);
    }

    private function serve(Request $request, ?string $path)
    {
        abort_unless($path, 404);

        if (str_starts_with($path, 'http')) {
            return redirect()->away($path);
        }

        $disk = Storage::disk('local')->exists($path) ? 'local' : null;
        $disk ??= Storage::disk('public')->exists($path) ? 'public' : null;

        abort_unless($disk, 404);

        $filename = basename($path);
        $headers = [
            'Content-Type' => Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ];

        return $request->boolean('download')
            ? Storage::disk($disk)->download($path, $filename, $headers)
            : Storage::disk($disk)->response($path, $filename, $headers);
    }

    private function firmProfileIdsFor($user)
    {
        if ($user->role !== 'law_firm') {
            return collect();
        }

        return LawFirmProfile::where('user_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);
    }
}
