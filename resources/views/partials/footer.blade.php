<footer class="text-gray-600 body-font overflow-hidden mt-12">
    <div class="flex flex-wrap -mx-4 -mb-10 text-center items-center justify-between">
        <div class="w-full lg:w-1/2 mb-10 px-4 text-center lg:text-left">
            <p class="mb-2 font-medium text-gray-900">&copy;{{ date('Y') }} Andrew Schmelyun</p>
            <p>This site is powered by <a href="https://github.com/aschmelyun/cleaver" target="_blank" rel="noreferrer" class="inline-block py-0.5 px-2 rounded bg-white text-gray-600 border border-gray-600 text-xs font-medium tracking-widest hover:bg-gray-600 hover:text-white transition-colors duration-200">CLEAVER 🔥🔪</a></p>
        </div>
        <div class="w-full lg:w-1/2 mb-10 px-4 text-center lg:text-right">
            <p class="mb-2 font-medium text-gray-900">Follow me</p>
            @php
                $footerLinks = [
                    "GitHub" => "https://github.com/aschmelyun",
                    "Twitter" => "https://twitter.com/aschmelyun",
                    "YouTube" => "https://youtube.com/aschmelyun",
                    "Dev.to" => "https://dev.to/aschmelyun"
                ];
            @endphp
            <p>
                @foreach($footerLinks as $name => $link)
                    <a href="{{ $link }}" target="_blank" rel="noreferrer" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">{{ $name }}</a>
                    @if($name !== 'Dev.to')
                        &nbsp;&mdash;&nbsp;
                    @endif
                @endforeach
            </p>
        </div>
    </div>
</footer>
<script type="text/javascript" src="{{ $mix['/assets/js/app.js'] }}"></script>
@yield('bottom-scripts')