<script setup lang="ts">
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip,
    type ChartData,
    type ChartOptions,
} from 'chart.js';
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
);

interface Responsibility {
    title: string;
    importance: number;
    description: string;
}

const props = defineProps<{
    responsibilities: Responsibility[];
}>();

const chartData = computed<ChartData<'bar'>>(() => ({
    labels: props.responsibilities.map((r) => r.title),
    datasets: [
        {
            label: 'Importance Level',
            data: props.responsibilities.map((r) => r.importance),
            backgroundColor: 'rgba(139, 92, 246, 0.7)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 1,
            borderRadius: 6,
        },
    ],
}));

const chartOptions = computed<ChartOptions<'bar'>>(() => ({
    responsive: true,
    maintainAspectRatio: true,
    indexAxis: 'y',
    scales: {
        x: {
            beginAtZero: true,
            max: 100,
            ticks: {
                stepSize: 20,
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)',
            },
        },
        y: {
            grid: {
                display: false,
            },
        },
    },
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            callbacks: {
                label: (context) => {
                    return `Importance: ${context.parsed.x}/100`;
                },
            },
        },
    },
}));
</script>

<template>
    <div class="w-full">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
