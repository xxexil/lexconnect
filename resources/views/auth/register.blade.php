<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexConnect – Create Account</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 24px; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        .auth-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,.12); padding: 48px 40px; width: 100%; max-width: 520px; }
        .auth-logo { text-align: center; margin-bottom: 28px; }
        .auth-logo h1 { font-size: 1.8rem; font-weight: 700; color: #1e2d4d; margin: 0; }
        .auth-logo span { color: #b5860d; }
        .auth-logo p { color: #6c757d; font-size: .9rem; margin-top: 4px; }
        .role-toggle { display: flex; border: 2px solid #e9ecef; border-radius: 10px; overflow: hidden; margin-bottom: 28px; }
        .role-btn { flex: 1; padding: 11px; background: transparent; border: none; font-size: .9rem; font-weight: 600; cursor: pointer; font-family: inherit; color: #6c757d; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 7px; }
        .role-btn.active { background: #1e2d4d; color: #fff; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: .83rem; font-weight: 600; color: #1e2d4d; margin-bottom: 6px; }
        .form-group input, .form-group select { width: 100%; padding: 11px 14px; border: 1.5px solid #dee2e6; border-radius: 8px; font-size: .93rem; font-family: inherit; box-sizing: border-box; transition: border-color .2s; background: #fff; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #1e2d4d; }
        .form-group input.is-invalid { border-color: #dc3545; }
        .invalid-feedback { color: #dc3545; font-size: .82rem; margin-top: 4px; }
        .section-divider { font-size: .8rem; font-weight: 700; color: #b5860d; text-transform: uppercase; letter-spacing: 1px; margin: 8px 0 18px; padding-bottom: 8px; border-bottom: 1px solid #f0e6c8; }
        .btn-primary-auth { width: 100%; padding: 13px; background: #1e2d4d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; letter-spacing: .5px; transition: background .2s; }
        .btn-primary-auth:hover { background: #162240; }
        .auth-footer { text-align: center; margin-top: 24px; font-size: .88rem; color: #6c757d; }
        .auth-footer a { color: #b5860d; font-weight: 600; text-decoration: none; }
        #lawyerFields { display: none; }
        #firmFields   { display: none; }
        #clientFirmTerms { display: block; } /* Initially visible for clients */
        .doc-upload-label { display:flex; align-items:center; justify-content:center; gap:10px; padding:13px 16px; border:2px dashed #1e2d4d; border-radius:8px; cursor:pointer; font-size:.9rem; font-weight:600; color:#1e2d4d; transition:all .2s; background:#f8f9ff; height:52px; box-sizing:border-box; white-space:nowrap; overflow:hidden; }
        .doc-upload-label:hover { background:#e8ecf5; border-color:#b5860d; color:#b5860d; }
        .doc-upload-label.selected { border-color:#28a745; color:#28a745; background:#f0fff4; border-style:solid; }
        .doc-upload-label input[type=file] { display:none; }
        .terms-row { display:flex; align-items:flex-start; gap:10px; margin-bottom:18px; }
        .terms-row input[type=checkbox] { margin-top:3px; width:16px; height:16px; accent-color:#1e2d4d; flex-shrink:0; cursor:pointer; }
        .terms-row label { font-size:.85rem; font-weight:400; color:#555; cursor:pointer; line-height:1.5; }
        .terms-row a { color: #b5860d; text-decoration: none; font-weight: 600; cursor: pointer; }
        .terms-row a:hover { text-decoration: underline; }

        /* Legal Modal */
        .legal-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
        .legal-modal-content { background: #fff; width: 100%; max-width: 700px; max-height: 85vh; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.2); }
        .legal-modal-header { padding: 20px 24px; background: #1e2d4d; color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .legal-modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .legal-modal-close { background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; opacity: 0.7; transition: opacity .2s; }
        .legal-modal-close:hover { opacity: 1; }
        .legal-modal-body { padding: 30px 24px; overflow-y: auto; font-size: 0.95rem; color: #334155; line-height: 1.6; }
        .legal-modal-body h2 { color: #1e2d4d; font-size: 1.2rem; margin-top: 25px; margin-bottom: 12px; }
        .legal-modal-body p { margin-bottom: 15px; }
        .legal-modal-body ul { margin-bottom: 15px; padding-left: 20px; }
        .legal-modal-body li { margin-bottom: 8px; }
        .legal-modal-footer { padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right; }
        .btn-close-modal { padding: 10px 24px; background: #1e2d4d; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .doc-hint { font-size:.75rem; color:#888; text-align:center; margin-top:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:100%; display:block; }
        /* Firm preview card */
        .firm-preview-card { border: 1.5px solid #d0dce8; border-radius: 10px; padding: 16px 18px; background: #f5f8ff; }
        .firm-preview-name { font-size: 1rem; font-weight: 700; color: #1e2d4d; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .firm-verified-badge { background: #e6f4ea; color: #2d7a3a; font-size: .72rem; font-weight: 700; padding: 2px 9px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; }
        .firm-preview-tagline { font-size: .85rem; color: #555; font-style: italic; margin-bottom: 10px; }
        .firm-preview-desc { font-size: .83rem; color: #444; line-height: 1.55; margin-bottom: 10px; }
        .firm-preview-meta { display: flex; flex-wrap: wrap; gap: 6px 18px; margin-bottom: 8px; }
        .firm-meta-item { font-size: .78rem; color: #555; display: flex; align-items: center; gap: 5px; }
        .firm-meta-item i { color: #b5860d; width: 12px; text-align: center; }
        .firm-preview-specs { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .firm-spec-tag { background: #e8ecf8; color: #1e2d4d; font-size: .75rem; padding: 2px 10px; border-radius: 20px; font-weight: 500; }
    </style>
</head>
<body>
{{-- Hidden data for JavaScript --}}
<div id="registration-data" 
     data-law-firms='@json($lawFirms)'
     data-old-role="{{ old('role', 'client') }}"
     data-old-firm="{{ old('firm', '') }}"
     style="display:none;">
</div>

<div class="auth-card">
        <div class="auth-logo">
            <h1>Lex<span>Connect</span></h1>
            <p id="formSubtitle">Create your client account</p>
        </div>

        @if ($errors->any())
            <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:8px;padding:12px;margin-bottom:20px;">
                @foreach ($errors->all() as $e)
                    <div style="color:#dc3545;font-size:.85rem;">• {{ $e }}</div>
                @endforeach
            </div>
        @endif

        {{-- Role Toggle --}}
        <div class="role-toggle">
            <button type="button" class="role-btn active" id="btnClient" onclick="selectRole('client')">
                <i class="fas fa-user"></i> I'm a Client
            </button>
            <button type="button" class="role-btn" id="btnLawyer" onclick="selectRole('lawyer')">
                <i class="fas fa-gavel"></i> I'm a Lawyer
            </button>
            <button type="button" class="role-btn" id="btnFirm" onclick="selectRole('law_firm')">
                <i class="fas fa-building-columns"></i> Law Firm
            </button>
        </div>

        <form method="POST" action="/register" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'client') }}">

            <div class="section-divider">Personal Information</div>

            {{-- Shared: Full Name (Client & Law Firm only) --}}
            <div class="form-group" id="clientNameGroup">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Alex Johnson" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 6 characters" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </div>
            </div>

            {{-- Law Firm-only fields --}}
            <div id="firmFields">
                <div class="section-divider">Firm Information</div>
                <div class="form-group">
                    <label>Firm / Organization Name *</label>
                    <input type="text" name="firm_name" id="firmNameInput" value="{{ old('firm_name') }}" placeholder="e.g. Morrison &amp; Associates" class="{{ $errors->has('firm_name') ? 'is-invalid' : '' }}">
                    @error('firm_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. New York">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000">
                    </div>
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <input type="url" name="website" value="{{ old('website') }}" placeholder="https://yourfirm.com">
                </div>
                <div class="section-divider" style="text-align:center;">Required Registration Documents</div>
                <div class="form-row" style="margin-bottom:6px;">
                    <div class="form-group" style="margin-bottom:4px;min-width:0;">
                        <label class="doc-upload-label" id="dtiSecLabel">
                            <i class="fas fa-building-shield"></i> DTI/SEC Registration
                            <input type="file" name="dti_sec_registration" accept="image/*,.pdf" onchange="markUploaded(this,'dtiSecLabel','dtiSecName')">
                        </label>
                        <div class="doc-hint" id="dtiSecName"></div>
                        @error('dti_sec_registration')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:4px;min-width:0;">
                        <label class="doc-upload-label" id="businessPermitLabel">
                            <i class="fas fa-file-signature"></i> Business Permit
                            <input type="file" name="business_permit" accept="image/*,.pdf" onchange="markUploaded(this,'businessPermitLabel','businessPermitName')">
                        </label>
                        <div class="doc-hint" id="businessPermitName"></div>
                        @error('business_permit')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row" style="margin-bottom:6px;">
                    <div class="form-group" style="margin-bottom:4px;min-width:0;">
                        <label class="doc-upload-label" id="validIdLabel">
                            <i class="fas fa-id-card"></i> Valid ID
                            <input type="file" name="valid_id" accept="image/*,.pdf" onchange="markUploaded(this,'validIdLabel','validIdName')">
                        </label>
                        <div class="doc-hint" id="validIdName"></div>
                        @error('valid_id')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:4px;min-width:0;">
                        <label class="doc-upload-label" id="ibpFirmLabel">
                            <i class="fas fa-file-certificate"></i> IBP ID
                            <input type="file" name="firm_ibp_id" accept="image/*,.pdf" onchange="markUploaded(this,'ibpFirmLabel','ibpFirmName')">
                        </label>
                        <div class="doc-hint" id="ibpFirmName"></div>
                        @error('firm_ibp_id')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Lawyer-only fields --}}
            <div id="lawyerFields">
                {{-- Lawyer: split name fields --}}
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Juan" class="{{ $errors->has('first_name') ? 'is-invalid' : '' }}">
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. dela Cruz" class="{{ $errors->has('last_name') ? 'is-invalid' : '' }}">
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Middle Name <span style="font-weight:400;color:#888;">(optional)</span></label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g. Santos">
                </div>

                <div class="section-divider">Professional Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Specialty / Practice Area</label>
                        <input type="text" name="specialty" id="specialtyInput" list="specialtyList"
                               value="{{ old('specialty') }}"
                               placeholder="Select or type your specialty…"
                               class="{{ $errors->has('specialty') ? 'is-invalid' : '' }}"
                               autocomplete="off">
                        <datalist id="specialtyList">
                            @foreach(['Corporate Law','Family Law','Criminal Defense','Immigration Law','Real Estate','Personal Injury','Employment Law','Tax Law','Intellectual Property','Estate Planning','Civil Law','Labor Law','Environmental Law','Administrative Law','Banking & Finance Law','Maritime Law','Construction Law','Health Law','Cyber Law','Human Rights Law'] as $sp)
                                <option value="{{ $sp }}">
                            @endforeach
                        </datalist>
                        @error('specialty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Law Firm / Organization</label>
                        <select id="firmSelectDrop" onchange="handleFirmSelect(this)"
                                style="margin-bottom:0; {{ $errors->has('firm') ? 'border-color:#dc3545;' : '' }}">
                            <option value="">— Independent (no firm) —</option>
                            @foreach($lawFirms as $lf)
                                <option value="{{ $lf->firm_name }}"
                                        {{ (old('firm') === $lf->firm_name) ? 'selected' : '' }}>
                                    {{ $lf->firm_name }}{{ $lf->is_verified ? ' ✓' : '' }}
                                </option>
                            @endforeach
                            <option value="__custom__" {{ (old('firm') && !collect($lawFirms)->pluck('firm_name')->contains(old('firm'))) ? 'selected' : '' }}>Other (type manually)…</option>
                        </select>
                        <input type="hidden" name="firm" id="firmValue" value="{{ old('firm') }}">
                        <input type="text" id="firmCustomText"
                               placeholder="Enter firm name…"
                               style="display:none; margin-top:8px;"
                               oninput="document.getElementById('firmValue').value = this.value">
                        @error('firm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                {{-- Firm Preview Card --}}
                <div id="firmPreviewCard" style="display:none; margin-bottom:18px;"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Hourly Rate (PHP)</label>
                        <input type="number" name="hourly_rate" value="{{ old('hourly_rate', 3000) }}" min="0" step="100" placeholder="e.g. 3000">
                        @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Years of Experience</label>
                        <input type="number" name="experience_years" value="{{ old('experience_years', 1) }}" min="0" max="60" placeholder="e.g. 5">
                        @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Makati, Metro Manila">
                </div>

                {{-- Document Upload --}}
                <div class="section-divider" style="text-align:center;">Please Upload Documents for Verification</div>
                <div class="form-row" style="margin-bottom:6px;">
                    <div class="form-group" style="margin-bottom:4px;min-width:0;">
                        <label class="doc-upload-label" id="govIdLabel">
                            <i class="fas fa-id-card"></i> Government ID
                            <input type="file" name="government_id" accept="image/*,.pdf" onchange="markUploaded(this,'govIdLabel','govIdName')">
                        </label>
                        <div class="doc-hint" id="govIdName"></div>
                        @error('government_id')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:4px;min-width:0;">
                        <label class="doc-upload-label" id="ibpIdLabel">
                            <i class="fas fa-file-certificate"></i> IBP ID
                            <input type="file" name="ibp_id" accept="image/*,.pdf" onchange="markUploaded(this,'ibpIdLabel','ibpIdName')">
                        </label>
                        <div class="doc-hint" id="ibpIdName"></div>
                        @error('ibp_id')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Terms & Conditions --}}
                <div class="terms-row">
                    <input type="checkbox" name="agreed_terms" id="agreedTerms" value="1" {{ old('agreed_terms') ? 'checked' : '' }}>
                    <label for="agreedTerms">
                        I have read, understood, and accepted the
                        <a onclick="showLegal('terms')">Terms and Conditions</a> and
                        <a onclick="showLegal('privacy')">Privacy Policy</a>
                    </label>
                </div>
                @error('agreed_terms')<div class="invalid-feedback" style="display:block;margin-top:-12px;margin-bottom:12px;">You must accept the terms and conditions.</div>@enderror
            </div>

            {{-- Terms and Conditions (Client & Firm) --}}
            <div class="terms-row" id="clientFirmTerms">
                <input type="checkbox" name="agreed_terms_client_firm" id="agreed_terms_client_firm" value="1" {{ old('agreed_terms_client_firm') ? 'checked' : '' }}>
                <label for="agreed_terms_client_firm">
                    I have read, understood, and accepted the <a onclick="showLegal('terms')">Terms and Conditions</a> and <a onclick="showLegal('privacy')">Privacy Policy</a>
                </label>
            </div>
            @error('agreed_terms_client_firm')<div class="invalid-feedback" style="display:block;margin-top:-12px;margin-bottom:12px;">You must accept the terms and conditions.</div>@enderror

            <button type="submit" class="btn-primary-auth" id="submitBtn">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>

    {{-- Legal Modal --}}
    <div id="legalModal" class="legal-modal" onclick="handleOverlayClick(event)">
        <div class="legal-modal-content">
            <div class="legal-modal-header">
                <h3 id="legalTitle">Terms and Conditions</h3>
                <button type="button" class="legal-modal-close" onclick="closeLegal()">&times;</button>
            </div>
            <div class="legal-modal-body" id="legalBody">
                <!-- Content will be injected here -->
            </div>
            <div class="legal-modal-footer">
                <button type="button" class="btn-close-modal" onclick="closeLegal()">I Understand</button>
            </div>
        </div>
    </div>

    <script>
        function showLegal(type) {
            const modal = document.getElementById('legalModal');
            const title = document.getElementById('legalTitle');
            const body = document.getElementById('legalBody');
            
            if (type === 'terms') {
                title.innerText = 'Terms and Conditions';
                body.innerHTML = `
                    <p>Last Updated: March 23, 2026</p>
                    <p>Welcome to LexConnect. By accessing or using our platform, you agree to be bound by these Terms and Conditions. Please read them carefully.</p>
                    <h2>1. Services Provided</h2>
                    <p>LexConnect is a platform that connects clients with certified legal professionals. We facilitate consultations, messaging, and document sharing but do not provide legal advice ourselves.</p>
                    <h2>2. User Responsibilities</h2>
                    <ul>
                        <li>Users must provide accurate and complete information during registration.</li>
                        <li>Clients are responsible for the details provided in consultation requests.</li>
                        <li>Lawyers must maintain active certification and follow ethical guidelines of their jurisdiction.</li>
                    </ul>
                    <h2>3. Payments and Refunds</h2>
                    <p>Payments for consultations are handled securely through our platform. Downpayments are required to secure a booking.</p>
                    <h2>4. Confidentiality</h2>
                    <p>Communication between lawyers and clients through LexConnect is intended to be confidential.</p>
                    <h2>5. Limitation of Liability</h2>
                    <p>LexConnect is not liable for the outcome of legal consultations or the quality of advice provided by independent lawyers.</p>
                `;
            } else {
                title.innerText = 'Privacy Policy';
                body.innerHTML = `
                    <p>Last Updated: March 23, 2026</p>
                    <p>At LexConnect, we take your privacy seriously. This Privacy Policy explains how we collect, use, and protect your personal information.</p>
                    <h2>1. Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as your name, email address, profile details, and payment information.</p>
                    <h2>2. How We Use Your Information</h2>
                    <ul>
                        <li>To provide and improve our legal connection services.</li>
                        <li>To process payments and verify identities.</li>
                        <li>To facilitate secure communication between lawyers and clients.</li>
                    </ul>
                    <h2>3. Data Security</h2>
                    <p>We implement industry-standard security measures to protect your data from unauthorized access.</p>
                    <h2>4. Data Sharing</h2>
                    <p>We do not sell your personal data. We only share information with lawyers you choose to connect with.</p>
                `;
            }
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }

        function closeLegal() {
            document.getElementById('legalModal').style.display = 'none';
            document.body.style.overflow = ''; // Restore scroll
        }

        function handleOverlayClick(e) {
            if (e.target.id === 'legalModal') {
                closeLegal();
            }
        }

        let LAW_FIRMS = [];
        const registrationData = document.getElementById('registration-data');
        if (registrationData) {
            try {
                LAW_FIRMS = JSON.parse(registrationData.dataset.lawFirms || '[]');
            } catch (e) {
                console.error('Failed to parse law firms data', e);
            }
        }

        const prevRole = registrationData ? registrationData.dataset.oldRole : 'client';
        if (prevRole === 'lawyer') selectRole('lawyer');
        else if (prevRole === 'law_firm') selectRole('law_firm');
        else selectRole('client'); // Ensure defaults are set for client too

        function setSectionInputsEnabled(sectionId, enabled) {
            const section = document.getElementById(sectionId);
            if (!section) return;
            section.querySelectorAll('input, select, textarea, button').forEach(el => {
                el.disabled = !enabled;
            });
        }

        function selectRole(role) {
            document.getElementById('roleInput').value = role;
            const isLawyer  = role === 'lawyer';
            const isFirm    = role === 'law_firm';
            document.getElementById('btnClient').classList.toggle('active', role === 'client');
            document.getElementById('btnLawyer').classList.toggle('active', isLawyer);
            document.getElementById('btnFirm').classList.toggle('active', isFirm);
            document.getElementById('lawyerFields').style.display = isLawyer ? 'block' : 'none';
            document.getElementById('firmFields').style.display   = isFirm   ? 'block' : 'none';
            document.getElementById('clientFirmTerms').style.display = isLawyer ? 'none' : 'block';
            setSectionInputsEnabled('lawyerFields', isLawyer);
            setSectionInputsEnabled('firmFields', isFirm);

            // Disable/Enable terms inputs based on role to avoid validation conflicts
            document.getElementById('agreedTerms').disabled = !isLawyer;
            document.getElementById('agreed_terms_client_firm').disabled = isLawyer;

            document.getElementById('clientNameGroup').style.display = isLawyer ? 'none' : 'block';
            const nameInput = document.querySelector('#clientNameGroup input');
            if (nameInput) nameInput.required = !isLawyer;

            const subtitles = { client: 'Create your client account', lawyer: 'Create your lawyer account', law_firm: 'Register your law firm' };
            const btnLabels = { client: 'Create Account', lawyer: 'Register as Lawyer', law_firm: 'Register Firm' };
            document.getElementById('formSubtitle').textContent = subtitles[role] || subtitles.client;
            document.getElementById('submitBtn').textContent = btnLabels[role] || btnLabels.client;

            const specialtyEl = document.querySelector('[name=specialty]');
            if (specialtyEl) specialtyEl.required = isLawyer;
            const firmNameEl = document.getElementById('firmNameInput');
            if (firmNameEl) firmNameEl.required = isFirm;
        }

        function handleFirmSelect(selectEl) {
            const val         = selectEl.value;
            const hiddenInput = document.getElementById('firmValue');
            const customText  = document.getElementById('firmCustomText');
            const previewCard = document.getElementById('firmPreviewCard');

            if (val === '__custom__') {
                hiddenInput.value = '';
                customText.style.display = 'block';
                customText.value = '';
                previewCard.style.display = 'none';
            } else if (val === '') {
                hiddenInput.value = '';
                customText.style.display = 'none';
                previewCard.style.display = 'none';
            } else {
                hiddenInput.value = val;
                customText.style.display = 'none';
                const firm = LAW_FIRMS.find(f => f.firm_name === val);
                if (firm) {
                    renderFirmPreview(firm);
                    previewCard.style.display = 'block';
                } else {
                    previewCard.style.display = 'none';
                }
            }
        }

        function renderFirmPreview(firm) {
            const card  = document.getElementById('firmPreviewCard');
            const specs = Array.isArray(firm.specialties) ? firm.specialties : [];
            card.innerHTML = `
                <div class="firm-preview-card">
                    <div class="firm-preview-name">
                        ${esc(firm.firm_name)}
                        ${firm.is_verified ? '<span class="firm-verified-badge"><i class="fas fa-check-circle"></i> Verified</span>' : '<span style="font-size:.72rem;color:#888;font-weight:400;">Not yet verified</span>'}
                    </div>
                    ${firm.tagline ? `<div class="firm-preview-tagline">"${esc(firm.tagline)}"</div>` : ''}
                    ${firm.description ? `<div class="firm-preview-desc">${esc(firm.description)}</div>` : '<div class="firm-preview-desc" style="color:#aaa;">No description provided.</div>'}
                    <div class="firm-preview-meta">
                        ${firm.city     ? `<span class="firm-meta-item"><i class="fas fa-map-marker-alt"></i> ${esc(firm.city)}</span>` : ''}
                        ${firm.phone    ? `<span class="firm-meta-item"><i class="fas fa-phone"></i> ${esc(firm.phone)}</span>` : ''}
                        ${firm.website  ? `<span class="firm-meta-item"><i class="fas fa-globe"></i> <a href="${esc(firm.website)}" target="_blank" rel="noopener noreferrer" style="color:#b5860d;">${esc(firm.website)}</a></span>` : ''}
                        ${firm.founded_year ? `<span class="firm-meta-item"><i class="fas fa-calendar-alt"></i> Est. ${esc(String(firm.founded_year))}</span>` : ''}
                        ${firm.firm_size ? `<span class="firm-meta-item"><i class="fas fa-users"></i> ${esc(firm.firm_size.charAt(0).toUpperCase()+firm.firm_size.slice(1))} firm</span>` : ''}
                        ${firm.cut_percentage !== null && firm.cut_percentage !== undefined ? `<span class="firm-meta-item"><i class="fas fa-percent"></i> ${esc(String(firm.cut_percentage))}% firm cut</span>` : ''}
                        ${firm.rating   ? `<span class="firm-meta-item"><i class="fas fa-star" style="color:#f59e0b;"></i> ${firm.rating}/5 (${firm.reviews_count} review${firm.reviews_count===1?'':'s'})</span>` : ''}
                    </div>
                    ${firm.cut_percentage !== null && firm.cut_percentage !== undefined ? `<div class="firm-preview-desc" style="margin-bottom:0;"><strong>Compensation:</strong> This firm currently takes ${esc(String(firm.cut_percentage))}% of the completed consultation balance.</div>` : ''}
                    ${specs.length ? `<div class="firm-preview-specs">${specs.map(s=>`<span class="firm-spec-tag">${esc(s)}</span>`).join('')}</div>` : ''}
                </div>`;
        }

        function esc(str) {
            const d = document.createElement('div');
            d.textContent = String(str);
            return d.innerHTML;
        }

        function markUploaded(input, labelId, nameId) {
            const label = document.getElementById(labelId);
            const nameDiv = document.getElementById(nameId);
            if (input.files && input.files[0]) {
                label.classList.add('selected');
                label.querySelector('i').className = 'fas fa-check-circle';
                if (nameDiv) nameDiv.textContent = input.files[0].name;
            } else {
                label.classList.remove('selected');
                if (nameDiv) nameDiv.textContent = '';
            }
        }

        // Restore firm selection state after validation failure
        (function () {
            if (!registrationData) return;
            const oldFirm  = registrationData.dataset.oldFirm;
            const firmSel  = document.getElementById('firmSelectDrop');
            if (!firmSel || !oldFirm) return;
            let matched = false;
            for (const opt of firmSel.options) {
                if (opt.value === oldFirm) {
                    opt.selected = true;
                    handleFirmSelect(firmSel);
                    matched = true;
                    break;
                }
            }
            if (!matched) {
                for (const opt of firmSel.options) {
                    if (opt.value === '__custom__') { opt.selected = true; break; }
                }
                const ct = document.getElementById('firmCustomText');
                if (ct) {
                    ct.style.display = 'block';
                    ct.value = oldFirm;
                }
                const fv = document.getElementById('firmValue');
                if (fv) fv.value = oldFirm;
            }
        })();
    </script>
</body>
</html>
