@props(['data'])

<div class="h-80"
     x-data="{
        data: @js($data),
        init() {
            const getThemeColor = (color) => {
                return document.documentElement.classList.contains('dark') ?
                    {
                        grid: 'rgba(255, 255, 255, 0.1)',
                        ticks: 'rgba(255, 255, 255, 0.7)',
                    }[color] : {
                        grid: 'rgba(0, 0, 0, 0.1)',
                        ticks: 'rgba(0, 0, 0, 0.7)',
                    }[color];
            }

            const chart = new Chart(this.$el.querySelector('canvas'), {
                type: 'line',
                data: {
                    labels: this.data.labels,
                    datasets: [
                        {
                            label: 'Actual Balance',
                            data: this.data.values,
                            borderColor: '#094d92',
                            backgroundColor: 'rgba(9, 77, 146, 0.1)',
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Ideal Trend',
                            data: this.data.idealValues,
                            borderColor: 'rgba(156, 163, 175, 0.5)',
                            fill: false,
                            borderDash: [5, 5],
                            tension: 0.4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                             labels: {
                                color: getThemeColor('ticks'),
                             }
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: getThemeColor('grid'),
                            },
                            ticks: {
                                color: getThemeColor('ticks'),
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: getThemeColor('ticks'),
                            }
                        }
                    }
                }
            });

            const observer = new MutationObserver(() => {
                chart.options.scales.y.grid.color = getThemeColor('grid');
                chart.options.scales.y.ticks.color = getThemeColor('ticks');
                chart.options.scales.x.ticks.color = getThemeColor('ticks');
                chart.options.plugins.legend.labels.color = getThemeColor('ticks');
                chart.update();
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        }
     }">
    <canvas></canvas>
</div>