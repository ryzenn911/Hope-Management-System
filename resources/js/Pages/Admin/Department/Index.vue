<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";

// ទទួលទិន្នន័យពី Controller
const props = defineProps({
    employees: Array,
    positions: Array,
    totalStaff: Number,
    filters: Object,
});

// បង្កើត Reactive Variable សម្រាប់ចាប់យកតម្លៃ Filter
const selectedPosition = ref(props.filters.position_id || "");

// ប្រើប្រាស់ Watcher នៅពេល User ដូរ Position វានឹងរុញទៅ Backend ភ្លាមៗ (Auto-submit)
watch(selectedPosition, (newValue) => {
    router.get(
        route("departments.index"),
        { position_id: newValue },
        { preserveState: true, preserveScroll: true },
    );
});
</script>

<template>
    <Head title="Departments | Hope for Cambodia Children" />
    <AdminLayout>
        <div class="p-6 bg-white rounded-lg shadow-sm xl:mx-5 mx-0">
            <div
                class="flex flex-col md:flex-row md:justify-between md:items-center border-b pb-4 mb-6"
            >
                <div>
                    <h1
                        class="text-xl font-semibold text-gray-800 font-poppins"
                    >
                        Departments
                    </h1>
                    <p class="text-sm text-gray-500 mt-1 font-poppins">
                        List Staff and Positions
                    </p>
                </div>

                <div class="mt-4 md:mt-0 flex items-center gap-3">
                    <label
                        class="text-sm text-gray-600 font-medium whitespace-nowrap"
                        >Filter by Position:</label
                    >
                    <select
                        v-model="selectedPosition"
                        class="block w-64 border border-gray-300 font-siemreap leading-7 rounded-md p-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 cursor-pointer"
                    >
                        <option value="">All Positions</option>
                        <option
                            v-for="pos in positions"
                            :key="pos.id"
                            :value="pos.id"
                            class="font-siemreap"
                        >
                            {{ pos.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div
                class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between max-w-sm"
            >
                <div>
                    <p class="text-sm text-[#01AAEB] font-medium">
                        Total Staff
                    </p>
                    <h2 class="text-2xl font-bold text-[#01AAEB] mt-1">
                        {{ totalStaff }}
                    </h2>
                </div>
                <div class="p-3 bg-[#01AAEB] rounded-full text-white">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-200 font-poppins whitespace-nowrap">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Staff ID
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Name KH
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Name EN
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Gender
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Marital Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Position
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="emp in employees"
                            :key="emp.id"
                            class="hover:bg-gray-50 transition-colors"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                            >
                                {{ emp.employee_code }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-siemreap"
                            >
                                {{ emp.name_kh }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 uppercase"
                            >
                                {{ emp.name_en }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ emp.gender }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize text-center"
                            >
                                {{ emp.marital_status }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium font-siemreap"
                            >
                                {{ emp.position?.name || "-" }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="[
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        emp.status === 'Active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{ emp.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="employees.length === 0">
                            <td
                                colspan="7"
                                class="px-6 py-10 text-center text-sm text-gray-500 font-siemreap"
                            >
                                No employees found for this position.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
