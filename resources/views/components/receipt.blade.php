<div class="absolute right-0 top-0 w-64 h-96 overflow-hidden">
    <!-- Slit background -->
    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-48 h-2 bg-slate-800 rounded-full shadow-inner"></div>

    <!-- Receipt -->
    <div id="receipt" class="absolute left-1/2 transform -translate-x-1/2 w-56 bg-white shadow-lg" style="font-family: 'Courier New', monospace; bottom: -100%;">
        <div class="p-6">
            <!-- Receipt Header -->
            <div class="text-center border-b-2 border-dashed border-slate-300 pb-3 mb-3">
                <div class="text-xs font-bold">ASCHMELYUN.COM</div>
                <div class="text-xs">Content & Code</div>
                <div class="text-xs">Since 2016</div>
                <div class="text-xs mt-1">@aschmelyun</div>
            </div>

            <!-- Receipt Date/Time -->
            <div class="text-xs mb-3">
                <div>Date: {{ date('m/d/Y') }}</div>
                <div>Time: {{ date('H:i:s') }}</div>
                <div>Developer: Andrew</div>
            </div>

            <!-- Items -->
            <div class="border-t-2 border-b-2 border-dashed border-slate-300 py-3 mb-3">
                <div class="flex justify-between text-xs mb-1">
                    <span>Laravel Projects</span>
                    <span>$FREE</span>
                </div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Docker Workflows</span>
                    <span>$FREE</span>
                </div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Vue Components</span>
                    <span>$FREE</span>
                </div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Blog Posts</span>
                    <span>$FREE</span>
                </div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Video Tutorials</span>
                    <span>$FREE</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>GitHub Stars</span>
                    <span>3.5K+</span>
                </div>
            </div>

            <!-- Total -->
            <div class="border-b-2 border-dashed border-slate-300 pb-3 mb-3">
                <div class="flex justify-between text-xs mb-1">
                    <span>SUBTOTAL:</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Open Source Tax:</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between text-sm font-bold">
                    <span>TOTAL:</span>
                    <span>$0.00</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs">
                <div class="mb-1">THANK YOU!</div>
                <div class="text-[10px]">Building things & teaching</div>
                <div class="text-[10px]">people since 2016</div>
            </div>
        </div>
    </div>
</div>
