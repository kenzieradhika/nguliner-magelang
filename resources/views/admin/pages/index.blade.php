@extends('admin.layouts.app')

@section('title', 'Halaman CMS')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Halaman CMS</h1>
            <p class="mt-1 text-sm text-ink-500">Halaman konten dinamis dengan editor blok section</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="ng-btn-primary"><x-icon name="plus" class="h-4 w-4" /> Buat Halaman</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="px-5 py-3.5">Halaman</th>
                    <th class="px-5 py-3.5">Slug</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach($pages as $page)
                    <tr class="transition hover:bg-cream-50">
                        <td class="px-5 py-4 font-semibold">{{ $page->title }}</td>
                        <td class="px-5 py-4 text-ink-400">/halaman/{{ $page->slug }}</td>
                        <td class="px-5 py-4">
                            @if($page->is_published)
                                <span class="ng-badge ng-badge-green">Tayang</span>
                            @else
                                <span class="ng-badge ng-badge-neutral">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="ng-btn-outline !px-3 !py-1.5 !text-xs"><x-icon name="eye" class="h-3 w-3" /> Lihat</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="ng-btn-outline !px-3 !py-1.5 !text-xs"><x-icon name="edit" class="h-3 w-3" /> Edit</a>
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
