<div>
    <div id="items-cost" class="tab-content card dashboard" style="display: none;" wire:ignore.self>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Item Cost</h5>
            </div>
            <div class="card-body">
                <!-- First Row: Items Table & Search -->
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Search items" 
                                class="form-control" 
                            />
                        </div>
                        <div class="card">
                            <div class="card-header">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                <h5 class="card-title mb-0">Items</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Item Description</th>
                                                <th>Supplier</th>
                                                <th>Cost</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($priceLevels as $level)
                                                <tr wire:key="{{ $level['item_id'] }}">
                                                    <td>{{ $level['timestamp'] }}</td>
                                                    <td>{{ $level['item_name'] }}</td>
                                                    <td>{{ $level['supplier_name'] }}</td>
                                                    <td>
                                                        @if ($level['cost'] !== null)
                                                            {{ number_format($level['cost'], 2) }}
                                                        @else
                                                            <span class="text-muted">No Cost</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($level['cost'] !== null)
                                                            <button 
                                                                wire:click="showChart({{ $level['item_id'] }})"
                                                                class="btn btn-primary btn-sm">
                                                                View Trend
                                                            </button>
                                                        @else
                                                            <span class="text-muted">No Data</span>
                                                        @endif
                                                        <button 
                                                            wire:click="setItem({{ $level['item_id'] }}, '{{ $level['item_name'] }}')"
                                                            type="button"
                                                            class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addCostModal">
                                                            Add Cost
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{ $priceLevels->links() }}
                        </div>

                        <!-- Add Cost Modal -->
                        @if($showForm)
                        <div 
                            class="modal fade" 
                            id="addCostModal" 
                            tabindex="-1" 
                            data-bs-backdrop="static"
                            aria-labelledby="addCostModalLabel" 
                            aria-hidden="true" 
                            wire:ignore.self>
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCostModalLabel">
                                            Add Cost for {{ $selectedItemName ?? 'Item' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form wire:submit.prevent="saveCost">
                                            <input type="hidden" wire:model="selectedItemId" />
                                            <div class="mb-3">
                                                <label for="cost" class="form-label">Cost</label>
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    wire:model="newCost" 
                                                    id="cost" 
                                                    class="form-control" 
                                                    required>
                                                @error('newCost') 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="supplier" class="form-label">Supplier</label>
                                                <div class="input-group">
                                                    <select 
                                                        wire:model="supplierId" 
                                                        id="supplier" 
                                                        class="form-select" 
                                                        required>
                                                        <option value="">Select Supplier</option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">{{ $supplier->supp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#supplierModal">
                                                        Add
                                                    </button>
                                                </div>
                                                @error('supplierId') 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button 
                                            type="button" 
                                            class="btn btn-secondary" 
                                            data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button 
                                            type="button" 
                                            class="btn btn-primary" 
                                            wire:click="saveCost">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Supplier Form Modal -->
                        <div 
                            class="modal fade" 
                            id="supplierModal" 
                            tabindex="-1" 
                            aria-labelledby="supplierModalLabel" 
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="supplierModalLabel">Supplier Form</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="supplierForm" method="POST" wire:ignore>
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="supp_name" class="form-label">Name <span style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="supp_name" name="supp_name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="postal_address" class="form-label">Postal ID <span style="color: red;">*</span></label>
                                                    <input type="number" class="form-control" id="postal_address" name="postal_address" placeholder="ex. 8200" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="contact_no_1" class="form-label">Contact No. 1</label>
                                                    <input type="text" class="form-control" id="contact_no_1" name="contact_no_1">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="supp_address" class="form-label">Address <span style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" id="supp_address" name="supp_address" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="contact_no_2" class="form-label">Contact No. 2</label>
                                                    <input type="text" class="form-control" id="contact_no_2" name="contact_no_2">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="tin_number" class="form-label">TIN No.</label>
                                                    <input type="text" class="form-control" id="tin_number" name="tin_number">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="contact_person" class="form-label">Contact Person</label>
                                                    <input type="text" class="form-control" id="contact_person" name="contact_person">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="input_tax" class="form-label">Input Tax <span style="color: red;">*</span></label>
                                                    <select name="input_tax" id="input_tax" class="form-select" required>
                                                        <option value="NON-VAT">NON-VAT</option>
                                                        <option value="VATABLE">VATABLE</option>
                                                        <option value="UNDECLARED">UNDECLARED</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="supplier_code" class="form-label">Supplier Code</label>
                                                    <input type="text" class="form-control" id="supplier_code" name="supplier_code">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea name="description" id="description" class="form-control" placeholder="Description"></textarea>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-outline-success" onclick="saveSupplier()">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row: Cost Trend Chart with Filters -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Cost Trend for {{ $selectedItemName ?? 'Item' }}</h5>
                                <div class="d-flex gap-2">
                                    <select wire:model="chartYear" 
                                            class="form-select form-select-sm"
                                            style="width: 100px;">
                                        <option value="">All Years</option>
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    <select wire:model="chartMonth" 
                                            class="form-select form-select-sm"
                                            style="width: 120px;">
                                        <option value="">All Months</option>
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-body position-relative" style="height: 400px; min-height: 400px;">
                                @if($chartLoading)
                                    <div class="chart-overlay">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                @endif
                                @if(empty($chartData))
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-muted">
                                            @if($selectedItemId)
                                                Cost of {{ now()->format('F Y') }} not available
                                            @else
                                                Select an item to view cost trend
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <canvas id="itemCostChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chart-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
    </style>

    <script>
    document.addEventListener('livewire:init', () => {
        const chartManager = (() => {
            let chartInstance = null;
            let chartData = [];

            const chartConfig = {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const index = context.dataIndex;
                                    const supplier = chartData[index]?.supplier || 'N/A';
                                    return `Cost: ₱${context.parsed.y.toFixed(2)} (${supplier})`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            title: { display: true, text: 'Date', color: '#6b7280' }, 
                            grid: { color: 'rgba(229, 231, 235, 0.5)' } 
                        },
                        y: { 
                            title: { display: true, text: 'Cost', color: '#6b7280' }, 
                            beginAtZero: true, 
                            ticks: { callback: (value) => `₱${value.toFixed(2)}`, color: '#6b7280' }, 
                            grid: { color: 'rgba(229, 231, 235, 0.5)' } 
                        }
                    }
                }
            };

            function initChart() {
                const ctx = document.getElementById('itemCostChart')?.getContext('2d');
                if (!ctx) return;
                if (chartInstance) chartInstance.destroy();
                chartInstance = new Chart(ctx, {
                    ...chartConfig,
                    data: {
                        labels: chartData.map(d => d.date),
                        datasets: [{
                            label: 'Cost',
                            data: chartData.map(d => d.cost),
                            borderColor: '#3b82f6',
                            backgroundColor: createGradient(ctx),
                            fill: true,
                            tension: 0.3,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3b82f6',
                            borderWidth: 2
                        }]
                    }
                });
            }

            function createGradient(ctx) {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
                return gradient;
            }

            return {
                handleNewData: (newData) => {
                    if (!Array.isArray(newData)) return;
                    chartData = newData.flat().map(d => ({
                        date: d.date || 'Invalid Date',
                        cost: typeof d.cost === 'number' ? d.cost : parseFloat(d.cost) || 0,
                        supplier: d.supplier || 'N/A'
                    }));
                    initChart();
                },
                destroy: () => {
                    if (chartInstance) chartInstance.destroy();
                }
            };
        })();

        Livewire.on('loadAndRenderChart', (data) => {
            chartManager.handleNewData(data);
        });

        Livewire.on('openAddCostModal', ({ itemId, itemName }) => {
            console.log('Opening modal for item:', { itemId, itemName }); // Debug log
            const modalElement = document.getElementById('addCostModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
        });

        Livewire.on('closeAddCostModal', () => {
            const modalElement = document.getElementById('addCostModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.hide();
        });

        document.addEventListener('livewire:navigating', () => {
            chartManager.destroy();
        });

        window.saveSupplier = function() {
            const form = document.getElementById('supplierForm');
            const formData = new FormData(form);

            fetch("{{ route('suppliers.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const supplierModal = bootstrap.Modal.getInstance(document.getElementById('supplierModal'));
                    supplierModal.hide();
                    @this.call('render').then(() => {
                        @this.set('supplierId', data.supplierId);
                    });
                } else {
                    alert('Error saving supplier: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save supplier');
            });
        };
    });
    </script>
</div>