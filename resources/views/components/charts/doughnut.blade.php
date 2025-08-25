@props(['data'])

<div class="h-80"
     x-data="{
        data: @js($data),
        init() {
            const chart = new Chart(this.$el.querySelector('canvas'), {
                type: 'doughnut',
                data: {
                    labels: this.data.labels,
                    datasets: [{
                        data: this.data.values,
                        backgroundColor: ['#34D399', '#60A5FA', '#FBBF24', '#F87171', '#A78BFA', '#A3E635'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            labels: { color: document.documentElement.classList.contains('dark') ? 'white' : 'black' }
                        } 
                    }
                }
            });

            // Listen for an event from Livewire to update the chart
            Livewire.on('chartDataUpdated', (newData) => {
                chart.data.labels = newData.labels;
                chart.data.datasets[0].data = newData.values;
                chart.update();
            });
        }
     }">
    <canvas></canvas>
</div>