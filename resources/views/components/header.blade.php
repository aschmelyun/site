<header class="text-gray-900 font-semibold">
    <div class="container mx-auto px-3 md:px-0 flex flex-wrap py-4 flex-col md:flex-row items-center">
        <a href="/" id="link-home" class="flex title-font font-semibold items-center text-gray-900 mb-4 md:mb-0 hover:opacity-75 transition-opacity duration-200">
            <img src="{{ env('APP_URL') }}/storage/images/andrew-schmelyun-profile.jpg" alt="Andrew Schmelyun profile picture" class="w-16 h-16 rounded-full">
        </a>
        <nav class="w-full md:w-auto md:ml-auto flex flex-wrap items-center font-normal text-base text-gray-900 justify-around space-x-0 md:space-x-4 lg:justify-center relative">
            <a href="/blog/" class="py-1.5 px-3 md:px-4 bg-white rounded-full border-b {{ request()->is('blog') || request()->is('blog/*') ? 'border-indigo-300 shadow-sm shadow-indigo-200' : 'border-slate-300 border-opacity-0 hover:border-opacity-100 bg-opacity-0 hover:bg-opacity-100 shadow-none hover:shadow-sm' }}">Blog</a>
            <a href="/courses/" class="py-1.5 px-3 md:px-4 bg-white rounded-full border-b {{ request()->is('courses') ? 'border-indigo-300 shadow-sm shadow-indigo-200' : 'border-slate-300 border-opacity-0 hover:border-opacity-100 bg-opacity-0 hover:bg-opacity-100 shadow-none hover:shadow-sm' }}">Courses</a>
            <a href="/projects/" class="py-1.5 px-3 md:px-4 bg-white rounded-full border-b {{ request()->is('projects') ? 'border-indigo-300 shadow-sm shadow-indigo-200' : 'border-slate-300 border-opacity-0 hover:border-opacity-100 bg-opacity-0 hover:bg-opacity-100 shadow-none hover:shadow-sm' }}">Projects</a>
            <a href="mailto:me@aschmelyun.com" class="shadow-none hover:shadow-sm py-1.5 px-3 md:px-4 bg-white bg-opacity-0 hover:bg-opacity-100 rounded-full border-b border-opacity-0 hover:border-opacity-100 border-slate-300">Contact</a>

            <!-- Sponsor Me Callout -->
            <div class="hidden md:block absolute -top-12 right-0 text-sm font-bold text-indigo-600" style="font-family: 'Comic Sans MS', cursive;">
                <div class="flex items-end space-x-1">
                    <span>Sponsor me!</span>
                    <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="transform rotate-12">
                        <path d="M12 2 Q 8 6, 10 12 Q 11 16, 8 20 Q 6 24, 10 28 L 12 30" stroke="#4f46e5" stroke-width="2" fill="none" stroke-linecap="round"/>
                        <path d="M12 30 L 8 26 L 12 28 L 10 24" fill="#4f46e5"/>
                    </svg>
                </div>
            </div>
        </nav>
    </div>
</header>