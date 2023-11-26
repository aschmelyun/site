@php
$dotCategoryMap = [
    'discussions' => 'bg-cyan-500',
    'vue' => 'bg-emerald-500',
    'laravel' => 'bg-red-500',
    'javascript' => 'bg-amber-500',
    'docker' => 'bg-blue-500',
    'experiments' => 'bg-fuchsia-500',
    'php' => 'bg-indigo-500',
    'inertia' => 'bg-purple-500',
    'arduino' => 'bg-teal-500',
    'tools' => 'bg-lime-500',
    'productivity' => 'bg-pink-500',
    'wordpress' => 'bg-sky-500',
    'aws' => 'bg-orange-500',
];
@endphp
@foreach(explode(',', $slot) as $category)
    @if (isset($plain))
        <span class="text-xs lowercase bg-white border border-slate-200 border-b-slate-300 text-slate-500 rounded-full py-0.5 px-2 inline-block mr-0.5 hover:cursor-default">
            <span class="inline-block h-2 w-2 rounded-full mr-0.5 {{ $dotCategoryMap[trim($category)] }}"></span>
            {{ trim($category) }}
        </span>
    @else
        <a href="{{ route('posts.index', ['category' => trim($category)]) }}" class="text-xs lowercase bg-white border border-slate-200 border-b-slate-300 text-slate-500 rounded-full py-0.5 px-2 inline-block mr-0.5 hover:border-indigo-200 hover:border-b-indigo-300 hover:shadow-sm hover:shadow-indigo-200">
            <span class="inline-block h-2 w-2 rounded-full mr-0.5 {{ $dotCategoryMap[trim($category)] }}"></span>
            {{ trim($category) }}
        </a>
    @endif
@endforeach