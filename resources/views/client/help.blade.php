@extends('layouts.app')
@section('title', 'Help & Support')
@section('content')

<style>
.ch-wrap { max-width: 860px; margin: 0 auto; }
.ch-page-title { font-size: 1.5rem; font-weight: 700; color: #1e2d4d; margin-bottom: 4px; }
.ch-page-sub   { font-size: .88rem; color: #6b7280; margin-bottom: 28px; }

.ch-hero {
    background: linear-gradient(135deg,#1e2d4d,#2a3f6f);
    border-radius: 16px; padding: 36px 40px; color: #fff;
    text-align: center; margin-bottom: 28px;
}
.ch-hero h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
.ch-hero p  { color: rgba(255,255,255,.7); font-size: .92rem; margin-bottom: 20px; }
.ch-search-box {
    display: flex; max-width: 480px; margin: 0 auto; gap: 0;
    background: #fff; border-radius: 10px; overflow: hidden;
}
.ch-search-input {
    flex: 1; padding: 12px 18px; border: none; outline: none;
    font-size: .9rem; color: #1e2d4d; font-family: inherit;
}
.ch-search-btn {
    padding: 12px 20px; background: #b5860d; color: #fff;
    border: none; cursor: pointer; font-size: .9rem; font-weight: 600;
    font-family: inherit;
}

.ch-quick-links { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 28px; }
.ch-quick-link {
    background: #fff; border: 1px solid #eef0f3; border-radius: 14px;
    padding: 22px 20px; text-align: center; text-decoration: none;
    box-shadow: 0 1px 4px rgba(0,0,0,.04); transition: box-shadow .15s, border-color .15s;
    cursor: pointer;
}
.ch-quick-link:hover { border-color: #c7d0de; box-shadow: 0 4px 12px rgba(0,0,0,.08); text-decoration: none; }
.ch-quick-icon { width: 52px; height: 52px; border-radius: 14px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
.ch-quick-icon.blue  { background: #eff5ff; color: #2563eb; }
.ch-quick-icon.gold  { background: #fff8ea; color: #d97706; }
.ch-quick-icon.green { background: #ecfdf5; color: #059669; }
.ch-quick-title { font-size: .9rem; font-weight: 700; color: #1e2d4d; }
.ch-quick-desc  { font-size: .78rem; color: #6b7280; margin-top: 4px; }

.ch-card {
    background: #fff; border: 1px solid #eef0f3; border-radius: 16px;
    padding: 28px 32px; box-shadow: 0 1px 4px rgba(0,0,0,.04); margin-bottom: 24px;
}
.ch-card-title { font-size: 1rem; font-weight: 700; color: #1e2d4d; margin-bottom: 20px;
    padding-bottom: 12px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; gap: 8px; }
.ch-card-title i { color: #2563eb; }

/* FAQ accordion */
.ch-faq-item { border-bottom: 1px solid #f0f2f5; }
.ch-faq-item:last-child { border-bottom: none; }
.ch-faq-q {
    display: flex; justify-content: space-between; align-items: center;
    padding: 15px 0; cursor: pointer; font-size: .92rem;
    font-weight: 600; color: #1e2d4d;
}
.ch-faq-q:hover { color: #2563eb; }
.ch-faq-q i { transition: transform .2s; color: #6b7280; font-size: .8rem; flex-shrink: 0; }
.ch-faq-item.open .ch-faq-q i { transform: rotate(180deg); }
.ch-faq-a {
    display: none; padding: 0 0 16px; font-size: .88rem;
    line-height: 1.7; color: #4b5563;
}
.ch-faq-item.open .ch-faq-a { display: block; }

/* Contact support */
.ch-contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.ch-contact-opt {
    border: 1.5px solid #eef0f3; border-radius: 12px;
    padding: 20px; display: flex; gap: 14px; align-items: flex-start;
}
.ch-contact-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.ch-contact-icon.blue  { background: #eff5ff; color: #2563eb; }
.ch-contact-icon.green { background: #ecfdf5; color: #059669; }
.ch-contact-title { font-size: .9rem; font-weight: 700; color: #1e2d4d; }
.ch-contact-desc  { font-size: .8rem; color: #6b7280; margin-top: 3px; }
.ch-contact-btn {
    display: inline-flex; align-items: center; gap: 6px; margin-top: 10px;
    padding: 7px 16px; background: #1e2d4d; color: #fff; border: none;
    border-radius: 7px; font-size: .82rem; font-weight: 600;
    cursor: pointer; font-family: inherit; text-decoration: none;
}
.ch-contact-btn:hover { background: #162340; color: #fff; text-decoration: none; }
</style>

<div class="ch-wrap">
    <div class="ch-page-title">Help & Support</div>
    <p class="ch-page-sub">Find answers, guides, and get in touch with our support team</p>

    {{-- Hero search --}}
    <div class="ch-hero">
        <h2>How can we help you?</h2>
        <p>Search our knowledge base or browse topics below</p>
        <div class="ch-search-box">
            <input type="text" class="ch-search-input" id="faqSearchInput"
                placeholder="e.g. How to book a consultation..." oninput="filterFaq(this.value)">
            <button class="ch-search-btn"><i class="fas fa-search"></i></button>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="ch-quick-links">
        <div class="ch-quick-link" onclick="scrollToFaq()">
            <div class="ch-quick-icon blue"><i class="fas fa-question-circle"></i></div>
            <div class="ch-quick-title">FAQ</div>
            <div class="ch-quick-desc">Browse common questions</div>
        </div>
        <a href="{{ route('messages') }}" class="ch-quick-link">
            <div class="ch-quick-icon gold"><i class="fas fa-comment-dots"></i></div>
            <div class="ch-quick-title">Live Chat</div>
            <div class="ch-quick-desc">Message a support agent</div>
        </a>
        <div class="ch-quick-link" onclick="scrollToContact()">
            <div class="ch-quick-icon green"><i class="fas fa-envelope"></i></div>
            <div class="ch-quick-title">Email Support</div>
            <div class="ch-quick-desc">We reply within 24 hours</div>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="ch-card" id="faqSection">
        <div class="ch-card-title"><i class="fas fa-question-circle"></i> Frequently Asked Questions</div>
        <div id="faqList">
            @php
            $faqs = [
                ['q' => 'How do I book a consultation with a lawyer?',
                 'a' => 'Go to <strong>Find Lawyers</strong> from the navigation bar, browse or search by specialty, then click on a lawyer\'s profile and use the booking form to pick a date, time, and session type (video, phone, or in-person).'],
                ['q' => 'What types of consultations are available?',
                 'a' => 'LexConnect offers three types: <strong>Video Call</strong> — a live video session via our built-in platform; <strong>Phone Call</strong> — an audio-only call; and <strong>In-Person</strong> — a face-to-face meeting at the lawyer\'s listed office.'],
                ['q' => 'How do I join my video consultation?',
                 'a' => 'From your Dashboard or the Consultations page, find the upcoming appointment and click the <strong>Join Call</strong> button. Make sure your camera and microphone are allowed in your browser settings.'],
                ['q' => 'Can I cancel or reschedule a consultation?',
                 'a' => 'You can cancel an upcoming consultation from your Dashboard or Consultations page before the session starts. Rescheduling must be done by messaging the lawyer directly.'],
                ['q' => 'How does payment work?',
                 'a' => 'Payment is charged at the time of booking based on the lawyer\'s hourly rate and your chosen session duration. You can view all transactions under <strong>Payments</strong> in the navigation.'],
                ['q' => 'Is my conversation with a lawyer confidential?',
                 'a' => 'Yes. All messages and session contents are private between you and your chosen lawyer. LexConnect staff do not access individual conversation content.'],
                ['q' => 'What if my lawyer doesn\'t show up?',
                 'a' => 'If a lawyer misses a confirmed session, please contact our support team immediately. We will investigate and issue a full refund if the session did not take place.'],
                ['q' => 'How do I update my profile or password?',
                 'a' => 'Click your name in the top-right corner and select <strong>Profile</strong> to update your personal information, or <strong>Settings</strong> to change your password.'],
            ];
            @endphp
            @foreach($faqs as $faq)
            <div class="ch-faq-item" data-q="{{ strtolower($faq['q']) }}">
                <div class="ch-faq-q" onclick="toggleFaq(this)">
                    {{ $faq['q'] }}
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="ch-faq-a">{!! $faq['a'] !!}</div>
            </div>
            @endforeach
            <div id="faqNoResult" style="display:none;padding:20px;text-align:center;color:#9ca3af;font-size:.9rem;">
                No matching questions found. Try different keywords or contact support below.
            </div>
        </div>
    </div>

    {{-- Contact support --}}
    <div class="ch-card" id="contactSection">
        <div class="ch-card-title"><i class="fas fa-headset"></i> Contact Support</div>
        <div class="ch-contact-grid">
            <div class="ch-contact-opt">
                <div class="ch-contact-icon blue"><i class="fas fa-comment-dots"></i></div>
                <div>
                    <div class="ch-contact-title">Message Us</div>
                    <div class="ch-contact-desc">Chat with a support agent via the Messages module. Available Mon–Fri, 9 AM – 6 PM.</div>
                    <a href="{{ route('messages') }}" class="ch-contact-btn">
                        <i class="fas fa-arrow-right"></i> Open Messages
                    </a>
                </div>
            </div>
            <div class="ch-contact-opt">
                <div class="ch-contact-icon green"><i class="fas fa-envelope"></i></div>
                <div>
                    <div class="ch-contact-title">Email Support</div>
                    <div class="ch-contact-desc">Send us an email and we'll respond within 24 hours on business days.</div>
                    <a href="mailto:support@lexconnect.ph" class="ch-contact-btn">
                        <i class="fas fa-envelope"></i> support@lexconnect.ph
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFaq(el) {
    el.parentElement.classList.toggle('open');
}
function scrollToFaq() {
    document.getElementById('faqSection').scrollIntoView({ behavior: 'smooth' });
}
function scrollToContact() {
    document.getElementById('contactSection').scrollIntoView({ behavior: 'smooth' });
}
function filterFaq(val) {
    var items = document.querySelectorAll('.ch-faq-item');
    var found = 0;
    items.forEach(function(item) {
        var match = item.dataset.q.includes(val.toLowerCase());
        item.style.display = match ? '' : 'none';
        if (match) found++;
    });
    document.getElementById('faqNoResult').style.display = found === 0 ? '' : 'none';
}
</script>

@endsection
