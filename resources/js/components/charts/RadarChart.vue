<script setup lang="ts">
import {
    Chart as ChartJS,
    Filler,
    Legend,
    LineElement,
    PointElement,
    RadialLinearScale,
    Tooltip,
    type ChartData,
    type ChartOptions,
} from 'chart.js';
import { computed } from 'vue';
import { Radar } from 'vue-chartjs';

ChartJS.register(
    RadialLinearScale,
    PointElement,
    LineElement,
    Filler,
    Tooltip,
    Legend,
);

interface Skill {
    name: string;
    level: number;
    category: string;
}

const props = defineProps<{
    skills: Skill[];
}>();

const chartData = computed<ChartData<'radar'>>(() => ({
    labels: props.skills.map((skill) => skill.name),
    datasets: [
        {
            label: 'Skill Level',
            data: props.skills.map((skill) => skill.level),
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 2,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(59, 130, 246)',
        },
    ],
}));

const chartOptions = computed<ChartOptions<'radar'>>(() => ({
    responsive: true,
    maintainAspectRatio: true,
    scales: {
        r: {
            beginAtZero: true,
            max: 100,
            ticks: {
                stepSize: 20,
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.1)',
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
                    return `Level: ${context.parsed.r}/100`;
                },
            },
        },
    },
}));
</script>

<template>
    <div class="w-full">
        <Radar :data="chartData" :options="chartOptions" />
    </div>
</template>
