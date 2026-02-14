@section('title', 'Estimate #EXT-' . str_pad($estimate->id, 5, '0', STR_PAD_LEFT))

<div>
<div class="py-12 px-4 sm:px-6 lg:px-8 print:py-0 print:px-0">
    <div class="max-w-7xl mx-auto print:max-w-none">
        
        <!-- Web Action Bar -->
        <div class="mb-6 flex justify-between items-center print:hidden">
            <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 flex items-center" wire:navigate>
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
            
            <div class="flex items-center space-x-3" x-data="{ 
                copying: false,
                copyHtml() {
                    this.copying = true;
                    const html = `{{ str_replace(["\r", "\n", "'"], ["", "", "\'"], $this->getEmailHtml()) }}`;
                    const blob = new Blob([html], { type: 'text/html' });
                    const blobText = new Blob([html.replace(/<[^>]*>/g, '')], { type: 'text/plain' });
                    const data = [new ClipboardItem({ 'text/html': blob, 'text/plain': blobText })];
                    
                    navigator.clipboard.write(data).then(() => {
                        setTimeout(() => this.copying = false, 2000);
                    });
                }
            }">
                <button @click="copyHtml" :class="copying ? 'bg-green-600' : 'bg-white dark:bg-gray-700'" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                    <svg x-show="!copying" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-1 4h.01M9 16h5m0 0l-1.5-1.5M14 16l-1.5 1.5"></path></svg>
                    <svg x-show="copying" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="copying ? 'Copied!' : 'Copy for Email'"></span>
                </button>

                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print / Export PDF
                </button>
                <button wire:click="delete" 
                        wire:confirm="Are you sure you want to delete this estimate?"
                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-sm font-medium">
                    Delete
                </button>
            </div>
        </div>

        <!-- Document Envelope -->
        <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg print:shadow-none print:bg-white print:text-black print:overflow-visible">
            
            <!-- Hidden Print Header -->
            <div class="hidden print:flex justify-between items-center mb-6 border-b-2 border-gray-100 pb-4">
                <div class="flex items-center">
                    <x-application-logo class="h-8 w-auto mr-2" />
                    <span class="text-lg font-black tracking-tighter text-gray-900">{{ config('app.name') }}</span>
                </div>
                <div class="text-right text-[10px] text-gray-400 uppercase tracking-widest font-bold">
                    Document #EXT-{{ str_pad($estimate->id, 5, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="p-8 sm:p-12 text-gray-900 dark:text-gray-100 print:p-0 print:text-black">
                
                <!-- Title & Meta -->
                <div class="border-b border-gray-200 dark:border-gray-700 print:border-gray-300 pb-6 mb-6">
                    <div class="flex flex-row justify-between items-end gap-6">
                        <div>
                            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 print:text-black mb-1">Project Estimate</h2>
                            <p class="text-base text-gray-500 dark:text-gray-400 print:text-gray-600">
                                Prepared by <span class="font-bold text-indigo-600 dark:text-indigo-400 print:text-black">{{ $estimate->user->name }}</span>
                            </p>
                        </div>
                        <div class="text-right pb-1">
                             <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 print:text-gray-500 uppercase tracking-widest">Estimation Date</p>
                             <p class="text-lg font-bold text-gray-800 dark:text-gray-200 print:text-black mt-0.5">{{ $estimate->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-indigo-50/30 dark:bg-indigo-900/10 print:bg-white p-4 rounded-xl text-sm text-gray-600 dark:text-gray-400 border-l-4 border-indigo-400 print:border-gray-200">
                        <span class="block text-[10px] font-black uppercase text-indigo-500 dark:text-indigo-400 print:text-gray-400 mb-1">Project Brief:</span>
                        "{{ $estimate->brief }}"
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 print:block">
                    
                    <!-- Breakdown Section -->
                    <div class="lg:col-span-2 print:block">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-500 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Task Breakdown
                        </h3>
                        <div class="space-y-3 print:block">
                            @foreach ($estimate->tasks ?? [] as $task)
                                <div class="p-4 bg-gray-50/50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-xl print:bg-white print:border-gray-200 break-inside-avoid mb-3">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="flex-1">
                                            <p class="text-base font-bold text-gray-900 dark:text-gray-100 print:text-black">{{ $task['name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed print:text-gray-700">{{ $task['description'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 whitespace-nowrap print:bg-gray-50 print:text-black print:border print:border-gray-100">
                                                {{ $task['hours'] }} hrs
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="space-y-6 print:mt-8 print:break-inside-avoid">
                        <div class="bg-gray-900 dark:bg-black text-white p-6 rounded-2xl shadow-xl h-fit print:bg-white print:text-black print:border-2 print:border-black print:shadow-none print:p-5">
                            <h3 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2 print:border-black">Summary</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400 print:text-gray-500">Resource Time</span>
                                    <span class="font-bold">{{ number_format($estimate->total_hours, 1) }} hrs</span>
                                </div>

                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400 print:text-gray-500">Billable Rate</span>
                                    <span class="font-bold">{{ number_format($estimate->hourly_rate > 0 ? $estimate->hourly_rate : (auth()->user()->hourly_rate ?? 45), 2) }} <span class="text-[10px] ml-0.5">{{ $estimate->currency }}</span></span>
                                </div>

                                <div class="pt-4 border-t border-gray-700 print:border-black text-center sm:text-left">
                                    <p class="text-[10px] text-indigo-400 uppercase font-black tracking-widest mb-1 print:text-gray-500">Project Total</p>
                                    <p class="text-3xl font-extrabold text-white print:text-black tabular-nums">
                                        {{ $estimate->currency }} {{ number_format($estimate->price_low) }}<span class="mx-1 text-gray-600 print:text-gray-400 text-xl font-normal">–</span>{{ number_format($estimate->price_high) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if(!empty($estimate->notes))
                            <div class="p-4 bg-amber-50/50 dark:bg-amber-900/10 border-l-2 border-amber-400 rounded-r-xl print:hidden">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-500 mb-1">Technical Considerations (Internal)</h4>
                                <p class="text-xs text-amber-900/70 dark:text-amber-200/70 leading-relaxed print:text-gray-700">
                                    {{ $estimate->notes }}
                                </p>
                            </div>
                        @endif

                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 print:hidden">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                <p class="text-[10px] text-gray-500">AI Analysis: <span class="font-bold">{{ ucfirst($estimate->provider) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer / Disclaimers -->
                <div class="mt-12 pt-6 border-t border-gray-100 dark:border-gray-800 text-center text-[10px] text-gray-400 print:text-gray-400">
                    <p>Generated by {{ config('app.name') }} — Professional Estimation Platform</p>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 20mm 15mm 25mm 15mm;
    }

    nav, .print\:hidden {
        display: none !important;
    }

    body {
        margin: 0;
        background: white !important;
        color: black !important;
        font-family: 'Inter', system-ui, sans-serif;
    }

    .py-12 { padding-top: 0 !important; padding-bottom: 0 !important; }

    .break-inside-avoid {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
}
</style>
</div>
