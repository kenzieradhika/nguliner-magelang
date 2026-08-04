@extends('admin.layouts.app')

@section('title', 'Halaman CMS')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Halaman CMS</h1>
            <p class="mt-1 text-sm text-neutral-500">Halaman konten dinamis dengan editor blok section</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="ng-btn">+ Buat Halaman</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase tracking-wider text-neutral-400">
                <tr>
                    <th class="px-5 py-3.5">Halaman</th>
                    <th class="px-5 py-3.5">Slug</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach($pages as $page)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-5 py-4 font-semibold">{{ $page->title }}</td>
                        <td class="px-5 py-4 text-neutral-400">/halaman/{{ $page->slug }}</td>
                        <td class="px-5 py-4">
                            @if($page->is_published)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-green-700">Tayang</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-neutral-500">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs transition hover:bg-neutral-100">Lihat</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs transition hover:bg-neutral-100">Edit</a>
                                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Hapus halaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 transition hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $pages->links() }}</div>
@endsection
