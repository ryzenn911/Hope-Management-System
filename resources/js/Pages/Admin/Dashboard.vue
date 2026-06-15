<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import { Bar } from "vue-chartjs";
import ChartDataLabels from "chartjs-plugin-datalabels";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ChartDataLabels,
);

const props = defineProps({
    activeStaffCount: Number,
    totalStaff: Number,
    chartLabels: Array,
    chartCounts: Array,
});

const chartData = computed(() => ({
    labels: props.chartLabels,
    datasets: [
        {
            label: "Total Staff",
            backgroundColor: "#01AAEB",
            borderRadius: 4,
            data: props.chartCounts,
        },
    ],
}));

const chartOptions = {
    indexAxis: "x",

    responsive: true,
    maintainAspectRatio: false,

    plugins: {
        tooltip: {
            titleFont: {
                family: "Siemreap",
                size: 14,
            },
            bodyFont: {
                family: "Siemreap",
                size: 13,
            },
            footerFont: {
                family: "Siemreap",
                size: 12,
            },
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || "";
                    if (label) {
                        label += ": ";
                    }
                    if (context.parsed.y !== null) {
                        label += context.parsed.y + " នាក់";
                    }
                    return label;
                },
            },
        },

        datalabels: {
            anchor: "end",
            align: "center",
            offset: 4,
            formatter: (value) => {
                return value > 0 ? value + " នាក់" : "";
            },
            font: {
                weight: "bold",
                family: "Siemreap",
                size: 12,
            },
            color: "#1E293B",
        },

        legend: {
            position: "bottom",
            labels: {
                font: {
                    family: "Siemreap",
                    size: 12,
                },
            },
        },
    },

    scales: {
        x: {
            beginAtZero: true,
            grace: "15%",
            ticks: {
                font: {
                    family: "Siemreap",
                    size: 11,
                },
            },
        },

        y: {
            ticks: {
                autoSkip: false,
                stepSize: 1,
                precision: 0,
                font: {
                    family: "Siemreap",
                    size: 11,
                    weight: "bold",
                },
            },
        },
    },
};
</script>

<template>
    <Head title="Dashboard | Hope for Cambodian Children" />
    <AdminLayout>
        <div
            class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 md:gap-6 gap-2 mb-8 md:mt-5 mt-0 md:mx-7 mx-1"
        >
            <div
                class="bg-[#8BC34A] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        Total Staff
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">
                        {{ activeStaffCount }}
                    </p>
                </div>
            </div>

            <div
                class="bg-[#01AAEB] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        Present Today
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">0</p>
                </div>
            </div>

            <div
                class="bg-[#F44336] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        Absent Today
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">0</p>
                </div>
            </div>

            <div
                class="bg-[#FFC107] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        On Leave Today
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">0</p>
                </div>
            </div>
        </div>
        <div class="xl:mx-10 mx-2">
            <div class="h-96 w-full">
                <Bar :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </AdminLayout>
</template>
