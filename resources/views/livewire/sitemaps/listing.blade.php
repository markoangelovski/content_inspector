<div>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('websites.listing') }}" wire:navigate>Websites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('websites.detail', $website) }}" wire:navigate>{{ $website->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Sitemaps</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-page-header :name="$website->name" :url="$website->url" />

    <div class="space-y-4">
        <table class="w-full rounded-xl border border-gray-200">
            <thead class="">
                <tr>
                    <th class="px-4 py-2 text-left">URL</th>
                    <th class="px-4 py-2 text-left">Created</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($sitemaps as $sitemap)
                    <tr class="border-t">
                        <td class="px-4 py-2 text-sm">
                            <a href="{{ $sitemap->url }}" target="_blank" rel="noopener noreferrer"
                                class="hover:underline truncate block">
                                {{ $sitemap->url }}
                            </a>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            {{ $sitemap->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center">
                            No sitemaps found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="">
            {{ $sitemaps->links() }}
        </div>
    </div>
</div>
