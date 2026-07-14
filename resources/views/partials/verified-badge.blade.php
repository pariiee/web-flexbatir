{{-- Verified badge — rosette/starburst dengan animasi pulse --}}
<span class="verified-badge inline-flex items-center justify-center relative flex-shrink-0" style="width:{{ $size ?? '20' }}px; height:{{ $size ?? '20' }}px;" title="Akun Terverifikasi">
    <style>
        @keyframes verified-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .85; transform: scale(1.08); }
        }
        .verified-badge svg {
            animation: verified-pulse 2.5s ease-in-out infinite;
        }
    </style>
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;">
        {{-- Rosette/starburst background --}}
        <path d="M12 2L14.09 8.26L20.63 6.5L18.09 12.63L23.51 16.5L17.09 17.74L17.51 24L12 20.77L6.49 24L6.91 17.74L0.49 16.5L5.91 12.63L3.37 6.5L9.91 8.26L12 2Z"
              fill="#3B82F6"/>
        {{-- Checkmark --}}
        <path d="M8.5 12L11 14.5L16 9.5"
              stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
