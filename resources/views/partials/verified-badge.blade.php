{{-- Verified badge — 12-point rosette (mathematically precise), mirip Instagram/Twitter --}}
<span class="vbadge inline-flex items-center justify-center flex-shrink-0"
      style="width:{{ $size ?? '20' }}px; height:{{ $size ?? '20' }}px;"
      title="Akun Terverifikasi">
    <style>
        @keyframes vbadge-glow {
            0%, 100% { filter: drop-shadow(0 0 1.5px #3B82F6bb); }
            50%       { filter: drop-shadow(0 0 4px #3B82F6ff); }
        }
        .vbadge svg { animation: vbadge-glow 2.5s ease-in-out infinite; }
    </style>
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
         style="width:100%;height:100%;" aria-label="Verified">
        {{-- 12-point rosette: outer r=11, inner r=8, center (12,12) --}}
        <polygon
            fill="#3B82F6"
            points="12.00,1.00 14.07,4.27 17.50,2.47 17.66,6.34 21.53,6.50 19.73,9.93 23.00,12.00 19.73,14.07 21.53,17.50 17.66,17.66 17.50,21.53 14.07,19.73 12.00,23.00 9.93,19.73 6.50,21.53 6.34,17.66 2.47,17.50 4.27,14.07 1.00,12.00 4.27,9.93 2.47,6.50 6.34,6.34 6.50,2.47 9.93,4.27"
        />
        {{-- Checkmark --}}
        <path d="M8.5 12.5 L11.2 15.2 L16 9.5"
              fill="none"
              stroke="white"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"/>
    </svg>
</span>
