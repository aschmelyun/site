<div class="bg-white shadow-lg py-6 px-6 font-mono text-sm max-w-sm rotate-3">
    {{-- Store Header --}}
    <div class="text-center pb-3">
        <div class="font-bold text-lg tracking-wider">ANDREW SCHMELYUN</div>
        <div class="text-xs">aschmelyun.com</div>
    </div>

    <div class="border-t border-dashed border-slate-300 pt-2 mb-2"></div>

    {{-- Transaction Info --}}
    <div class="text-xs mb-4 space-y-0.5">
        <div class="flex justify-between">
            <span>TRANSACTION #</span>
            <span>{{ rand(1111111,9999999) }}</span>
        </div>
        <div class="flex justify-between">
            <span>DATE</span>
            <span>{{ strtoupper(date('M d, Y H:i')) }}</span>
        </div>
    </div>

    <div class="border-t border-dashed border-slate-300 pt-2 mb-2"></div>

    {{-- Items --}}
    <div class="space-y-1.5 text-xs mb-3">

        <div>
            <div class="flex justify-between">
                <span>VIDEOS PUBLISHED</span>
                <span>109</span>
            </div>
        </div>

        <div>
            <div class="flex justify-between">
                <span>AUDIENCE REACHED</span>
                <span>61.2K</span>
            </div>
        </div>

        <div>
            <div class="flex justify-between">
                <span>IDEAS BACKLOGGED</span>
                <span>176</span>
            </div>
        </div>

        <div>
            <div class="flex justify-between">
                <span>POSTS WRITTEN</span>
                <span>{{ $postsCount }}</span>
            </div>
        </div>

        <div>
            <div class="flex justify-between">
                <span>LATTES CONSUMED</span>
                <span>∞</span>
            </div>
        </div>
    </div>

    <div class="border-t border-dashed border-slate-300 pt-2 mb-2"></div>

    {{-- Total --}}
    <div class="font-bold text-xs mb-4">
        <div class="flex justify-between">
            <span>TOTAL VALUE</span>
            <span>$NaN</span>
        </div>
    </div>

    <div class="border-t border-dashed border-slate-300 pt-3 mb-3"></div>

    {{-- Footer Message --}}
    <div class="text-center text-xs mb-4">
        <div>THANK YOU FOR VISITING!</div>
    </div>

    {{-- Barcode --}}
    <div class="text-center mb-2">
        <div class="inline-block">
            <div class="flex gap-[1px] h-12">
                <div class="w-[2px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[3px] bg-black"></div>
                <div class="w-[2px] bg-white"></div>
                <div class="w-[1px] bg-black"></div>
                <div class="w-[2px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[3px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[2px] bg-white"></div>
                <div class="w-[1px] bg-black"></div>
                <div class="w-[3px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[3px] bg-white"></div>
                <div class="w-[1px] bg-black"></div>
                <div class="w-[2px] bg-white"></div>
                <div class="w-[3px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[2px] bg-white"></div>
                <div class="w-[1px] bg-black"></div>
                <div class="w-[3px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[2px] bg-black"></div>
                <div class="w-[1px] bg-white"></div>
                <div class="w-[3px] bg-black"></div>
                <div class="w-[2px] bg-white"></div>
                <div class="w-[1px] bg-black"></div>
            </div>
        </div>
    </div>
    <div class="text-center text-[10px] tracking-widest mb-1">
        * {{ date('Y') }}{{ str_pad(date('z'), 3, '0', STR_PAD_LEFT) }}ASCH{{ date('His') }} *
    </div>
</div>