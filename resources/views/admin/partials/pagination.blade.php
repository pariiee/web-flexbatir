{{-- Simple pagination partial untuk admin --}}
<div class="flex items-center justify-between text-sm">
    <p class="text-slate-500 text-xs">
        Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        dari {{ $paginator->total() }} hasil
    </p>
    <div class="flex gap-1">
        {{-- Previous --}}
        @if($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-xs text-slate-600 border border-[#2a2a2a] rounded-lg cursor-not-allowed">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1.5 text-xs text-slate-400 border border-[#2a2a2a] rounded-lg hover:bg-[#2a2a2a] transition-colors">←</a>
        @endif

        {{-- Page numbers --}}
        @foreach($elements as $element)
            @if(is_string($element))
                <span class="px-3 py-1.5 text-xs text-slate-600">{{ $element }}</span>
            @endif
            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 text-xs bg-brand text-white rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1.5 text-xs text-slate-400 border border-[#2a2a2a] rounded-lg hover:bg-[#2a2a2a] transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1.5 text-xs text-slate-400 border border-[#2a2a2a] rounded-lg hover:bg-[#2a2a2a] transition-colors">→</a>
        @else
            <span class="px-3 py-1.5 text-xs text-slate-600 border border-[#2a2a2a] rounded-lg cursor-not-allowed">→</span>
        @endif
    </div>
</div>
