<div>
    <style>
        /* Dashboard Styles */
        * {
            transition: all 0.25s ease-in-out;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0A84FF;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .stat-change.positive {
            color: #10b981;
            background: #d1fae5;
        }

        .stat-change.negative {
            color: #ef4444;
            background: #fee2e2;
        }

        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #f3f4f6;
        }

        .order-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .order-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #f59e0b;
        }

        .status-processing {
            background: #dbeafe;
            color: #3b82f6;
        }

        .status-completed {
            background: #d1fae5;
            color: #10b981;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #ef4444;
        }

        .mode-toggle-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid;
        }

        .mode-toggle-btn.admin-mode {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #764ba2;
            color: white;
        }

        .mode-toggle-btn.seller-mode {
            background: white;
            border-color: #0A84FF;
            color: #0A84FF;
        }

        .mode-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .mode-toggle-btn:active {
            transform: scale(0.98);
        }

        .mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .mode-badge.admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .mode-badge.seller {
            background: #E8F4F8;
            color: #0A84FF;
        }

        .chart-canvas {
            max-height: 300px;
        }

        .btn-system:active {
            transform: scale(0.98);
        }
    </style>

    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                            {{ $adminMode ? '平台' : '銷售' }}儀表板
                        </h1>
                        <span class="mode-badge {{ $adminMode ? 'admin' : 'seller' }}">
                            <i class="fas fa-{{ $adminMode ? 'globe' : 'user' }}"></i>
                            {{ $adminMode ? '管理員模式' : '個人模式' }}
                        </span>
                    </div>
                    <p class="text-lg text-gray-600">
                        {{ $adminMode ? '追蹤整個平台的銷售表現和業務洞察' : '追蹤您的銷售表現和業務洞察' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <!-- 管理員切換按鈕 -->
                    @if(auth()->user()->is_admin)
                        <button
                            wire:click="toggleAdminMode"
                            class="mode-toggle-btn {{ $adminMode ? 'admin-mode' : 'seller-mode' }}">
                            <i class="fas fa-{{ $adminMode ? 'user' : 'globe' }}"></i>
                            切換至{{ $adminMode ? '個人' : '平台' }}模式
                        </button>
                    @endif

                    <select wire:model.live="period" class="px-4 py-2 bg-white border border-gray-300 rounded-lg">
                        <option value="7days">最近7天</option>
                        <option value="30days">最近30天</option>
                        <option value="90days">最近90天</option>
                        <option value="1year">最近一年</option>
                    </select>

                </div>
            </div>
        </div>
    </section>

    <!-- Stats Overview -->
    <section class="py-12 bg-white">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                <!-- 總銷售額 -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <i class="text-xl text-blue-500 fas fa-dollar-sign"></i>
                        </div>
                        @if(isset($stats['comparison']))
                            <div class="stat-change {{ $stats['comparison']['sales_change'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-arrow-{{ $stats['comparison']['sales_change'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($stats['comparison']['sales_change']) }}%
                            </div>
                        @endif
                    </div>
                    <div class="stat-value">$ {{ number_format($stats['total_sales'] ?? 0) }}</div>
                    <div class="font-medium text-gray-600">總銷售額</div>
                    @if(isset($stats['comparison']['sales_diff']))
                        <div class="mt-1 text-sm text-gray-400">
                            較上期 {{ $stats['comparison']['sales_diff'] >= 0 ? '+' : '' }}$ {{ number_format($stats['comparison']['sales_diff']) }}
                        </div>
                    @endif
                </div>

                <!-- 總訂單數 -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                            <i class="text-xl text-green-600 fas fa-shopping-cart"></i>
                        </div>
                        @if(isset($stats['comparison']))
                            <div class="stat-change {{ $stats['comparison']['orders_change'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-arrow-{{ $stats['comparison']['orders_change'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($stats['comparison']['orders_change']) }}%
                            </div>
                        @endif
                    </div>
                    <div class="stat-value">{{ $stats['total_orders'] ?? 0 }}</div>
                    <div class="font-medium text-gray-600">總訂單數</div>
                    @if(isset($stats['comparison']['orders_diff']))
                        <div class="mt-1 text-sm text-gray-400">
                            較上期 {{ $stats['comparison']['orders_diff'] >= 0 ? '+' : '' }}{{ $stats['comparison']['orders_diff'] }}
                        </div>
                    @endif
                </div>

                <!-- 在售商品 / 總用戶數 -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                            <i class="fas fa-{{ $adminMode ? 'users' : 'box' }} text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        {{ $adminMode ? ($stats['total_users'] ?? 0) : ($stats['active_products'] ?? 0) }}
                    </div>
                    <div class="font-medium text-gray-600">
                        {{ $adminMode ? '總用戶數' : '在售商品' }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        @if($adminMode)
                            平台註冊用戶
                        @else
                            <a href="{{ route('seller.products.index') }}" class="text-blue-500 hover:underline">
                                前往管理 →
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 平均評價 / 總賣家數 -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                            <i class="fas fa-{{ $adminMode ? 'store' : 'star' }} text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        {{ $adminMode ? ($stats['total_sellers'] ?? 0) : ($stats['average_rating']['average'] ?? 0) }}
                    </div>
                    <div class="font-medium text-gray-600">
                        {{ $adminMode ? '總賣家數' : '平均評價' }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        {{ $adminMode ? '活躍賣家數量' : '共 ' . ($stats['average_rating']['total'] ?? 0) . ' 個評價' }}
                    </div>
                </div>

                <!-- 管理員模式額外顯示 -->
                @if($adminMode)
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg">
                                <i class="text-xl text-indigo-600 fas fa-box"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['active_products'] ?? 0 }}</div>
                        <div class="font-medium text-gray-600">在售商品</div>
                        <div class="mt-1 text-sm text-gray-400">平台所有商品</div>
                    </div>

                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                                <i class="text-xl text-orange-600 fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['pending_orders'] ?? 0 }}</div>
                        <div class="font-medium text-gray-600">待處理訂單</div>
                        <div class="mt-1 text-sm text-gray-400">需要關注</div>
                    </div>

                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-pink-100 rounded-lg">
                                <i class="text-xl text-pink-600 fas fa-star"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['average_rating']['average'] ?? 0 }}</div>
                        <div class="font-medium text-gray-600">平台平均評價</div>
                        <div class="mt-1 text-sm text-gray-400">共 {{ $stats['average_rating']['total'] ?? 0 }} 個評價</div>
                    </div>
                @endif
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
                <!-- 銷售趨勢圖表 -->
                <div class="chart-container">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">
                        {{ $adminMode ? '平台' : '' }}銷售趨勢
                    </h3>
                    @if(count($salesTrend) > 0)
                        <canvas id="salesTrendChart" class="chart-canvas"></canvas>
                    @else
                        <div style="height: 300px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-weight: 500;">
                            <div class="text-center">
                                <i class="mb-2 text-3xl fas fa-chart-line"></i>
                                <div>此期間暫無銷售數據</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 熱門商品 -->
                <div class="chart-container">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">
                        {{ $adminMode ? '平台' : '' }}熱門商品
                    </h3>
                    @if(count($topProducts) > 0)
                        <div class="space-y-4">
                            @foreach($topProducts as $product)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-10 h-10 overflow-hidden bg-blue-100 rounded-lg">
                                            @if(isset($product['product_image']) && $product['product_image'])
                                                <img src="{{ $product['product_image'] }}" alt="{{ $product['product_name'] }}" class="object-cover w-full h-full">
                                            @else
                                                <i class="text-blue-600 fas fa-box"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ Str::limit($product['product_name'], 20) }}</div>
                                            <div class="text-sm text-gray-500">
                                                銷售額: $ {{ number_format($product['total_sales']) }}
                                                @if($adminMode && isset($product['seller']['name']))
                                                    <span class="ml-2 text-xs text-gray-400">by {{ $product['seller']['name'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">{{ $product['total_quantity'] }}</div>
                                        <div class="text-sm text-gray-500">件</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="height: 300px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-weight: 500;">
                            <div class="text-center">
                                <i class="mb-2 text-3xl fas fa-box-open"></i>
                                <div>此期間暫無銷售商品</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 管理員模式: 頂級賣家 -->
                @if($adminMode && count($topSellers) > 0 && false)
                    <div class="chart-container lg:col-span-2">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            <i class="mr-2 text-yellow-500 fas fa-trophy"></i>賣家銷售排行
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                            @foreach($topSellers as $index => $seller)
                                <div class="p-4 text-center border border-gray-200 rounded-lg bg-gradient-to-br from-gray-50 to-gray-100">
                                    <div class="text-3xl font-bold {{ $index === 0 ? 'text-yellow-500' : ($index === 1 ? 'text-gray-400' : ($index === 2 ? 'text-orange-600' : 'text-gray-600')) }} mb-2">
                                        #{{ $index + 1 }}
                                    </div>
                                    <div class="mb-1 font-semibold text-gray-900">{{ $seller['seller_name'] }}</div>
                                    <div class="mb-2 text-sm text-gray-600">
                                        <div>$ {{ number_format($seller['total_sales']) }}</div>
                                        <div class="text-xs">{{ $seller['total_orders'] }} 筆訂單</div>
                                    </div>
                                    <div class="flex items-center justify-center text-xs text-yellow-600">
                                        <i class="mr-1 fas fa-star"></i>
                                        {{ $seller['avg_rating'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Recent Orders & Activity -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Recent Orders -->
                <div class="chart-container">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">最新訂單</h3>
                        <a href="{{ route('seller.orders.index') }}" class="text-sm text-blue-500 hover:underline">
                            查看全部
                        </a>
                    </div>

                    @if(count($recentOrders) > 0)
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                                <div class="order-item">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="font-medium text-gray-900">#{{ $order['order_number'] }}</div>
                                        <span class="status-badge status-{{ $this->getStatusClass($order['status']) }}">
                                            {{ $this->getStatusText($order['status']) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between mb-2 text-sm text-gray-600">
                                        <span>{{ Str::limit($order['product_name'], 25) }}</span>
                                        <span>$ {{ number_format($order['price']) }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        買家：{{ $order['buyer_name'] }}
                                        @if($adminMode && isset($order['seller_name']))
                                            <span class="ml-2">• 賣家：{{ $order['seller_name'] }}</span>
                                        @endif
                                        <span class="ml-2">• {{ $order['created_at']->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400">
                            <i class="mb-2 text-4xl fas fa-inbox"></i>
                            <p>暫無訂單</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Activity -->
                <div class="chart-container">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">最新活動</h3>
                        <button
                            wire:click="clearNotifications"
                            class="text-sm text-blue-500 hover:underline">
                            清除通知
                        </button>
                    </div>

                    @if(count($recentActivities) > 0)
                        <div class="space-y-4">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-start py-3 space-x-3">
                                    <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400">
                            <i class="mb-2 text-4xl fas fa-bell-slash"></i>
                            <p>暫無最新活動</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Indicator -->
    <div wire:loading.flex style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
        <div style="background:white;padding:2rem;border-radius:12px;text-align:center;">
            <div class="w-12 h-12 mx-auto border-b-2 border-blue-500 rounded-full animate-spin"></div>
            <p style="margin-top:1rem;color:#666;">載入中...</p>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let salesChart = null;

            function initSalesChart() {
                const canvas = document.getElementById('salesTrendChart');
                if (!canvas) return;

                const salesData = @json($salesTrend);

                if (salesData.length === 0) return;

                // Destroy existing chart
                if (salesChart) {
                    salesChart.destroy();
                }

                const labels = salesData.map(item => item.date);
                const totals = salesData.map(item => parseFloat(item.total));
                const orders = salesData.map(item => parseInt(item.orders));

                const ctx = canvas.getContext('2d');

                salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: '銷售額 ($)',
                                data: totals,
                                borderColor: '#0A84FF',
                                backgroundColor: 'rgba(10, 132, 255, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y',
                            },
                            {
                                label: '訂單數',
                                data: orders,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 0) {
                                            label += '$ ' + context.parsed.y.toLocaleString();
                                        } else {
                                            label += context.parsed.y + ' 筆';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: {
                                    color: '#f3f4f6'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$ ' + value.toLocaleString();
                                    },
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + ' 筆';
                                    },
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Initialize on load
            initSalesChart();

            // Re-initialize when Livewire updates
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    initSalesChart();
                }, 100);
            });

            // Notification handler
            Livewire.on('notify', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                const toast = document.createElement('div');

                let bgColor = 'bg-blue-500';
                let icon = 'info-circle';

                if (data.type === 'success') {
                    bgColor = 'bg-green-500';
                    icon = 'check-circle';
                }
                if (data.type === 'error') {
                    bgColor = 'bg-red-500';
                    icon = 'exclamation-circle';
                }
                if (data.type === 'warning') {
                    bgColor = 'bg-yellow-500';
                    icon = 'exclamation-triangle';
                }

                toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
                toast.innerHTML = `<i class="fas fa-${icon}"></i>${data.message}`;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            });
        });
    </script>
    @endpush
</div>
