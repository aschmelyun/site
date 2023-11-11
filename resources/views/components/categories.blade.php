@php
$classCategoryMap = [
    'discussions' => 'border-cyan-300 text-cyan-500',
    'vue' => 'border-emerald-300 text-emerald-500',
    'laravel' => 'border-red-300 text-red-500',
    'javascript' => 'border-amber-300 text-amber-500',
    'docker' => 'border-blue-300 text-blue-500',
    'experiments' => 'border-fuchsia-300 text-fuchsia-500',
    'php' => 'border-indigo-300 text-indigo-500',
];

$dotCategoryMap = [
    'discussions' => 'bg-cyan-500',
    'vue' => 'bg-emerald-500',
    'laravel' => 'bg-red-500',
    'javascript' => 'bg-amber-500',
    'docker' => 'bg-blue-500',
    'experiments' => 'bg-fuchsia-500',
    'php' => 'bg-indigo-500',
];
@endphp
@foreach(explode(',', $slot) as $category)
    <a href="{{ route('posts.index', ['category' => trim($category)]) }}" class="text-xs lowercase bg-white border border-slate-200 border-b-slate-300 text-slate-500 rounded-full py-0.5 px-2 inline-block mr-0.5 hover:border-indigo-200 hover:border-b-indigo-300 hover:shadow-sm hover:shadow-indigo-200">
        <span class="inline-block h-2 w-2 rounded-full mr-0.5 {{ $dotCategoryMap[trim($category)] }}"></span>
        {{ trim($category) }}
    </a>
@endforeach