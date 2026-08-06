@extends('admin.layouts.app')

@section('title', 'Halaman CMS')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Konten statis</p>
            <h2 class="adm-page-title">Halaman CMS</h2>
            <p class="adm-page-subtitle">Halaman konten dinamis dengan editor blok section</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="adm-btn"><x-icon name="plus" class="h-4 w-4" /> Buat Halaman</a>
    </div>

    <div class="adm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="adm-table min-w-[700px]">
                <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                    <tr>
                        <th class="adm-th">Halaman</th>
                        <th class="adm-th">Slug</th>
                        <th class="adm-th">Status</th>
                        <th class="adm-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/[0.05]">
                    @foreach($pages as $page)
                        <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                            <td class="adm-td font-semibold text-ink-900">{{ $page->title }}</td>
                            <td class="adm-td text-xs text-ink-400">/halaman/{{ $page->slug }}</td>
                            <td class="adm-td">
                                @if($page->is_published)
                                    <span class="adm-badge adm-badge-soft-green">Tayang</span>
                                @else
                                    <span class="adm-badge adm-badge-soft-neutral">Draft</span>
                                @endif
                            </td>
                            <td class="adm-td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="adm-btn-ghost"><x-icon name="eye" class="h-3.5 w-3.5" /> Lihat</a>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="adm-btn-ghost"><x-icon name="edit" class="h-3.5 w-3.5" /> Edit</a>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Hapus halaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $pages->links() }}</div>
@endsection
