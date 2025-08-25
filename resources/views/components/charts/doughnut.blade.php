@props(['data'])

<div class="h-80"
     x-data="{
        data: @js($data),
        init() {
            // Function to set the legend color based on the current theme
            const getLegendColor = () => {
                return document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#1F2937';
            }

            // Create the chart instance
            const chart = new Chart(this.$el.querySelector('canvas'), {
                type: 'doughnut',
                data: {
                    labels: this.data.labels,
                    datasets: [{
                        data: this.data.values,
                        backgroundColor: ['#30bced', '#094d92', '#d5ecd4', '#ed474a', '#0f1a20'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            labels: {
                                color: getLegendColor() // Set initial color
                            }
                        } 
                    }
                }
            });

            // Create an observer to watch for class changes on the <html> element
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        // When the class changes (theme switch), update the legend color
                        chart.options.plugins.legend.labels.color = getLegendColor();
                        chart.update();
                    }
                });
            });

            // Start observing the <html> element
            observer.observe(document.documentElement, { attributes: true });
        }
     }">
    <canvas></canvas>
</div>