<div>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('websites.listing') }}" wire:navigate>Websites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('websites.detail', $website) }}" wire:navigate>{{ $website->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Pages</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-header :name="$website->name" :url="$website->url" />

    <div class="flex flex-col rounded-xl border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between mb-4 gap-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-zinc-100">
                    Pages
                </h2>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 dark:text-zinc-500">
                        {{ $totalCount }} pages
                    </span>

                    {{-- Per-page selector --}}
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">
                            Show
                        </span>

                        <select wire:model.live="perPage"
                            class=" rounded-md border px-2 py-1 bg-white text-gray-900 border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}">
                                    {{ is_numeric($option) ? $option : 'All' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <div wire:loading wire:target="perPage" class="text-xs text-gray-400">
                    Updating…
                </div>
            </div>

            {{-- Search --}}
            <div class="relative">
                {{-- Heroicon: magnifying-glass --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                    class=" absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 dark:text-zinc-500"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                </svg>

                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search pages by path"
                    class=" w-full bg-white text-gray-900 placeholder-gray-400 border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder-zinc-500 dark:border-zinc-700" />

            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-auto">
            <div class="p-4">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-zinc-400">
                        <tr class="border-b border-gray-200 dark:border-zinc-800">
                            <th class="pb-2 pr-4">Path</th>
                            <th class="pb-2 pr-4">Slug</th>
                            <th class="pb-2 pr-4">Date Created</th>
                            <th class="pb-2 text-center">View Content</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pages as $page)
                            <tr wire:key="page-{{ $page->id }}" wire:click="selectPage('{{ $page->id }}')"
                                class=" border-b cursor-pointer transition-colors border-gray-200 hover:bg-gray-50 dark:border-zinc-800/50 dark:hover:bg-zinc-800/30 {{ $selectedPageId === $page->id ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-700 dark:text-zinc-300' }}">
                                <td class="py-3 pr-4">
                                    {{ $page->path }}
                                </td>

                                <td class="py-3 pr-4 text-gray-400 dark:text-zinc-400">
                                    {{ $page->slug }}
                                </td>

                                <td class="py-3 pr-4 text-xs text-gray-500 dark:text-zinc-500">
                                    {{ $page->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td class="py-3 text-center">
                                    <button type="button" wire:click.stop="openViewer('{{ $page->id }}')"
                                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 dark:text-zinc-400 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                                        title="View content">
                                        <flux:icon.eye class="size-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500 dark:text-zinc-500">
                                    No pages found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Drawer --}}
                <div x-data="{ open: @entangle('viewerOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex">
                    {{-- Backdrop --}}
                    <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-black/50">
                    </div>

                    {{-- Panel --}}
                    <div x-show="open" x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        @keydown.escape.window="open = false"
                        class="relative ml-auto h-full w-full max-w-2xl bg-white dark:bg-zinc-900 border-l border-gray-200 dark:border-zinc-800 flex flex-col">
                        {{-- Header --}}
                        <div
                            class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-zinc-800">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-zinc-100">
                                Page Content
                            </h2>

                            <button type="button" wire:click="closeViewer"
                                class="text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 cursor-pointer">
                                ✕
                            </button>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 overflow-auto p-4 space-y-6 text-sm">
                            @if ($viewerPage)
                                {{-- Metadata --}}
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-500">URL:</span>
                                        <p class="mt-1 break-all text-gray-700 dark:text-zinc-300">
                                            {{ $viewerPage->url }}
                                        </p>
                                    </div>

                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-500">Path:</span>
                                        <p class="mt-1">{{ $viewerPage->path }}</p>
                                    </div>

                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-500">Slug:</span>
                                        <p class="mt-1">{{ $viewerPage->slug }}</p>
                                    </div>

                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-500">Date Created:</span>
                                        <p class="mt-1">
                                            {{ $viewerPage->created_at->format('Y-m-d H:i') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- JSON content --}}
                                <div class="pt-4 border-t border-gray-200 dark:border-zinc-800">
                                    <div class="mb-3 text-gray-500 dark:text-zinc-400">
                                        Extracted Content:
                                    </div>

                                    <div
                                        class="rounded-lg border border-gray-200 bg-gray-50 dark:border-zinc-800 dark:bg-zinc-950 p-4 overflow-auto text-xs">
                                        <pre class="text-gray-800 dark:text-zinc-200 whitespace-pre-wrap">{{ json_encode($viewerPage->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-full text-gray-500 dark:text-zinc-500">
                                    No page selected
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($perPage !== 'all')
                    <div class="mt-4">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


</div>
