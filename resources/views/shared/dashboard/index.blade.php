@extends('layouts.app')
@section('title', 'Dashboard | ' . auth()->user()->role_label . ' Polsek Bojongloa Kidul')

@section('content')
<div class="space-y-4 lg:space-y-5">

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        @if(auth()->user()->hasRole(['kasium','pimpinan']))
        @php
        $totalPagu = $programs->sum('pagu_dipa');
        $totalRealisasi = $programs->sum('total_realisasi');
        $totalSisa = $programs->sum('sisa_anggaran');
        @endphp
        <x-stat-card
            label="Total Anggaran (Rp)"
            value="Rp {{ number_format($totalPagu, 0, ',', '.') }}"
            tag="Tahun {{ now()->year }}" />
        <x-stat-card
            label="Realisasi Terpakai (Rp)"
            value="Rp {{ number_format($totalRealisasi, 0, ',', '.') }}"
            tag="Realisasi Terpakai" />
        <x-stat-card
            label="Sisa Anggaran (Rp)"
            value="Rp {{ number_format($totalSisa, 0, ',', '.') }}"
            tag="Per Hari Ini" />
        @endif

        @if(auth()->user()->hasRole('kasium'))
        <x-stat-card
            label="Pengajuan Menunggu Verifikasi"
            value="{{ $stats['menunggu_verifikasi'] ?? 0 }}"
            tag="Perlu Diproses"
            :highlight="($stats['menunggu_verifikasi'] ?? 0) > 0" />

        @elseif(auth()->user()->hasRole('pimpinan'))
        <x-stat-card
            label="Menunggu Persetujuan"
            value="{{ $stats['menunggu_approval'] ?? 0 }}"
            tag="Perlu ACC Anda"
            :highlight="($stats['menunggu_approval'] ?? 0) > 0" />

        @elseif(auth()->user()->hasRole('staf_unit'))
        <x-stat-card
            label="Draft Saya"
            value="{{ $stats['draft'] ?? 0 }}"
            tag="Belum Dikirim" />
        <x-stat-card
            label="Menunggu Verifikasi"
            value="{{ $stats['menunggu_verifikasi'] ?? 0 }}"
            tag="Sedang Diproses" />
        <x-stat-card
            label="Disetujui / Proses"
            value="{{ $stats['disetujui'] ?? 0 }}"
            tag="Dalam Proses Cair" />
        <x-stat-card
            label="Ditolak"
            value="{{ $stats['ditolak'] ?? 0 }}"
            tag="Perlu Revisi"
            :highlight="($stats['ditolak'] ?? 0) > 0" />
        @endif
    </div>

    <!-- CHART + RECENT TABLE -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- GRAFIK REALISASI -->
        <div class="lg:col-span-2 bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4 lg:p-5">
            <h3 class="font-semibold text-sipbo-gold text-sm mb-4">
                Grafik Realisasi Bulanan — Per Program
            </h3>
            <div class="h-60 lg:h-72">
                <canvas id="realisasiChart"></canvas>
            </div>
        </div>

        <!-- PENGAJUAN TERBARU -->
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4 lg:p-5 flex flex-col">
            <h3 class="font-semibold text-sipbo-gold text-sm mb-3">Pengajuan Terbaru</h3>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-sipbo-text-muted dark:text-light-text-muted
                                   border-b border-sipbo-border dark:border-light-border">
                            <th class="text-left py-2 pr-2">ID</th>
                            <th class="text-left py-2 pr-2">Unit</th>
                            <th class="text-left py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $p)
                        <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                                   hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light
                                   cursor-pointer transition"
                            onclick="window.location='{{ route('pengajuan.show', $p->id) }}'">
                            <td class="py-2 pr-2 text-sipbo-text-muted dark:text-light-text-muted">
                                #{{ $p->id }}
                            </td>
                            <td class="py-2 pr-2 text-sipbo-text dark:text-light-text truncate max-w-[80px]">
                                {{ $p->unitKerja->kode_unit }}
                            </td>
                            <td class="py-2">
                                @php [$class, $label] = $p->statusBadge(); @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium {{ $class }}">
                                    {{ $label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-sipbo-text-muted dark:text-light-text-muted text-xs">
                                Belum ada aktivitas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PROGRAM CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($programs as $p)
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4">
            <div class="flex justify-between items-start mb-3">
                <h4 class="font-medium text-sm text-sipbo-text dark:text-light-text leading-tight">
                    {{ $p['nama_program'] }}
                </h4>
                <span class="text-[10px] bg-sipbo-panel-light dark:bg-light-panel-light
                             text-sipbo-text-muted dark:text-light-text-muted
                             px-2 py-0.5 rounded-full flex-shrink-0 ml-2">
                    {{ $p['kode_program'] }}
                </span>
            </div>
            <div class="w-full bg-sipbo-bg dark:bg-light-panel-light rounded-full h-2 mb-2">
                <div class="h-2 rounded-full transition-all
                            {{ $p['persentase_terserap'] > 90 ? 'bg-red-500' : 'bg-sipbo-gold' }}"
                    style="width: {{ min($p['persentase_terserap'], 100) }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-sipbo-text-muted dark:text-light-text-muted">
                <span>{{ $p['persentase_terserap'] }}% terserap</span>
                <span>Sisa: Rp {{ number_format($p['sisa_anggaran'], 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
$labels = $chartLabels ?? ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$data = $chartData ?? [];
@endphp
<script>
    const ctx = document.getElementById('realisasiChart');
    const isLightTheme = document.documentElement.classList.contains('dark');
    const gridColor = isLightTheme ? '#e2e6ea' : '#2d313a';
    const labelColor = isLightTheme ? '#6b7280' : '#9ca3af';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Realisasi (Rp)',
                data: @json($data),
                borderColor: '#d4af37',
                backgroundColor: 'rgba(212,175,55,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#d4af37',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: labelColor,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: labelColor,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: gridColor
                    }
                },
                y: {
                    ticks: {
                        color: labelColor,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: gridColor
                    }
                }
            }
        }
    });

    window.addEventListener('themechange', function() {
        const isLightTheme = document.documentElement.classList.contains('dark');
        const grid = isLightTheme ? '#e2e6ea' : '#2d313a';
        const label = isLightTheme ? '#6b7280' : '#9ca3af';

        realisasiChart.options.plugins.legend.labels.color = label;
        realisasiChart.options.scales.x.ticks.color = label;
        realisasiChart.options.scales.x.grid.color = grid;
        realisasiChart.options.scales.y.ticks.color = label;
        realisasiChart.options.scales.y.grid.color = grid;
        realisasiChart.update();
    });
</script>
@endpush