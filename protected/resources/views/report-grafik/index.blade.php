@extends('layouts.app')

@section('htmlheader_title')
    Report
@stop

@section('contentheader_title')
    Report
@stop

@section('main-content')
    <style>
        #summaryTable_wrapper > .row:first-child {
            padding: 10px 10px 0;
        }
    </style>
    <div class="row">
        <div class="col-xs-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('success') }}.
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('error') }}
                </div>
            @endif
            <div class="box">
                <div class="box-header">
                    <input type="text" name="daterange" class="form-control" value="{{ request('start_date') ? request('start_date') . ' - ' . request('end_date') : '' }}"
                        style="width: 17rem; margin: 1rem 0" />
                    <div class="box-tools" x-data="{
                        user: '{{ request('user') }}',
                        search() {
                            window.location.href = `{{ route('report-grafik.index') }}?user=${this.user}`
                        }
                    }">
                        <select name="user" id="user" class="form-control" x-model="user" @change="search()">
                            <option value="">All Users</option>
                            <option value="{{ $users->filter(function ($q) {
                                $keywords = ['Shopee', 'Tokopedia', 'Lazada', 'Tiktok'];
                                foreach ($keywords as $keyword) {
                                    if (str_contains($q->name, $keyword)) {
                                        return true;
                                    }
                                }
                                return false;
                            })->pluck('id')->implode(',') }}">Online</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Sales Graphs -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Product Sales Analysis</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="quantityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart for Percentage -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Product Sales Percentage (Top 10)</h3>
                </div>
                <div class="box-body" style="width: 60rem; margin: 0 auto">
                    <canvas id="pieChart" height="120"></canvas>
                </div>
            </div>

            <!-- Product Summary Table -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Product Summary</h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table id="summaryTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Product Name</th>
                                @foreach ($dates as $date)
                                    <th class="text-center">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                                @endforeach
                                <th class="text-center">Median</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaryData as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    @foreach ($dates as $date)
                                        <td class="text-center">{{ number_format($row['daily_quantities'][$date] ?? 0) }}</td>
                                    @endforeach
                                    <td class="text-center">
                                        {{
                                            (function() use ($dates, $row) {
                                                $values = collect($dates)->map(fn($d) => $row['daily_quantities'][$d] ?? 0)->toArray();
                                                sort($values);
                                                $count = count($values);
                                                if ($count === 0) return 0;
                                                $mid = (int) ($count / 2);
                                                if ($count % 2) {
                                                    return $values[$mid];
                                                } else {
                                                    return ($values[$mid - 1] + $values[$mid]) / 2;
                                                }
                                            })()
                                        }}
                                    </td>
                                    <td class="text-center">{{ number_format($row['total_quantity']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#summaryTable').DataTable({
        order: [[{{ 2 + count($dates) }}, 'desc']],
        pageLength: 25,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Wait for jQuery to be available
    if (typeof jQuery != 'undefined') {
        initializeCharts();
    } else {
        // If jQuery is not loaded yet, wait for it
        window.addEventListener('load', function() {
            initializeCharts();
        });
    }
});

function initializeCharts() {
    const dates = @json($dates);
    const products = @json($graphData);

    // Prepare data for sales chart
    const salesDatasets = products.map(product => ({
        label: product.name,
        data: product.sales,
        borderColor: getRandomColor(),
        fill: false,
        tension: 0.1
    }));

    // Prepare data for quantity chart
    const quantityDatasets = products.map(product => ({
        label: product.name,
        data: product.quantities,
        borderColor: getRandomColor(),
        fill: false,
        tension: 0.1
    }));

    // Create sales chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: salesDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Sales by Product'
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Create quantity chart
    new Chart(document.getElementById('quantityChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: quantityDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Quantity Sold by Product'
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Pie Chart for Product Percentage
    const pieLabels = @json(collect($graphData)->pluck('name'));
    const pieData = @json(collect($graphData)->pluck('total_quantity'));
    const pieColors = [
        '#3366cc', '#dc3912', '#ff9900', '#109618', '#990099', '#0099c6', '#dd4477', '#66aa00', '#b82e2e', '#316395',
        '#994499', '#22aa99', '#aaaa11', '#6633cc', '#e67300', '#8b0707', '#651067', '#329262', '#5574a6', '#3b3eac'
    ];

    const pieChart = new Chart(document.getElementById('pieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: pieColors.slice(0, pieLabels.length),
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.parsed;
                            const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${value} (${percent}%)`;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: function(value, context) {
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                        return percent + '%';
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
</script>
@stop
