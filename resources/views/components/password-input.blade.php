{{--
    Input kata sandi dengan tombol tampilkan/sembunyikan (ikon mata).

    Pemakaian:
        <x-password-input name="password" />
        <x-password-input name="password_confirmation" autocomplete="new-password" />

    Props:
      name         : atribut name (wajib)
      id           : atribut id (default = name)
      autocomplete : default 'new-password'
      placeholder  : opsional
    Atribut lain (class, required, dst.) diteruskan ke <input>.

    Style: resources/css/courses/forms.css (.password-field, .password-toggle)
--}}
@props([
    'name',
    'id'           => null,
    'autocomplete' => 'new-password',
    'placeholder'  => null,
])

@php($id = $id ?? $name)

<div class="password-field">
    <input
        type="password"
        name="{{ $name }}"
        id="{{ $id }}"
        autocomplete="{{ $autocomplete }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >

    <button
        type="button"
        class="password-toggle"
        data-toggle-password="{{ $id }}"
        aria-label="Tampilkan kata sandi"
        aria-pressed="false"
        title="Tampilkan kata sandi"
    >
        {{-- ikon mata (kata sandi tersembunyi) --}}
        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>

        {{-- ikon mata dicoret (kata sandi sedang terlihat) --}}
        <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17.94 17.94A10.5 10.5 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 4.06-5.06"/>
            <path d="M9.9 4.24A9.6 9.6 0 0 1 12 4c6.5 0 10 7 10 7a18 18 0 0 1-2.16 3.19"/>
            <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
            <line x1="2" y1="2" x2="22" y2="22"/>
        </svg>
    </button>
</div>

{{-- Skrip dimuat sekali saja per halaman, walau komponen dipakai berkali-kali --}}
@once
    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-toggle-password]');
            if (!btn) return;

            const input = document.getElementById(btn.dataset.togglePassword);
            if (!input) return;

            const show  = input.type === 'password';
            const label = show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi';

            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', label);
            btn.title = label;
        });
    </script>
@endonce